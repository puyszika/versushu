@extends('layouts.app')

@section('content')
    <div class="max-w-xl mx-auto px-4 py-6 ">

        <h1 class="text-3xl font-bold text-white mb-6">Csatlakozás egy meglévő csapathoz</h1>

        <form method="POST" action="{{ route('teams.handleJoin') }}" class="bg-gray-800 p-6 rounded shadow space-y-6">
            @csrf

            <div>
                <label for="invite_code" class="block text-white font-semibold mb-1">Meghívókód</label>
                <input
                    type="text"
                    name="invite_code"
                    id="invite_code"
                    class="w-full px-4 py-2 rounded bg-gray-700 text-white border border-gray-600 focus:outline-none focus:ring-2 focus:ring-orange-500"
                    required
                >
                @error('invite_code')
                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white px-6 py-2 rounded font-semibold transition">
                Csatlakozom
            </button>
        </form>
</div>
    <x-footer></x-footer>
@endsection
