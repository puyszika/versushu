<?php

// app/Events/MapFinalized.php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;

class MapFinalized implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $lobbyCode;
    public $map;

    public function __construct(string $lobbyCode, string $map)
    {
        $this->lobbyCode = $lobbyCode;
        $this->map = $map;
    }

    public function broadcastOn()
    {
        return new PresenceChannel('lobby.' . $this->lobbyCode);
    }

    public function broadcastWith()
    {
        return [
            'map' => $this->map
        ];
    }
}
