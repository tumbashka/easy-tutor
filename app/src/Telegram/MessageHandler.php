<?php

namespace App\src\Telegram;

use App\Models\Homework;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Telegram\Bot\Api;
use Telegram\Bot\Objects\Message;

class MessageHandler extends BaseHandler
{
    private ?string $text;

    private ?string $command;

    private ?string $param;

    public function __construct(Api $telegram, Message $message)
    {
        parent::__construct($telegram, $message->chat, $message->from, $message);

        $this->message = $message;
        $this->text = $message->text;

        $this->text = Str::remove('@'.config('telegram.bots.mybot.username'), $this->text);
        $string_arr = explode(' ', $this->text, 2);

        $this->command = $string_arr[0] ?? null;
        $this->param = $string_arr[1] ?? null;
    }

    #[\Override] public function process(): void
    {
        if ($this->message->getReplyToMessage() && Cache::get("awaiting_homework_description_{$this->chat->id}")) {
            $this->createHomework();
        } else {
            switch ($this->command) {
                case '/start':
                    $this->handleStart();
                    break;
                case '/menu':
                    $this->sendMenu();
                    break;
                default:
                    $this->handleUnknownCommand();
            }
        }
    }
    #[\Override] protected function handleUnknownCommand(): void
    {
        Log::debug("Команда: {$this->command} не существует.");
        $this->sendTextMessage("Команда: {$this->command} не существует.");
    }

    private function handleStart(): void
    {
        if (!$this->isPrivate()) {
            $this->sendPrivateError();

            return;
        }
        if (!$this->param) {
            $this->sendStartTokenError();

            return;
        }

        if ($user = User::firstWhere('telegram_token', $this->param)) {
            $user->telegram_id = $this->from->id;
            $user->telegram_username = $this->from->username;
            $user->update();
            $this->sendTextMessage(
                "Телеграмм аккаунт: ***{$user->telegram_username}*** успешно привязан к аккаунту сервиса"
                . config('app.name')
                . ": ***{$user->name}***"
            );
        } else {
            $this->sendTextMessage('Токен не действителен');
        }
    }

    private function createHomework(): void
    {
        if (!$this->isConfirmedUser()) {
            $this->sendConfirmedUserError();

            return;
        }
        if (!$this->isGroup()) {
            $this->sendGroupError();

            return;
        }
        if (strlen($this->text) > 250) {
            $this->sendTextMessage('Описание не должно превышать 250 символов');
            $this->sendMessage([
                'chat_id' => $this->chat->id,
                'text' => 'Пожалуйста, введите краткое описание домашнего задания:',
                'reply_markup' => json_encode(['force_reply' => true]),
            ]);

            return;
        }

        $telegram_reminder = $this->getTelegramReminder();
        $student = $telegram_reminder->student;

        Homework::create([
            'student_id' => $student->id,
            'description' => $this->text,
        ]);
        Cache::forget("awaiting_homework_description_{$this->chat->id}");

        $this->sendTextMessage("Домашнее задание \"{$this->text}\" успешно добавлено!");
        $this->sendHomeworkMenu();
    }
}
