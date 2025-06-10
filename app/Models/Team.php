<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TeamLog;
use App\Models\Championship;

class Team extends Model
{
    protected $fillable = [
        'name',
        'owner_id',
        'description',
        'logo_path',
        'invite_code',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function members()
    {
        return $this->hasMany(User::class);
    }

    public function logs() {
        return $this->hasMany(TeamLog::class);
    }

    public function championships()
    {
        return $this->belongsToMany(Championship::class, 'championship_team');
    }

    public function matchesAsTeam1()
    {
        return $this->hasMany(TournamentMatch::class, 'team1_id');
    }

    public function matchesAsTeam2()
    {
        return $this->hasMany(TournamentMatch::class, 'team2_id');
    }

    public function wins()
    {
        return $this->hasMany(TournamentMatch::class, 'winner_id');
    }
}
