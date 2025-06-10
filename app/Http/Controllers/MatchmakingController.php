<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;


class MatchmakingController extends Controller
{
    public function index()
    {
        return view('matchmaking');
    }

    public function join(Request $request)
    {
        $user = Auth::user();
        $queue = Cache::get('matchmaking_queue', []);

        if (!in_array($user->id, $queue)) {
            $queue[] = $user->id;
            Cache::put('matchmaking_queue', $queue, now()->addMinutes(30));
        }

        return response()->json(['message' => 'Csatlakoztál a várólistához.', 'queue' => $queue]);
    }

    public function leave(Request $request)
    {
        $user = Auth::user();
        $queue = Cache::get('matchmaking_queue', []);

        $queue = array_filter($queue, fn ($id) => $id !== $user->id);
        Cache::put('matchmaking_queue', array_values($queue), now()->addMinutes(30));

        return response()->json(['message' => 'Kiléptél a várólistából.', 'queue' => $queue]);
    }

    public function status()
    {
        $queue = Cache::get('matchmaking_queue', []);
        return response()->json(['players' => $queue]);
    }
}