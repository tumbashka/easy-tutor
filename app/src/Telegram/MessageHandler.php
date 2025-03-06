<?php

namespace App\src\Telegram;

use App\Models\Homework;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Telegram\Bot\Api;
use Telegram\Bot\Objects\Message;

class MessageHandler extends BaseHandler
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
        if ($this->message->getReplyToMessage() && Cache::get("awaiting_homework_description_{$this->chat->id}")) {
            $this->createHomework();
            Cache::forget("awaiting_homework_description_{$this->chat->id}");
        } else {
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
                case '/homework':
                case '/homework@easy_tutor_helper_bot':
                    $this->handleHomework();
                    break;
                default:
            }
        }
    }

    private function handleStart(): void
    {
        if (!$this->is_private()) {
            $this->send_private_error();
            return;
        }
        if (!$this->param) {
            $this->send_start_token_error();
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
            $this->send_start_token_error();
            return;
        }
        if ($this->is_group()) {
            $this->sendGroupSetting();
            return;
        }
        $this->send_group_error();

    }

    private function handleHomework(): void
    {
        if (!$this->is_confirmed_user()) {
            $this->send_confirmed_user_error();
            $this->send_start_token_error();
            return;
        }
        if (!$this->is_group()) {
            $this->send_group_error();
            return;
        }
        if (!$this->getTelegramReminder()) {
            $this->send_student_dont_connect_error();
            return;
        }


        $this->sendHomeworkMenu();

    }

    private function createHomework(): void
    {
        if (!$this->is_confirmed_user()) {
            $this->send_confirmed_user_error();
            return;
        }
        if (!$this->is_group()) {
            $this->send_group_error();
            return;
        }
        if (strlen($this->text) > 250) {
            $this->send_message('Описание не должно превышать 250 символов');
            $response = $this->telegram->sendMessage([
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
        $this->send_message("Домашнее задание \"{$this->text}\" успешно добавлено!");
        $this->sendHomeworkMenu();
    }


}
