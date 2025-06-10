<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class LobbyMap extends Model {
    protected $fillable = ['lobby_id','map_name','status','order'];
    public function lobby() {
        return $this->belongsTo(Lobby::class);
    }
}
