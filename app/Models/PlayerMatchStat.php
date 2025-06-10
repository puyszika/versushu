<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlayerMatchStat extends Model
{
    protected $fillable = [
        'user_id',
        'match_id',
        'steam_name',
        'team_key',
        'kills',
        'assists',
        'deaths',
        'mvp',
        'score',
    ];

    public function match()
    {
        return $this->belongsTo(TournamentMatch::class, 'match_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
