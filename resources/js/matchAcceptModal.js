export default function matchAcceptModal() {
    return {
        visible: false,
        pendingLobbyCode: null,
        countdown: 50,
        timer: null,

        init() {
            window.Echo.private(`user.${window.USER_ID}`)
                .listen('.match-found', (e) => {
                    console.log('🎯 Match found event érkezett:', e);
                    showMatchAcceptModal(e.lobby_code); // itt hívjuk meg globálisan
                });
        },

        initModal(code) {
            this.pendingLobbyCode = code;
            this.visible = true;
            this.startCountdown();
        },

        showMatchAcceptModal(lobbyCode) {
            this.pendingLobbyCode = lobbyCode;
            this.visible = true;
            this.startCountdown();
        },

        startCountdown() {
            this.countdown = 50;
            this.timer = setInterval(() => {
                this.countdown--;
                if (this.countdown <= 0) {
                    clearInterval(this.timer);
                    this.decline();
                }
            }, 1000);
        },

        accept() {
            fetch('/match/accept', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                },
                body: JSON.stringify({ lobby_code: this.pendingLobbyCode })
            });
            this.visible = false;
            clearInterval(this.timer);
        },

        decline() {
            fetch('/match/decline', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                },
                body: JSON.stringify({ lobby_code: this.pendingLobbyCode })
            });
            this.visible = false;
            clearInterval(this.timer);
        }
    };
}
