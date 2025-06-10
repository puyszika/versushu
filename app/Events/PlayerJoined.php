<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;

class PlayerJoined implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public string $lobbyCode;
    public User $user;
    public string $role;
    public int $slot;

    public function __construct(string $lobbyCode, User $user, string $role, int $slot)
    {
        $this->lobbyCode = $lobbyCode;
        $this->user = $user;
        $this->role = $role;
        $this->slot = $slot;
    }

    public function broadcastOn()
    {
        return new PresenceChannel('lobby.' . $this->lobbyCode);
    }

    public function broadcastWith(): array
    {
        return [
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'avatar' => $this->user->profile_picture
                    ? asset('storage/profiles/' . $this->user->profile_picture)
                    : asset('images/default-avatar.png'),
            ],
            'role' => $this->role,
            'slot' => $this->slot,
        ];
    }
}
