<?php

namespace App\Events\LessonTime;

use App\Models\LessonTime;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Log\Logger;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class LessonTimeDeleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public LessonTime $lessonTime;
    public User $user;
    /**
     * Create a new event instance.
     */
    public function __construct(LessonTime $lessonTime)
    {
        $this->lessonTime = $lessonTime;
        $this->user = $lessonTime->student->user;
        Log::debug('Событие - удалили занятие ученика');
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
