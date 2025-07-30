<?php

namespace App\Http\Controllers;

use App\Models\GameServer;
use App\Models\Lobby;
use Illuminate\Http\Request;

class GameServerController extends Controller
{
    public function index()
    {
        $servers = GameServer::all();
        return view('admin.servers.index', compact('servers'));
    }

    public function start($id)
    {
        $server = GameServer::findOrFail($id);
        // indítási logika, status váltás, SSH/shell, error catch
        // ...
        $server->update(['status' => 'busy']);
        return back()->with('success', 'Szerver elindítva!');
    }

    public function stop($id)
    {
        $server = GameServer::findOrFail($id);
        // leállítási logika
        $server->update(['status' => 'available']);
        return back()->with('success', 'Szerver leállítva!');
    }

    public function restart($id)
    {
        // ...
    }

    public function editConfig($id)
    {
        // ...
    }

    public function saveConfig(Request $request, $id)
    {
        // ...
    }

    public function editWhitelist($id)
    {
        // ...
    }

    public function saveWhitelist(Request $request, $id)
    {
        // ...
    }
}
