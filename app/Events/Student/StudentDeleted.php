<?php

namespace App\Events\Student;

use App\Models\Student;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StudentDeleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    public Student $student;
    public User $user;
    /**
     * Create a new event instance.
     */
    public function __construct(Student $student)
    {
        $this->student = $student;
        $this->user = $student->user;
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
