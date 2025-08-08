<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class MessageRead implements ShouldBroadcastNow
{
    public ?int $chatId;
    public ?int $messageId;
    public User $user;

    public function __construct($chatId, $messageId, $user)
    {
        $this->chatId = $chatId;
        $this->messageId = $messageId;
        $this->user = $user;
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('App.Models.User.' . auth()->id());
    }

    public function broadcastAs(): string
    {
        return 'message-read';
    }

    public function broadcastWith(): array
    {
        return [
            'chatId' => $this->chatId,
            'messageId' => $this->messageId,
            'user' => $this->user,
        ];
    }
}
