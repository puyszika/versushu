@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 py-6 ">
        <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-white animate-slide-up">Bajnokságok</h1>
        </div>
        {{-- Bajnokság lista --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($championships as $championship)
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    @if($championship->image_path)
                        <img src="{{ asset('storage/' . $championship->image_path) }}" alt="{{ $championship->name }}" class="w-full h-40 object-cover">
                    @endif
                    <div class="p-4">
                        <h3 class="text-lg font-bold mb-1">{{ $championship->name }}</h3>
                        <p class="text-sm text-gray-600 mb-2">{{ Str::limit($championship->description, 100) }}</p>

                        <div class="text-xs text-gray-500 mb-2">
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

                        <div class="flex justify-between items-center">
                            <a href="{{ route('championships.show', $championship) }}" class="text-blue-600 hover:underline">Megtekintés</a>

                            @if(session('success'))
                                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                                    {{ session('success') }}
                                </div>
                            @endif

                            @if(session('error'))
                                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                                    {{ session('error') }}
                                </div>
                            @endif


                            <form action="{{ route('championships.register', $championship) }}" method="POST">
                                @csrf
                                <button class="text-green-600 hover:underline">Regisztráció</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if($championships->isEmpty())
            <div class="text-center text-gray-500 mt-8">
                Még nincs aktív bajnokság.
            </div>
        @endif
    </div>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>



    <x-footer></x-footer>
@endsection
