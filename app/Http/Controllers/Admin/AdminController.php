<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Team;
use App\Models\Championship;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;

class AdminController extends Controller
{

    public function dashboard()
    {
        // Alap statisztikák cache-ben (60 sec)
        $userCount = Cache::remember('admin_user_count', 60, fn() => User::count());
        $teamCount = Cache::remember('admin_team_count', 60, fn() => Team::count());
        $championshipCount = Cache::remember('admin_championship_count', 60, fn() => Championship::count());
        $registrationCount = Cache::remember('admin_registration_count', 60, fn() => \DB::table('championship_team')->count());

        // Legutóbbi entitások
        $recentUsers = User::latest()->take(5)->get();
        $recentTeams = Team::latest()->take(5)->get();
        $recentChampionships = Championship::latest()->take(5)->get();

        // Havi felhasználói regisztráció stat
        $monthly = User::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Magyar hónapnevek generálása (Carbon formázással)
        setlocale(LC_TIME, 'hu_HU.UTF-8');
        $chartLabels = $monthly->map(fn($row) =>
        Carbon::now()->startOfYear()->addMonths($row->month - 1)->locale('hu')->translatedFormat('F')
        );

        $chartData = $monthly->pluck('count');

        return view('admin.dashboard', compact(
            'userCount',
            'teamCount',
            'championshipCount',
            'registrationCount',
            'recentUsers',
            'recentTeams',
            'recentChampionships',
            'chartLabels',
            'chartData'
        ));
    }
}
