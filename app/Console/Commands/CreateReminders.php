<?php

namespace App\Console\Commands;

use App\Models\Homework;
use App\Models\Lesson;
use App\Models\Reminder;
use App\Models\TelegramReminder;
use App\Models\User;
use App\src\Schedule\Schedule;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class CreateReminders extends Command
{
    protected $signature = 'reminders:create';
    protected $description = 'Create reminders for upcoming lessons';

    public function handle(): void
    {
        $now = now();

        $actualUsers = User::query()
            ->where('is_active', true)
            ->where('email_verified_at', '!=', null)
            ->get();

        $todayLessons = new Collection();
        foreach ($actualUsers as $user) {
            $schedule = new Schedule($user);
            $todayLessons->put($user->email, $schedule->getDateLessons($now));
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

            if (!$homeworks) {
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
                $text .= $key + 1 . ". {$homework->description}\n";
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
}
