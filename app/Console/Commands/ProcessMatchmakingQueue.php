<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MatchmakingQueue;
use App\Models\User;
use App\Models\Lobby; // majd később
use Illuminate\Support\Facades\Log;
use App\Models\MatchProposal;
use Carbon\Carbon;
use App\Events\MatchProposalCreated;

class ProcessMatchmakingQueue extends Command
{
    
    protected $signature = 'matchmaking:process';
    protected $description = 'Matchmaking queue ellenőrzése és meccsek indítása';

    public function handle()
    {
        $waiting = MatchmakingQueue::where('status', 'waiting')->limit(10)->get();

        if ($waiting->count() == 10) {
        Log::info('🎯 10 játékos elérve, létrejön match proposal');

        $userIds = $waiting->pluck('user_id')->toArray();

        $proposal = MatchProposal::create([
            'players' => $userIds,
            'accepted_players' => [],
            'declined_players' => [],
        ]);

        // Broadcast mind a 10 usernek
        foreach ($userIds as $userId) {
            broadcast(new MatchProposalCreated($userId, $proposal->id));
        }
        MatchmakingQueue::whereIn('user_id', $userIds)->delete();

    }
    }
}