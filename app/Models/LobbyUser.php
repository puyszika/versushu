<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LobbyUser extends Model
{
    use HasFactory;

    protected $table = 'lobby_user'; // 👈 EZ fontos, mert nem "lobby_users"!

    protected $fillable = [
        'lobby_id',
        'user_id',
        'role',
        'slot_index',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lobby()
    {
        return $this->belongsTo(Lobby::class);
    }
}
