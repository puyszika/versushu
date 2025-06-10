<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;

class MapBanned implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $lobbyCode;
    public $map;
    public $byRole;

    public function __construct(string $lobbyCode, string $map, string $byRole)
    {
        $this->lobbyCode = $lobbyCode;
        $this->map = $map;
        $this->byRole = $byRole;
    }

    public function broadcastOn()
    {
        return new PresenceChannel('lobby.' . $this->lobbyCode);
    }

    public function broadcastWith()
    {
        return [
            'map' => $this->map,
            'byRole' => $this->byRole,
        ];
    }
}

