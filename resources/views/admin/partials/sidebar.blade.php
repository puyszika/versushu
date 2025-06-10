<aside class="w-64 bg-gray-800 text-white flex flex-col justify-between min-h-screen">
    <div>
    <div class="p-6 text-xl font-bold border-b border-gray-700">Admin Panel</div>
    <nav class="p-4">
        <ul class="space-y-2">
            <li><a href="{{ route('admin.dashboard') }}" class="block hover:bg-gray-700 p-2 rounded">Statisztika</a></li>
            <li><a href="{{ route('admin.users.index') }}" class="block hover:bg-gray-700 p-2 rounded">Felhasználók</a></li>
            <li><a href="{{ route('admin.championships.index') }}" class="block hover:bg-gray-700 p-2 rounded">Bajnokságok</a></li>
            <li><a href="{{ route('admin.teams.index') }}" class="block hover:bg-gray-700 p-2 rounded">Csapatok</a></li>
            <li><a href="{{ route('admin.posts.index') }}" class="block hover:bg-gray-700 p-2 rounded">Bejegyzések</a></li>
            <li><a href="{{ route('admin.sliders.index') }}" class="block hover:bg-gray-700 p-2 rounded">Sliderek</a></li>
        </ul>
    </nav>
    </div>

    <!-- Kijelentkezés -->
    <form method="POST" action="{{ route('logout') }}" class="p-4 border-t border-gray-700">
        @csrf
        <button type="submit" class="w-full text-left hover:bg-gray-700 p-2 rounded text-red-400 hover:text-red-200 text-sm">
            ⏻ Kijelentkezés
        </button>
    </form>
</aside>
