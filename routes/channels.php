<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('lobby.{code}', function ($user, $code) {
    return [
        'id' => $user->id,
        'name' => $user->name,
        'avatar' => $user->profile_picture ? asset('storage/profiles/' . $user->profile_picture) : asset('images/default-avatar.png'),
        'role' => $user->pivot?->role ?? null,
        'slot' => $user->pivot?->slot_index ?? null,
    ];
});

Broadcast::channel('user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});