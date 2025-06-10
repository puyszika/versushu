<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MatchProposal extends Model
{
    protected $fillable = ['players', 'accepted_players', 'declined_players', 'status'];

    protected $casts = [
        'players' => 'array',
        'accepted_players' => 'array',
        'declined_players' => 'array',
    ];
}
