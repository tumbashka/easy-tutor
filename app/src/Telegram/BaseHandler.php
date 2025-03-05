<?php

namespace App\src\Telegram;


use App\Models\TelegramReminder;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\User;
use Telegram\Bot\Api;
use Telegram\Bot\Objects\Chat;

abstract class BaseHandler
{
    public function __construct(
        protected Api  $telegram,
        protected Chat $chat,
        protected User $from,
    )
    {

    }

    abstract function process();

    protected function is_confirmed_user(): bool
    {
        $user = \App\Models\User::getUserByTelegramID($this->from->id);
        if ($user) {
            return true;
        }
        return false;
    }

    protected function is_private(): bool
    {
        if ($this->chat->type == 'private') {
            return true;
        }
        return false;
    }

    protected function is_group(): bool
    {
        if ($this->chat->type == 'group') {
            return true;
        }
        return false;
    }

    protected function send_message($text): void
    {
        $this->telegram->sendMessage([
            'chat_id' => $this->chat->id,
            'text' => $text,
            'parse_mode' => 'Markdown',
        ]);
    }
    protected function send_confirmed_user_error(): void
    {
        $this->send_message('Данная команда разрешена только для подтверждённого аккаунта преподавателя');
    }
    protected function send_private_error(): void
    {
        $this->send_message('Данная команда разрешена только в личной переписке с ботом');
    }
    protected function send_group_error(): void
    {
        $this->send_message('Данная команда разрешена только в групповом чате');
    }

    protected function sendGroupSetting(): void
    {
        $telegram_reminder = TelegramReminder::firstWhere('chat_id', $this->chat->id);

        if ($telegram_reminder != null) {
            $disable = [[['text' => '🚫 Выключить напоминания 🔔', 'callback_data' => 'disable_remind']]];
            $enable = [[['text' => '✅ Включить напоминания 🔔', 'callback_data' => 'enable_remind']]];

            $keyboard = [
                [['text' => '🙋🏻‍♀️ Назначить ученика 🙋🏻‍♂️', 'callback_data' => 'set_student_menu'],],
                [['text' => '✏️ Изменить интервал напоминания о занятии 🔔', 'callback_data' => 'change_before_lesson_minutes'],],
                [['text' => '✏️ Изменить время ежедневного напоминания о ДЗ 📝', 'callback_data' => 'change_homework_reminder_time'],],
                [['text' => '❌ Закрыть ❌', 'callback_data' => 'close'],],
            ];
            if ($telegram_reminder->is_enabled) {
                $keyboard = array_merge($disable, $keyboard);
            } else {
                $keyboard = array_merge($enable, $keyboard);
            }
        } else {
            $keyboard = [
                [['text' => '🙋🏻‍♀️ Назначить ученика 🙋🏻‍♂️', 'callback_data' => 'set_student_menu'],],
                [['text' => '❌ Закрыть ❌', 'callback_data' => 'close'],],
            ];

            Telegram::sendMessage([
                'chat_id' => $this->chat->id,
                'text' => 'Ученик для группы не назначен',
                'parse_mode' => 'Markdown',
                'reply_markup' => json_encode(['inline_keyboard' => $keyboard]),
            ]);
            return;
        }

        if ($telegram_reminder->is_enabled) {
            $text_body = <<<EOD
            Напоминания: ***включены***
            Напоминание перед занятием за ***{$telegram_reminder->before_lesson_minutes} мин.***
            Ежедневное напоминание о ДЗ в ***{$telegram_reminder->homework_reminder_time}***
            EOD;
        } else {
            $text_body = "Напоминания: ***выключены***";
        }
        $text = <<<EOD
            Настройки группы:
            Группе назначен ученик: ***{$telegram_reminder->student->name}***.
            {$text_body}
            EOD;

        Telegram::sendMessage([
            'chat_id' => $this->chat->id,
            'text' => $text,
            'parse_mode' => 'Markdown',
            'reply_markup' => json_encode(['inline_keyboard' => $keyboard]),
        ]);
    }

    protected function sendKeyboardSetStudent(): void
    {
        if (!$this->is_confirmed_user()) {
            $this->send_confirmed_user_error();
            return;
        }
        if (!$this->is_group()) {
            $this->send_group_error();
            return;
        }

        $user = \App\Models\User::getUserByTelegramID($this->from->id);
        $students = $user->students;

        $keyboard = [];
        foreach ($students as $student) {
            $keyboard[] = [['text' => $student->name, 'callback_data' => 'set_student ' . $student->id]];
        }
        $keyboard[] = [['text' => '❌ Закрыть ❌', 'callback_data' => 'close']];
        Telegram::sendMessage([
            'chat_id' => $this->chat->id,
            'text' => 'Выберите ученика для группы:',
            'reply_markup' => json_encode(['inline_keyboard' => $keyboard])
        ]);
    }

}
