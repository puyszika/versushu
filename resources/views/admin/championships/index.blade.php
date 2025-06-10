@extends('layouts.app2')

@section('content')

    <div class="flex min-h-screen">
        @include('admin.partials.sidebar')


        <div class="max-w-7xl mx-auto p-6 w-full">

            <h2 class="text-3xl font-bold mb-6">Bajnokságok</h2>

            <!-- Kereső -->
            <form method="GET" action="{{ route('admin.championships.index') }}" class="mb-6 flex justify-end">
                <input
                    type="text"
                    name="search"
                    placeholder="🔍 Bajnokság keresése..."
                    class="border border-gray-300 px-4 py-2 rounded shadow-sm focus:outline-none focus:ring focus:border-blue-300 w-full md:w-1/3"
                    value="{{ request('search') }}"
                >
            </form>

            <div class="flex justify-end mb-6">
                <a href="{{ route('admin.championships.create') }}"
                   class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded">
                    ➕ Bajnokság létrehozása
                </a>
            </div>
            <!-- Bajnokság kártyák -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($championships as $championship)
                    <div class="bg-white shadow-md rounded-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                        @if($championship->image_path)
                            <img src="{{ asset('storage/' . $championship->image_path) }}" alt="Bajnokság képe" class="w-full h-40 object-cover">
                        @endif

                        <div class="p-4">
                            <h3 class="text-xl font-semibold mb-2">{{ $championship->name }}</h3>
                            <p class="text-gray-600 mb-2">{{ Str::limit($championship->description, 100) }}</p>

                            <div class="mb-2 text-sm text-gray-500">
                                <strong>Formátum:</strong> {{ $championship->format }}<br>
                                <strong>Double Elimination:</strong> {{ $championship->double_elimination ? 'Igen' : 'Nem' }}
                            </div>

                            <div class="text-sm mb-3">
                                <strong>Díjazás:</strong>
                                <ul class="list-disc pl-5">
                                    @foreach (range(1, 4) as $i)
                                        @php $field = 'reward_' . $i; @endphp
                                        @if (!empty($championship->$field))
                                            <li>{{ $championship->$field }}</li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>


                            <div class="flex justify-between items-center mt-4 space-x-2">
                                <a href="{{ route('admin.championships.show', $championship) }}"
                                   class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 text-sm">
                                    Megtekintés
                                </a>
                                <a href="{{ route('admin.championships.edit', $championship) }}"
                                   class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600 text-sm">
                                    Szerkesztés
                                </a>
                                <form action="{{ route('admin.championships.destroy', $championship) }}" method="POST"
                                      onsubmit="return confirm('Biztosan törölni szeretnéd?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700 text-sm">
                                        Törlés
                                    </button>
                                </form>

                                @if($championship->status === 'pending')
                                    <form action="{{ route('admin.championships.startTournament', $championship) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700 text-sm">
                                            ▶️ Elindítás
                                        </button>
                                    </form>
                                @endif

                                @if($championship->status === 'in_progress')
                                    <form action="{{ route('admin.championships.finish', $championship) }}" method="POST" class="inline ml-2">
                                        @csrf
                                        <button type="submit" class="bg-gray-700 text-white px-3 py-1 rounded hover:bg-gray-800 text-sm">
                                            🛑 Lezárás
                                        </button>
                                    </form>
                                @endif
                                <a href="{{ route('admin.championships.bracket', $championship) }}" class="text-sm text-blue-600 hover:underline">
                                    Bracket megtekintése
                                </a>


                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Üzenet ha nincs találat -->
            @if($championships->count() === 0 && request('search'))
                <div class="mt-6 text-center text-gray-500">
                    Nincs találat a keresésre.
                </div>
            @elseif($championships->count() === 0)
                <div class="mt-6 text-center text-gray-500">
                    Nincs még létrehozott bajnokság.
                </div>
            @endif
        </div>
    </div>
@endsection
