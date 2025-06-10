<?php

// app/Events/SpectatorJoined.php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;

class SpectatorJoined implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public string $lobbyCode;
    public User $user;
    public int $slot;

    public function __construct(string $lobbyCode, User $user, int $slot)
    {
        $this->lobbyCode = $lobbyCode;
        $this->user = $user;
        $this->slot = $slot;
    }

    public function broadcastOn()
    {
        return new PresenceChannel('lobby.' . $this->lobbyCode);
    }

    public function broadcastWith(): array
    {
        $avatar = $this->user->profile_picture
            ? asset('storage/' . $this->user->profile_picture)
            : asset('images/default-avatar.png');

        return [
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'avatar' => $this->user->profile_picture ? asset('storage/profiles/' . $this->user->profile_picture) : null,
            ],
            'slot' => $this->slot,
        ];
    }
}
