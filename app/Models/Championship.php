<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Championship extends Model
{
    protected $fillable = [
        'name', 'description', 'image_path',
        'reward_1', 'reward_2', 'reward_3', 'reward_4',
        'format', 'double_elimination', 'mvp_user_id'
    ];

    public function mvp()
    {
        return $this->belongsTo(User::class, 'mvp_user_id');
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class, 'championship_team');
    }
    public function matches()
    {
        return $this->hasMany(\App\Models\TournamentMatch::class);
    }
}
