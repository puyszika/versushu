<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MatchAccept extends Model
{
    protected $fillable = ['lobby_id', 'user_id', 'accepted'];
}
