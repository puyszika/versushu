@extends('layouts.app')

@section('content')
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Lobby: {{ $lobby->code }}</title>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script>
            console.log("💡 Auth::user() teszt:", @json(Auth::user()));
            window.AuthUser = @json(Auth::user());
        </script>
    </head>
    <body class="bg-navy text-white">
    <div class="max-w-6xl mx-auto px-4 py-6 text-white" x-data="Lobby('{{ $lobby->code }}')" x-init="init()">

    <h2 class="text-xl font-bold mb-6">Lobby kód: {{ $lobby->code }}</h2>

        <div class="flex justify-between gap-10">
            <!-- CT oldal -->
            <div>
                <h2 class="text-center text-white font-semibold mb-2" x-text="getTeamName('ct')"></h2>
                <template x-for="(slot, i) in slots.ct" :key="i">
                    <div class="bg-blue-600 text-white text-left py-2 px-3 rounded mb-2 flex items-center gap-2 cursor-pointer hover:bg-blue-700"
                         @click="joinSlot('ct', i)">
                        <template x-if="slot">
                            <div class="flex items-center gap-2">
                                <img :src="slot.avatar ?? '/images/default-avatar.png'"
                                     class="w-6 h-6 rounded-full object-cover" alt="profilkép">
                                <span x-text="slot.name"></span>
                            </div>
                        </template>
                        <template x-if="!slot">
                            <span>Üres</span>
                        </template>
                    </div>
                </template>

            </div>

            <!-- LOG rész -->
            <div class="mt-8 text-white max-w-xl mx-auto bg-gray-800 bg-opacity-50 rounded p-4 shadow">
                <h3 class="text-lg font-bold mb-2">Lobby események</h3>
                <ul class="text-sm space-y-1 max-h-40 overflow-y-auto" x-ref="logContainer">
                    <template x-for="entry in log" :key="entry.id">
                        <li x-text="entry.text"></li>
                    </template>
                </ul>
            <!--    <p class="text-white">MyRole: <span x-text="getMyRole()"></span></p>
                <p class="text-white">CurrentTurn: <span x-text="currentTurn"></span></p> -->
            </div>


            <!-- T oldal -->
            <div>
                <h2 class="text-center text-white font-semibold mb-2" x-text="getTeamName('t')"></h2>
                <template x-for="(slot, i) in slots.t" :key="i">
                    <div class="bg-red-600 text-white text-left py-2 px-3 rounded mb-2 flex items-center gap-2 cursor-pointer hover:bg-red-700"
                         @click="joinSlot('t', i)">
                        <template x-if="slot">
                            <div class="flex items-center gap-2">
                                <img :src="slot.avatar ?? '/images/default-avatar.png'"
                                     class="w-6 h-6 rounded-full object-cover" alt="profilkép">
                                <span x-text="slot.name"></span>
                            </div>
                        </template>
                        <template x-if="!slot">
                            <span>Üres</span>
                        </template>
                    </div>
                </template>

            </div>
        </div>


        <!-- Nézők -->
        <template x-for="(slot, i) in spectators" :key="i">
            <div class="bg-gray-600 text-white text-center py-1 px-3 rounded mb-2 cursor-pointer hover:bg-gray-700"
                 @click="joinSpectator(i)">
                Néző #<span x-text="i + 1"></span> (<span x-text="slot ? slot.name : 'üres'"></span>)
            </div>
        </template>

        <!-- Pick & Ban -->
        <div class="mt-6">
            <h3 class="text-white font-semibold mb-2">Pick & Ban</h3>
            <div class="flex flex-wrap justify-center gap-4">
                <template x-for="(map, i) in maps" :key="map.map_name + '-' + map.status">
                    <div
                        @click="canClickMap(map) && onMapClick(map)"
                        class="w-28 h-20 bg-cover bg-center rounded relative shadow-lg transition-all duration-300"
                        :class="{
                              'cursor-pointer hover:scale-105 hover:ring-2 hover:ring-white': map.status === 'available',
                              'grayscale opacity-60 scale-95 pointer-events-none transition-all duration-700 ease-in-out': map.status === 'banned',
                              'border-4 border-yellow-400': map.status === 'picked' && finalMap !== map.map_name,
                              'ring-4 ring-green-300': finalMap === map.map_name
                        }"
                        :style="`
      background-image: url('${getMapImage(map.map_name)}');
      ${map.status === 'available' ? 'border-color: transparent;' : ''}
    `"
                    ></div>
                    <p class="text-xs text-white mt-1" x-text="map.map_name + ': ' + map.status"></p>
                    <p class="text-white mt-1">Final map: <span x-text="finalMap"></span></p>
                </template>
            </div>
        </div>

       <!-- <pre class="text-white text-xs bg-black p-2 rounded mt-4" x-text="JSON.stringify(maps, null, 2)"></pre>    LOBBI LOGOLÁS HELYBEN    -->


        <!-- Start gomb -->
        <button x-show="isHost" @click="startTimerManually()" class="mt-4 px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
            Pick/Ban fázis indítása
        </button>

        <p class="text-white mt-2">Következő műveletig: <span x-text="phaseTimer"></span> mp</p>
    </div>
    </body>
    <x-footer></x-footer>

    <script>
        window.isHost = {{ auth()->id() === $lobby->host_user_id ? 'true' : 'false' }};
    </script>
@endsection
