<?php

namespace App\Http\Controllers;

use App\Models\Championship;
use App\Models\Team;
use App\Models\TournamentMatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Services\MatchService;
use App\Models\MatchSubmission;
use App\Services\OcrService;
use Illuminate\Support\Facades\Log;
use App\Services\GoogleOcrService;
use Illuminate\Support\Carbon;

class ChampionshipController extends Controller
{
    public function publicIndex()
    {
        $championships = Championship::latest()->paginate(10);
        return view('championships.index', compact('championships'));
    }

    public function register(Championship $championship)
    {
        $user = Auth::user();

        if (!$user->team) {
            return back()->with('error', 'Előbb csatlakoznod kell egy csapathoz!');
        }

        if ($championship->teams()->where('team_id', $user->team->id)->exists()) {
            return back()->with('error', 'A csapatod már regisztrált erre a bajnokságra!');
        }

        $championship->teams()->attach($user->team->id);

        return back()->with('success', 'A csapat sikeresen regisztrált a bajnokságra!');
    }

    public function publicShow(Championship $championship)
    {
        $championship->load('teams.members');
        return view('championships.show', compact('championship'));
    }

    public function index(Request $request)
    {
        $query = Championship::query();

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->input('search') . '%');
        }

        $championships = $query->paginate(10);
        return view('admin.championships.index', compact('championships'));
    }

    public function create()
    {
        return view('admin.championships.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'reward_1' => 'nullable|string|max:255',
            'reward_2' => 'nullable|string|max:255',
            'reward_3' => 'nullable|string|max:255',
            'reward_4' => 'nullable|string|max:255',
            'format' => 'required|in:BO1,BO3,BO5',
            'double_elimination' => 'nullable',
        ]);

        $imagePath = $request->hasFile('image') ?
            $request->file('image')->store('championships', 'public') : null;

        Championship::create([
            'name' => $request->name,
            'description' => $request->description,
            'image_path' => $imagePath,
            'reward_1' => $request->reward_1,
            'reward_2' => $request->reward_2,
            'reward_3' => $request->reward_3,
            'reward_4' => $request->reward_4,
            'format' => $request->input('format'),
            'double_elimination' => $request->boolean('double_elimination'),
        ]);

        return redirect()->route('admin.championships.index')->with('success', 'Bajnokság létrehozva!');
    }

    public function show(Championship $championship)
    {
        $championship->load('teams.members');
        return view('admin.championships.show', compact('championship'));
    }

    public function edit(Championship $championship)
    {
        return view('admin.championships.edit', compact('championship'));
    }

    public function update(Request $request, Championship $championship)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'reward_1' => 'nullable|string|max:255',
            'reward_2' => 'nullable|string|max:255',
            'reward_3' => 'nullable|string|max:255',
            'reward_4' => 'nullable|string|max:255',
            'format' => 'required|in:BO1,BO3,BO5',
            'double_elimination' => 'nullable',
        ]);

        if ($request->hasFile('image')) {
            if ($championship->image_path) {
                Storage::disk('public')->delete($championship->image_path);
            }
            $championship->image_path = $request->file('image')->store('championships', 'public');
        }

        $championship->update($request->except('image'));

        return redirect()->route('admin.championships.index')->with('success', 'Bajnokság frissítve!');
    }

    public function destroy(Championship $championship)
    {
        if ($championship->image_path) {
            Storage::disk('public')->delete($championship->image_path);
        }
        $championship->delete();
        return redirect()->route('admin.championships.index')->with('success', 'Bajnokság törölve!');
    }

    public function detachTeam(Championship $championship, Team $team)
    {
        $championship->teams()->detach($team->id);
        return back()->with('success', 'A csapat eltávolítva a bajnokságból.');
    }

    public function finish(Championship $championship)
    {
        if ($championship->status !== 'in_progress') {
            return back()->withErrors('Ez a bajnokság nincs folyamatban.');
        }

        $championship->status = 'finished';
        $championship->save();

        return back()->with('success', 'Bajnokság lezárva!');
    }

    public function startTournament(Championship $championship, MatchService $matchService)
    {
        \Log::info('🏁 Bajnokság indítása: generateInitialBracket indul', ['championship_id' => $championship->id]);

        $matchService->generateInitialBracket($championship);

        $championship->status = 'in_progress';
        $championship->save();

        return redirect()->back()->with('success', 'Bajnokság elindítva!');
    }

    public function bracket(Championship $championship)
    {
        $matches = $championship->matches()
            ->with(['team1', 'team2', 'winner', 'submissions.team'])
            ->orderBy('round')
            ->get(); // az adminnak mutasson mindent, még az üreseket is

        $matchesByRound = $matches->where('round', '!=', 99)->groupBy('round');
        $previousMatches = [];

        foreach ($matches as $match) {
            if ($match->round > 1 && $match->team1_id && $match->team2_id && $match->round !== 99) {
                $prevRound = $match->round - 1;

                $prev1 = $matches->first(fn($m) =>
                    $m->round === $prevRound &&
                    ($m->team1_id === $match->team1_id || $m->team2_id === $match->team1_id)
                );

                $prev2 = $matches->first(fn($m) =>
                    $m->round === $prevRound &&
                    ($m->team1_id === $match->team2_id || $m->team2_id === $match->team2_id)
                );

                $previousMatches[$match->id] = [$prev1?->id, $prev2?->id];
            }
        }

        $bronzeMatch = null;
        $finalRound = $matchesByRound->keys()->max();

        if ($finalRound >= 2) {
            $semiFinalMatches = $matchesByRound[$finalRound - 1] ?? collect();
            if ($semiFinalMatches->count() >= 2) {
                $semiLosers = $semiFinalMatches->filter(fn($m) =>
                    $m->winner_id !== $m->team1_id && $m->winner_id !== $m->team2_id
                )->pluck('id');

                if ($semiLosers->count() === 2) {
                    $bronzeMatch = $matches->firstWhere('round', 99);
                }
            }
        }

        return view('admin.championships.bracket', compact('championship', 'matchesByRound', 'previousMatches', 'bronzeMatch'));
    }
    //Ez a fix csapat továbbengedő amit az admin irányít
   /* public function setWinner(TournamentMatch $match, Request $request, MatchService $matchService)
    {
        $request->validate([
            'winner_id' => 'nullable|exists:teams,id',
            'admin_note' => 'nullable|string|max:1000',
            'disqualified_team_id' => 'nullable|exists:teams,id',
        ]);

        $match->admin_note = $request->admin_note;
        $match->disqualified_team_id = $request->disqualified_team_id;

        // Automatikus győztes, ha az egyik csapat ki van zárva
        if ($match->disqualified_team_id) {
            if ($match->team1_id && $match->disqualified_team_id == $match->team1_id) {
                $match->winner_id = $match->team2_id;
            } elseif ($match->team2_id && $match->disqualified_team_id == $match->team2_id) {
                $match->winner_id = $match->team1_id;
            }
        } else {
            $match->winner_id = $request->winner_id;
        }

        $match->status = 'completed';
        $match->save();

        if ($match->winner_id) {
            $matchService->advanceWinner($match, $match->winner_id);
        }

        return back()->with('success', 'Eredmény frissítve.');
    } */

    public function setMvp(Championship $championship, Request $request)
    {
        $request->validate([
            'mvp_id' => 'required|exists:users,id',
        ]);

        $championship->mvp_id = $request->mvp_id;
        $championship->save();

        return back()->with('success', 'MVP kiválasztva!');
    }
    public function submitResult(Request $request, TournamentMatch $match)
    {
        $user = Auth::user();
        $team = $user->team;

        if (!$team || ($match->team1_id !== $team->id && $match->team2_id !== $team->id)) {
            return back()->with('error', 'Nincs jogosultságod ehhez a meccshez.');
        }

        $alreadySubmitted = MatchSubmission::where('match_id', $match->id)
            ->where('team_id', $team->id)
            ->exists();

        if ($alreadySubmitted) {
            return back()->with('error', 'Ezt a meccset a csapatod már beküldte!');
        }

        $request->validate([
            'result_image' => 'required|image|mimes:jpg,jpeg,png|max:5120',
            'custom_result_text' => 'nullable|string|max:255',
        ]);

        $path = $request->file('result_image')->store('match_results', 'public');

        MatchSubmission::create([
            'match_id' => $match->id,
            'team_id' => $team->id,
            'result_image_path' => $path,
            'custom_result_text' => $request->custom_result_text,
            'is_verified' => false,
        ]);

        $match->submitted_by_team_id = $team->id;
        $match->save();

        Log::info('📤 Meccs eredmény beküldve', [
            'match_id' => $match->id,
            'team_id' => $team->id,
        ]);

        return back()->with('success', 'Eredmény beküldve, admin jóváhagyásra vár.');
    }
    public function verifyResult(
        TournamentMatch $match,
        MatchService $matchService,
        GoogleOcrService $ocrService
    ) {
        Log::info('🧠 verifyResult elindult', ['match_id' => $match->id]);

        $match->refresh();

        if (!$match->submitted_by_team_id) {
            Log::warning('⚠️ Nincs submitted_by_team_id', ['match_id' => $match->id]);
            return back()->with('error', 'Nincs beküldött eredmény ehhez a meccshez.');
        }

        $submission = MatchSubmission::where('match_id', $match->id)
            ->where('team_id', $match->submitted_by_team_id)
            ->first();

        if (!$submission) {
            Log::warning('❌ Nincs megfelelő beküldés a csapattól.', ['match_id' => $match->id]);
            return back()->with('error', 'Nincs megfelelő beküldés a csapattól.');
        }

        $fullPath = storage_path('app/public/' . $submission->result_image_path);

        if (!file_exists($fullPath)) {
            Log::error('🚫 Nem található a beküldött kép!', ['path' => $fullPath]);
            return back()->with('error', 'Hiba történt: nem található a beküldött kép.');
        }

        try {
            Log::info('🔍 OCR feldolgozás indul', ['match_id' => $match->id]);

            $structured = $ocrService->extractStructuredData($fullPath, $match->id);
            $ocrService->saveStatsFromStructured($structured, $match->id);

            $submission->ocr_result = json_encode($structured, JSON_UNESCAPED_UNICODE);
            $submission->save();

            // ✅ Map, duration és időpont mentése
            $match->map = $structured['map'] ?? null;
            $match->duration = $structured['duration'] ?? null;
            $match->played_at = now(); // itt a fix: nem kerül be '-' érték
            $match->ct_score = (int)($structured['teams']['CT']['score'] ?? 0);
            $match->t_score = (int)($structured['teams']['T']['score'] ?? 0);
            $match->status = 'completed';

            Log::info('📊 Score mentés', [
                'ct_score' => $match->ct_score,
                't_score' => $match->t_score
            ]);

            Log::info('✅ OCR feldolgozás sikeres', [
                'match_id' => $match->id,
                'mvp' => $structured['mvp'] ?? null,
                'map' => $structured['map'] ?? null
            ]);
        } catch (\Exception $e) {
            Log::error('💥 OCR hiba', ['error' => $e->getMessage()]);
            return back()->with('error', 'Hiba történt az OCR feldolgozás során.');
        }

        // ✅ Győztes és státusz mentése
        $winnerId = request()->input('winner_id');
        $match->winner_id = $winnerId;
        $match->is_verified = true;
        $match->status = 'completed';

        if ($disqualified = request()->input('disqualified_team_id')) {
            $match->disqualified_team_id = $disqualified;
        }

        $match->save();

        // ➡️ Győztes továbbjuttatása
        $matchService->advanceWinner($match, $winnerId);

        Log::info('🏁 Meccs lezárva és továbbküldve', [
            'match_id' => $match->id,
            'winner_id' => $winnerId,
        ]);

        return back()->with('success', 'Eredmény elfogadva. OCR lefutott.');
    }




    public function publicBracket(Championship $championship)
    {
        $matches = $championship->matches()
            ->with(['team1', 'team2', 'winner', 'submissions.team'])
            ->where(function ($query) {
                $query->whereNotNull('team1_id')
                    ->orWhereNotNull('team2_id');
            })
            ->get();

        $matchesByRound = $matches->where('round', '!=', 99)->groupBy('round');
        $previousMatches = [];

        foreach ($matches as $match) {
            if ($match->round > 1 && $match->team1_id && $match->team2_id && $match->round !== 99) {
                $prevRound = $match->round - 1;

                $prev1 = $matches->first(fn($m) =>
                    $m->round === $prevRound &&
                    ($m->team1_id === $match->team1_id || $m->team2_id === $match->team1_id)
                );

                $prev2 = $matches->first(fn($m) =>
                    $m->round === $prevRound &&
                    ($m->team1_id === $match->team2_id || $m->team2_id === $match->team2_id)
                );

                $previousMatches[$match->id] = [$prev1?->id, $prev2?->id];
            }
        }

        $bronzeMatch = null;
        $finalRound = $matchesByRound->keys()->max();

        if ($finalRound >= 2) {
            $semiFinalMatches = $matchesByRound[$finalRound - 1] ?? collect();
            if ($semiFinalMatches->count() >= 2) {
                $semiLosers = $semiFinalMatches->filter(fn($m) =>
                    $m->winner_id !== $m->team1_id && $m->winner_id !== $m->team2_id
                )->pluck('id');

                if ($semiLosers->count() === 2) {
                    $bronzeMatch = $matches->firstWhere('round', 99);
                }
            }
        }

        return view('championships.bracket', compact('championship', 'matchesByRound', 'previousMatches', 'bronzeMatch'));
    }

}
