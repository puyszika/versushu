<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PlayerRedirectedToLobby implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $userId;
    public string $lobbyCode;

    public function __construct(int $userId, string $lobbyCode)
    {
        $this->userId = $userId;
        $this->lobbyCode = $lobbyCode;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('user.' . $this->userId);
    }

    public function broadcastWith(): array
    {
        return ['lobby_code' => $this->lobbyCode];
    }
    public function broadcastAs()
    {
        return 'player-redirected-to-lobby';
    }
}


