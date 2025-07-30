<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lobby;
use App\Services\GameServerService;

class MatchLobbyController extends Controller
{
    public function startMatchServer(Lobby $lobby)
    {
        $players = $lobby->players->pluck('steam_id')->toArray();
        $map = $lobby->map ?? 'de_mirage'; // vagy használd a választott map mezőt

        $server = app(GameServerService::class)->getAvailableServer();

        if (!$server) {
            return response()->json(['error' => 'Nincs szabad szerver.'], 503);
        }

        $data = [
            'map' => $map,
            'players' => $players,
            'match_id' => $lobby->id,
        ];

        app(GameServerService::class)->startServer($server, $data);

        return response()->json(['message' => 'Szerver indítás elindítva.']);
    }
}
