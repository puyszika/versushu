@extends('layouts.app')

@section('content')
    <br>
    <div class="max-w-6xl mx-auto p-6 bg-white rounded-lg shadow-md">
        <!-- Profil fejléc -->
        <div class="flex items-center space-x-6 mb-6">
            <img src="{{ asset('storage/' . $user->profile_picture) }}" alt="Profilkép" class="w-24 h-24 rounded-full shadow">
            <div>
                <h2 class="text-3xl font-bold">{{ $user->name }}</h2>
                <p class="text-sm text-gray-600">Steam ID: <span class="font-mono">{{ $user->steam_id }}</span></p>
                <p class="text-sm text-gray-600">Discord: {{ $user->discord_handle }}</p>
            </div>
        </div>

        <!-- Összegző kártyák -->
        @if ($stats)
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                @php
                    $statList = collect($stats['stats'] ?? [])->pluck('value', 'name');
                    $get = fn($key) => $statList->get($key, 0);
                @endphp

                <div class="bg-gray-100 p-4 rounded shadow text-center">
                    <h4 class="text-sm text-gray-500">Killek</h4>
                    <p class="text-xl font-bold text-green-600">{{ $get('total_kills') }}</p>
                </div>
                <div class="bg-gray-100 p-4 rounded shadow text-center">
                    <h4 class="text-sm text-gray-500">Halálok</h4>
                    <p class="text-xl font-bold text-red-600">{{ $get('total_deaths') }}</p>
                </div>
                <div class="bg-gray-100 p-4 rounded shadow text-center">
                    <h4 class="text-sm text-gray-500">MVP</h4>
                    <p class="text-xl font-bold">{{ $get('total_mvps') }}</p>
                </div>
                <div class="bg-gray-100 p-4 rounded shadow text-center">
                    <h4 class="text-sm text-gray-500">Játszott meccsek</h4>
                    <p class="text-xl font-bold">{{ $get('total_matches_played') }}</p>
                </div>
            </div>

            <!-- Extra rész: K/D arány -->
            <div class="mb-8">
                @php
                    $kd = $get('total_kills') / max(1, $get('total_deaths'));
                @endphp
                <h3 class="text-xl font-semibold mb-2">K/D arány</h3>
                <div class="text-3xl font-bold text-indigo-600">{{ number_format($kd, 2) }}</div>
            </div>

            <!-- Fegyveres rész előkészítve -->
            <div class="mt-6">
                <h3 class="text-xl font-semibold mb-4">Kiemelt fegyver statok</h3>
                <ul class="list-disc pl-6 text-sm text-gray-700">
                    <li>AK-47: {{ $get('total_kills_ak47') }} kill</li>
                    <li>AWP: {{ $get('total_kills_awp') }} kill</li>
                    <li>Deagle: {{ $get('total_kills_deagle') }} kill</li>
                    <li>Knife: {{ $get('total_kills_knife') }} kill</li>
                </ul>
            </div>

        @else
            <div class="text-gray-500">Nincs elérhető statisztika ehhez a Steam ID-hoz.</div>
        @endif
        <div>

            @if ($user->matchStats->count())
                <h2 class="text-xl font-bold text-cyan-400 mt-6">📊 Meccs statisztikáid</h2>
                <table class="table-auto w-full mt-4 border border-gray-700">
                    <thead>
                    <tr class="bg-gray-800 text-left">
                        <th class="p-2">Meccs</th>
                        <th class="p-2">Csapat</th>
                        <th class="p-2">Kill</th>
                        <th class="p-2">Assist</th>
                        <th class="p-2">Death</th>
                        <th class="p-2">MVP</th>
                        <th class="p-2">Score</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($user->matchStats as $stat)
                        <tr class="border-t border-gray-700">
                            <td class="p-2">#{{ $stat->match_id }}</td>
                            <td class="p-2">{{ $stat->team_key }}</td>
                            <td class="p-2">{{ $stat->kills }}</td>
                            <td class="p-2">{{ $stat->assists }}</td>
                            <td class="p-2">{{ $stat->deaths }}</td>
                            <td class="p-2">{{ $stat->mvp }}</td>
                            <td class="p-2">{{ $stat->score }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
        </div>


    </div>
    <br>
    <x-footer></x-footer>
@endsection
