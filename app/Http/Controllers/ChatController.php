<?php

namespace App\Http\Controllers;


use App\Http\Requests\Common\StoreMessageRequest;
use App\Models\Chat;
use App\Models\User;
use App\Services\ChatService;
use Illuminate\Http\Request;

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
        $selectedChat = $chat->load(['users', 'messages.reads']);
        $chats = $service->getUserChats();
        $newChats = $service->getUserNewChats();

        $title = $service->getChatName($chat);

        return view('chat.show', compact('selectedChat', 'title', 'chats', 'newChats'));
    }

    public function findUserChatOrCreate(User $user, ChatService $service)
    {
        if ($res = $service->getPersonalChat($user)){
            return redirect()->route('chat.show', ['chat' => $res]);
        }
        abort(404);
    }

    public function store_message(StoreMessageRequest $request, Chat $chat)
    {
        $chat->messages()->create([
            'text' => $request->input('text'),
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name,
        ]);

        return back();
    }

    public function accept(Chat $chat)
    {
        $user = auth()->user();
        $userChat = $user->chats()->firstWhere('chat_id', $chat->id);
        $userChat->pivot->accepted = true;
        $userChat->pivot->save();

        return back();
    }
}
