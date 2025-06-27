<?php

namespace App\src\Telegram;

use App\Models\TelegramReminder;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Api;
use Telegram\Bot\Objects\Chat;
use Telegram\Bot\Objects\Message;
use Telegram\Bot\Objects\User;
use App\Models\User as UserModel;

abstract class BaseHandler
{
    public function __construct(
        protected Api $telegram,
        protected Chat $chat,
        protected User $from,
        protected Message $message,
    ) {
    }

    abstract public function process();

    abstract protected function handleUnknownCommand();

    protected function isConfirmedUser(): bool
    {
        $user = UserModel::getUserByTelegramID($this->from->id);
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
        $this->sendMessage([
            'chat_id' => $this->chat->id,
            'text' => $text,
            'parse_mode' => 'Markdown',
        ]);
    }

    protected function sendMessage(array $params): void
    {
        try {
            $this->telegram->sendMessage($params);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            echo $exception->getMessage();
        }
    }protected function editMessageText(array $params): void
    {
        try {
            $this->telegram->editMessageText($params);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            echo $exception->getMessage();
        }
    }

    protected function deleteMessage(): bool
    {
        if ($this->message->from->isBot) {
            try {
                return $this->telegram->deleteMessage([
                    'chat_id' => $this->chat->id,
                    'message_id' => $this->message->messageId,
                ]);
            } catch (Exception $e) {
                $this->sendTextMessage($e->getMessage());
                Log::error($e->getMessage());
            }
        }
        return false;
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
        $this->sendTextMessage(
            'Для привязки к аккаунту, отправьте мне токен из настроек профиля в формате `/start <token>`'
        );
    }

    protected function sendGroupSetting(): void
    {
        $telegram_reminder = $this->getTelegramReminder();

        if ($telegram_reminder != null) {
            $disable = [[['text' => '🚫 Выключить напоминания 🔔', 'callback_data' => 'disable_remind']]];
            $enable = [[['text' => '✅ Включить напоминания 🔔', 'callback_data' => 'enable_remind']]];

            $keyboard = [
                [['text' => '🙋🏻‍♀️ Назначить ученика 🙋🏻‍♂️', 'callback_data' => 'sendKeyboardSetStudent']],
                [
                    [
                        'text' => '✏️ Изменить интервал напоминания о занятии 🔔',
                        'callback_data' => 'change_before_lesson_minutes'
                    ]
                ],
                [
                    [
                        'text' => '✏️ Изменить время ежедневного напоминания о ДЗ 📝',
                        'callback_data' => 'change_homework_reminder_time'
                    ]
                ],
                [
                    [
                        ['text' => '◀ Назад ◀', 'callback_data' => "handleMenu"],
                        ['text' => '❌ Закрыть ❌', 'callback_data' => 'close']
                    ]
                ],
            ];
            if ($telegram_reminder->is_enabled) {
                $keyboard = array_merge($disable, $keyboard);
            } else {
                $keyboard = array_merge($enable, $keyboard);
            }
        } else {
            $keyboard = [
                [['text' => '🙋🏻‍♀️ Назначить ученика 🙋🏻‍♂️', 'callback_data' => 'sendKeyboardSetStudent']],
                [
                    ['text' => '◀ Назад ◀', 'callback_data' => "handleMenu"],
                    ['text' => '❌ Закрыть ❌', 'callback_data' => 'close']
                ],
            ];

            $this->deleteMessage();

            $this->sendMessage([
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
        $this->deleteMessage();

        $this->sendMessage([
            'chat_id' => $this->chat->id,
            'text' => $text,
            'parse_mode' => 'Markdown',
            'reply_markup' => json_encode(['inline_keyboard' => $keyboard]),
        ]);
    }

    protected function sendKeyboardSetStudent(): void
    {
        if (!$this->isConfirmedUser()) {
            $this->sendConfirmedUserError();
            $this->sendStartTokenError();

            return;
        }
        if (!$this->isGroup()) {
            $this->sendGroupError();

            return;
        }

        $user = UserModel::getUserByTelegramID($this->from->id);
        $students = $user->students->sortBy('name');

        $keyboard = [];
        foreach ($students as $student) {
            $keyboard[] = [['text' => $student->name, 'callback_data' => 'set_student ' . $student->id]];
        }
        $keyboard[] = [
            ['text' => '◀ Назад ◀', 'callback_data' => "handleMenu"],
            ['text' => '❌ Закрыть ❌', 'callback_data' => 'close']
        ];

        $this->editMessageText([
            'chat_id' => $this->chat->id,
            'message_id' => $this->message->messageId,
            'text' => 'Выберите ученика для группы:',
            'reply_markup' => json_encode(['inline_keyboard' => $keyboard]),
        ]);
    }

    protected function sendHomeworkMenu(): void
    {
        if (!$this->isConfirmedUser()) {
            $this->sendConfirmedUserError();
            $this->sendStartTokenError();

            return;
        }
        if (!$this->isGroup()) {
            $this->sendGroupError();

            return;
        }
        $keyboard[] = [['text' => 'Добавить задание ➕', 'callback_data' => 'add_homework']];
        $keyboard[] = [['text' => 'Просмотреть задания 👀', 'callback_data' => 'get_list_homework']];
        $keyboard[] = [['text' => 'Отметить выполнение ✅', 'callback_data' => 'get_complete_homework_menu']];
        $keyboard[] = [
            ['text' => '◀ Назад ◀', 'callback_data' => "handleMenu"],
            ['text' => '❌ Закрыть ❌', 'callback_data' => 'close']
        ];
        $this->deleteMessage();

        $this->sendMessage([
            'chat_id' => $this->chat->id,
            'text' => 'Домашнее задание:',
            'reply_markup' => json_encode(['inline_keyboard' => $keyboard]),
        ]);
    }

    protected function getTelegramReminder(): ?TelegramReminder
    {
        return TelegramReminder::firstWhere('chat_id', $this->chat->id);
    }

    protected function getStudent()
    {
        $reminder = $this->getTelegramReminder();

        return $reminder->student ?? null;
    }

    protected function putToCacheData(string $key, mixed $data): void
    {
        Cache::put("telegram_data_{$this->chat->id}_{$key}", $data, now()->addMinutes(5));
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

    protected function handleLessonsSchedule(Carbon|string|null $date = null): void
    {
        $date = is_null($date) ? Carbon::today() : Carbon::parse($date);

        $dayOfWeek = getDayName($date);
        $formattedDate = $date->format('d.m.Y');

        $message = "📅 *Расписание на ";

        $message .= $date->isToday() ? "Сегодня" : $formattedDate;
        $message .= " {$dayOfWeek}";
        $message .= "*\n\n";

        $user = UserModel::getUserByTelegramID($this->from->id);
        $lessons = $user->getLessonsOnDate($date);

        if ($lessons->isEmpty()) {
            $message .= "😔 Занятий нет.";
        } else {
            $message .= "🕒 *Занятия:*\n";
            foreach ($lessons as $key => $lesson) {
                $startTime = $lesson->start->format('H:i');
                $endTime = $lesson->end->format('H:i');
                $paymentStatus = $lesson->is_paid ? '✅ Оплачено ✅' : '❌ Не оплачено ❌';

                $message .= sprintf(
                    "%d. *%s*\n⏰ %s–%s\n%s\n\n",
                    $key + 1,
                    $lesson->student_name,
                    $startTime,
                    $endTime,
                    $paymentStatus
                );
            }
        }
        $paginationDatesStep = 3;
        $dateStart = $date->copy()->subDays($paginationDatesStep);

        $keyboard = [];
        $row = [];
        for ($i = 0; $i <= $paginationDatesStep * 2; $i++) {
            $row[] = [
                'text' => $dateStart->format('d.m'),
                'callback_data' => "handleLessonsSchedule {$dateStart}"
            ];
            $dateStart->addDay();
        }
        $keyboard[] = $row;
        if ($lessons->isNotEmpty()) {
            $keyboard[] = [['text' => 'Отметить оплату', 'callback_data' => "getPaymentMenu {$date}"]];
        }
        $keyboard[] = [
            ['text' => '◀ Назад ◀', 'callback_data' => "handleMenu"],
            ['text' => '❌ Закрыть ❌', 'callback_data' => 'close']
        ];


        $this->deleteMessage();

        $this->sendMessage([
            'chat_id' => $this->chat->id,
            'text' => $message,
            'parse_mode' => 'Markdown',
            'reply_markup' => json_encode(['inline_keyboard' => $keyboard]),
        ]);
    }

    protected function handleMenu(): void
    {
        $keyboard = [];

        $student = $this->getStudent();

        $keyboard[] = [['text' => '🙋🏻‍♀️ Назначить ученика 🙋🏻‍♂️', 'callback_data' => "sendKeyboardSetStudent"]];
        if ($student) {
            $keyboard[] = [['text' => '📝 Домашнее задание 📝', 'callback_data' => "sendHomeworkMenu"]];
        }
        $keyboard[] = [['text' => '🕒 Расписание 🕒', 'callback_data' => "handleLessonsSchedule"]];
        $keyboard[] = [['text' => '🛠️ Настройки 🛠️', 'callback_data' => "sendGroupSetting"]];
        $keyboard[] = [['text' => '❌ Закрыть ❌', 'callback_data' => 'close']];


        if ($this->from->isBot) {
            $this->editMessageText([
                'chat_id' => $this->chat->id,
                'message_id' => $this->message->messageId,
                'text' => '*Меню:*',
                'parse_mode' => 'Markdown',
                'reply_markup' => json_encode(['inline_keyboard' => $keyboard]),
            ]);
        } else {
            $this->deleteMessage();
            $this->sendMessage([
                'chat_id' => $this->chat->id,
                'text' => '*Меню:*',
                'parse_mode' => 'Markdown',
                'reply_markup' => json_encode(['inline_keyboard' => $keyboard]),
            ]);
        }
    }
}
