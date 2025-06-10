@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 py-6 " x-data="{ modalOpen: false, selectedMatch: null }">
        <h1 class="text-2xl font-bold mb-6">{{ $championship->name }} – Ágrajz</h1>

        <div class="flex gap-10 overflow-auto">
            @foreach ($matchesByRound as $round => $matches)
                <div class="flex flex-col gap-6 min-w-[200px]">
                    <h2 class="text-center font-semibold">Kör {{ $round }}</h2>

                    @foreach ($matches as $match)
                        @php
                            $isCaptain = auth()->check() && auth()->user()->team && auth()->user()->id === auth()->user()->team->owner_id;
                            $userTeamId = auth()->user()->team_id ?? null;
                            $canSubmit = $isCaptain && ($match->team1_id === $userTeamId || $match->team2_id === $userTeamId);
                        @endphp

                        <div
                            class="border p-3 bg-white rounded shadow text-sm text-center hover:bg-gray-100 {{ $canSubmit ? 'cursor-pointer' : '' }}"
                            @if($canSubmit)
                                @click="modalOpen = true; selectedMatch = {
                                id: {{ $match->id }},
                                team1_name: '{{ $match->team1->name ?? '---' }}',
                                team2_name: '{{ $match->team2->name ?? '---' }}'
                            }"
                            @endif

                        >

                            <div class="{{ $match->winner_id === $match->team1_id ? 'text-green-600 font-bold' : '' }}">
                                {{ $match->team1->name ?? '---' }} @if($match->winner_id === $match->team1_id) 👑 @endif
                            </div>
                            <div class="{{ $match->winner_id === $match->team2_id ? 'text-green-600 font-bold' : '' }}">
                                {{ $match->team2->name ?? '---' }} @if($match->winner_id === $match->team2_id) 👑 @endif
                            </div>

                            @if($match->winner)
                                <p class="text-xs text-purple-600 mt-1">✔ Nyertes: {{ $match->winner->name }}</p>
                            @elseif($match->result_image_path)
                                <p class="text-xs text-gray-500 mt-1">Beküldve, admin jóváhagyásra vár</p>
                            @else
                                <p class="text-xs text-gray-400 mt-1">Nincs beküldve eredmény</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>

        {{-- Modal --}}
        <template x-if="modalOpen && selectedMatch">
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                <div class="bg-white p-6 rounded-lg shadow-lg w-[500px] relative">
                    <h2 class="text-xl font-bold mb-4">Meccs eredmény beküldése</h2>

                    <p class="mb-2 font-medium">Team 1: <span x-text="selectedMatch.team1_name"></span></p>
                    <p class="mb-2 font-medium">Team 2: <span x-text="selectedMatch.team2_name"></span></p>

                    <form :action="'/matches/' + selectedMatch.id + '/submit-result'" method="POST" enctype="multipart/form-data">
                        @csrf

                        <label class="block text-sm font-semibold mb-1">Meccs eredménye (pl. 2-1)</label>
                        <input type="text" name="custom_result_text" placeholder="pl. 2-0 vagy 16-10"
                               class="w-full border rounded mb-3" />

                        <label class="block text-sm font-semibold mb-1">Képernyőkép feltöltése</label>
                        <input type="file" name="result_image" class="w-full border rounded mb-4" accept="image/*" required />

                        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded w-full">
                            Beküldés
                        </button>
                    </form>

                    <p class="text-sm text-gray-500 mt-4">Beküldés után admin jóváhagyásra vár.</p>

                    <button @click="modalOpen = false" class="absolute top-2 right-3 text-gray-500 hover:text-black text-xl">
                        &times;
                    </button>
                </div>
            </div>
        </template>
    </div>
@endsection
