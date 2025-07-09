<?php

namespace App\Console\Commands;

use App\Models\Homework;
use App\Models\Lesson;
use App\Models\Reminder;
use App\Models\Task;
use App\Models\TelegramReminder;
use App\Models\User;
use App\Services\LessonService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class CreateReminders extends Command
{
    protected $signature = 'reminders:create';

    protected $description = 'Create reminders for upcoming lessons and tasks';

    public function handle(): void
    {
        $now = now();

        $actualUsers = User::query()
            ->where('is_active', true)
            ->where('email_verified_at', '!=', null)
            ->get();

        $todayLessons = new Collection;
        foreach ($actualUsers as $user) {
            $scheduleService = app(LessonService::class, compact('user'));
            $todayLessons->put($user->email, $scheduleService->getActualLessonsOnDate($now));
        }

        foreach ($todayLessons as $userEmail => $lessons) {
            /** @var $lesson Lesson */
            foreach ($lessons as $lesson) {
                $student = $lesson->student;
                $reminderSettings = $student->telegram_reminder;
                if ($reminderSettings) {
                    $this->createHomeworkReminder($lesson, $reminderSettings, $now);
                    $this->createLessonStartReminder($lesson, $reminderSettings, $now);
                }
            }
        }

        $this->createTasksReminders($actualUsers, $now);

    }

    private function createTasksReminders(\Illuminate\Database\Eloquent\Collection $users, Carbon $now): void
    {
        /** @var $user User */
        foreach ($users as $user) {
            if (! $user->is_enabled_task_reminders) {
                continue;
            }

            $tasks = $user
                ->tasks()
                ->whereNotNull('deadline')
                ->whereNull('completed_at')
                ->get();

            /** @var $task Task */
            foreach ($tasks as $task) {
                $chatId = $user->telegram_id;
                if (! $chatId) {
                    continue;
                }

                if ($task->reminder_before_deadline && $task->reminder_before_hours) {
                    $reminderTime = $task->deadline->copy()->subHours($task->reminder_before_hours);

                    if ($now->diffInMinutes($reminderTime) <= 5 && $now->lt($reminderTime)) {
                        $this->createDeadlineReminder($task, $chatId, $task->deadline);
                    }
                }

                if ($task->reminder_daily && $task->reminder_daily_time) {
                    $reminderTime = $task->reminder_daily_time->copy()->setDate($now->year, $now->month, $now->day);

                    if ($now->diffInMinutes($reminderTime) <= 5 && $now->lt($reminderTime)) {
                        $this->createDailyReminder($task, $chatId, $task->deadline, $now);
                    }
                }
            }
        }
    }

    private function createHomeworkReminder(Lesson $lesson, TelegramReminder $settings, Carbon $now): void
    {
        $lessonDate = Carbon::parse($lesson->date);
        $reminderTime = Carbon::parse($settings->homework_reminder_time)
            ->setDate($lessonDate->year, $lessonDate->month, $lessonDate->day);

        if ($now->diffInMinutes($reminderTime) <= 5 && $now->lt($reminderTime)) {
            $homeworks = Homework::query()
                ->where('student_id', $lesson->student_id)
                ->where('completed_at', null)
                ->get();

            if (! $homeworks) {
                return;
            }

            $reminder = Reminder::query()
                ->where('chat_id', $settings->chat_id)
                ->where('key', 'homework')
                ->whereToday('created_at')
                ->get();

            if ($reminder->isNotEmpty()) {
                return;
            }

            $date = Carbon::parse($lesson->date)->format('d.m.Y');
            $time = $lesson->start->format('H:i');
            $text = "Напоминание: Не забудьте сделать домашнее задание на {$date} в {$time}:\n";
            foreach ($homeworks as $key => $homework) {
                $text .= $key + 1 .". {$homework->description}\n";
            }

            Reminder::create([
                'chat_id' => $settings->chat_id,
                'text' => $text,
                'key' => 'homework',
                'is_notified' => false,
            ]);
        }
    }

    private function createLessonStartReminder(Lesson $lesson, TelegramReminder $settings, Carbon $now): void
    {
        $reminderTime = $lesson->start->copy()->subMinutes($settings->before_lesson_minutes);

        if ($now->diffInMinutes($reminderTime) <= 5 && $now->lt($reminderTime)) {
            $key = "lesson_{$lesson->id}";
            $reminder = Reminder::query()
                ->where('chat_id', $settings->chat_id)
                ->where('key', $key)
                ->whereToday('created_at')
                ->get();

            if ($reminder->isNotEmpty()) {
                return;
            }

            $time = $lesson->start->format('H:i');
            $text = "Напоминание: Урок начнется через {$settings->before_lesson_minutes} минут в {$time}.";

            Reminder::create([
                'chat_id' => $settings->chat_id,
                'text' => $text,
                'key' => $key,
                'is_notified' => false,
            ]);
        }
    }

    private function createDeadlineReminder(Task $task, $chatId, Carbon $deadline): void
    {
        $reminder = Reminder::query()
            ->where('chat_id', $chatId)
            ->where('key', "task_deadline_{$task->id}")
            ->whereToday('created_at')
            ->first();

        if ($reminder) {
            return;
        }

        $deadlineFormatted = $deadline->format('d.m.Y в H:i');
        $text = "Напоминание: Дедлайн задачи \"{$task->title}\" истекает {$deadlineFormatted}.";

        Reminder::create([
            'chat_id' => $chatId,
            'text' => $text,
            'key' => "task_deadline_{$task->id}",
            'is_notified' => false,
        ]);
    }

    private function createDailyReminder(Task $task, $chatId, Carbon $deadline, Carbon $now): void
    {
        $reminder = Reminder::query()
            ->where('chat_id', $chatId)
            ->where('key', "task_daily_{$task->id}")
            ->whereToday('created_at')
            ->first();

        if ($reminder) {
            return;
        }

        $deadlineFormatted = $deadline->format('d.m.Y в H:i');

        if ($deadline->isPast()) {
            $diffInDays = $deadline->diffInDays($now);
            $diffInHours = $deadline->diffInHours($now) % 24; // Остаток часов после дней

            $diffInDaysFloored = floor($diffInDays);
            $delayText = $diffInDays > 0
                ? "{$diffInDaysFloored} дн. и {$diffInHours} ч."
                : "{$diffInHours} ч.";

            $text = "Напоминание: Задача \"{$task->title}\" просрочена! \n"
                ."Дедлайн был {$deadlineFormatted}. \n"
                ."Просрочено на: {$delayText}.";
        } else {
            $text = "Ежедневное напоминание: Не забудьте про задачу \"{$task->title}\"! \n"
                ."Дедлайн {$deadlineFormatted}.";
        }

        Reminder::create([
            'chat_id' => $chatId,
            'text' => $text,
            'key' => "task_daily_{$task->id}",
            'is_notified' => false,
        ]);
    }
}
