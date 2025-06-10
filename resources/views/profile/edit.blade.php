@extends('layouts.app')
    @section('content')
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6 ">

            @if (session('status') === 'profile-updated')
                <div class="bg-green-600 text-white px-4 py-2 rounded shadow">
                    Profil frissítve!
                </div>
            @endif

            <!-- Profil információ -->
            <div class="p-6 bg-gray-800 shadow sm:rounded-lg border-white">
                <h1 class="text-lg font-semibold text-white mb-4 animate-slide-up">Profil információk szerkesztése</h1>

                <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    @method('PATCH')

                    <!-- Név -->
                    <div>
                        <label for="name" class="block text-white">Név</label>
                        <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}"
                               class="mt-1 block w-full bg-gray-700 text-white border border-gray-600 rounded px-4 py-2" required>
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-white">Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}"
                               class="mt-1 block w-full bg-gray-700 text-white border border-gray-600 rounded px-4 py-2" required>
                    </div>

                    <!-- Steam ID -->
                    <div>
                        <label for="steam_id" class="block text-white">Steam ID</label>
                        <input id="steam_id" name="steam_id" type="text" value="{{ old('steam_id', $user->steam_id) }}"
                               class="mt-1 block w-full bg-gray-700 text-white border border-gray-600 rounded px-4 py-2">
                    </div>

                    <!-- Steam név -->
                    <div>
                        <label for="steam_name" class="block text-white">Steam név</label>
                        <input id="steam_name" type="text" class="mt-1 block w-full bg-gray-700 text-white border border-gray-600 rounded px-4 py-2" name="steam_name" value="{{ old('steam_name', auth()->user()->steam_name) }}">
                        @error('steam_name')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Discord Tag -->
                    <div>
                        <label for="discord_tag" class="block text-white">Discord tag (pl. Versus#1234)</label>
                        <input id="discord_tag" name="discord_tag" type="text" value="{{ old('discord_tag', $user->discord_tag) }}"
                               class="mt-1 block w-full bg-gray-700 text-white border border-gray-600 rounded px-4 py-2">
                    </div>

                    <!-- Profilkép -->
                    <div>
                        <label for="avatar" class="block text-white">Profilkép (opcionális)</label>
                        <input id="avatar" name="avatar" type="file"
                               class="mt-1 block w-full text-white bg-gray-700 border border-gray-600 rounded px-4 py-2">

                        @if ($user->avatar_path)
                            <img src="{{ asset('storage/' . $user->avatar_path) }}" class="mt-2 h-20 rounded" alt="Profilkép">
                        @endif
                    </div>

                    <div class="pt-4 ">
                        <button type="submit"
                                class="bg-orange-600 hover:bg-orange-700 text-white font-semibold px-6 py-2 rounded">
                            Mentés
                        </button>
                    </div>
                </form>
            </div>

            <!-- Jelszóváltás -->
            <div class="p-6 bg-gray-800 shadow sm:rounded-lg">
                @include('profile.partials.update-password-form')
            </div>

            <!-- Fiók törlése -->
            <div class="p-6 bg-gray-800 shadow sm:rounded-lg">
                @include('profile.partials.delete-user-form')
            </div>

        </div>
    </div>
    <x-footer></x-footer>
    @endsection

