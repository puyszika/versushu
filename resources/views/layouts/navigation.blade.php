<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}">
                        <img src="{{ asset('images/logo.png') }}" alt="Versus logó" class="h-10 w-auto inline-block">
                    </a>
                </div>

                <!-- Navigation Links -->
                @auth
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <x-nav-link :href="route('home')" :active="request()->routeIs('home')">Kezdőlap</x-nav-link>
                        <x-nav-link :href="route('teams.index')" :active="request()->routeIs('teams.index')">Csapatok</x-nav-link>
                       <!-- <x-nav-link :href="route('championships.public')" :active="request()->routeIs('championships.public')">Bajnokságok</x-nav-link> -->
                        <x-nav-link :href="route('blog.index')" :active="request()->routeIs('blog.index')">Bejegyzések</x-nav-link>
                        <x-nav-link href="#" @click.prevent="createLobby()">Pick/Ban MIX</x-nav-link>

                        @if(auth()->user()->is_admin)
                            <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">Admin Dashboard</x-nav-link>
                        @endif
                    </div>
                @else
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <x-nav-link :href="route('home')" :active="request()->routeIs('home')">Kezdőlap</x-nav-link>
                        <x-nav-link :href="route('blog.index')" :active="request()->routeIs('blog.index')">Bejegyzések</x-nav-link>
                    </div>
                @endauth
            </div>

            <!-- Jobb oldal: profil / login-regisztráció -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                @auth
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition">
                                <div>{{ Auth::user()->name }}</div>
                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('users.show', Auth::id())">Profilom</x-dropdown-link>
                            <x-dropdown-link :href="route('profile.edit')">Profil szerkesztése</x-dropdown-link>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                    Kijelentkezés
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <div class="space-x-4">
                        <a href="{{ route('login') }}" class="text-sm text-gray-700 hover:underline">Bejelentkezés</a>
                        <a href="{{ route('register') }}" class="text-sm text-gray-700 hover:underline">Regisztráció</a>
                    </div>
                @endauth
            </div>

            <!-- Hamburger menü -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open}" class="inline-flex" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open}" class="hidden" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobil navigáció -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('home')" :active="request()->routeIs('home')">Kezdőlap</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('blog.index')" :active="request()->routeIs('blog.index')">Bejegyzések</x-responsive-nav-link>
        </div>

        @auth
            <div class="pt-2 pb-3 space-y-1">
                <x-responsive-nav-link :href="route('teams.index')" :active="request()->routeIs('teams.index')">Csapatok</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('championships.public')" :active="request()->routeIs('championships.public')">Bajnokságok</x-responsive-nav-link>

                @if(auth()->user()->is_admin)
                    <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">Admin Dashboard</x-responsive-nav-link>
                @endif
            </div>

            <!-- Mobilon profilmenü -->
            <div class="pt-4 pb-1 border-t border-gray-200">
                <div class="px-4">
                    <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>

                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('users.show', Auth::id())">Profilom</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('profile.edit')">Profil szerkesztése</x-responsive-nav-link>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                            Kijelentkezés
                        </x-responsive-nav-link>
                    </form>
                </div>
            </div>
        @endauth

        @guest
            <div class="pt-4 pb-1 border-t border-gray-200 space-y-1">
                <x-responsive-nav-link :href="route('login')">Bejelentkezés</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('register')">Regisztráció</x-responsive-nav-link>
            </div>
        @endguest
    </div>
</nav>
