<?php

namespace App\src\Telegram;

use App\Models\Student;
use App\Models\User;
use Telegram\Bot\Api;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Message;

class CommandHandler
{
    private Api $telegram;
    private Message $message;
    private int $chat_id;
    private string|null $text;
    private string|null $command;
    private string|null $param;

    public function __construct(Api $telegram, Message $message)
    {
        $this->telegram = $telegram;
        $this->message = $message;
        $this->chat_id = $message->chat->id;
        $this->text = $this->message->text;

        $string_arr = explode(' ', $this->text, 2);
        $this->command = $string_arr[0] ?? null;
        $this->param = $string_arr[1] ?? null;
    }

    public function process(): void
    {
        switch ($this->command) {
            case '/start':
                $this->handleStart();
                break;
            case '/set_student':
                $this->handleSetStudent();
                break;
            default:
                $this->send_message("Команда {$this->command} не найдена");
        }
    }

    private function handleStart(): void
    {
        if (!$this->is_private()) {
            $this->send_message('Данная команда разрешена только в личной переписке с ботом');
            return;
        }
        if (!$this->param) {
            $this->send_message('Для привязки к аккаунту, отправьте токен в формате `/start <token>`');
            return;
        }
        if ($user = User::firstWhere('telegram_token', $this->param)) {
            $user->telegram_chat_id = $this->chat_id;
            $user->telegram_username = $this->message->chat->username;
            $user->update();
            $this->send_message("Телеграмм аккаунт: ***{$user->telegram_username}*** успешно привязан к аккаунту: ***{$user->name}***");
        } else {
            $this->send_message("Токен не действителен");
        }
    }

    private function handleSetStudent(): void
    {
        if (!$this->is_confirmed_user()) {
            $this->send_message('Данная команда разрешена только для подтверждённого аккаунта преподавателя');
            return;
        }
        if (!$this->is_group()) {
            $this->send_message('Данная команда разрешена только в групповом чате');
            return;
        }

        $user = User::getUserByTelegramChatID($this->message->from->id);
        $students = $user->students;
//        dump($students);
        $keyboard = [];
        foreach ($students as $student) {
            $keyboard[] = [['text' => $student->name, 'callback_data' => 'set_student_' . $student->id]];
        }

        Telegram::sendMessage([
            'chat_id' => $this->chat_id,
            'text' => 'Выберите ученика для группы:',
            'reply_markup' => json_encode(['inline_keyboard' => $keyboard])
        ]);

        echo 'handleSetStudent';
    }

    private function is_confirmed_user(): bool
    {
        $user = User::firstWhere('telegram_chat_id', $this->message->from->id);
        if ($user) {
            return true;
        }
        return false;
    }

    private function is_private(): bool
    {
        if ($this->message->chat->type == 'private') {
            return true;
        }
        return false;
    }

    private function is_group(): bool
    {
        if ($this->message->chat->type == 'group') {
            return true;
        }
        return false;
    }

    private function send_message($text): void
    {
        $this->telegram->sendMessage([
            'chat_id' => $this->chat_id,
            'text' => $text,
            'parse_mode' => 'Markdown',
        ]);
    }

}
