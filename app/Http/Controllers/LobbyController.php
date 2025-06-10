<?php
namespace App\Http\Controllers;

use App\Models\Lobby;
use App\Models\LobbyMap;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

// Broadcast események
use App\Events\PlayerJoined;
use App\Events\SpectatorJoined;
use App\Events\MapBanned;
use App\Events\MapPicked;
use App\Events\MapFinalized;

class LobbyController extends Controller
{
    // ✅ 1. GET /lobby/{code} – oldal megjelenítés
    public function show($code)
    {
        $lobby = Lobby::where('code', $code)->firstOrFail();
        return view('lobby.show', compact('lobby'));
    }

    // ✅ 2. POST /api/lobby – új lobby létrehozása
    public function store(Request $request)
    {
        $code = strtoupper(Str::random(6));
        $user = $request->user();

        $lobby = Lobby::create([
            'code' => $code,
            'host_user_id' => $user->id,
            'status' => 'waiting',
        ]);

        // alapértelmezett térképek
        $maps = ['Dust2', 'Inferno', 'Mirage', 'Nuke', 'Ancient', 'Overpass', 'Vertigo'];
        foreach ($maps as $mapName) {
            $lobby->maps()->create([
                'map_name' => $mapName,
                'status' => 'available', // mapok elérhetősége.
            ]);
        }

        // host hozzáadása CT 0. slotra
        $lobby->users()->attach($user->id, [
            'role' => 'ct',
            'slot_index' => 0
        ]);

        return response()->json(['code' => $lobby->code]);
    }

    // ✅ 3. POST /api/lobby/{code}/join – csatlakozás lobbyhoz
    public function join(Request $request, $code)
    {
        $lobby = Lobby::where('code', $code)->firstOrFail();
        $user = $request->user();

        // Töröljük a usert minden előző slotból
        $lobby->users()->detach($user->id);

        $role = $request->input('role');

        $currentCount = $lobby->users()->wherePivot('role', $role)->count();
        $max = $role === 'spectator' ? 2 : 5;

        if ($currentCount >= $max) {
            return response()->json(['error' => 'Nincs több szabad hely ebben a szerepben.'], 400);
        }

        $lobby->users()->attach($user->id, [
            'role' => $role,
            'slot_index' => $currentCount
        ]);

        if ($role === 'spectator') {
            broadcast(new SpectatorJoined($lobby->code, $user, $currentCount));
        } else {
            broadcast(new PlayerJoined($lobby->code, $user, $role, $currentCount));
        }

        return response()->json([
            'success' => true,
            'joined_user' => $user,
            'team' => $role,
            'slot' => $currentCount
        ]);
    }


    // ✅ 4. POST /api/lobby/{code}/map-action – map pick / ban
    public function mapAction(Request $request, $code)
    {
        $user = $request->user();
        $lobby = Lobby::where('code', $code)->firstOrFail();

        $action = $request->input('action'); // "ban" vagy "pick"
        $mapName = trim($request->input('map'));

        $map = $lobby->maps()->where('map_name', $mapName)->first();

        if (!$map) {
            return response()->json(['error' => 'Nem található ilyen térkép ebben a lobbyban.'], 404);
        }

        if ($map->status !== 'available') {
            return response()->json(['error' => 'Ez a térkép már nem választható.'], 400);
        }

        $pivot = $lobby->users()->where('user_id', $user->id)->first()?->pivot;

        if (!$pivot) {
            return response()->json(['error' => 'A felhasználó nem része a lobbynak.'], 403);
        }

        $userRole = $pivot->role;

        if ($action === 'ban') {
            $map->update(['status' => 'banned']);
            broadcast(new MapBanned($lobby->code, $mapName, $userRole));
        } elseif ($action === 'pick') {
            $map->update(['status' => 'picked']);
            broadcast(new MapPicked($lobby->code, $mapName, $userRole));
        } else {
            return response()->json(['error' => 'Érvénytelen művelet.'], 400);
        }

        // 🧠 ELLENŐRZÉS: ha csak 1 map maradt hátra, azt most "picked"-dé tesszük
        $remaining = $lobby->maps()->where('status', 'available')->get();
        if ($remaining->count() === 1) {
            $finalMap = $remaining->first();
            $finalMap->update(['status' => 'picked']);
            broadcast(new MapPicked($lobby->code, $finalMap->map_name, $userRole)); // frontend is reagál
            broadcast(new MapFinalized($lobby->code, $finalMap->map_name)); // zöld keret
        }

        return response()->json(['success' => true]);
    }


    public function joinSlot(Request $request, $code)
    {
        $lobby = Lobby::where('code', $code)->firstOrFail();
        $user = $request->user();

        $role = $request->input('role'); // ct vagy t
        $slot = $request->input('slot_index');

        if (!is_numeric($slot)) {
            return response()->json(['error' => 'Érvénytelen slot index.'], 400);
        }

        // 🔥 Itt töröljük a user minden előző slotját
        $lobby->users()->detach($user->id);

        // Most jöhet a beállítás
        $lobby->users()->attach($user->id, [
            'role' => $role,
            'slot_index' => $slot,
        ]);

        $slot = (int) $slot;

        if ($user instanceof \App\Models\User) {
            broadcast(new PlayerJoined($lobby->code, $user, $role, $slot));
        }

        return response()->json(['success' => true]);
    }

    public function joinSpectator(Request $request, $code)
    {
        $lobby = Lobby::where('code', $code)->firstOrFail();
        $user = $request->user();

        $slot = (int) $request->input('slot_index');

        if (!is_numeric($slot) || $slot < 0 || $slot > 1) {
            return response()->json(['error' => 'Érvénytelen slot index.'], 400);
        }

        // 🔥 Először eltávolítjuk a usert minden slotból
        $lobby->users()->detach($user->id);

        // ✅ Ezután csatlakoztatjuk nézőként a megadott helyre
        $lobby->users()->attach($user->id, [
            'role' => 'spectator',
            'slot_index' => $slot,
        ]);

        // 📡 Broadcast az új állapotról
        if ($user instanceof \App\Models\User) {
            broadcast(new SpectatorJoined($lobby->code, $user, $slot));
        }

        return response()->json(['success' => true]);
    }

    public function getMaps($code)
    {
        $lobby = Lobby::where('code', $code)->firstOrFail();
        $maps = $lobby->maps()->get();

        return response()->json($maps);
    }

}
