@extends('layouts.app2')

@section('content')
    <div class="flex min-h-screen">
        @include('admin.partials.sidebar')
    <div class="container mx-auto p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Blogbejegyzések</h1>
            <a href="{{ route('admin.posts.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">+ Új bejegyzés</a>
        </div>

        <div class="grid grid-cols-1 gap-4">
            @foreach($posts as $post)
                <div class="p-4 bg-white rounded shadow flex justify-between items-center">
                    <div>
                        <h2 class="text-lg font-semibold">{{ $post->title }}</h2>
                        <p class="text-sm text-gray-500">Létrehozva: {{ $post->created_at->format('Y.m.d') }}</p>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('admin.posts.edit', $post) }}" class="text-yellow-600 hover:underline">Szerkesztés</a>
                        <form action="{{ route('admin.posts.destroy', $post) }}" method="POST" onsubmit="return confirm('Biztos törlöd?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline">Törlés</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
