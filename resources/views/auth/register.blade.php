<x-guest-layout>
    <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Felhasználónév')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('E-mail')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="name" :value="__('Steam ID')"/>
            <x-text-input type="text" class="block mt-1 w-full" name="steam_id" class="form-control"/>
        </div>

        <!-- Súgó link -->
        <div class="mt-2">
            <a href="{{ url('https://store.steampowered.com/account/') }}" target="_blank" class="text-sm text-blue-500 hover:underline">
                A steamID ide kattintva a profilnév alatt található számsor!
            </a>
        </div>

        <!-- Steam név -->
        <div class="mt-4">
            <label for="steam_name" class="block font-medium text-sm text-gray-700">Steam név</label>
            <input id="steam_name" class="block mt-1 w-full" type="text" name="steam_name" required autofocus placeholder="Kötelező, az eredmények kezelése miatt.">
            @error('steam_name')
            <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <div class="mt-4">
            <x-input-label for="name" :value="__('Discord')" />
            <x-text-input type="text" class="block mt-1 w-full" name="discord_handle" class="form-control" placeholder="Discord profilnév"/>
        </div>

        <div class="mt-4">
            <x-input-label for="name" :value="__('Profilkép')"/>
            <x-text-input type="file" name="profile_picture" class="form-control"/>
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Jelszó')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Jelszó megerősítése')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Már regisztráltál?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Regisztráció') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
