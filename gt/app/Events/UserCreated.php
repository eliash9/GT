<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $userName;

    public function __construct(string $userName)
    {
        $this->userName = $userName;
        $this->message = "New user registered: {$userName}";
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('notifications'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'user.created';
    }
}
