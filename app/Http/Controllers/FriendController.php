<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Friend;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FriendController extends Controller
{
    // Barát felkérés küldése
    public function sendRequest(Request $request)
    {
        $user = Auth::user();
        $friendId = $request->input('friend_id');

        // Ne legyen saját magára barát
        if ($user->id == $friendId) {
            return back()->with('error', 'Magadat nem jelölheted be!');
        }

        // Ne legyen duplikáció
        Friend::updateOrCreate(
            ['user_id' => $user->id, 'friend_id' => $friendId],
            ['status' => 'pending']
        );

        return back()->with('success', 'Barát felkérés elküldve!');
    }

    // Barát felkérés elfogadása
    public function acceptRequest($id)
    {
        $friendRequest = Friend::findOrFail($id);
        if ($friendRequest->friend_id != Auth::id()) abort(403);

        $friendRequest->status = 'accepted';
        $friendRequest->save();

        return back()->with('success', 'Elfogadtad a barát felkérést!');
    }

    // Barát felkérés visszautasítása
    public function declineRequest($id)
    {
        $friendRequest = Friend::findOrFail($id);
        if ($friendRequest->friend_id != Auth::id()) abort(403);

        $friendRequest->status = 'declined';
        $friendRequest->save();

        return back()->with('success', 'Elutasítottad a barát felkérést!');
    }

    // Barát törlése
    public function removeFriend($id)
    {
        $user = Auth::user();
        Friend::where(function ($q) use ($user, $id) {
            $q->where('user_id', $user->id)->where('friend_id', $id);
        })->orWhere(function ($q) use ($user, $id) {
            $q->where('user_id', $id)->where('friend_id', $user->id);
        })->delete();

        return back()->with('success', 'Barát eltávolítva!');
    }
}
