@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto px-4 py-6 space-y-8 ">

        <h1 class="text-3xl font-bold text-white">Csapat szerkesztése</h1>

        @if ($errors->any())
            <div class="bg-red-600 text-white p-4 rounded shadow">
                @foreach ($errors->all() as $error)
                    <p>• {{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('teams.update') }}" enctype="multipart/form-data" class="space-y-6 bg-gray-800 p-6 rounded shadow">
            @csrf

            <div>
                <label for="name" class="block text-white font-semibold mb-1">Csapat neve</label>
                <input type="text" name="name" value="{{ $team->name }}" class="w-full px-4 py-2 rounded bg-gray-700 text-white border border-gray-600 focus:ring-2 focus:ring-orange-500" required>
            </div>

            <div>
                <label for="description" class="block text-white font-semibold mb-1">Leírás</label>
                <textarea name="description" rows="3" class="w-full px-4 py-2 rounded bg-gray-700 text-white border border-gray-600 focus:ring-2 focus:ring-orange-500">{{ $team->description }}</textarea>
            </div>

            <div>
                <label for="logo" class="block text-white font-semibold mb-1">Csapat logó</label>
                <input type="file" name="logo" class="w-full bg-gray-700 text-white border border-gray-600 rounded px-4 py-2">
                @if($team && $team->logo_path)
                    <img src="{{ asset('storage/' . $team->logo_path) }}" alt="Logó" class="mt-2 h-20">
                @endif
            </div>

            <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white px-6 py-2 rounded font-semibold transition">
                Mentés
            </button>
        </form>

        {{-- Csapattagok kezelése --}}
        <div class="bg-gray-800 p-6 rounded shadow">
            <h3 class="text-white text-xl font-semibold mb-4">Csapattagok kezelése</h3>
            <ul class="space-y-3">
                @foreach($team->members as $member)
                    <li class="flex justify-between items-center bg-gray-700 p-3 rounded text-white">
                        <span>{{ $member->name }}</span>
                        @if($member->id !== Auth::id())
                            <form method="POST" action="{{ route('teams.kick', $member) }}">
                                @csrf
                                <button class="bg-red-600 hover:bg-red-700 text-white text-sm px-3 py-1 rounded" onclick="return confirm('Biztosan kirúgod {{ $member->name }} játékost?')">
                                    Kirúgás
                                </button>
                            </form>
                        @else
                            <span class="text-xs bg-blue-600 px-2 py-1 rounded">Kapitány</span>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>

        {{-- Kapitányság átadása --}}
        @if(Auth::id() === $team->owner_id && $team->members->count() > 1)
            <div class="bg-gray-800 p-6 rounded shadow">
                <h4 class="text-white text-lg font-semibold mb-4">Csapatkapitány átadása</h4>
                <form method="POST" action="{{ route('teams.transferOwnership') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label for="new_owner_id" class="block text-white mb-1">Új kapitány:</label>
                        <select name="new_owner_id" class="w-full px-4 py-2 rounded bg-gray-700 text-white border border-gray-600">
                            @foreach($team->members as $member)
                                @if($member->id !== Auth::id())
                                    <option value="{{ $member->id }}">{{ $member->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-2 rounded font-semibold">
                        Átadás
                    </button>
                </form>
            </div>
        @endif

        {{-- Meghívókód --}}
        @if($team && $team->invite_code)
            <div class="bg-gray-800 text-white p-4 rounded shadow">
                <strong>Meghívókód:</strong> <code class="bg-gray-700 px-2 py-1 rounded text-orange-400">{{ $team->invite_code }}</code>
            </div>
        @endif

    </div>
    <x-footer></x-footer>
@endsection
