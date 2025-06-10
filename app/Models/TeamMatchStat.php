<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeamMatchStat extends Model
{
    protected $fillable = [
        'team_id',
        'match_id',
        'map',
        'score',
        'opponent_score',
        'won',
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function match()
    {
        return $this->belongsTo(TournamentMatch::class, 'match_id');
    }
}
