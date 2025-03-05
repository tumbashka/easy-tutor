<?php

namespace App\src\Telegram;

use App\Models\Student;
use App\Models\TelegramReminder;
use App\Models\User;
use Telegram\Bot\Api;
use Telegram\Bot\Objects\CallbackQuery;
use Telegram\Bot\Objects\Message;
use Telegram\Bot\Traits\Telegram;

class CallbackQueryHandler extends BaseHandler
{
    private CallbackQuery $callbackQuery;
    private string|null $command;
    private string|null $param;

    public function __construct(Api $telegram, CallbackQuery $callbackQuery)
    {
        parent::__construct($telegram, $callbackQuery->message->chat, $callbackQuery->from);

        $this->callbackQuery = $callbackQuery;

        $arr_string = explode(' ', $callbackQuery->data, 2);

        $this->command = $arr_string[0] ?? null;
        $this->param = $arr_string[1] ?? null;
    }

    public function process(): void
    {
        dump($this->callbackQuery);

        switch ($this->command) {
            case 'set_student':
                $this->handleSetStudent();
                break;
            case 'close':
                $this->handleClose();
                break;
            case 'disable_remind':
                $this->handleDisableRemind();
                break;
            case 'enable_remind':
                $this->handleEnableRemind();
                break;
            case 'set_student_menu':
                $this->handleSetStudentMenu();
                break;
            case 'change_before_lesson_minutes':
                $this->handleChangeBeforeLessonMinutes();
                break;
            default:
        }
    }

    private function handleSetStudent(): void
    {
        if (!$this->is_confirmed_user()) {
            $this->send_confirmed_user_error();
            return;
        }
        if (!$this->param) {
            return;
        }

        $user = User::getUserByTelegramID($this->from->id);

        /**
         * @var $student Student
         */

        $student = $user->students()
            ->firstWhere('id', $this->param);

        if (!$student) {
            $this->send_message('Ошибка, ученик не найден');
            return;
        }

        $telegram_reminder = TelegramReminder::updateOrCreate(
            ['chat_id' => $this->chat->id,],
            ['student_id' => $student->id,]
        );
        dump($telegram_reminder);

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
        if (!$this->is_confirmed_user()) {
            $this->send_confirmed_user_error();
            return;
        }
        $this->telegram->deleteMessage([
            'chat_id' => $this->chat->id,
            'message_id' => $this->callbackQuery->message->messageId,
        ]);
    }

    private function handleDisableRemind(): void
    {
        if (!$this->is_confirmed_user()) {
            $this->send_confirmed_user_error();
            return;
        }
        $telegram_reminder = TelegramReminder::firstWhere('chat_id', $this->chat->id);
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
        if (!$this->is_confirmed_user()) {
            $this->send_confirmed_user_error();
            return;
        }
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
        if (!$this->is_confirmed_user()) {
            $this->send_confirmed_user_error();
            return;
        }
        $this->telegram->deleteMessage([
            'chat_id' => $this->chat->id,
            'message_id' => $this->callbackQuery->message->messageId,
        ]);
        $this->sendKeyboardSetStudent();
    }

    private function handleChangeBeforeLessonMinutes(): void
    {
        if (!$this->is_confirmed_user()) {
            $this->send_confirmed_user_error();
            return;
        }
        $this->telegram->deleteMessage([
            'chat_id' => $this->chat->id,
            'message_id' => $this->callbackQuery->message->messageId,
        ]);

        $user = \App\Models\User::getUserByTelegramID($this->from->id);
        $students = $user->students;

//        $keyboard = [];
//        for (){
//
//        }
        foreach ($students as $student) {
            $keyboard[] = [['text' => $student->name, 'callback_data' => 'set_student ' . $student->id]];
        }
        $keyboard[] = [['text' => '❌ Закрыть ❌', 'callback_data' => 'close']];
        $this->telegram->sendMessage([
            'chat_id' => $this->chat->id,
            'text' => 'Выберите ученика для группы:',
            'reply_markup' => json_encode(['inline_keyboard' => $keyboard])
        ]);


    }
}
