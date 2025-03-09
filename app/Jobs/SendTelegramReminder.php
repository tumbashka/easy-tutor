<?php

namespace App\Jobs;

use App\Models\Reminder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Telegram\Bot\Api;

class SendTelegramReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(Api $telegram)
    {
        $reminders = Reminder::where('is_notified', false)->get();

        foreach ($reminders as $reminder) {
            $telegram->sendMessage([
                'chat_id' => $reminder->chat_id,
                'text' => $reminder->text,
                'parse_mode' => 'Markdown',
            ]);

            $reminder->update(['is_notified' => true]);
        }
    }
}
