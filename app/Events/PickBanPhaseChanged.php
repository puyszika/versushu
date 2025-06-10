<?php

// app/Events/PickBanPhaseChanged.php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;

class PickBanPhaseChanged implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $lobbyCode;
    public $newPhase;

    public function __construct(string $lobbyCode, int $newPhase)
    {
        $this->lobbyCode = $lobbyCode;
        $this->newPhase = $newPhase;
    }

    public function broadcastOn()
    {
        return new PresenceChannel('lobby.' . $this->lobbyCode);
    }

    public function broadcastWith()
    {
        return [
            'phase' => $this->newPhase
        ];
    }
}
