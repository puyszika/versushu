@extends('layouts.app2')

@section('content')
    <div class="flex min-h-screen">
    @include('admin.partials.sidebar')
    <div class="container mx-auto p-6 max-w-xl">

        <h1 class="text-2xl font-bold mb-4">Új blogbejegyzés</h1>

        <form action="{{ route('admin.posts.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <input type="text" name="title" placeholder="Cím" class="w-full border p-2 rounded" required>

            <textarea name="content" rows="10" placeholder="Tartalom" class="w-full border p-2 rounded" required></textarea>

            <input type="file" name="image" class="w-full border p-2 rounded">

            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Mentés</button>
        </form>
    </div>
    </div>
@endsection
