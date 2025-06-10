@extends('layouts.app2')

@section('content')
    <div class="flex min-h-screen">
        @include('admin.partials.sidebar')
    <div class="max-w-4xl mx-auto p-6 bg-white shadow rounded-xl">
        <h2 class="text-2xl font-bold mb-4">{{ $championship->name }}</h2>
        <p class="mb-4">{{ $championship->description }}</p>

        @if($championship->image_path)
            <img src="{{ asset('storage/' . $championship->image_path) }}" class="mb-4 w-full rounded">
        @endif

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div><strong>Formátum:</strong> {{ $championship->format }}</div>
            <div><strong>Double Elimination:</strong> {{ $championship->double_elimination ? 'Igen' : 'Nem' }}</div>
        </div>

        <div class="mt-4">
            <strong>Díjazás:</strong>
            <ul class="list-disc list-inside">
                @foreach (range(1, 4) as $i)
                    @php $field = 'reward_' . $i; @endphp
                    @if (!empty($championship->$field))
                        <li>{{ $championship->$field }}</li>
                    @endif
                @endforeach
            </ul>
        </div>
        <h3 class="text-xl font-semibold mt-6 mb-2">Csapatok a bajnokságban</h3>

        @if($championship->teams->isEmpty())
            <p class="text-gray-500">Nincs regisztrált csapat.</p>
        @else
            <ul class="space-y-3">
                @foreach ($championship->teams as $team)
                    <li class="flex justify-between items-center">
                        <div>
                            <a href="{{ route('teams.show', $team) }}" class="text-blue-600 hover:underline">
                                {{ $team->name }}
                            </a>
                            <span class="text-gray-500 text-sm">({{ $team->members->count() }} játékos)</span>
                        </div>

                        <form action="{{ route('admin.championships.teams.detach', [$championship, $team]) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-600 hover:underline">Eltávolítás</button>
                        </form>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
    </div>
@endsection
