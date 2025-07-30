<?php

namespace App\Http\Controllers;

use App\Models\MatchmakingQueue;
use App\Models\Lobby;
use App\Models\LobbyUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Events\PlayerRedirectedToLobby;
use App\Events\PlayerJoined;
use App\Events\MatchFound;
use App\Models\MatchAccept;
use App\Services\GameServerService;

class QueueController extends Controller
{
    public function join(Request $request)
    {
        logger('🛠️ join() elindult');
        $user = Auth::user();
        logger('👤 Felhasználó: ' . json_encode([
                'id' => $user->id,
                'name' => $user->name,
            ]));

        $party = $user->party()->first(); // az aktuális party, ha van
        $isLeader = $party && $party->leader_id == $user->id;

        // Ha már sorban van (akár partyban, akár solo), ne rakjuk be újra
        if (MatchmakingQueue::where('user_id', $user->id)->exists()) {
            logger('⚠️ Már sorban van: ' . $user->id);
            return response()->json(['error' => 'Már sorban vagy.'], 400);
        }

        if ($isLeader) {
            // Ha party leader vagy, az összes tagot bepakoljuk
            foreach ($party->users as $member) {
                if (MatchmakingQueue::where('user_id', $member->id)->exists()) {
                    continue; // már queue-ban van
                }
                MatchmakingQueue::updateOrCreate(
                    ['user_id' => $member->id],
                    ['party_id' => $party->id, 'joined_at' => now()]
                );
                logger("✅ Party tag bekerült a várólistába: {$member->id}");
            }
        } else {
            // Solo player vagy nem te vagy a leader
            MatchmakingQueue::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'party_id' => $party ? $party->id : null,
                    'joined_at' => now(),
                ]
            );
            logger('✅ Felhasználó bekerült a várólistába: ' . $user->id);
        }

        logger('📥 checkQueueAndStartLobby() meghívva');
        $this->checkQueueAndStartLobby();

        return response()->json(['message' => 'Sikeresen beléptél a váróba.']);
    }

    public function leave()
    {
        $user = Auth::user();

        MatchmakingQueue::where('user_id', $user->id)->delete();

        return response()->json(['message' => 'Kiléptél a váróból.']);
    }

    public function checkQueueAndStartLobby()
    {
        logger('📥 Queue jelenlegi állapota: ' . MatchmakingQueue::count());

        \DB::transaction(function () {
            $queue = MatchmakingQueue::orderBy('joined_at')->lockForUpdate()->take(10)->get();

            if ($queue->count() < 10) {
                return;
            }

            // Itt csoportosítjuk party szerint!
            $parties = [];
            foreach ($queue as $row) {
                if ($row->party_id) {
                    $parties['party_' . $row->party_id][] = $row->user_id;
                } else {
                    $parties['solo_' . $row->user_id][] = $row->user_id;
                }
            }
            $partyGroups = array_values($parties);
            shuffle($partyGroups);

            $userIds = [];
            foreach ($partyGroups as $group) {
                $userIds = array_merge($userIds, $group);
            }

            // Töröljük őket a várólistából
            MatchmakingQueue::whereIn('user_id', $userIds)->delete();

            // Létrehozzuk a lobby-t
            $this->createLobbyFromQueue($userIds);
        });
    }

    protected function createLobbyFromQueue(array $userIds)
    {
        $code = Str::upper(Str::random(6));

        // 1. PARTIES → partyGroups → csapatok
        $parties = [];
        foreach ($userIds as $uid) {
            $parties['solo_' . $uid][] = $uid; // mindenki solo egyelőre
        }

        $partyGroups = array_values($parties);
        shuffle($partyGroups);

        $ctTeam = [];
        $tTeam = [];

        foreach ($partyGroups as $group) {
            if (count($ctTeam) <= count($tTeam) && count($ctTeam) + count($group) <= 5) {
                $ctTeam = array_merge($ctTeam, $group);
            } elseif (count($tTeam) + count($group) <= 5) {
                $tTeam = array_merge($tTeam, $group);
            } else {
                logger("⚠️ Túl nagy party, nem fér be: " . implode(', ', $group));
            }
        }

        // 2. Kapitányok kiválasztása
        $ctCaptain = $ctTeam[array_rand($ctTeam)];
        $tCaptain = $tTeam[array_rand($tTeam)];

        // 3. Lobby létrehozása már a kapitány ID-kkal!
        $lobby = Lobby::create([
            'code' => $code,
            'status' => 'waiting',
            'ct_captain_id' => $ctCaptain,
            't_captain_id' => $tCaptain,
        ]);

        // 4. Slotok/sorok kitöltése
        foreach ($ctTeam as $i => $userId) {
            LobbyUser::create([
                'lobby_id' => $lobby->id,
                'user_id' => $userId,
                'role' => 'ct',
                'slot_index' => $i,
                'is_captain' => $userId === $ctCaptain,
            ]);
            $user = User::find($userId);
            event(new PlayerJoined($lobby->code, $user, 'ct', $i));
        }
        foreach ($tTeam as $i => $userId) {
            LobbyUser::create([
                'lobby_id' => $lobby->id,
                'user_id' => $userId,
                'role' => 't',
                'slot_index' => $i,
                'is_captain' => $userId === $tCaptain,
            ]);
            $user = User::find($userId);
            event(new PlayerJoined($lobby->code, $user, 't', $i));
        }

        // 🔥 Minden játékost redirectelünk a lobbyhoz Pusheren keresztül
        foreach (array_merge($ctTeam, $tTeam) as $userId) {
            MatchAccept::create([
                'lobby_id' => $lobby->id,
                'user_id' => $userId,
                'accepted' => null,
            ]);
            event(new MatchFound($userId, $lobby->code));
            logger("🎯 Kiküldve MatchFound event user: $userId, lobby: $lobby->code");
        }

        logger("✅ Lobby létrejött: {$lobby->code} | CT Captain: {$ctCaptain} | T Captain: {$tCaptain}");
    }

    public function accept(Request $request)
    {
        $userId = auth()->id();
        $lobbyCode = $request->input('lobby_code');

        $lobby = Lobby::where('code', $lobbyCode)->firstOrFail();

        MatchAccept::where('user_id', $userId)
            ->where('lobby_id', $lobby->id)
            ->update(['accepted' => true]);

        // 🔁 Ha mindenki elfogadta, akkor redirect eventet küldünk
        $stillPending = MatchAccept::where('lobby_id', $lobby->id)
            ->where(function ($q) {
                $q->whereNull('accepted')->orWhere('accepted', false);
            })
            ->count();

        if ($stillPending === 0) {
            foreach ($lobby->users as $user) {
                event(new PlayerRedirectedToLobby($user->id, $lobby->code));
                logger("➡️ Kiküldve PlayerRedirectedToLobby event user: {$user->id}, lobby: {$lobby->code}");
            }
        }

        return response()->json(['status' => 'accepted']);
    }

    public function decline(Request $request)
    {
        $userId = auth()->id();
        $lobbyCode = $request->input('lobby_code');

        $lobby = Lobby::where('code', $lobbyCode)->firstOrFail();

        MatchAccept::where('user_id', $userId)
            ->where('lobby_id', $lobby->id)
            ->update(['accepted' => false]);

        return response()->json(['status' => 'declined']);
    }

    public function startServerForLobby($lobbyId)
    {
        $lobby = Lobby::findOrFail($lobbyId);

        // Kér egy szabad szervert és elindítja
        $result = app(\App\Services\GameServerService::class)->assignToLobby($lobby);

        if ($result['error'] ?? false) {
            return response()->json(['error' => $result['error']], 500);
        }

        // Siker esetén:
        $server = $result['server'];
        $lobby->update(['game_server_id' => $server->id]);

        broadcast(new \App\Events\ServerReady($lobby->code, $server->ip, $server->port));

        return response()->json([
            'message' => 'Szerver indítva!',
            'ip' => $server->ip,
            'port' => $server->port,
        ]);
    }

}
