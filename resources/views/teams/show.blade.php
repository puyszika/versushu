@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto px-4 py-6 space-y-6 ">

        <div class="flex items-center gap-4">
            @if($team->logo_path)
                <img src="{{ asset('storage/' . $team->logo_path) }}" alt="Logó" class="h-20 w-20 object-contain rounded">
            @endif
            <h1 class="text-3xl font-bold text-white animate-slide-up">{{ $team->name }}</h1>
        </div>

        <div class="bg-gray-800 p-4 rounded shadow text-white">
            <p><strong>Leírás:</strong> {{ $team->description ?? '—' }}</p>
        </div>

        <div class="bg-gray-800 p-4 rounded shadow text-white">
            <h2 class="text-xl font-semibold mb-3">Csapattagok</h2>
            <ul class="space-y-2">
                @foreach($team->members as $member)
                    <li class="flex justify-between items-center bg-gray-700 px-4 py-2 rounded">
                        <span>{{ $member->name }}</span>
                        @if($member->id === $team->owner_id)
                            <span class="text-xs bg-blue-600 px-2 py-1 rounded">Kapitány</span>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>

        {{-- Meccs statisztikák --}}
        @if($teamMatches->count())
            <div class="bg-gray-800 p-4 rounded shadow">
                <h2 class="text-xl font-semibold mb-4">📊 Meccsek statisztikái</h2>

                @foreach($teamMatches as $match)
                    <div class="mb-6 rounded border border-gray-700 overflow-hidden {{ $match->winner_team_id === $team->id ? 'bg-green-700/20' : 'bg-gray-800' }}">
                        <div class="flex justify-between items-center px-4 py-3">
                            <div>
                                <h3 class="text-lg font-bold uppercase text-cyan-300">{{ $match->map ?? 'Ismeretlen pálya' }}</h3>
                                <p class="text-sm text-gray-400">Időpont: {{ $match->created_at->format('Y.m.d H:i') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-400">Eredmény</p>
                                <p class="text-xl font-bold">{{ $match->ct_score }} - {{ $match->t_score }}</p>
                            </div>
                        </div>

                        {{-- Lenytható rész --}}
                        <button
                            class="w-full text-left bg-gray-700 hover:bg-gray-600 px-4 py-2 text-cyan-400 font-semibold"
                            onclick="this.nextElementSibling.classList.toggle('hidden')">
                            👇 Játékos statisztikák megtekintése
                        </button>
                        <div class="hidden px-4 pb-4">
                            <table class="w-full table-auto text-sm mt-4 border border-gray-700">
                                <thead class="bg-gray-700 text-cyan-300">
                                <tr>
                                    <th class="p-2">Név</th>
                                    <th class="p-2">Kill</th>
                                    <th class="p-2">Assist</th>
                                    <th class="p-2">Death</th>
                                    <th class="p-2">Pont</th>
                                    <th class="p-2">MVP</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($match->playerStats as $stat)
                                    <tr class="border-b border-gray-600 {{ $stat->mvp ? 'bg-blue-800 font-bold' : '' }}">
                                        <td class="p-2">{{ $stat->steam_name }}</td>
                                        <td class="p-2">{{ $stat->kills }}</td>
                                        <td class="p-2">{{ $stat->assists }}</td>
                                        <td class="p-2">{{ $stat->deaths }}</td>
                                        <td class="p-2">{{ $stat->score }}</td>
                                        <td class="p-2">{{ $stat->mvp ? '★' : '' }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="bg-gray-800 p-4 rounded shadow text-white">
            <h2 class="text-xl font-semibold mb-3">Csapattörténet / Log</h2>
            <ul class="space-y-2">
                @foreach($team->logs()->latest()->take(10)->get() as $log)
                    <li class="bg-gray-700 px-4 py-2 rounded">
                        <strong>{{ $log->created_at->format('Y.m.d H:i') }}</strong> — {{ $log->details }}
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
    <x-footer></x-footer>
@endsection
