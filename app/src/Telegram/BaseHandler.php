<?php

namespace App\src\Telegram;

use App\Models\Homework;
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

    public function escapingSymbols(string $string, array $symbols = []): string
    {
        if(empty($symbols)) {
            $symbols = explode(' ', '_ [ ] ( ) ` > # + - = | { } . !');
        }
        dump($symbols);
        foreach ($symbols as $symbol) {
            $string = str_replace($symbol, "\\{$symbol}", $string);
        }
        return $string;
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
    }

    protected function editMessageText(array $params): void
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
            $disable = [[['text' => '🚫 Выключить напоминания 🔔', 'callback_data' => 'reminderChangeStatus']]];
            $enable = [[['text' => '✅ Включить напоминания 🔔', 'callback_data' => 'reminderChangeStatus']]];

            $keyboard = [
                [['text' => '🙋🏻‍♀️ Назначить ученика 🙋🏻‍♂️', 'callback_data' => 'sendKeyboardSetStudent']],
                [
                    [
                        'text' => '✏️ Изменить интервал напоминания о занятии 🔔',
                        'callback_data' => 'beforeLessonMinutesKeyboard'
                    ]
                ],
                [
                    [
                        'text' => '✏️ Изменить время ежедневного напоминания о ДЗ 📝',
                        'callback_data' => 'homeworkReminderTimeKeyboard'
                    ]
                ],
                [
                    ['text' => '◀ Назад ◀', 'callback_data' => "sendMenu"],
                    ['text' => '❌ Закрыть ❌', 'callback_data' => 'close']
                ],
            ];

            if ($telegram_reminder->is_enabled) {
                $keyboard = array_merge($disable, $keyboard);

                $text_body = <<<EOD
                Напоминания: ***включены***
                Напоминание перед занятием за ***{$telegram_reminder->before_lesson_minutes} мин.***
                Ежедневное напоминание о ДЗ в ***{$telegram_reminder->homework_reminder_time}***
                EOD;
            } else {
                $keyboard = array_merge($enable, $keyboard);

                $text_body = 'Напоминания: ***выключены***';
            }
            $text = <<<EOD
            Настройки группы:
            Группе назначен ученик: ***{$telegram_reminder->student->name}***.
            {$text_body}
            EOD;

        } else {
            $keyboard = [
                [['text' => '🙋🏻‍♀️ Назначить ученика 🙋🏻‍♂️', 'callback_data' => 'sendKeyboardSetStudent']],
                [
                    ['text' => '◀ Назад ◀', 'callback_data' => "sendMenu"],
                    ['text' => '❌ Закрыть ❌', 'callback_data' => 'close']
                ],
            ];

            $this->editMessageText([
                'chat_id' => $this->chat->id,
                'message_id' => $this->message->messageId,
                'text' => 'Ученик для группы не назначен',
                'parse_mode' => 'Markdown',
                'reply_markup' => json_encode(['inline_keyboard' => $keyboard]),
            ]);
        }

        $this->editMessageText([
            'chat_id' => $this->chat->id,
            'message_id' => $this->message->messageId,
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
            $keyboard[] = [['text' => $student->name, 'callback_data' => 'setStudent ' . $student->id]];
        }
        $keyboard[] = [
            ['text' => '◀ Назад ◀', 'callback_data' => "sendMenu"],
            ['text' => '❌ Закрыть ❌', 'callback_data' => 'close']
        ];

        $this->editMessageText([
            'chat_id' => $this->chat->id,
            'message_id' => $this->message->messageId,
            'text' => 'Выберите ученика для группы:',
            'reply_markup' => json_encode(['inline_keyboard' => $keyboard]),
        ]);
    }

    protected function sendHomeworkMenu($message = null): void
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
        $homeworksCount = Homework::where('student_id', $this->getStudent()->id)->count();

        $keyboard[] = [['text' => 'Добавить задание ➕', 'callback_data' => 'addHomework']];
        if ($homeworksCount){
            $keyboard[] = [['text' => 'Просмотреть задания 👀', 'callback_data' => 'getListHomework']];
            $keyboard[] = [['text' => 'Отметить выполнение ✅', 'callback_data' => 'completeHomeworkMenu']];
        }
        $keyboard[] = [
            ['text' => '◀ Назад ◀', 'callback_data' => "sendMenu"],
            ['text' => '❌ Закрыть ❌', 'callback_data' => 'close']
        ];

        if ($this->message->from->isBot) {
            $this->editMessageText([
                'chat_id' => $this->chat->id,
                'message_id' => $this->message->messageId,
                'text' => $message ?? 'Домашнее задание:',
                'parse_mode' => 'Markdown',
                'reply_markup' => json_encode(['inline_keyboard' => $keyboard]),
            ]);
        } else {
            $this->deleteMessage();
            $this->sendMessage([
                'chat_id' => $this->chat->id,
                'text' => $message ?? 'Домашнее задание:',
                'parse_mode' => 'Markdown',
                'reply_markup' => json_encode(['inline_keyboard' => $keyboard]),
            ]);
        }
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

    protected function lessonsSchedule(Carbon|string|null $date = null): void
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
                $paymentStatus = $lesson->is_paid ? '✅ Оплачено' : '❌ Не оплачено';

                $studentName = $lesson->is_canceled ? "~~~{$lesson->student_name}~~~ (Отменён)" : $lesson->student_name;
                $message .= sprintf(
                    "%d. *%s*\n⏰ %s–%s\n%s\n\n",
                    $key + 1,
                    $studentName,
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
                'callback_data' => "lessonsSchedule {$dateStart}"
            ];
            $dateStart->addDay();
        }
        $keyboard[] = $row;
        if ($lessons->isNotEmpty()) {
            $keyboard[] = [
                ['text' => 'Оплата', 'callback_data' => "paymentMenu {$date}"],
                ['text' => 'Отмена', 'callback_data' => "cancelMenu {$date}"]
            ];
        }
        $keyboard[] = [
            ['text' => '◀ Назад ◀', 'callback_data' => "sendMenu"],
            ['text' => '❌ Закрыть ❌', 'callback_data' => 'close']
        ];

        $message = $this->escapingSymbols($message);
        $this->editMessageText([
            'chat_id' => $this->chat->id,
            'message_id' => $this->message->messageId,
            'text' => $message,
            'parse_mode' => 'MarkdownV2',
            'reply_markup' => json_encode(['inline_keyboard' => $keyboard]),
        ]);
    }

    protected function sendMenu(): void
    {
        $keyboard = [];

        $student = $this->getStudent();

        $keyboard[] = [['text' => '🕒 Расписание 🕒', 'callback_data' => "lessonsSchedule"]];
        if ($student) {
            $keyboard[] = [['text' => '📝 Домашнее задание 📝', 'callback_data' => "sendHomeworkMenu"]];
            $keyboard[] = [['text' => '🛠️ Настройки 🛠️', 'callback_data' => "sendGroupSetting"]];
        } else{
            $keyboard[] = [['text' => '🙋🏻‍♀️ Назначить ученика 🙋🏻‍♂️', 'callback_data' => "sendKeyboardSetStudent"]];
        }
        $keyboard[] = [['text' => '❌ Закрыть ❌', 'callback_data' => 'close']];

        if ($this->message->from->isBot) {
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
