<?php

use Illuminate\Support\Facades\Broadcast;


Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int)$user->id === (int)$id;
});

//Broadcast::channel('Chat.{id}', function ($user, $chatId) {
//    $chat = $user->chats()->where('chat_id', $chatId)->first();
//
//    return (bool)$chat;
//});
