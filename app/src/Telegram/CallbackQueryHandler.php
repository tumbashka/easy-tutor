<?php

namespace App\src\Telegram;

use App\Models\Homework;
use App\Models\Lesson;
use App\Models\TelegramReminder;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Telegram\Bot\Api;
use App\Models\User as UserModel;
use Telegram\Bot\Objects\CallbackQuery;

class CallbackQueryHandler extends BaseHandler
{
    private CallbackQuery $callbackQuery;

    private ?string $command;

    private ?string $param;

    public function __construct(Api $telegram, CallbackQuery $callbackQuery)
    {
        parent::__construct($telegram, $callbackQuery->message->chat, $callbackQuery->from, $callbackQuery->message);

        $this->callbackQuery = $callbackQuery;

        $arr_string = explode(' ', $callbackQuery->data, 2);

        $this->command = $arr_string[0] ?? null;
        $this->param = $arr_string[1] ?? null;
    }

    private const array AVAILABLE_HANDLERS = [
        'setStudent',
        'close',
        'reminderChangeStatus',
        'beforeLessonMinutesKeyboard',
        'setBeforeLessonMinutes',
        'homeworkReminderTimeKeyboard',
        'setHomeworkReminderTime',
        'addHomework',
        'getListHomework',
        'completeHomeworkMenu',
        'changeHomeworkStatus',
        'allCompleteHomework',
        'scheduleToday',
        'scheduleAnotherDay',
        'lessonsSchedule',
        'paymentMenu',
        'changeLessonPayment',
        'sendGroupSetting',
        'sendKeyboardSetStudent',
        'sendMenu',
        'sendHomeworkMenu',
        'cancelAddingHomework',
    ];

    public function process(): void
    {
        if (!$this->isConfirmedUser()) {
            $this->sendConfirmedUserError();

            return;
        }

        $handler = in_array($this->command,self::AVAILABLE_HANDLERS) ? $this->command: 'handleUnknownCommand';
        $this->$handler($this->param);
    }

    protected function handleUnknownCommand(): void
    {
        $this->sendTextMessage("Команда: {$this->command} не существует.");
    }

    private function setStudent(): void
    {
        if (!$this->param) {
            return;
        }

        $user = User::getUserByTelegramID($this->from->id);

        $student = $user->students()
            ->firstWhere('id', $this->param);

        if (!$student) {
            $this->sendTextMessage('Ошибка, ученик не найден');

            return;
        }

        $telegram_reminder = TelegramReminder::updateOrCreate(
            ['chat_id' => $this->chat->id],
            ['student_id' => $student->id]
        );

        $this->sendGroupSetting();
    }

    private function close(): void
    {
        $this->deleteMessage();
    }

    private function reminderChangeStatus(): void
    {
        $telegram_reminder = $this->getTelegramReminder();
        if (!$telegram_reminder) {
            $this->sendStudentDontConnectError();

            return;
        }
        $telegram_reminder->is_enabled = !$telegram_reminder->is_enabled;
        $telegram_reminder->update();

        $this->sendGroupSetting();
    }

    private function beforeLessonMinutesKeyboard(): void
    {
        if (!$this->getTelegramReminder()) {
            $this->sendStudentDontConnectError();

            return;
        }

        $keyboard = [];
        for ($i = 5; $i <= 60; $i += 5) {
            $keyboard[] = [['text' => "{$i} мин.", 'callback_data' => 'setBeforeLessonMinutes ' . $i]];
        }
        $keyboard[] = [
            ['text' => '◀ Назад ◀', 'callback_data' => "sendGroupSetting"],
            ['text' => '❌ Закрыть ❌', 'callback_data' => 'close']
        ];

        $this->editMessageText([
            'chat_id' => $this->chat->id,
            'message_id' => $this->message->messageId,
            'text' => 'Выберите, за сколько напоминать о занятии:',
            'reply_markup' => json_encode(['inline_keyboard' => $keyboard]),
        ]);
    }

    private function setBeforeLessonMinutes(): void
    {
        if (!$this->getTelegramReminder()) {
            $this->sendStudentDontConnectError();

            return;
        }
        if (!$this->param) {
            return;
        }
        if ($this->param <= 0 || $this->param > 1440) {
            $this->sendTextMessage('Переданное значение выходит за границы');

            return;
        }

        $telegram_reminder = TelegramReminder::firstWhere('chat_id', $this->chat->id);
        $telegram_reminder->before_lesson_minutes = $this->param;
        $telegram_reminder->update();

        $this->sendGroupSetting();
    }

    private function homeworkReminderTimeKeyboard(): void
    {
        if (!$this->getTelegramReminder()) {
            $this->sendStudentDontConnectError();

            return;
        }

        $keyboard = [];
        $time = Carbon::createFromTime(8);
        while ($time->lt('22:00')) {
            $arr = [];
            $arr[] = [
                'text' => $time->format('H:i'),
                'callback_data' => "setHomeworkReminderTime {$time->format('H:i')}"
            ];
            $time->addMinutes(30);
            $arr[] = [
                'text' => $time->format('H:i'),
                'callback_data' => "setHomeworkReminderTime {$time->format('H:i')}"
            ];
            $time->addMinutes(30);
            $keyboard[] = $arr;
        }
        $keyboard[] = [
            ['text' => '◀ Назад ◀', 'callback_data' => "sendGroupSetting"],
            ['text' => '❌ Закрыть ❌', 'callback_data' => 'close']
        ];

        $this->editMessageText([
            'chat_id' => $this->chat->id,
            'message_id' => $this->message->messageId,
            'text' => 'Выберите, во сколько ежедневное напоминание о ДЗ:',
            'reply_markup' => json_encode(['inline_keyboard' => $keyboard]),
        ]);
    }

    private function setHomeworkReminderTime(): void
    {
        if (!$this->getTelegramReminder()) {
            $this->sendStudentDontConnectError();

            return;
        }
        if (!$this->param) {
            return;
        }
        $time = Carbon::createFromTimeString($this->param);
        if ($time->lt('8:00') || $time->gt('22:00')) {
            $this->sendTextMessage('Переданное значение выходит за границы');

            return;
        }

        $telegram_reminder = TelegramReminder::firstWhere('chat_id', $this->chat->id);
        $telegram_reminder->homework_reminder_time = $time->format('H:i');
        $telegram_reminder->update();

        $this->sendGroupSetting();
    }

    private function addHomework(): void
    {
        if (!$this->getTelegramReminder()) {
            $this->sendStudentDontConnectError();

            return;
        }

        Cache::put("awaiting_homework_description_{$this->chat->id}", true, now()->addMinutes(5));

        $this->sendMessage([
            'chat_id' => $this->chat->id,
            'text' => 'Пожалуйста, введите краткое описание домашнего задания:',
            'reply_markup' => json_encode(['force_reply' => true]),
        ]);
    }

    private function getListHomework(): void
    {
        if (!$this->getTelegramReminder()) {
            $this->sendStudentDontConnectError();

            return;
        }
        $page = (int)$this->param;
        $perPage = 4;
        $homeworks = Homework::where('student_id', $this->getStudent()->id)
            ->orderByCompleted()
            ->paginate($perPage, ['*'], 'page', $page);

        if ($homeworks->isNotEmpty()) {
            $text = "Домашние задания:\n";
            foreach ($homeworks as $index => $homework) {
                $status = $homework->completed_at ? '✅' : '❌';
                $text .= ($index + 1 + ($homeworks->firstItem() - 1)) . ". {$status} {$homework->description}\n";
            }
            $text .= "\nСтраница {$homeworks->currentPage()} из {$homeworks->lastPage()}";
        } else {
            $text = 'Список домашних заданий пуст.';
        }

        $keyboard = $this->getPaginationKeyboard($homeworks, 'getListHomework', 'sendHomeworkMenu');

        $this->editMessageText([
            'chat_id' => $this->chat->id,
            'message_id' => $this->message->messageId,
            'text' => $text,
            'reply_markup' => json_encode(['inline_keyboard' => $keyboard]),
        ]);
    }

    protected function getPaginationKeyboard(LengthAwarePaginator $paginator, $data_method, $backAction = null): array
    {
        $keyboard = [];
        if ($paginator->hasPages()) {
            $row = [];
            if ($paginator->currentPage() > 1) {
                $row[] = ['text' => '◀ Назад', 'callback_data' => "{$data_method} " . ($paginator->currentPage() - 1)];
            }
            if ($paginator->hasMorePages()) {
                $row[] = ['text' => 'Вперёд ▶', 'callback_data' => "{$data_method} " . ($paginator->currentPage() + 1)];
            }
            $keyboard[] = $row;
        }
        $buttons = [];
        if (!is_null($backAction)) {
            $buttons[] = ['text' => '◀ Назад ◀', 'callback_data' => $backAction];
        }
        $buttons[] = ['text' => '❌ Закрыть ❌', 'callback_data' => 'close'];

        $keyboard[] = $buttons;

        return $keyboard;
    }

    protected function allCompleteHomework(): void
    {
        if (!$this->getTelegramReminder()) {
            $this->sendStudentDontConnectError();

            return;
        }
        $student = $this->getStudent();
        $student->homeworks()->where('completed_at', null)->update([
            'completed_at' => now(),
        ]);
        $this->sendTextMessage('Все задания отмечены как выполненные');
        $this->completeHomeworkMenu();
    }

    protected function completeHomeworkMenu(): void
    {
        if (!$this->getTelegramReminder()) {
            $this->sendStudentDontConnectError();

            return;
        }
        if ($this->command === 'completeHomeworkMenu') {
            $page = (int)$this->param;
        } else {
            $page = $this->pullCachedData('complete_homework_page');
        }

        $perPage = 4;
        $homeworks = Homework::where('student_id', $this->getStudent()->id)
            ->orderByRaw('CASE WHEN completed_at IS NOT NULL THEN 1 ELSE 0 END ASC, created_at DESC')
            ->paginate($perPage, ['*'], 'page', $page);

        $this->putToCacheData('complete_homework_page', $homeworks->currentPage());

        if ($homeworks->isNotEmpty()) {
            $messageText = 'Нажмите на домашнее задание для смены его готовности.';
        } else {
            $messageText = 'Список домашних заданий пуст.';
        }

        $homeworkKeyboard = [];
        foreach ($homeworks as $index => $homework) {
            $status = $homework->completed_at ? '✅' : '❌';
            $text = ($index + 1 + ($homeworks->firstItem() - 1)) . ". {$status} {$homework->description}";
            $homeworkKeyboard[] = [['text' => $text, 'callback_data' => "changeHomeworkStatus {$homework->id}"]];
        }

        if ($homeworks->isNotEmpty()) {
            $homeworkKeyboard[] = [
                [
                    'text' => '✅Отметить все задания выполненными✅',
                    'callback_data' => "allCompleteHomework {$page}"
                ]
            ];
        }
        $paginationKeyboard = $this->getPaginationKeyboard($homeworks, 'completeHomeworkMenu');

        $keyboard = array_merge($homeworkKeyboard, $paginationKeyboard);

        $params = [
            'chat_id' => $this->chat->id,
            'message_id' => $this->message->messageId,
            'text' => $messageText,
            'reply_markup' => json_encode(['inline_keyboard' => $keyboard]),
        ];
        $this->editMessageText($params);
    }

    protected function changeHomeworkStatus(): void
    {
        $param = (int)$this->param;
        $homework = Homework::firstWhere('id', $param);
        if ($homework->completed_at) {
            $homework->completed_at = null;
        } else {
            $homework->completed_at = now();
        }
        $homework->update();

        $this->completeHomeworkMenu();
    }

    protected function paymentMenu($date = null): void
    {
        $date = is_null($date) ? Carbon::today() : Carbon::parse($date);

        $dayOfWeek = getDayName($date);
        $formattedDate = $date->format('d.m.Y');

        $message = "📅 *Оплата занятий на {$formattedDate} {$dayOfWeek}*\n\n";

        $user = UserModel::getUserByTelegramID($this->from->id);
        $lessons = $user->getLessonsOnDate($date);

        $keyboard = [];

        foreach ($lessons as $key => $lesson) {
            $startTime = $lesson->start->format('H:i');
            $endTime = $lesson->end->format('H:i');
            $paymentStatus = $lesson->is_paid ? '✅' : '❌';

            $text = sprintf(
                "%d. %s %s–%s %s %dр.",
                $key + 1,
                $lesson->student_name,
                $startTime,
                $endTime,
                $paymentStatus,
                $lesson->price
            );

            $keyboard[] = [
                [
                    'text' => $text,
                    'callback_data' => "changeLessonPayment {$lesson->id}"
                ]
            ];
        }

        $keyboard[] = [
            ['text' => '◀ Назад ◀', 'callback_data' => "lessonsSchedule {$date}"],
            ['text' => '❌ Закрыть ❌', 'callback_data' => 'close']
        ];

        $this->editMessageText([
            'chat_id' => $this->chat->id,
            'message_id' => $this->message->messageId,
            'text' => $message,
            'parse_mode' => 'Markdown',
            'reply_markup' => json_encode(['inline_keyboard' => $keyboard]),
        ]);
    }

    protected function handlechangeLessonPayment($lessonId): void
    {
        $lesson = Lesson::firstWhere('id', $lessonId);
        if (is_null($lesson)) {
            $this->sendTextMessage('Урок не найден');
            return;
        }

        $lesson->is_paid = !$lesson->is_paid;
        $lesson->update();

        $this->paymentMenu($lesson->date);
    }
}
