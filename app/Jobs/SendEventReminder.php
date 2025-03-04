<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Telegram\Bot\Api;

class SendEventReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        $telegram = new Api(config('telegram.bots.mybot.token'));

        $now = Carbon::now();
        $oneHourLater = $now->copy()->addHour();

//        $events = Event::whereBetween('event_time', [$now, $oneHourLater])
//            ->where('is_notified', false)
//            ->get();
//
//        foreach ($events as $event) {
//            $telegram->sendMessage([
//                'chat_id' => $event->telegram_user_id,
//                'text' => "Напоминание: Через час начнется событие \"$event->title\" в $event->event_time!",
//            ]);

//        $event->update(['is_notified' => true]);
//    }
    }
}
