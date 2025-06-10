@extends('layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto px-6 py-10 text-gray-100">
        <h1 class="text-3xl font-bold text-cyan-400 text-center mb-8">📸 Google Vision OCR - CS2 Scoreboard</h1>

        @if ($errors->any())
            <div class="bg-red-700 text-white p-4 rounded mb-4">
                <strong>Hibák:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- 🖼️ Kép feltöltő form mindig legyen látható --}}
        <form action="{{ url('/ocr-teszt') }}" method="POST" enctype="multipart/form-data" class="bg-gray-800 p-6 rounded shadow mb-8">
            @csrf
            <label for="image" class="block text-cyan-300 font-semibold mb-2">Scoreboard kép feltöltése (max 5MB):</label>
            <input type="file" name="image" id="image" accept="image/*,application/pdf" required
                   class="w-full p-3 mb-4 rounded border border-gray-600 bg-gray-700 text-white">
            <button type="submit"
                    class="w-full bg-cyan-400 hover:bg-cyan-300 text-gray-900 font-bold py-2 px-4 rounded">
                Feldolgozás
            </button>
        </form>


        {{-- Ellenőrzés, hogy van-e feldolgozott adat --}}
        @if(isset($structuredData))
            <div class="bg-gray-800 p-6 rounded shadow mb-6">
                <p><strong>Pálya:</strong> {{ $structuredData['map'] }}</p>
                <p><strong>Időtartam:</strong> {{ $structuredData['duration'] ?? '-' }}</p>
                <p><strong>Dátum:</strong> {{ $structuredData['date'] ?? '-' }}</p>
                <p><strong class="text-green-400">🏆 MVP:</strong>
                    <span class="bg-blue-800 px-2 py-1 rounded text-white font-semibold">{{ $structuredData['mvp'] ?? 'Nincs' }}</span>
                </p>
            </div>

            {{-- Játékos táblázatok --}}
            @foreach ($structuredData['teams'] as $key => $team)
            <h2 class="text-2xl font-semibold mb-4 {{ $key === 'CT' ? 'text-blue-400' : 'text-orange-400' }}">
                {{ $team['name'] }} ({{ $team['score'] }} pont)
            </h2>

            <div class="overflow-x-auto mb-12">
                <table class="w-full text-sm table-auto border border-gray-700 bg-gray-900 rounded overflow-hidden">
                    <thead class="bg-gray-700 text-cyan-300">
                    <tr>
                        <th class="p-3 border border-gray-600 text-left">Név</th>
                        <th class="p-3 border border-gray-600 text-center">Kill</th>
                        <th class="p-3 border border-gray-600 text-center">Assist</th>
                        <th class="p-3 border border-gray-600 text-center">Death</th>
                        <th class="p-3 border border-gray-600 text-center">Pont</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($team['players'] as $player)
                        <tr class="{{ $player['name'] === $structuredData['mvp'] ? 'bg-blue-800 text-white font-bold' : 'hover:bg-gray-800' }}">
                            <td class="p-3 border border-gray-700">{{ $player['name'] }}</td>
                            <td class="p-3 border border-gray-700 text-center">{{ $player['kills'] }}</td>
                            <td class="p-3 border border-gray-700 text-center">{{ $player['assists'] }}</td>
                            <td class="p-3 border border-gray-700 text-center">{{ $player['deaths'] }}</td>
                            <td class="p-3 border border-gray-700 text-center">{{ $player['score'] }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach
        @else
            {{-- Alap nézet feltöltés előtt --}}
            <div class="text-gray-400 text-center mt-8">
                <p>Még nem lett feltöltve kép feldolgozásra.</p>
            </div>
        @endif
    </div>
@endsection
