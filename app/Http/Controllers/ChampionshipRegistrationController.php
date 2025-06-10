<?php

namespace App\Http\Controllers;

use App\Models\Championship;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ChampionshipRegistrationController extends Controller
{
    public function store(Championship $championship)
    {
        $user = Auth::user();

        if (!$user->team) {
            return back()->with('error', 'Előbb létre kell hoznod vagy csatlakoznod egy csapathoz!');
        }

        if ($championship->teams->contains($user->team->id)) {
            return back()->with('error', 'A csapatod már regisztrált erre a bajnokságra!');
        }

        $championship->teams()->attach($user->team->id);

        Log::info("Team ID {$user->team->id} registered to Championship ID {$championship->id}");

        return back()->with('success', 'Sikeresen regisztráltatok a bajnokságra!');
    }
}
