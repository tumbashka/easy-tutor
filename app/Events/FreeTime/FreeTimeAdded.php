<?php

namespace App\Events\FreeTime;

use App\Models\FreeTime;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FreeTimeAdded
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public FreeTime $freeTime;
    public User $user;

    /**
     * Create a new event instance.
     */
    public function __construct(FreeTime $freeTime)
    {
        $this->freeTime = $freeTime;
        $this->user = $freeTime->user;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
