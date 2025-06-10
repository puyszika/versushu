@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-6 ">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-white animate-slide-up">🎮 Matchmaking</h1>
        </div>
<div x-data="Matchmaking()" x-init="checkQueueStatus()" class="p-6 space-y-4">

    <!-- GOMB: belépés matchmakingbe -->
    <template x-if="!inQueue">
        <button @click="startMatchmaking" class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition">
            🚀 Matchmaking indítása
        </button>
    </template>

    <!-- VÁRAKOZÁS -->
    <template x-if="inQueue">
        <div class="flex items-center gap-4">
            <span class="text-yellow-400 font-semibold">⌛ Várakozás játékosokra...</span>
            <button @click="leaveQueue" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">❌ Kilépés</button>
        </div>
    </template>

    <!-- MATCH ELFOGADÓ POPUP -->
    <div x-show="showMatchPopup" class="fixed top-0 left-0 w-full h-full bg-black bg-opacity-80 flex items-center justify-center z-50">
        <div class="bg-gray-900 p-6 rounded-xl text-white text-center space-y-4">
            <h2 class="text-2xl font-bold">⚔️ Match kész!</h2>
            <p class="text-lg">15 másodperc, hogy elfogadd!</p>
            <div class="flex justify-center gap-4">
                <button @click="acceptMatch()" class="bg-green-600 px-4 py-2 rounded hover:bg-green-700">✅ Elfogadom</button>
                <button @click="declineMatch()" class="bg-red-600 px-4 py-2 rounded hover:bg-red-700">❌ Elutasítom</button>
            </div>
        </div>
    </div>

</div>
</div>
@endsection

@section('scripts')
<script>
function Matchmaking() {
    return {
        inQueue: false,
        showMatchPopup: false,
        proposalId: null,
        timer: null,

        checkQueueStatus() {
            // opcionális: lekérdezés, hogy benne van-e a váróban
        },

        startMatchmaking() {
            fetch('/api/matchmaking/join', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
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
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
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
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
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
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            });
        },

        // esemény figyelés
        init() {
            Echo.private('user.{{ auth()->id() }}')
                .listen('MatchProposalCreated', (e) => {
                    this.proposalId = e.proposal_id;
                    this.showMatchPopup = true;

                    this.timer = setTimeout(() => {
                        this.declineMatch();
                    }, 15000);
                });
        }
    }
}
</script>
@endsection
