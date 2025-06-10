<?php

namespace App\Services;

use App\Models\TournamentMatch;
use App\Models\Championship;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MatchService
{
    public function generateInitialBracket(Championship $championship)
    {
        $teams = $championship->teams()->inRandomOrder()->get();
        $teams = $teams->values(); // újraindexelés
        $teamCount = $teams->count();
        $matches = [];

        DB::transaction(function () use (&$teams, $championship, &$matches) {
            $teams = $teams->values();
            $teamCount = $teams->count();

            $byeTeam = null;
            if ($teamCount % 2 !== 0) {
                $byeTeam = $teams->pop();
            }

            // 1️⃣ Első kör generálása
            for ($i = 0; $i < floor($teams->count() / 2); $i++) {
                $team1 = $teams->get($i * 2);
                $team2 = $teams->get($i * 2 + 1);

                $match = TournamentMatch::create([
                    'championship_id' => $championship->id,
                    'round' => 1,
                    'match_index' => $i,
                    'team1_id' => $team1?->id,
                    'team2_id' => $team2?->id,
                    'status' => ($team1 && $team2) ? 'pending' : 'waiting',
                ]);

                $matches[] = $match;
            }

            $currentRound = 1;
            $nextRound = $currentRound + 1;
            $nextMatches = [];
            $matchIndex = 0;

            // 2️⃣ Második kör: byeTeam beillesztése
            if ($byeTeam) {
                $match = TournamentMatch::create([
                    'championship_id' => $championship->id,
                    'round' => $nextRound,
                    'match_index' => $matchIndex++,
                    'team1_id' => $byeTeam->id,
                    'status' => 'waiting',
                ]);

                $matches[0]->next_match_id = $match->id;
                $matches[0]->save();

                $nextMatches[] = $match;
            }

            // 3️⃣ Második kör többi meccse
            for ($i = ($byeTeam ? 1 : 0); $i < count($matches); $i += 2) {
                $match = TournamentMatch::create([
                    'championship_id' => $championship->id,
                    'round' => $nextRound,
                    'match_index' => $matchIndex++,
                    'status' => 'waiting',
                ]);

                if (isset($matches[$i])) {
                    $matches[$i]->next_match_id = $match->id;
                    $matches[$i]->save();
                }

                if (isset($matches[$i + 1])) {
                    $matches[$i + 1]->next_match_id = $match->id;
                    $matches[$i + 1]->save();
                }

                $nextMatches[] = $match;
            }

            // 4️⃣ További körök automatikusan
            $currentMatches = $nextMatches;
            $currentRound = $nextRound;

            while (count($currentMatches) > 1) {
                $nextRound = $currentRound + 1;
                $nextMatches = [];

                for ($i = 0; $i < count($currentMatches); $i += 2) {
                    $match = TournamentMatch::create([
                        'championship_id' => $championship->id,
                        'round' => $nextRound,
                        'match_index' => floor($i / 2),
                        'status' => 'waiting',
                    ]);

                    if (isset($currentMatches[$i])) {
                        $currentMatches[$i]->next_match_id = $match->id;
                        $currentMatches[$i]->save();
                    }

                    if (isset($currentMatches[$i + 1])) {
                        $currentMatches[$i + 1]->next_match_id = $match->id;
                        $currentMatches[$i + 1]->save();
                    }

                    $nextMatches[] = $match;
                }

                $currentMatches = $nextMatches;
                $currentRound = $nextRound;
            }
        });
    }


    public function advanceWinner(TournamentMatch $match, int $winnerId): void
    {
        \Log::info('➡️ advanceWinner fut', ['match_id' => $match->id, 'winner_id' => $winnerId]);

        if (!$match->next_match_id) {
            \Log::warning('⚠️ Nincs next_match_id', ['match_id' => $match->id]);
            return;
        }

        $nextMatch = TournamentMatch::find($match->next_match_id);

        if (!$nextMatch) {
            \Log::error('❌ Nem található a következő meccs', ['next_match_id' => $match->next_match_id]);
            return;
        }

        if (!$nextMatch->team1_id) {
            $nextMatch->team1_id = $winnerId;
        } elseif (!$nextMatch->team2_id) {
            $nextMatch->team2_id = $winnerId;
        } else {
            \Log::warning('❗ Mindkét hely foglalt a következő meccsben', ['next_match_id' => $nextMatch->id]);
            return;
        }

        $nextMatch->status = 'pending';
        $nextMatch->save();

        \Log::info('✅ Nyertes továbbjutott', ['next_match_id' => $nextMatch->id, 'csapat' => $winnerId]);
    }
}
