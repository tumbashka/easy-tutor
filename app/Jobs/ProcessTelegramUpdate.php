<?php

namespace App\Jobs;


use App\Models\User;
use App\src\Telegram\CallbackQueryHandler;
use App\src\Telegram\CommandHandler;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Telegram\Bot\Api;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Message;
use Telegram\Bot\Objects\Update;

class ProcessTelegramUpdate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private Update $update)
    {
    }

    public function handle(Api $telegram): void
    {
        if ($this->update->objectType() === "message") {
            $message = $this->update->message;

            $commandHandler = new CommandHandler($telegram, $message);
            $commandHandler->process();
        }
        if($this->update->objectType() === "callback_query"){
            $callbackQuery = $this->update->callbackQuery;

            $callbackQueryHandler = new CallbackQueryHandler($telegram, $callbackQuery);
            $callbackQueryHandler->process();
        }

    }
}
