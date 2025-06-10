@extends('layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto px-4 py-6 ">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-white animate-slide-up">Csapatok</h1>

            @if($userTeam)
                <a href="{{ route('teams.edit') }}" class="bg-yellow-500 hover:bg-yellow-600 text-black px-4 py-2 rounded font-semibold transition">
                    Csapat szerkesztése
                </a>
            @else
                <a href="{{ route('teams.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded font-semibold transition">
                    Csapat létrehozása
                </a>
            @endif
        </div>

        <form method="GET" action="{{ route('teams.index') }}" class="flex flex-wrap gap-2 mb-6">
            <input
                type="text"
                name="search"
                placeholder="Csapat keresése..."
                value="{{ request('search') }}"
                class="w-full sm:w-1/2 px-4 py-2 rounded border border-gray-600 bg-gray-800 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500"
            >
            <button
                type="submit"
                class="bg-orange-600 text-white px-4 py-2 rounded hover:bg-orange-700 transition"
            >
                Keresés
            </button>
        </form>

        @if($userTeam)
            <div class="mb-6 bg-gray-800 p-4 rounded shadow text-white">
                <h3 class="text-lg font-semibold mb-1">Saját csapatod:</h3>
                <a href="{{ route('teams.edit') }}" class="text-orange-400 hover:underline">
                    {{ $userTeam->name }}
                </a>
            </div>
        @endif

        @if($teams->count())
            <div class="space-y-4">
                @foreach($teams as $team)
                    <div class="flex justify-between items-center bg-gray-800 p-4 rounded shadow hover:bg-gray-700 transition">
                        <div>
                            <a href="{{ route('teams.show', $team) }}" class="text-white font-semibold text-lg hover:underline">
                                {{ $team->name }}
                            </a>
                            @if($team->description)
                                <p class="text-sm text-gray-400 mt-1">
                                    {{ Str::limit($team->description, 50) }}
                                </p>
                            @endif
                        </div>
                        @if($team->logo_path)
                            <img src="{{ asset('storage/' . $team->logo_path) }}" alt="Csapat logó" class="w-12 h-12 object-contain rounded">
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $teams->links() }}
            </div>
        @else
            <p class="text-gray-400 mt-4">Nincs találat.</p>
        @endif

    </div>
    <x-footer></x-footer>
@endsection
