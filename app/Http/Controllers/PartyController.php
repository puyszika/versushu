<?php

namespace App\Http\Controllers;

use App\Models\Party;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PartyController extends Controller
{
    // Party létrehozás
    public function create()
    {
        $user = Auth::user();
        // Ha már van partyja, ne csináljon újat
        if ($user->party()->exists()) {
            return back()->with('error', 'Már vagy egy partyban!');
        }
        $party = Party::create(['leader_id' => $user->id]);
        $party->users()->attach($user->id);

        return back()->with('success', 'Party létrehozva!');
    }

    // Meghívás (invite)
    public function invite(Request $request)
    {
        $user = Auth::user();
        $friendId = $request->input('friend_id');
        $party = $user->leadingParty;

        if (!$party) {
            return back()->with('error', 'Csak party leader hívhat meg!');
        }

        // Ha már tag
        if ($party->users()->where('user_id', $friendId)->exists()) {
            return back()->with('error', 'Már party tag!');
        }

        // Itt lehet küldeni notificationt is!
        $party->users()->attach($friendId);

        return back()->with('success', 'Barát csatlakozott a partyhoz!');
    }

    // Party elhagyása
    public function leave()
    {
        $user = Auth::user();
        $party = $user->party()->first();

        if ($party) {
            $party->users()->detach($user->id);
            // Ha leader ment ki, töröljük a party-t is (vagy átadható)
            if ($party->leader_id == $user->id) {
                $party->delete();
            }
        }

        return back()->with('success', 'Kiléptél a partyból!');
    }
}
