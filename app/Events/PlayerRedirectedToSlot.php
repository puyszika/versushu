<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PlayerRedirectedToSlot implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $lobbyCode;
    public int $userId;
    public string $side;
    public int $slotIndex;

    public function __construct(string $lobbyCode, int $userId, string $side, int $slotIndex)
    {
        $this->lobbyCode = $lobbyCode;
        $this->userId = $userId;
        $this->side = $side;
        $this->slotIndex = $slotIndex;
    }

    public function broadcastOn(): PresenceChannel
    {
        return new PresenceChannel('lobby.' . $this->lobbyCode);
    }

    public function broadcastAs(): string
    {
        return 'player.redirected';
    }
}
