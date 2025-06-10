@extends('layouts.app2')

@section('content')
    <div class="flex min-h-screen" x-data="{ modalOpen: false, selectedMatch: null }">
        @include('admin.partials.sidebar')

        <div class="p-6 w-full">
            <h1 class="text-2xl font-bold mb-6">{{ $championship->name }} – Admin ágrajz</h1>

            <div class="flex gap-16 overflow-auto">
                @foreach ($matchesByRound as $round => $matches)
                    <div class="flex flex-col gap-4 min-w-[220px]">
                        <h2 class="text-center font-semibold text-lg">Kör {{ $round }}</h2>
                        <p class="text-center text-sm text-gray-500">Összesen: {{ $matches->count() }} meccs</p>

                        @foreach ($matches as $match)
                            <div
                                class="border p-3 bg-white rounded shadow text-sm hover:bg-gray-100 cursor-pointer"
                                @click="
                                    selectedMatch = {
                                        id: {{ $match->id }},
                                        team1_id: {{ $match->team1_id ?? 'null' }},
                                        team2_id: {{ $match->team2_id ?? 'null' }},
                                        team1: { name: '{{ $match->team1->name ?? '---' }}' },
                                        team2: { name: '{{ $match->team2->name ?? '---' }}' },
                                        winner_id: {{ $match->winner_id ?? 'null' }},
                                        result_image_path: '{{ $match->result_image_path ?? '' }}',
                                        custom_result_text: '{{ $match->custom_result_text ?? '' }}',
                                        submissions: JSON.parse(atob('{{ base64_encode(json_encode(
                                            $match->submissions->map(fn($s) => [
                                                'team_name' => $s->team->name ?? 'Ismeretlen',
                                                'result_image_path' => $s->result_image_path,
                                                'custom_result_text' => $s->custom_result_text,
                                                'ocr_result' => $s->ocr_result,
                                            ]),
                                            JSON_UNESCAPED_UNICODE
                                        )) }}'))
                                    };
                                    modalOpen = true;
                                "
                            >
                                <div class="{{ $match->winner_id === $match->team1_id ? 'text-green-600 font-bold' : '' }}">
                                    {{ $match->team1->name ?? '---' }} @if($match->winner_id === $match->team1_id) 👑 @endif
                                </div>
                                <div class="{{ $match->winner_id === $match->team2_id ? 'text-green-600 font-bold' : '' }}">
                                    {{ $match->team2->name ?? '---' }} @if($match->winner_id === $match->team2_id) 👑 @endif
                                </div>

                                <p class="mt-2 text-xs text-gray-500">
                                    {{ $match->status === 'completed' ? 'Eredmény rögzítve' : 'Kattints a szerkesztéshez' }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>

            {{-- Modal --}}
            <template x-if="modalOpen && selectedMatch">
                <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                    <div class="bg-white p-6 rounded-lg shadow-lg w-[500px] relative">
                        <h2 class="text-xl font-bold mb-4">Meccs részletei</h2>

                        <p class="mb-2 font-medium">Team 1: <span x-text="selectedMatch.team1.name"></span></p>
                        <p class="mb-2 font-medium">Team 2: <span x-text="selectedMatch.team2.name"></span></p>

                        <div x-show="selectedMatch.submissions && selectedMatch.submissions.length > 0" class="mt-4">
                            <h3 class="text-md font-semibold mb-2">Beküldött eredmények:</h3>

                            <template x-for="submission in selectedMatch.submissions" :key="submission.result_image_path">
                                <div class="border border-gray-300 rounded p-3 mb-4 text-sm">
                                    <p class="font-semibold mb-1 text-gray-800">
                                        Beküldő csapat: <span x-text="submission.team_name || 'Ismeretlen'"></span>
                                    </p>

                                    <template x-if="submission.custom_result_text">
                                        <p class="text-blue-600 mb-2">📜 <span x-text="'\"' + submission.custom_result_text + '\"'"></span></p>
                                    </template>

                                    <template x-if="submission.result_image_path">
                                        <a :href="'/storage/' + submission.result_image_path" target="_blank"
                                           class="text-indigo-600 underline text-sm mb-2 block">
                                            📁 Kép megnyitása új lapon
                                        </a>
                                    </template>

                                    <template x-if="submission.ocr_result">
                                        <div class="bg-gray-100 border border-dashed border-gray-400 rounded p-2 mt-2">
                                            <p class="text-xs text-gray-500 mb-1">🔍 OCR által kiolvasott szöveg:</p>
                                            <pre class="text-sm text-gray-700 whitespace-pre-wrap" x-text="submission.ocr_result"></pre>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>

                        {{-- ✅ Fix form --}}
                        <form :action="`/admin/matches/${selectedMatch.id}/verify`" method="POST" class="mt-4">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">

                            <label class="block text-sm font-semibold mb-1">Győztes kiválasztása</label>
                            <select name="winner_id" class="w-full border rounded mb-4">
                                <template x-if="selectedMatch.team1">
                                    <option :value="selectedMatch.team1_id" x-text="selectedMatch.team1.name"></option>
                                </template>
                                <template x-if="selectedMatch.team2">
                                    <option :value="selectedMatch.team2_id" x-text="selectedMatch.team2.name"></option>
                                </template>
                            </select>

                            <label class="block text-sm font-semibold mb-1">Diszkvalifikált csapat (opcionális)</label>
                            <select name="disqualified_team_id" class="w-full border rounded mb-4">
                                <option value="">-- Nincs kizárva --</option>
                                <template x-if="selectedMatch.team1">
                                    <option :value="selectedMatch.team1_id" x-text="selectedMatch.team1.name"></option>
                                </template>
                                <template x-if="selectedMatch.team2">
                                    <option :value="selectedMatch.team2_id" x-text="selectedMatch.team2.name"></option>
                                </template>
                            </select>

                            <label class="block text-sm font-semibold mb-1">Admin megjegyzés (nem kötelező)</label>
                            <textarea name="admin_note" rows="3" class="w-full border rounded mb-4"
                                      placeholder="Pl. screenshot nem megfelelő, szabályszegés, stb."></textarea>

                            <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded w-full">
                                Mentés
                            </button>
                        </form>

                        <button @click="modalOpen = false"
                                class="absolute top-2 right-3 text-gray-500 hover:text-black text-xl">
                            &times;
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>
@endsection
