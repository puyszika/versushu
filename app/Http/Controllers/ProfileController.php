<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;
use App\Models\User;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $request->user()->avatar_path = $path;
        }


        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Show a public user profile with optional Steam stats.
     */
    public function show(User $user)
    {
        $stats = null;

        if ($user->steam_id) {
            $stats = Cache::remember("steam_stats_{$user->steam_id}", now()->addMinutes(10), function () use ($user) {
                return $this->getSteamStats($user->steam_id);
            });
        }

        return view('users.show', compact('user', 'stats'));
    }

    /**
     * Fetch Counter-Strike stats from Steam API.
     */
    private function getSteamStats($steamId)
    {
        $apiKey = config('services.steam.key');
        $url = "https://api.steampowered.com/ISteamUserStats/GetUserStatsForGame/v0002/?appid=730&key={$apiKey}&steamid={$steamId}";

        $response = Http::get($url);

        if ($response->ok()) {
            return $response->json()['playerstats'] ?? null;
        }

        return null;
    }
}
