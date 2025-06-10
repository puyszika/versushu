<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MatchProposalController extends Controller
{
        public function accept(MatchProposal $proposal)
    {
        $userId = auth()->id();

        if (!in_array($userId, $proposal->players)) {
            return response()->json(['message' => 'Nem vagy része ennek a meccsnek'], 403);
        }

        $accepted = $proposal->accepted_players ?? [];
        if (!in_array($userId, $accepted)) {
            $accepted[] = $userId;
            $proposal->update(['accepted_players' => $accepted]);
        }

        // Ellenőrzés: mindenki elfogadta?
        if (count($accepted) === 10) {
            $proposal->update(['status' => 'accepted']);
            // TODO: Lobby generálás itt
        }

        return response()->json(['message' => 'Elfogadtad a meccset!']);
    }

    public function decline(MatchProposal $proposal)
    {
        $userId = auth()->id();

        $declined = $proposal->declined_players ?? [];
        if (!in_array($userId, $declined)) {
            $declined[] = $userId;
            $proposal->update(['declined_players' => $declined, 'status' => 'cancelled']);
        }

        return response()->json(['message' => 'Elutasítottad a meccset.']);
    }
}
