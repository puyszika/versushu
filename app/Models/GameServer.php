<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameServer extends Model
{
    protected $fillable = ['ip', 'port', 'status', 'token'];
}
