<?php

namespace App\Http\Controllers;


use App\Events\NewMessageOnChat;
use App\Http\Requests\Common\StoreMessageRequest;
use App\Http\Resources\MessageResource;
use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use App\Services\ChatService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ChatController extends Controller
{
    public function index(ChatService $service)
    {
        $chats = $service->getUserChats();
        $newChats = $service->getUserNewChats();

        $title = __('Сообщения');

        return view('chat.show', compact('title', 'chats', 'newChats'));
    }

    public function show(Chat $chat, ChatService $service)
    {
        $user = auth()->user();

        $earliestUnreadMessage = $chat->messages()
            ->where('user_id', '!=', $user->id)
            ->whereDoesntHave('reads', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->orderBy('id')
            ->first();

        if ($earliestUnreadMessage) {
            $startId = max(1, $earliestUnreadMessage->id - 10);
            $initialMessages = $chat->messages()
                ->with('reads')
                ->where('id', '>=', $startId)
                ->orderBy('id', 'asc')
                ->take(20)
                ->with('reads')
                ->get();
        } else {
            // If no unread messages, load the latest 20 messages
            $initialMessages = $chat->messages()
                ->with('reads')
                ->orderBy('id', 'desc')
                ->take(20)
                ->with('reads')
                ->get()
                ->sortBy('id');
        }
        $selectedChat = $chat;
        $chats = $service->getUserChats();
        $newChats = $service->getUserNewChats();
        $title = $service->getChatName($chat);

        return view('chat.show', compact('selectedChat', 'initialMessages', 'title', 'chats', 'newChats'));
    }

    public function loadMoreMessages(Chat $chat, Request $request)
    {
        $lastId = $request->input('last_id');
        $direction = $request->input('direction'); // 'older' or 'newer'

        if ($direction === 'older') {
            $messages = $chat->messages()
                ->where('id', '<', $lastId)
                ->orderBy('id', 'asc')
                ->take(20)
                ->with('reads')
                ->get()
                ->reverse();
        } else {
            $messages = $chat->messages()
                ->where('id', '>', $lastId)
                ->orderBy('id', 'asc')
                ->take(20)
                ->with('reads')
                ->get();
        }

        return MessageResource::collection($messages);
    }

    public function findUserChatOrCreate(User $user, ChatService $service)
    {
        if ($res = $service->getPersonalChat($user)) {
            return redirect()->route('chat.show', ['chat' => $res]);
        }
        abort(404);
    }

    public function accept(Chat $chat)
    {
        $user = auth()->user();
        $userChat = $user->chats()->firstWhere('chat_id', $chat->id);
        $userChat->pivot->accepted = true;
        $userChat->pivot->save();

        return back();
    }

    public function storeMessage(StoreMessageRequest $request, Chat $chat)
    {
        $message = $chat->messages()->create([
            'text' => $request->input('text'),
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name,
        ]);

        event(new NewMessageOnChat($message));

        return response()->json([
            'success' => true,
            'id' => $message->id,
            'user_name' => $message->user_name,
            'text' => $message->text,
            'created_at' => $message->created_at->format('H:i'),
        ]);
    }

    public function make_read(Chat $chat, Message $message)
    {
        $user = auth()->user();

        if ($message->user_id === $user->id) {
            return response()->json([
                'success' => false,
            ]);
        }

        $unreadMessages = $chat->messages()
            ->where('user_id', '!=', $user->id)
            ->where('id', '<=', $message->id)
            ->whereDoesntHave('reads', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->get();

        $unreadMessages->each(function ($unreadMessage) use ($user) {
            $unreadMessage->reads()->create([
                'user_id' => $user->id,
            ]);
        });

        event(new \App\Events\MessageRead($chat->id, $message->id, $user));

        return response()->json([
            'success' => true,
        ]);
    }

    public function countUnread()
    {
        $user = auth()->user();
        $count = $user->count_unread_chats;

        return response()->json(compact('count'));
    }

    public function countUnreadMessages(Chat $chat)
    {
        $user = auth()->user();
        $unreadCount = $chat->messages()
            ->where('user_id', '!=', $user->id)
            ->whereDoesntHave('reads', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->count();

        \Log::info("Unread count for chat {$chat->id} and user {$user->id}: {$unreadCount}");

        return response()->json(['unread_count' => $unreadCount]);
    }

    public function getMessageReads(Chat $chat, Message $message)
    {
        if ($message->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $reads = $message->reads()->with('user')->get()->map(function ($read) {
            return [
                'user_name' => $read->user->name,
                'read_at' => $read->created_at->format('Y-m-d H:i:s'),
            ];
        });

        return response()->json($reads);
    }

}
