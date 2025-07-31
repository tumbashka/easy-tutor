<?php

use Illuminate\Support\Facades\Broadcast;


Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
//    Log::info('Authorizing channel user.' . $id, ['user_id' => $user->id]);

    return (int) $user->id === (int) $id;
});
