export default function(code) {
    return {
        code,
        maps: [],
        finalMap: null,
        log: [],
        logCounter: 0,
        currentTurn: null,
        slots: { ct: Array(5).fill(null), t: Array(5).fill(null) },
        spectators: [null, null],
        isHost: false,
        phaseTimer: 30,
        timerInterval: null,
        banPhase: 1,
        banSteps: 0,

        init() {
            console.log('🟢 Alpine init fut');
            this.isHost = window.isHost ?? false;
            this.currentTurn = Math.random() > 0.5 ? 'ct' : 't';
            this.addLog(`🎲 A ${this.currentTurn.toUpperCase()} oldal kezdheti a bannolást.`);

            axios.get(`/api/lobby/${this.code}/maps`).then(res => {
                this.maps = res.data;
                console.log("🧠 Kezdeti map állapotok:", this.maps);
            });

            Echo.join(`lobby.${this.code}`)
                .here(users => {
                users.forEach(user => {
                    if (user.role && user.slot !== null) {
                        if (user.role === 'spectator') {
                            this.spectators[user.slot] = user;
                        } else {
                            this.addPlayer(user, user.role, user.slot);
                        }
                    }
                    this.addLog(`${user.name} már bent van.`);
                });
            })
                .joining(user => this.addLog(`${user.name} csatlakozott a lobbyhoz.`))
                .listen('PlayerJoined', e => this.addPlayer(e.user, e.role, e.slot))
                .listen('SpectatorJoined', e => this.spectators[e.slot] = e.user)

                .listen('MapBanned', e => {
                    this.updateMapStatus(e.map, 'banned');
                    this.addLog(`❌ ${e.byRole.toUpperCase()} bannolta: ${e.map}`);
                })
                .listen('MapPicked', e => {
                    this.updateMapStatus(e.map, 'picked');
                    this.addLog(`${e.role.toUpperCase()} választotta: ${e.map}`);
                })
                .listen('MapFinalized', e => {
                    this.finalMap = e.map;
                    this.updateMapStatus(e.map, 'picked');
                    this.addLog(`🏁 A végső pálya: ${e.map}`);
                });

            console.log("🔌 Echo listener setup:", Echo);
        },

        updateMapStatus(mapName, newStatus) {
            this.maps = this.maps.map(m =>
                m.map_name === mapName ? { ...m, status: newStatus } : m
            );
        },

        addLog(text) {
            this.log.push({ id: this.logCounter++, text: `[${new Date().toLocaleTimeString()}] ${text}` });
            this.$nextTick(() => {
                const c = this.$refs?.logContainer;
                if (c) c.scrollTop = c.scrollHeight;
            });
        },

        getMyRole() {
            const userId = window.AuthUser?.id;
            return ['ct', 't'].find(role =>
                this.slots[role].some(p => p?.id === userId)
            );
        },

        onMapClick(map) {
            const userRole = this.getMyRole();
            const action = this.phaseTimer > 0 ? 'ban' : 'pick';

            if (!this.canClickMap(map)) return;

            axios.post(`/api/lobby/${this.code}/map-action`, {
                action, map: map.map_name
            }).then(() => {
                this.addLog(`📨 ${action.toUpperCase()} request elküldve: ${map.map_name}`);
                if (action === 'ban') {
                    this.updateMapStatus(map.map_name, 'banned');
                    this.handleBanAdvance();
                } else {
                    this.updateMapStatus(map.map_name, 'picked');
                }
            }).catch(err => {
                this.addLog("❌ Hiba történt: " + err.response?.data?.error);
            });
        },

        canClickMap(map) {
            return this.getMyRole() === this.currentTurn &&
                map.status === 'available' &&
                this.timerInterval !== null &&
                this.maps.filter(m => m.status === 'available').length > 1;
        },

        addPlayer(user, role, slot) {
            this.removeUserFromAllSlots(user.id);
            this.slots[role][slot] = user;
        },

        joinSlot(role, slotIndex) {
            axios.post(`/api/lobby/${this.code}/join`, {
                role, slot_index: slotIndex
            }).then(() => {
                this.addLog(`✅ ${role.toUpperCase()} oldalra csatlakoztál a(z) ${slotIndex + 1}. helyre.`);
            });
        },

        joinSpectator(index) {
            this.removeUserFromAllSlots(window.AuthUser?.id);
            axios.post(`/api/lobby/${this.code}/spectate`, {
                slot_index: index
            }).then(() => {
                this.addLog(`👁️ Nézőként csatlakoztál (${index + 1})`);
            });
        },

        getMapImage(mapName) {
            return `/images/maps/logos/${mapName.replace(/\s/g, '')}.png`;
        },

        startTimerManually() {
            if (this.timerInterval) return;
            this.addLog('⏱️ Pick/Ban fázis elindítva!');
            this.timerInterval = setInterval(() => {
                if (this.phaseTimer > 0) {
                    this.phaseTimer--;
                } else {
                    clearInterval(this.timerInterval);
                    this.autoBan();
                }
            }, 1000);
        },

        autoBan() {
            const available = this.maps.filter(m => m.status === 'available');
            if (available.length === 1) {
                this.finalizeMap();
                return;
            }

            const map = available[Math.floor(Math.random() * available.length)];
            this.addLog(`🤖 Automatikus tiltás: ${map.map_name}`);
            axios.post(`/api/lobby/${this.code}/map-action`, {
                action: 'ban', map: map.map_name
            }).then(() => this.handleBanAdvance());
        },

        finalizeMap() {
            const available = this.maps.filter(m => m.status === 'available');
            if (available.length === 1) {
                const mapName = available[0].map_name;
                axios.post(`/api/lobby/${this.code}/map-action`, {
                    action: 'pick', map: mapName
                }).catch(err => {
                    this.addLog("❌ Hiba a végső pálya kiválasztásakor: " + err.response?.data?.error);
                });
            }
        },

        handleBanAdvance() {
            clearInterval(this.timerInterval);
            this.banSteps++;

            if (this.banPhase === 1 && this.banSteps === 2) {
                this.banPhase = 2; this.banSteps = 0;
                this.currentTurn = this.currentTurn === 'ct' ? 't' : 'ct';
                this.addLog(`▶️ Fázis 2: ${this.currentTurn.toUpperCase()} 3 pályát tilthat.`);
            } else if (this.banPhase === 2 && this.banSteps === 3) {
                this.banPhase = 3; this.banSteps = 0;
                this.currentTurn = this.currentTurn === 'ct' ? 't' : 'ct';
                this.addLog(`▶️ Fázis 3: ${this.currentTurn.toUpperCase()} 1 pályát tilthat.`);
            } else if (this.banPhase === 3 && this.banSteps === 1) {
                this.addLog('🏁 A bann fázis véget ért!');
                this.finalizeMap();
                return;
            } else {
                this.currentTurn = this.currentTurn === 'ct' ? 't' : 'ct';
                this.addLog(`➡️ Most ${this.currentTurn.toUpperCase()} tilthat.`);
            }

            this.phaseTimer = 15;
            this.startTimerManually();
        },

        removeUserFromAllSlots(userId) {
            ['ct', 't'].forEach(role => {
                this.slots[role] = this.slots[role].map(p => (p?.id === userId ? null : p));
            });

            this.spectators = this.spectators.map(p => (p?.id === userId ? null : p));
        },
        getTeamName(role) {
            const captain = this.slots[role]?.[0];
            if (!captain || !captain.name) return role === 'ct' ? 'TEAM1' : 'TEAM2';
            return captain.team_name || `${captain.name}_csapata`;
        },
    };
}
