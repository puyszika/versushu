@extends('layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto px-4 py-6 ">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-white animate-slide-up">{{ $championship->name }}</h1>
    </div>
    <div class="max-w-6xl mx-auto p-6 bg-white shadow rounded-xl">

        <h1 class="text-3xl font-bold mb-4">{{ $championship->name }}</h1>

        @if($championship->image_path)
            <img src="{{ asset('storage/' . $championship->image_path) }}" class="w-full h-64 object-cover rounded mb-4" alt="Bajnokság képe">
        @endif

        <p class="mb-4">{{ $championship->description }}</p>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
            @for($i = 1; $i <= 4; $i++)
                @php $reward = 'reward_' . $i; @endphp
                @if (!empty($championship->$reward))
                    <div class="bg-gray-100 p-2 rounded text-center">
                        <strong>{{ $i }}. Díj</strong><br>
                        {{ $championship->$reward }}
                    </div>
                @endif
            @endfor
        </div>

        <p><strong>Formátum:</strong> {{ $championship->format }}</p>
        <p><strong>Alsó-felső ágas:</strong> {{ $championship->double_elimination ? 'Igen' : 'Nem' }}</p>

        <form action="{{ route('championships.register', $championship) }}" method="POST" class="mt-6">
            @csrf
            <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Regisztráció a bajnokságra
            </button>
        </form>

        <h3 class="text-xl font-semibold mt-6 mb-2">Regisztrált csapatok</h3>

        @if($championship->teams->isEmpty())
            <p class="text-gray-500">Még nincs regisztrált csapat.</p>
        @else
            <ul class="space-y-2">
                @foreach ($championship->teams as $team)
                    <li>
                        <a href="{{ route('teams.show', $team) }}" class="text-blue-600 hover:underline">
                            {{ $team->name }}
                        </a>
                        <span class="text-gray-500 text-sm">({{ $team->members->count() }} játékos)</span>
                    </li>
                @endforeach
            </ul>
        @endif

    </div>
    </div>
    <x-footer></x-footer>
@endsection
