@extends('layouts.app')

@section('content')
    <div class="max-w-3xl mx-auto px-4 py-6 ">

        <h1 class="text-3xl font-bold text-white mb-6">Új csapat létrehozása</h1>

        @if ($errors->any())
            <div class="mb-4 bg-red-600 text-white p-4 rounded shadow">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('teams.store') }}" enctype="multipart/form-data" class="space-y-6 bg-gray-800 p-6 rounded shadow">
            @csrf

            <div>
                <label for="name" class="block text-white font-semibold mb-1">Csapat neve</label>
                <input type="text" name="name" id="name" class="w-full px-4 py-2 rounded bg-gray-700 text-white border border-gray-600 focus:outline-none focus:ring-2 focus:ring-orange-500" required>
                @error('name')
                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description" class="block text-white font-semibold mb-1">Csapat leírása</label>
                <textarea name="description" id="description" rows="3" class="w-full px-4 py-2 rounded bg-gray-700 text-white border border-gray-600 focus:outline-none focus:ring-2 focus:ring-orange-500"></textarea>
            </div>

            <div>
                <label for="logo" class="block text-white font-semibold mb-1">Csapat logó</label>
                <input type="file" name="logo" id="logo" accept="image/*" class="w-full text-white bg-gray-700 border border-gray-600 rounded px-4 py-2">
            </div>

            <div>
                <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white px-6 py-2 rounded font-semibold transition">
                    Létrehozás
                </button>
            </div>
        </form>
    </div>
    <x-footer></x-footer>
@endsection
