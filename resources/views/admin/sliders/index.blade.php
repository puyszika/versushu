@extends('layouts.app2')

@section('content')
    <div class="flex min-h-screen">
        <!-- Sidebar -->
    @include('admin.partials.sidebar')

        <div class="max-w-4xl mx-auto bg-gray-800 text-white p-6 rounded-xl shadow-xl mt-10">
            <h2 class="text-2xl font-bold mb-6">🎞️ Slider képek kezelése</h2>

            {{-- Feltöltés --}}
            <form action="{{ route('admin.sliders.store') }}" method="POST" enctype="multipart/form-data" class="flex items-center space-x-4 mb-8">
                @csrf
                <input type="file" name="image" required
                       class="block w-full text-sm text-gray-300 bg-gray-700 border border-gray-600 rounded-lg cursor-pointer focus:outline-none">
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200">
                    📤 Feltöltés
                </button>
            </form>

            {{-- Galéria --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                @foreach ($sliders as $slider)
                    <div class="relative rounded-lg overflow-hidden shadow-lg group">
                        <img src="{{ asset($slider->image_path) }}" alt="Slider kép"
                             class="w-full h-48 object-cover transition-transform duration-300 group-hover:scale-105">

                        <form action="{{ route('admin.sliders.destroy', $slider) }}" method="POST"
                              class="absolute top-2 right-2">
                            @csrf
                            @method('DELETE')
                            <button class="bg-red-600 hover:bg-red-700 text-white text-sm px-3 py-1 rounded-lg shadow-md transition duration-200">
                                ❌ Törlés
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>

@endsection
