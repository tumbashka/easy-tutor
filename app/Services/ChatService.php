<?php

namespace App\Services;

use App\Enums\ChatType;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class ChatService
{
    public function __construct(
        public ?User $user = null,
    ) {
        if (is_null($this->user)) {
            $this->user = auth()->user();
        }
    }

    public function getPersonalChat(User $anotherUser): false|Chat
    {
        $chat = Chat::where('type', ChatType::Personal)
            ->whereHas('users', fn($query) => $query->where('user_id', $this->user->id))
            ->whereHas('users', fn($query) => $query->where('user_id', $anotherUser->id))
            ->first();

        if ($chat) {
            return $chat;
        }

        $chat = $this->createPersonalChat($anotherUser);
        if ($chat) {
            return $chat;
        } else {
            return false;
        }
    }

    public function getUserChats()
    {
        return $this->user->chats()
            ->with(['users', 'lastMessage', 'lastMessage.reads'])
            ->where('chat_user.accepted', true)
            ->withMax('messages as last_message_created_at', 'created_at')
            ->orderByDesc('last_message_created_at')
            ->paginate();
    }

    public function getUserNewChats()
    {
        return $this->user->chats()
            ->with(['users', 'lastMessage', 'lastMessage.reads'])
            ->where('chat_user.accepted', false)
            ->withMax('messages as last_message_created_at', 'created_at')
            ->orderByDesc('last_message_created_at')
            ->paginate();
    }

    private function createPersonalChat(User $anotherUser): Chat|false
    {
        $chat = null;
        try {
            DB::transaction(function () use (&$chat, $anotherUser) {
                $chat = Chat::create([
                    'type' => ChatType::Personal,
                ]);

                $chat->users()->attach([
                    $anotherUser->id => ['accepted' => false, 'user_name' => $anotherUser->name],
                    $this->user->id => ['accepted' => true, 'user_name' => $this->user->name],
                ]);
            });
        } catch (Throwable $e) {
            Log::error(
                'Ошибка при создании чата',
                [
                    'user_id' => $this->user->id,
                    'another_user_id' => $anotherUser->id,
                    'message' => $e->getMessage(),
                ]
            );

            return false;
        }

        return $chat;
    }

    public function getChatName(Chat $chat)
    {
        return match ($chat->type) {
            ChatType::Personal => $this->user->name,
            ChatType::Group => $chat->name,
            default => __('Новый чат'),
        };
    }

}
