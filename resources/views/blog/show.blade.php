@extends('layouts.app')

@section('content')
    <div class="max-w-3xl mx-auto p-6">
        {{-- Cím --}}
        <h1 class="text-3xl font-bold mb-4 text-white">{{ $post->title }}</h1>

        {{-- Kép, ha van --}}
        @if ($post->image)
            <img src="{{ asset('storage/' . $post->image) }}" alt="Borítókép" class="w-full mb-6 rounded shadow h-150">
        @endif

        {{-- Tartalom --}}
        <div class="prose prose-lg max-w-none text-white">
            {!! nl2br(e($post->content)) !!}
        </div>

        {{-- Létrehozás dátuma és szerző (opcionális) --}}
        <p class="text-sm text-gray-500 mt-6">
            Közzétéve: {{ $post->created_at->format('Y. m. d.') }}
            @if($post->user)
                &nbsp;|&nbsp; Írta: <strong>{{ $post->user->name }}</strong>
            @endif
        </p>

        <hr class="my-6">

        <h3 class="text-xl font-bold mt-8 text-white">Hozzászólások</h3>

        @foreach($post->comments as $comment)
            <div class="mb-4 p-4 bg-gray-100 rounded">
                <p class="font-semibold">{{ $comment->user->name }} írta:</p>
                <p>{{ $comment->content }}</p>

                <p class="text-sm text-gray-500">{{ $comment->created_at->diffForHumans() }}</p>

                {{-- Válasz link --}}
                @auth
                    <button onclick="toggleReplyForm({{ $comment->id }})" class="text-blue-600 text-sm mt-2">Válasz</button>

                    @auth
                        @if(auth()->user()->is_admin || auth()->id() === $comment->user_id)
                            <form action="{{ route('comments.destroy', $comment) }}" method="POST" class="inline-block ml-2" onsubmit="return confirm('Biztosan törlöd a hozzászólást?');">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-600 text-sm hover:underline">Törlés</button>
                            </form>
                        @endif
                    @endauth

                    {{-- Válasz form --}}
                    <form method="POST" action="{{ route('comments.store', $post) }}" id="reply-form-{{ $comment->id }}" class="mt-2 hidden">
                        @csrf
                        <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                        <textarea name="content" rows="2" class="w-full border p-2 rounded" placeholder="Válasz..."></textarea>
                        <button class="bg-blue-500 text-white px-3 py-1 rounded mt-1">Küldés</button>
                    </form>
                @endauth

                {{-- Válasz lenyitás --}}
                @if($comment->replies->count())
                    <div x-data="{ open: false }" class="mt-3">
                        <button @click="open = !open" class="text-sm text-gray-700 hover:underline">
                            <span x-show="!open">Válaszok megjelenítése ({{ $comment->replies->count() }})</span>
                            <span x-show="open">Válaszok elrejtése</span>
                        </button>

                        <div x-show="open" x-transition class="mt-2 space-y-2 ml-6">
                            @foreach($comment->replies as $reply)
                                <div class="bg-white p-3 rounded border">
                                    <p class="font-semibold">{{ $reply->user->name }} válasza:</p>
                                    <p>{{ $reply->content }}</p>
                                    <p class="text-xs text-gray-500">{{ $reply->created_at->diffForHumans() }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @endforeach

        {{-- Új fő komment --}}
        @auth
            <form method="POST" action="{{ route('comments.store', $post) }}" class="mt-6">
                @csrf
                <textarea name="content" rows="4" class="w-full border p-2 rounded" placeholder="Írj hozzászólást..." required></textarea>
                <button type="submit" class="mt-2 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Küldés</button>
            </form>
        @endauth

        {{-- Vissza gomb --}}
        <a href="{{ route('blog.index') }}" class="mt-6 inline-block text-blue-600 hover:underline">
            ← Vissza a bloghoz
        </a>
    </div>
    <x-footer></x-footer>
    <script>
        function toggleReplyForm(commentId) {
            const form = document.getElementById(`reply-form-${commentId}`);
            form.classList.toggle('hidden');
        }
    </script>

@endsection
