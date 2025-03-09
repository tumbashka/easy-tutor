<?php

namespace App\src\Telegram;

use App\Models\Homework;
use App\Models\Student;
use App\Models\TelegramReminder;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Telegram\Bot\Api;
use Telegram\Bot\Objects\CallbackQuery;

class CallbackQueryHandler extends BaseHandler
{
    private CallbackQuery $callbackQuery;
    private string|null $command;
    private string|null $param;

    public function __construct(Api $telegram, CallbackQuery $callbackQuery)
    {
        parent::__construct($telegram, $callbackQuery->message->chat, $callbackQuery->from, $callbackQuery->message);

        $this->callbackQuery = $callbackQuery;

        $arr_string = explode(' ', $callbackQuery->data, 2);

        $this->command = $arr_string[0] ?? null;
        $this->param = $arr_string[1] ?? null;
    }

    private const array COMMAND_HANDLERS = [
        'set_student' => 'handleSetStudent',
        'close' => 'handleClose',
        'disable_remind' => 'handleDisableRemind',
        'enable_remind' => 'handleEnableRemind',
        'set_student_menu' => 'handleSetStudentMenu',
        'change_before_lesson_minutes' => 'handleBeforeLessonMinutesKeyboard',
        'set_before_lesson_minutes' => 'handleSetBeforeLessonMinutes',
        'change_homework_reminder_time' => 'handleHomeworkReminderTimeKeyboard',
        'set_homework_reminder_time' => 'handleSetHomeworkReminderTime',
        'add_homework' => 'handleAddHomework',
        'get_list_homework' => 'handleGetListHomework',
        'get_complete_homework_menu' => 'handleCompleteHomeworkMenu',
        'change_homework_status' => 'handleChangeHomeworkStatus',
        'complete_all_homework' => 'handleAllCompleteHomework',
    ];

    public function process(): void
    {
        if (!$this->isConfirmedUser()) {
            $this->sendConfirmedUserError();
            return;
        }

        if (config('app.debug')) {
            \Log::debug('Callback Query:', ['data' => $this->callbackQuery->toArray()]);
        }

        $handler = self::COMMAND_HANDLERS[$this->command] ?? 'handleUnknownCommand';
        $this->$handler();
    }

    private function handleUnknownCommand(): void
    {
        \Log::warning('Unknown command received', ['command' => $this->command]);
        $this->sendTextMessage('Unknown command. Please try again.');
    }

    private function handleSetStudent(): void
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
            ['chat_id' => $this->chat->id,],
            ['student_id' => $student->id,]
        );

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
            Этой группе успешно назначен ученик: ***{$student->name}***.
            {$text_body}
            Для вызова настроек, используйте ***/settings***
            EOD;

        $this->telegram->editMessageText([
            'chat_id' => $this->chat->id,
            'message_id' => $this->callbackQuery->message->messageId,
            'text' => $text,
            'parse_mode' => 'Markdown',
            'reply_markup' => json_encode(['inline_keyboard' => []]),
        ]);

    }

    private function handleClose(): void
    {
        $this->telegram->deleteMessage([
            'chat_id' => $this->chat->id,
            'message_id' => $this->callbackQuery->message->messageId,
        ]);
    }

    private function handleDisableRemind(): void
    {
        $telegram_reminder = $this->getTelegramReminder();
        if (!$telegram_reminder) {
            $this->sendStudentDontConnectError();
            return;
        }
        $telegram_reminder->is_enabled = false;
        $telegram_reminder->update();
        $this->telegram->deleteMessage([
            'chat_id' => $this->chat->id,
            'message_id' => $this->callbackQuery->message->messageId,
        ]);
        $this->sendGroupSetting();
    }

    private function handleEnableRemind(): void
    {
        $telegram_reminder = TelegramReminder::firstWhere('chat_id', $this->chat->id);
        $telegram_reminder->is_enabled = true;
        $telegram_reminder->update();
        $this->telegram->deleteMessage([
            'chat_id' => $this->chat->id,
            'message_id' => $this->callbackQuery->message->messageId,
        ]);
        $this->sendGroupSetting();

    }

    private function handleSetStudentMenu(): void
    {
        $this->telegram->deleteMessage([
            'chat_id' => $this->chat->id,
            'message_id' => $this->callbackQuery->message->messageId,
        ]);
        $this->sendKeyboardSetStudent();
    }

    private function handleBeforeLessonMinutesKeyboard(): void
    {
        if (!$this->getTelegramReminder()) {
            $this->sendStudentDontConnectError();
            return;
        }
        $this->telegram->deleteMessage([
            'chat_id' => $this->chat->id,
            'message_id' => $this->callbackQuery->message->messageId,
        ]);

        $keyboard = [];
        for ($i = 5; $i <= 60; $i += 5) {
            $keyboard[] = [['text' => "{$i} мин.", 'callback_data' => 'set_before_lesson_minutes ' . $i]];
        }

        $keyboard[] = [['text' => '❌ Закрыть ❌', 'callback_data' => 'close']];
        $this->telegram->sendMessage([
            'chat_id' => $this->chat->id,
            'text' => 'Выберите, за сколько напоминать о занятии:',
            'reply_markup' => json_encode(['inline_keyboard' => $keyboard])
        ]);
    }

    private function handleSetBeforeLessonMinutes(): void
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
        $this->telegram->deleteMessage([
            'chat_id' => $this->chat->id,
            'message_id' => $this->callbackQuery->message->messageId,
        ]);
        $this->sendGroupSetting();

    }

    private function handleHomeworkReminderTimeKeyboard(): void
    {
        if (!$this->getTelegramReminder()) {
            $this->sendStudentDontConnectError();
            return;
        }
        $this->telegram->deleteMessage([
            'chat_id' => $this->chat->id,
            'message_id' => $this->callbackQuery->message->messageId,
        ]);

        $keyboard = [];
        $time = Carbon::createFromTime(8);
        while ($time->lt('22:00')) {
            $arr = [];
            $arr[] = ['text' => $time->format('H:i'), 'callback_data' => "set_homework_reminder_time {$time->format('H:i')}"];
            $time->addMinutes(30);
            $arr[] = ['text' => $time->format('H:i'), 'callback_data' => "set_homework_reminder_time {$time->format('H:i')}"];
            $time->addMinutes(30);
            $keyboard[] = $arr;
        }

        $keyboard[] = [['text' => '❌ Закрыть ❌', 'callback_data' => 'close']];
        $this->telegram->sendMessage([
            'chat_id' => $this->chat->id,
            'text' => 'Выберите, во сколько ежедневное напоминание о ДЗ:',
            'reply_markup' => json_encode(['inline_keyboard' => $keyboard])
        ]);
    }

    private function handleSetHomeworkReminderTime(): void
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
        $this->telegram->deleteMessage([
            'chat_id' => $this->chat->id,
            'message_id' => $this->callbackQuery->message->messageId,
        ]);
        $this->sendGroupSetting();
    }

    private function handleAddHomework(): void
    {
        if (!$this->getTelegramReminder()) {
            $this->sendStudentDontConnectError();
            return;
        }
        $chatId = $this->chat->id;
        Cache::put("awaiting_homework_description_{$chatId}", true, now()->addMinutes(5));

        $response = $this->telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => 'Пожалуйста, введите краткое описание домашнего задания:',
            'reply_markup' => json_encode(['force_reply' => true]),
        ]);

        $this->telegram->deleteMessage([
            'chat_id' => $chatId,
            'message_id' => $this->callbackQuery->message->messageId,
        ]);
    }

    private function handleGetListHomework(): void
    {
        if (!$this->getTelegramReminder()) {
            $this->sendStudentDontConnectError();
            return;
        }
        $page = (int)$this->param;
        $perPage = 4;
        $homeworks = Homework::where('student_id', $this->getStudent()->id)
            ->orderByRaw('CASE WHEN completed_at IS NOT NULL THEN 1 ELSE 0 END ASC, created_at DESC')
            ->paginate($perPage, ['*'], 'page', $page);

        $text = $this->getHomeworkText($homeworks);
        $keyboard = $this->getPaginationKeyboard($homeworks, 'get_list_homework');

        $params = [
            'chat_id' => $this->chat->id,
            'text' => $text ?: 'Список домашних заданий пуст.',
            'reply_markup' => json_encode(['inline_keyboard' => $keyboard]),
        ];

        if ($this->callbackQuery->message) {
            $params['message_id'] = $this->callbackQuery->message->messageId;
            $this->telegram->editMessageText($params);
        } else {
            $this->telegram->sendMessage($params);
        }
    }

    protected function getHomeworkText($homeworks): string
    {
        if ($homeworks->isEmpty()) {
            return '';
        }

        $text = "Домашние задания:\n";
        foreach ($homeworks as $index => $homework) {
            $status = $homework->completed_at ? '✅' : '❌';
            $text .= ($index + 1 + ($homeworks->firstItem() - 1)) . ". {$status} {$homework->description}\n";
        }
        $text .= "\nСтраница {$homeworks->currentPage()} из {$homeworks->lastPage()}";
        return $text;
    }

    protected function getPaginationKeyboard(LengthAwarePaginator $paginator, $data_method): array
    {
        $keyboard = [];
        if ($paginator->hasPages()) {
            $row = [];
            if ($paginator->currentPage() > 1) {
                $row[] = ['text' => '◀ Назад', 'callback_data' => "{$data_method} " . ($paginator->currentPage() - 1),];
            }
            if ($paginator->hasMorePages()) {
                $row[] = ['text' => 'Вперёд ▶', 'callback_data' => "{$data_method} " . ($paginator->currentPage() + 1),];
            }
            $keyboard[] = $row;
        }
        $keyboard[] = [['text' => '❌ Закрыть ❌', 'callback_data' => 'close']];
        return $keyboard;
    }

    protected function handleAllCompleteHomework(): void
    {
        if (!$this->getTelegramReminder()) {
            $this->sendStudentDontConnectError();
            return;
        }
        $student = $this->getStudent();
        $homeworks = $student->homework()->where('completed_at', null)->update([
            'completed_at' => now(),
        ]);
//        $this->telegram->deleteMessage([
//            'chat_id' => $this->chat->id,
//            'message_id' => $this->callbackQuery->message->messageId,
//        ]);
        $this->sendTextMessage('Все задания отмечены как выполненные');
        $this->handleCompleteHomeworkMenu((int)$this->param);
    }

    protected function handleCompleteHomeworkMenu(): void
    {
        if (!$this->getTelegramReminder()) {
            $this->sendStudentDontConnectError();
            return;
        }
        if($this->command === 'get_complete_homework_menu'){
            $page = (int)$this->param;
        }else{
            $page = $this->pullCachedData('complete_homework_page');
        }

        $perPage = 4;
        $homeworks = Homework::where('student_id', $this->getStudent()->id)
            ->orderByRaw('CASE WHEN completed_at IS NOT NULL THEN 1 ELSE 0 END ASC, created_at DESC')
            ->paginate($perPage, ['*'], 'page', $page);

        $this->putToCacheData('complete_homework_page', $homeworks->currentPage());

        if ($homeworks) {
            $messageText = 'Нажмите на домашнее задание для смены его готовности.';
        } else {
            $messageText = 'Список домашних заданий пуст.';
        }

        $homeworkKeyboard = [];
        foreach ($homeworks as $index => $homework) {
            $status = $homework->completed_at ? '✅' : '❌';
            $text = ($index + 1 + ($homeworks->firstItem() - 1)) . ". {$status} {$homework->description}";
            $homeworkKeyboard[] = [['text' => $text, 'callback_data' => "change_homework_status {$homework->id}"]];
        }

        if ($homeworks) {
            $homeworkKeyboard[] = [['text' => '✅Отметить все задания выполненными✅', 'callback_data' => "complete_all_homework {$page}"]];
        }
        $paginationKeyboard = $this->getPaginationKeyboard($homeworks, 'get_complete_homework_menu');

        $keyboard = array_merge($homeworkKeyboard, $paginationKeyboard);

        $params = [
            'chat_id' => $this->chat->id,
            'message_id' => $this->message->messageId,
            'text' => $messageText,
            'reply_markup' => json_encode(['inline_keyboard' => $keyboard]),
        ];
        $this->telegram->editMessageText($params);
    }

    protected function handleChangeHomeworkStatus(): void
    {
        $param = (int)$this->param;
        $homework = Homework::firstWhere('id', $param);
        if ($homework->completed_at) {
            $homework->completed_at = null;
        } else {
            $homework->completed_at = now();
        }
        $homework->update();

        $this->handleCompleteHomeworkMenu();
    }


}
