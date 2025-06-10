<?php

// app/Http/Controllers/MatchmakingQueueController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MatchmakingQueue;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class MatchmakingQueueController extends Controller
{
    public function joinQueue(Request $request)
    {
        $user = Auth::user();

        // Ne duplázzon
        if (MatchmakingQueue::where('user_id', $user->id)->where('status', 'waiting')->exists()) {
            return response()->json(['message' => 'Már a várólistán vagy.'], 400);
        }

        MatchmakingQueue::create([
            'user_id' => $user->id,
        ]);

        return response()->json(['message' => 'Sikeresen beléptél a váróba!']);
    }

    public function leaveQueue()
    {
        $user = Auth::user();

        MatchmakingQueue::where('user_id', $user->id)
            ->where('status', 'waiting')
            ->delete();

        return response()->json(['message' => 'Kiléptél a váróból.']);
    }
}

