<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MatchmakingQueue extends Model
{
    protected $fillable = ['user_id', 'status', 'queued_at'];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
