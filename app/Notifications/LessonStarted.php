<?php

namespace App\Notifications;

use App\Models\Lesson;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;

class LessonStarted extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private Lesson $lesson,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase(object $notifiable): array
    {
        $dayName = getShortDayName(Carbon::parse($this->lesson->date));

        return [
            'student_name' => $this->lesson->student_name,
            'date' => Carbon::parse($this->lesson->date)->format('d.m.Y'),
            'start' => $this->lesson->start->format('H:i'),
            'url' => route(
                'schedule.lesson.edit',
                [
                    'day' => Carbon::parse($this->lesson->date)->format('Y-m-d'),
                    'lesson' => $this->lesson->id,
                ]
            ),
            'text' => "Начинается занятие с учеником: {$this->lesson->student_name} ({$dayName}. {$this->lesson->start->format('H:i')})"
        ];
    }

    public function with(object $notifiable): array
    {
        return ['id' => $this->id];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        $dayName = getShortDayName(Carbon::parse($this->lesson->date));
        $notificationId = $this->id ?? null;

        return new BroadcastMessage([
            'id' => $notificationId,
            'student_name' => $this->lesson->student_name,
            'date' => Carbon::parse($this->lesson->date)->format('d.m.Y'),
            'start' => $this->lesson->start->format('H:i'),
            'note' => $this->lesson->note,
            'url' => route(
                'schedule.lesson.edit',
                [
                    'day' => Carbon::parse($this->lesson->date)->format('Y-m-d'),
                    'lesson' => $this->lesson->id,
                ]
            ),
            'text' => "Начинается занятие с учеником: {$this->lesson->student_name} ({$dayName}. {$this->lesson->start->format('H:i')})"
        ]);
    }

    public function broadcastType(): string
    {
        return 'lesson.started';
    }
}
