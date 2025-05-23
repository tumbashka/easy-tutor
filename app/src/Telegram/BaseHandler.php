<?php

namespace App\src\Telegram;

use App\Models\TelegramReminder;
use Illuminate\Support\Facades\Cache;
use Telegram\Bot\Api;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Chat;
use Telegram\Bot\Objects\Message;
use Telegram\Bot\Objects\User;

abstract class BaseHandler
{
    public function __construct(
        protected Api $telegram,
        protected Chat $chat,
        protected User $from,
        protected Message $message,
    ) {}

    abstract public function process();

    protected function isConfirmedUser(): bool
    {
        $user = \App\Models\User::getUserByTelegramID($this->from->id);
        if ($user) {
            return true;
        }

        return false;
    }

    protected function isPrivate(): bool
    {
        if ($this->chat->type == 'private') {
            return true;
        }

        return false;
    }

    protected function isGroup(): bool
    {
        if ($this->chat->type == 'group') {
            return true;
        }

        return false;
    }

    protected function sendTextMessage($text): void
    {
        $this->telegram->sendMessage([
            'chat_id' => $this->chat->id,
            'text' => $text,
            'parse_mode' => 'Markdown',
        ]);
    }

    protected function deleteMessage(): void
    {
        $this->telegram->deleteMessage([
            'chat_id' => $this->chat->id,
            'message_id' => $this->message->messageId,
        ]);
    }

    protected function sendConfirmedUserError(): void
    {
        $this->sendTextMessage('Данная команда разрешена только для подтверждённого аккаунта преподавателя');
    }

    protected function sendPrivateError(): void
    {
        $this->sendTextMessage('Данная команда разрешена только в личной переписке с ботом');
    }

    protected function sendGroupError(): void
    {
        $this->sendTextMessage('Данная команда разрешена только в групповом чате');
    }

    protected function sendStudentDontConnectError(): void
    {
        $this->sendTextMessage('Ошибка, ученик не подключен к этой группе.');

    }

    protected function sendStartTokenError(): void
    {
        $this->sendTextMessage('Для привязки к аккаунту, отправьте мне токен из настроек профиля в формате `/start <token>`');
    }

    protected function sendGroupSetting(): void
    {
        $telegram_reminder = TelegramReminder::firstWhere('chat_id', $this->chat->id);

        if ($telegram_reminder != null) {
            $disable = [[['text' => '🚫 Выключить напоминания 🔔', 'callback_data' => 'disable_remind']]];
            $enable = [[['text' => '✅ Включить напоминания 🔔', 'callback_data' => 'enable_remind']]];

            $keyboard = [
                [['text' => '🙋🏻‍♀️ Назначить ученика 🙋🏻‍♂️', 'callback_data' => 'set_student_menu']],
                [['text' => '✏️ Изменить интервал напоминания о занятии 🔔', 'callback_data' => 'change_before_lesson_minutes']],
                [['text' => '✏️ Изменить время ежедневного напоминания о ДЗ 📝', 'callback_data' => 'change_homework_reminder_time']],
                [['text' => '❌ Закрыть ❌', 'callback_data' => 'close']],
            ];
            if ($telegram_reminder->is_enabled) {
                $keyboard = array_merge($disable, $keyboard);
            } else {
                $keyboard = array_merge($enable, $keyboard);
            }
        } else {
            $keyboard = [
                [['text' => '🙋🏻‍♀️ Назначить ученика 🙋🏻‍♂️', 'callback_data' => 'set_student_menu']],
                [['text' => '❌ Закрыть ❌', 'callback_data' => 'close']],
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
            $text_body = 'Напоминания: ***выключены***';
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

    protected function sendPrivateSetting(): void {}

    protected function sendKeyboardSetStudent(): void
    {
        if (! $this->isConfirmedUser()) {
            $this->sendConfirmedUserError();
            $this->sendStartTokenError();

            return;
        }
        if (! $this->isGroup()) {
            $this->sendGroupError();

            return;
        }

        $user = \App\Models\User::getUserByTelegramID($this->from->id);
        $students = $user->students;

        $keyboard = [];
        foreach ($students as $student) {
            $keyboard[] = [['text' => $student->name, 'callback_data' => 'set_student '.$student->id]];
        }
        $keyboard[] = [['text' => '❌ Закрыть ❌', 'callback_data' => 'close']];
        Telegram::sendMessage([
            'chat_id' => $this->chat->id,
            'text' => 'Выберите ученика для группы:',
            'reply_markup' => json_encode(['inline_keyboard' => $keyboard]),
        ]);
    }

    protected function sendHomeworkMenu(): void
    {
        if (! $this->isConfirmedUser()) {
            $this->sendConfirmedUserError();
            $this->sendStartTokenError();

            return;
        }
        if (! $this->isGroup()) {
            $this->sendGroupError();

            return;
        }

        $keyboard[] = [['text' => 'Добавить задание ➕', 'callback_data' => 'add_homework']];
        $keyboard[] = [['text' => 'Просмотреть задания 👀', 'callback_data' => 'get_list_homework']];
        $keyboard[] = [['text' => 'Отметить выполнение ✅', 'callback_data' => 'get_complete_homework_menu']];
        $keyboard[] = [['text' => '❌ Закрыть ❌', 'callback_data' => 'close']];
        Telegram::sendMessage([
            'chat_id' => $this->chat->id,
            'text' => 'Домашнее задание:',
            'reply_markup' => json_encode(['inline_keyboard' => $keyboard]),
        ]);

    }

    protected function getTelegramReminder()
    {
        return TelegramReminder::firstWhere('chat_id', $this->chat->id);
    }

    protected function getStudent()
    {
        $reminder = $this->getTelegramReminder();
        if ($reminder) {
            return $reminder->student;
        }

        return null;
    }

    protected function putToCacheData(string $key, mixed $data): void
    {
        Cache::put("telegram_data_{$this->chat->id}_{$key}", $data, now()->addMinutes());
    }

    protected function getCachedData(string $key)
    {
        return Cache::get("telegram_data_{$this->chat->id}_{$key}");
    }

    protected function pullCachedData(string $key)
    {
        return Cache::pull("telegram_data_{$this->chat->id}_{$key}");
    }

    protected function forgetCachedData(string $key): void
    {
        Cache::forget("telegram_data_{$this->chat->id}_{$key}");
    }
}
