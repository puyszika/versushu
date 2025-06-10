<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Team;
use App\Models\TeamLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\TournamentMatch;

class TeamController extends Controller
{
    public function adminIndex(Request $request)
    {
        $query = Team::with('members');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $teams = $query->paginate(15); // ✅ itt a paginate

        return view('admin.teams.index', compact('teams'));
    }

    public function adminDestroy(Team $team)
    {
        $team->delete();

        return redirect()->route('admin.teams.index')->with('success', 'A csapat törölve lett.');
    }

    public function index(Request $request)
    {
        $userTeam = Auth::user()->team;
        $query = Team::query();

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $teams = $query->paginate(10);
        return view('teams.index', compact('userTeam', 'teams'));
    }

    public function show(Team $team)
    {

        $teamMatches = TournamentMatch::with(['playerStats'])
            ->where(function ($query) use ($team) {
                $query->where('team1_id', $team->id)
                    ->orWhere('team2_id', $team->id);
            })
            ->where('is_verified', true)
            ->latest()
            ->get();

        $team->load('members');
        return view('teams.show', compact('team', 'teamMatches'));
    }

    public function create()
    {
        return view('teams.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:teams|max:50',
            'description' => 'nullable|string|max:1000',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,svg,gif|max:2048',
        ]);

        $logoPath = $request->hasFile('logo')
            ? $request->file('logo')->store('logos', 'public')
            : null;

        $team = Team::create([
            'name' => $request->name,
            'description' => $request->description,
            'logo_path' => $logoPath,
            'invite_code' => Str::random(8),
            'owner_id' => Auth::id(),
        ]);

        $user = Auth::user();
        $user->team_id = $team->id;
        $user->save();

        return redirect()->route('teams.index')->with('success', 'Csapat sikeresen létrehozva!');
    }

    public function join()
    {
        return view('teams.join');
    }

    public function handleJoin(Request $request)
    {
        $request->validate([
            'invite_code' => 'required'
        ]);

        if (Auth::user()->team_id) {
            return back()->withErrors(['invite_code' => 'Már tagja vagy egy csapatnak!']);
        }

        $team = Team::where('invite_code', $request->invite_code)->first();

        if (!$team) {
            return back()->withErrors(['invite_code' => 'Érvénytelen meghívókód.']);
        }

        $user = Auth::user();
        $user->team_id = $team->id;
        $user->save();

        TeamLog::create([
            'team_id' => $team->id,
            'user_id' => $user->id,
            'action' => 'join',
            'details' => $user->name . ' csatlakozott a csapathoz.',
        ]);

        return redirect()->route('teams.index')->with('success', 'Sikeresen csatlakoztál a csapathoz!');
    }

    public function leave()
    {
        $user = Auth::user();

        if ($user->id === optional($user->team)->owner_id) {
            return back()->withErrors(['msg' => 'A csapat kapitánya nem léphet ki. Előbb add át a vezetést vagy töröld a csapatot.']);
        }

        $user->team_id = null;
        $user->save();

        return redirect()->route('teams.index')->with('success', 'Kiléptél a csapatból');
    }

    public function transferOwnership(Request $request)
    {
        $request->validate([
            'new_owner_id' => 'required|exists:users,id',
        ]);

        $user = Auth::user();
        $team = $user->ownedTeam;

        if (!$team) {
            return back()->withErrors(['msg' => 'Nem vagy csapatkapitány.']);
        }

        $newOwner = User::find($request->new_owner_id);
        if ($team->id !== $newOwner->team_id) {
            return back()->withErrors(['msg' => 'A kiválasztott felhasználó nem tagja a csapatodnak.']);
        }

        $team->owner_id = $newOwner->id;
        $team->save();

        TeamLog::create([
            'team_id' => $team->id,
            'user_id' => $user->id,
            'action' => 'transfer_ownership',
            'details' => 'A kapitányi szerepet átadta ' . $newOwner->name . ' felhasználónak.',
        ]);

        return redirect()->route('teams.index')->with('success', 'Kapitányi szerep sikeresen átadva!');
    }
    public function destroy()
    {
        $user = Auth::user();
        $team = $user->ownedTeam;

        if (!$team) {
            return back()->withErrors(['msg' => 'Csak a csapatkapitány törölheti a csapatot.']);
        }

        foreach ($team->members as $member) {
            $member->team_id = null;
            $member->save();
        }

        if ($team->logo_path) {
            Storage::disk('public')->delete($team->logo_path);
        }

        $team->delete();

        return redirect()->route('teams.index')->with('success', 'A csapat sikeresen törölve lett.');
    }

    public function edit()
    {
        $team = Auth::user()->team;

        if (!$team) {
            return redirect()->route('teams.index')->withErrors(['msg' => 'Nincs csapatod!']);
        }

        return view('teams.edit', compact('team'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|max:50|unique:teams,name,' . Auth::user()->team->id,
            'description' => 'nullable|string|max:1000',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,svg,gif|max:2048',
        ]);

        $team = Auth::user()->team;
        $team->name = $request->name;
        $team->description = $request->description;

        if ($request->hasFile('logo')) {
            if ($team->logo_path) {
                Storage::disk('public')->delete($team->logo_path);
            }

            $path = $request->file('logo')->store('logos', 'public');
            $team->logo_path = $path;
        }

        $team->save();

        return redirect()->route('teams.index')->with('success', 'Csapat sikeresen frissítve');
    }

    public function kick(User $user)
    {
        $authUser = Auth::user();
        $team = $authUser->ownedTeam;

        if (!$team) {
            return back()->withErrors(['msg' => 'Csak a csapatkapitány rúghat ki tagokat.']);
        }

        if ($user->team_id !== $team->id) {
            return back()->withErrors(['msg' => 'Ez a felhasználó nem a te csapatodban van.']);
        }

        if ($user->id === $authUser->id) {
            return back()->withErrors(['msg' => 'Saját magadat nem rúghatod ki.']);
        }

        $user->team_id = null;
        $user->save();

        TeamLog::create([
            'team_id' => $team->id,
            'user_id' => $authUser->id,
            'action' => 'kick',
            'details' => $user->name . ' eltávolítva a csapatból.',
        ]);

        return back()->with('success', 'Tag sikeresen eltávolítva a csapatból.');
    }
}
