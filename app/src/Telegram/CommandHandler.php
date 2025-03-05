<?php

namespace App\src\Telegram;

use App\Models\Student;
use App\Models\TelegramReminder;
use App\Models\User;
use Telegram\Bot\Api;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Message;

class CommandHandler extends BaseHandler
{
    private Message $message;
    private string|null $text;
    private string|null $command;
    private string|null $param;

    public function __construct(Api $telegram, Message $message)
    {
        parent::__construct($telegram, $message->chat, $message->from);

        $this->message = $message;
        $this->text = $message->text;

        $string_arr = explode(' ', $this->text, 2);
        $this->command = $string_arr[0] ?? null;
        $this->param = $string_arr[1] ?? null;
    }

    public function process(): void
    {
        switch ($this->command) {
            case '/start':
            case '/start@easy_tutor_helper_bot':
                $this->handleStart();
                break;
            case '/set_student':
            case '/set_student@easy_tutor_helper_bot':
                $this->sendKeyboardSetStudent();
                break;
            case '/settings':
            case '/settings@easy_tutor_helper_bot':
                $this->handleSettings();
                break;
            default:
        }
    }

    private function handleStart(): void
    {
        echo 'handleStart';
        if (!$this->is_private()) {
            $this->send_private_error();
            return;
        }
        if (!$this->param) {
            $this->send_message('Для привязки к аккаунту, отправьте токен в формате `/start <token>`');
            return;
        }

        if ($user = User::firstWhere('telegram_token', $this->param)) {
            $user->telegram_id = $this->from->id;
            $user->telegram_username = $this->from->username;
            $user->update();
            $this->send_message("Телеграмм аккаунт: ***{$user->telegram_username}*** успешно привязан к аккаунту: ***{$user->name}***");
        } else {
            $this->send_message("Токен не действителен");
        }
    }



    private function handleSettings(): void
    {
        if (!$this->is_confirmed_user()) {
            $this->send_confirmed_user_error();
            return;
        }
        if ($this->is_group()) {
            $this->sendGroupSetting();
            return;
        }
    }


}
