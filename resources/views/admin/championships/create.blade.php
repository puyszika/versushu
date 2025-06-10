@extends('layouts.app')

@section('content')
    <br>
    <div class="max-w-4xl mx-auto p-6 bg-white shadow rounded-xl">
        <h2 class="text-2xl font-bold mb-6">Bajnokság létrehozása</h2>

        @error('name')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror

        <form action="{{ route('admin.championships.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-4">
                <label class="block font-semibold mb-1">Név</label>
                <input type="text" name="name" value="{{ old('name') }}" class="w-full border rounded px-3 py-2" required>
            </div>

            <div class="mb-4">
                <label class="block font-semibold mb-1">Leírás</label>
                <textarea name="description" class="w-full border rounded px-3 py-2" rows="4"></textarea>
            </div>

            <div class="mb-4">
                <label class="block font-semibold mb-1">Kép (logó vagy háttér)</label>
                <input type="file" name="image" accept="image/*" class="block">
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                <div>
                    <label class="block font-semibold mb-1">1. Díj</label>
                    <input type="text" name="reward_1" value="{{ old('name') }}" class="w-full border rounded px-2 py-1">
                </div>
                <div>
                    <label class="block font-semibold mb-1">2. Díj</label>
                    <input type="text" name="reward_2" value="{{ old('name') }}" class="w-full border rounded px-2 py-1">
                </div>
                <div>
                    <label class="block font-semibold mb-1">3. Díj</label>
                    <input type="text" name="reward_3" value="{{ old('name') }}" class="w-full border rounded px-2 py-1">
                </div>
                <div>
                    <label class="block font-semibold mb-1">4. Díj</label>
                    <input type="text" name="reward_4" value="{{ old('name') }}" class="w-full border rounded px-2 py-1">
                </div>
            </div>

            <div class="mb-4">
                <label class="block font-semibold mb-1">Formátum</label>
                <select name="format" class="w-full border rounded px-3 py-2">
                    <option value="BO1">Best of 1</option>
                    <option value="BO3">Best of 3</option>
                    <option value="BO5">Best of 5</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="double_elimination" class="mr-2">
                    Alsó-felső ágas (Double Elimination)
                </label>
            </div>

            <div class="mt-6">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Bajnokság létrehozása
                </button>
            </div>
        </form>
    </div>
@endsection
