@extends('layouts.app2')

@section('content')
    <div class="flex min-h-screen">
        @include('admin.partials.sidebar')
    <div class="max-w-4xl mx-auto p-6 bg-white shadow rounded-xl">
        <h2 class="text-2xl font-bold mb-6">Bajnokság szerkesztése</h2>

        <form action="{{ route('admin.championships.update', $championship) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block font-semibold mb-1">Név</label>
                <input type="text" name="name" value="{{ old('name', $championship->name) }}" class="w-full border rounded px-3 py-2" required>
            </div>

            <div class="mb-4">
                <label class="block font-semibold mb-1">Leírás</label>
                <textarea name="description" class="w-full border rounded px-3 py-2" rows="4">{{ old('description', $championship->description) }}</textarea>
            </div>

            <div class="mb-4">
                <label class="block font-semibold mb-1">Kép (logó vagy háttér)</label>
                <input type="file" name="image" accept="image/*" class="block">
                @if ($championship->image_path)
                    <img src="{{ asset('storage/' . $championship->image_path) }}" class="mt-2 h-24 object-cover">
                @endif
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                @foreach (range(1, 4) as $i)
                    @php $field = 'reward_' . $i; @endphp
                    <div>
                        <label class="block font-semibold mb-1">{{ $i }}. Díj</label>
                        <input type="text" name="{{ $field }}" value="{{ old($field, $championship->$field) }}" class="w-full border rounded px-2 py-1">
                    </div>
                @endforeach
            </div>

            <div class="mb-4">
                <label class="block font-semibold mb-1">Formátum</label>
                <select name="format" class="w-full border rounded px-3 py-2">
                    @foreach(['BO1', 'BO3', 'BO5'] as $format)
                        <option value="{{ $format }}" {{ old('format', $championship->format) === $format ? 'selected' : '' }}>{{ $format }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="double_elimination" value="1" class="mr-2" {{ old('double_elimination', $championship->double_elimination) ? 'checked' : '' }}>
                    Alsó-felső ágas (Double Elimination)
                </label>
            </div>

            <div class="mt-6">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Mentés
                </button>
            </div>
        </form>
    </div>
    </div>
@endsection
