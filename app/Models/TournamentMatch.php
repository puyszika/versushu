<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Team;
use App\Models\MatchSubmission;
use App\Services\MatchService;


class TournamentMatch extends Model
{
    protected $fillable = [
        'championship_id',
        'round',
        'match_index',
        'team1_id',
        'team2_id',
        'winner_team_id',
        'next_match_id',
        'result_image_path',
        'submitted_by_team_id',
        'is_verified',
        'status',
        'map',
        'duration',
        'played_at',
        'disqualified_team_id',
        'ct_score',
        't_score',
    ];

    public function team1()
    {
        return $this->belongsTo(\App\Models\Team::class, 'team1_id');
    }

    public function team2()
    {
        return $this->belongsTo(\App\Models\Team::class, 'team2_id');
    }

    public function winner() {
        return $this->belongsTo(Team::class, 'winner_id');
    }

    public function championship()
    {
        return $this->belongsTo(\App\Models\Championship::class);
    }
    // Kapcsolat a beküldő csapathoz
    public function submittedByTeam()
    {
        return $this->belongsTo(Team::class, 'submitted_by_team_id');
    }

    public function submissions()
    {
        return $this->hasMany(MatchSubmission::class, 'match_id');
    }

    public function playerStats()
    {
        return $this->hasMany(\App\Models\PlayerMatchStat::class, 'match_id');
    }
}
