<?php

namespace App\Services;

use App\Models\GameServer;
use App\Models\Lobby;

class GameServerService
{
    public function assignToLobby(Lobby $lobby)
    {
        $server = GameServer::where('status', 'available')->first();

        if (!$server) {
            return ['error' => 'Nincs szabad szerver!'];
        }

        // Whitelist, config előkészítés...
        $users = $lobby->users()->get();
        $steamids = $users->map(fn($u) => $u->steamid)->toArray();

        // Itt jön a shell_exec vagy SSH logika!
        $cmd = "ssh user@{$server->ip} '/start_cs2_server.sh {$server->port} {$lobby->picked_map} ".implode(',', $steamids)."'";
        shell_exec($cmd);

        $server->update(['status' => 'busy']);

        return ['server' => $server];
    }
}
