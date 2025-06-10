<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Lobby extends Model {
    protected $fillable = ['code','host_user_id','status'];

    public function users() {
        return $this->belongsToMany(User::class)
            ->withPivot('role','slot_index')
            ->withTimestamps();
    }
    public function maps() {
        return $this->hasMany(LobbyMap::class);
    }
}
