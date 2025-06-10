<?php

namespace App\Services;

use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use App\Models\PlayerMatchStat;
use App\Models\TeamMatchStat;
use App\Models\User;
use App\Models\TournamentMatch;

class GoogleOcrService
{
    protected ImageAnnotatorClient $client;

    private const TEAM_CT_KEYWORDS = ['TERRORELHÁRÍTÓK', 'COUNTER-TERRORISTS', 'CT'];
    private const TEAM_T_KEYWORDS  = ['TERRORISTÁK', 'TERRORISTS', 'T'];
    private const PLAYER_NAME_PREFIX_GARBAGE_REGEX = '/^[\s●◎🎤 माइक्‍क।♦❖ߐ⭐★☆✪📢]+/u';

    public function __construct()
    {
        $this->client = new ImageAnnotatorClient([
            'credentials' => storage_path('app/vision-key.json'),
        ]);
    }

    public function extractStructuredData(string $imagePath): array
    {
        $img = imagecreatefromjpeg($imagePath);

        $regions = [
            'ct_score' => [583, 473, 498, 394],
            't_score' => [579, 635, 502, 708],
            'map_name' => [660, 322, 594, 288],
            // játékosnevek
            'ct1' => [708, 394, 1004, 419], 'ct2' => [708, 421, 1006, 444], 'ct3' => [708, 446, 1004, 469],
            'ct4' => [706, 471, 998, 496], 'ct5' => [710, 496, 998, 523], 't1' => [708, 635, 1006, 658],
            't2' => [708, 660, 1009, 683], 't3' => [708, 685, 1002, 710], 't4' => [708, 712, 1002, 735],
            't5' => [710, 737, 996, 760],
            'ct5_k' => [1202, 521, 1156, 498],
            'ct4_k' => [1202, 471, 1156, 496],
            'ct3_k' => [1154, 446, 1206, 471],
            'ct2_k' => [1204, 444, 1156, 421],
            'ct1_k' => [1154, 394, 1202, 421],
            't1_k' => [1206, 633, 1152, 662],
            't2_k' => [1204, 687, 1154, 660],
            't3_k' => [1154, 710, 1208, 685],
            't4_k' => [1208, 737, 1154, 712],
            't5_k' => [1210, 762, 1156, 737],
            'ct1_d' => [1253, 392, 1204, 421],
            'ct2_d' => [1253, 442, 1204, 421],
            'ct3_d' => [1253, 469, 1206, 444],
            'ct4_d' => [1256, 496, 1202, 471],
            'ct5_d' => [1258, 521, 1202, 496],
            't1_d' => [1253, 633, 1208, 658],
            't2_d' => [1256, 685, 1208, 660],
            't3_d' => [1256, 710, 1208, 687],
            't4_d' => [1256, 735, 1208, 712],
            't5_d' => [1256, 760, 1208, 737],
            'ct1_a' => [1301, 394, 1256, 421],
            'ct2_a' => [1301, 442, 1256, 421],
            'ct3_a' => [1301, 469, 1258, 444],
            'ct4_a' => [1303, 496, 1258, 471],
            'ct5_a' => [1303, 521, 1258, 496],
            't1_a' => [1301, 633, 1258, 660],
            't2_a' => [1303, 685, 1256, 662],
            't3_a' => [1256, 687, 1303, 710],
            't4_a' => [1303, 737, 1258, 712],
            't5_a' => [1305, 762, 1258, 737],
            'ct1_points' => [1365, 392, 1426, 419],
            'ct2_points' => [1424, 442, 1365, 421],
            'ct3_points' => [1426, 471, 1368, 446],
            'ct4_points' => [1422, 496, 1368, 473],
            'ct5_points' => [1422, 521, 1368, 498],
            't1_points' => [1422, 660, 1365, 635],
            't2_points' => [1422, 683, 1370, 662],
            't3_points' => [1422, 708, 1365, 687],
            't4_points' => [1424, 733, 1365, 712],
            't5_points' => [1422, 760, 1368, 735],
        ];

        // ➕ add hozzá az összes többi régiót (kill, death, assist, points) mint eddig
        $statKeys = ['k', 'd', 'a', 'points'];
        foreach (['ct', 't'] as $team) {
            for ($i = 1; $i <= 5; $i++) {
                foreach ($statKeys as $stat) {
                    $key = "{$team}{$i}_{$stat}";

                }
            }
        }

        $rawResults = [];

        foreach ($regions as $key => [$x1, $y1, $x2, $y2]) {
            $x = min($x1, $x2);
            $y = min($y1, $y2);
            $width = abs($x2 - $x1);
            $height = abs($y2 - $y1);

            $crop = imagecrop($img, ['x' => $x, 'y' => $y, 'width' => $width, 'height' => $height]);
            if ($crop) {
                ob_start();
                imagepng($crop);
                $imageData = ob_get_clean();
                $response = $this->client->textDetection($imageData);
                $text = $response->getTextAnnotations();
                $rawResults[$key] = isset($text[0]) ? $text[0]->getDescription() : '';
            }
        }
        return $this->formatStructuredResult($rawResults);
    }

    protected function formatStructuredResult(array $raw): array
    {
        $teams = [
            'CT' => [
                'name' => 'Terrorelhárítók',
                'score' => $raw['ct_score'] ?? '?',
                'players' => [],
            ],
            'T' => [
                'name' => 'Terroristák',
                'score' => $raw['t_score'] ?? '?',
                'players' => [],
            ],
        ];

        foreach (['ct', 't'] as $team) {
            for ($i = 1; $i <= 5; $i++) {
                $prefix = $team . $i;
                $teams[strtoupper($team)]['players'][] = [
                    'name' => $raw[$prefix] ?? 'N/A',
                    'kills' => $raw["{$prefix}_k"] ?? 0,
                    'assists' => $raw["{$prefix}_a"] ?? 0,
                    'deaths' => $raw["{$prefix}_d"] ?? 0,
                    'score' => $raw["{$prefix}_points"] ?? 0,
                    'mvp' => 0,
                ];
            }
        }

        // MVP meghatározása pontszám alapján
        $allPlayers = array_merge($teams['CT']['players'], $teams['T']['players']);
        $mvp = collect($allPlayers)->sortByDesc('score')->first();
        $mvpName = $mvp['name'] ?? null;

        return [
            'map' => $raw['map_name'] ?? 'Ismeretlen',
            'duration' => $raw['duration'] ?? '-',
            'date' => $raw['date'] ?? '-',
            'teams' => $teams,
            'mvp' => $mvpName,
        ];
    }

    public function saveStatsFromStructured(array $structured, int $matchId): void
    {
        if ($matchId !== null) {
            foreach ($structured['teams'] as $teamKey => $teamData) {
                foreach ($teamData['players'] as $player) {
                    $user = User::where('steam_name', $player['name'])->first();

                    $alreadyExists = PlayerMatchStat::where('match_id', $matchId)
                        ->where('steam_name', $player['name'])
                        ->exists();

                    if (!$alreadyExists) {
                        PlayerMatchStat::create([
                            'user_id' => $user?->id,
                            'match_id' => $matchId,
                            'steam_name' => $player['name'],
                            'team_key' => $teamKey,
                            'kills' => (int)$player['kills'],
                            'assists' => (int)$player['assists'],
                            'deaths' => (int)$player['deaths'],
                            'mvp' => (int)$player['mvp'],
                            'score' => (int)$player['score'],
                        ]);
                    }
                }

                // 🧠 Meccshez tartozó csapatstatisztika mentése
                $match = TournamentMatch::find($matchId);

                if ($match) {
                    $teamId = $teamKey === 'CT' ? $match->team1_id : $match->team2_id;

                    if ($teamId) {
                        $alreadyTeamStatExists = TeamMatchStat::where('match_id', $matchId)
                            ->where('team_id', $teamId)
                            ->exists();

                        if (!$alreadyTeamStatExists) {
                            TeamMatchStat::create([
                                'match_id' => $matchId,
                                'team_id' => $teamId,
                                'team_key' => $teamKey,
                                'score' => (int)($teamData['score'] ?? 0),
                                'map' => $structured['map'] ?? null,
                                'mvp' => $structured['mvp'] ?? null,
                            ]);
                        }
                    }
                }
            }
        }
    }
    // Ide jöhet pl. a getCoordinatesFor() ha modularizálni akarod
}
