@extends('layouts.app')

@section('content')

    <div class="max-w-6xl mx-auto px-4 py-6 ">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-white">Blogbejegyzések</h1>
            @auth
                @if(auth()->user()->is_admin)
                    <a href="{{ route('admin.posts.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">+ Új bejegyzés</a>
                @endif
            @endauth
        </div>

        <form method="GET" action="{{ route('blog.index') }}" class="mb-6">
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Keresés..."
                class="w-full md:w-1/2 p-2 border rounded">
        </form>


        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($posts as $post)
                <a href="{{ route('blog.show', $post) }}" class="block bg-white rounded-xl overflow-hidden shadow transition-transform duration-300 hover:-translate-y-1 hover:shadow-lg">
                    @if ($post->image)
                        <img src="{{ asset('storage/' . $post->image) }}" alt="Borítókép" class="w-full h-48 object-cover">
                    @endif

                    <div class="p-4">
                        <h2 class="text-xl font-bold mb-2">{{ $post->title }}</h2>
                        <p class="text-gray-700">{{ Str::limit(strip_tags($post->content), 100) }}</p>
                        <p class="text-sm text-gray-500 mt-2">Létrehozva: {{ $post->created_at->format('Y.m.d') }}</p>
                    </div>
                </a>
            @empty
                <p class="text-gray-600 col-span-full">Nincs találat a keresésre.</p>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $posts->appends(['search' => request('search')])->links() }}
        </div>
    </div>
    <x-footer></x-footer>
@endsection
