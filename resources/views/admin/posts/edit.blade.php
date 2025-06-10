@extends('layouts.app2')

@section('content')
    <div class="flex min-h-screen">
        @include('admin.partials.sidebar')
    <div class="container mx-auto p-6 max-w-xl">
        <h1 class="text-2xl font-bold mb-4">Bejegyzés szerkesztése</h1>

        <form action="{{ route('admin.posts.update', $post) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf @method('PUT')
            <input type="text" name="title" value="{{ $post->title }}" class="w-full border p-2 rounded" required>

            <textarea name="content" rows="10" class="w-full border p-2 rounded" required>{{ $post->content }}</textarea>

            <input type="file" name="image" class="w-full border p-2 rounded">
            @if($post->image)
                <img src="{{ asset('storage/' . $post->image) }}" alt="Kép" class="w-32 mt-2 rounded shadow">
            @endif

            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Frissítés</button>
        </form>
    </div>
    </div>
@endsection
