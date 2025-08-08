<?php

namespace App\Events;

use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewMessageOnChat implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public User $user;
    public Chat $chat;
    public $pivot;

    public function __construct(
        public Message $message,
    ) {
        $this->chat = $message->chat;
        $this->user = $this->chat->users()->where('user_id', $this->message->user_id)->first();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        $users = $this->chat->users->except($this->user->id);
        $channels = [];
        foreach ($users as $user) {
            $channels[] = new PrivateChannel("App.Models.User.{$user->id}");
        }

        return $channels;
    }

    public function broadcastWith(): array
    {
        return [
            'message' => [
                'id' => $this->message->id,
                'text' => $this->message->text,
                'created_at' => $this->message->created_at,
                'updated_at' => $this->message->updated_at,
            ],
            'chat' => [
                'id' => $this->chat->id,
                'type' => $this->chat->type,
                'name' => $this->chat->name,
                'avatar_url' => $this->chat->avatar_url,
                'url' => $this->chat->url,
            ],
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'avatar_url' => $this->user->avatar_url,
            ],
            'user_chat' => [
                'accepted' => (bool)$this->user->pivot->accepted,
            ],
            'unread_chats' => $this->user->count_unread_chats,
        ];
    }

    public function broadcastAs(): string
    {
        return 'new-message-on-chat';
    }
}
