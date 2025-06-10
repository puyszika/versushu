<?php

// app/Events/SideChanged.php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;

class SideChanged implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $lobbyCode;
    public $userId;
    public $fromRole;
    public $toRole;
    public $slot;

    public function __construct($lobbyCode, $userId, $fromRole, $toRole, $slot)
    {
        $this->lobbyCode = $lobbyCode;
        $this->userId = $userId;
        $this->fromRole = $fromRole;
        $this->toRole = $toRole;
        $this->slot = $slot;
    }

    public function broadcastOn()
    {
        return new PresenceChannel('lobby.' . $this->lobbyCode);
    }

    public function broadcastWith()
    {
        return [
            'user_id' => $this->userId,
            'from' => $this->fromRole,
            'to' => $this->toRole,
            'slot' => $this->slot,
        ];
    }
}
