export default function Matchmaking() {
    return {
        inQueue: false,
        showMatchPopup: false,
        proposalId: null,
        timer: null,

        checkQueueStatus() {
            // Később: pl. lekérdezni, benne vagyunk-e
        },

        startMatchmaking() {
            fetch('/api/matchmaking/join', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.head.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                }
            }).then(() => {
                this.inQueue = true;
            });
        },

        leaveQueue() {
            fetch('/api/matchmaking/leave', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.head.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                }
            }).then(() => {
                this.inQueue = false;
            });
        },

        acceptMatch() {
            clearTimeout(this.timer);
            this.showMatchPopup = false;
            this.inQueue = false;

            fetch(`/api/match-proposal/${this.proposalId}/accept`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.head.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                }
            });
        },

        declineMatch() {
            clearTimeout(this.timer);
            this.showMatchPopup = false;
            this.inQueue = false;

            fetch(`/api/match-proposal/${this.proposalId}/decline`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.head.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                }
            });
        },

        init() {
            Echo.private('user.' + window.userId)
                .listen('MatchProposalCreated', (e) => {
                    this.proposalId = e.proposal_id;
                    this.showMatchPopup = true;

                    this.timer = setTimeout(() => {
                        this.declineMatch();
                    }, 15000);
                });
        }
    };
}
