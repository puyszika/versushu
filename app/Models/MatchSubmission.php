<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatchSubmission extends Model
{
    protected $fillable = [
        'match_id', 'team_id', 'result_image_path', 'custom_result_text', 'ocr_result', 'is_verified',
    ];

    public function match()
    {
        return $this->belongsTo(TournamentMatch::class, 'match_id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
