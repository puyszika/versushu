<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Friend extends Model
    {
    protected $fillable = ['user_id', 'friend_id', 'status'];

    // Felhasználó aki küldi
    public function user()
    {
    return $this->belongsTo(User::class, 'user_id');
    }

    // Felhasználó akit meghív
    public function friend()
    {
    return $this->belongsTo(User::class, 'friend_id');
    }
}
