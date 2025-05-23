<?php

namespace App\Repositories;

use App\Models\Lesson;
use App\Models\LessonTime;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class LessonRepository
{
    private User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getLessonsOnDate(Carbon $date): Collection
    {
        $lessonsOnDate = $this->getFromCacheLessonsOnDate($date);

        if ($lessonsOnDate == null || $lessonsOnDate->isEmpty()) {
            $lessonsOnDate = $this->user->getLessonsOnDate($date);
        }

        return $lessonsOnDate;
    }

    public function getUserFirstLesson()
    {
        return $this->user->lessons()->oldest('created_at')->first();
    }

    public function getFromCacheLessonsOnDate(Carbon $date): mixed
    {
        return Cache::tags("lessons_{$this->user->id}")->get("lessons_{$this->user->id}_{$date->format('Y-m-d')}");
    }

    public function putToCacheLessonsOnDate(Collection $lessonsOnDate, Carbon $date): void
    {
        Cache::tags("lessons_{$this->user->id}")->put("lessons_{$this->user->id}_{$date->format('Y-m-d')}", $lessonsOnDate, 3600); // сохраняем в кэш
    }

    public function getWeekDayLessonTimes(int $weekDayId): Collection
    {
        return $this->user->lessonTimes()->where('week_day', $weekDayId)->get();
    }

    public function generateLessonData(Carbon $date, LessonTime $lessonTime): array
    {
        return [
            'student_id' => $lessonTime->student_id,
            'user_id' => $this->user->id,
            'student_name' => $lessonTime->student->name,
            'date' => $date->copy(),
            'start' => $lessonTime->start,
            'end' => $lessonTime->end,
            'is_paid' => false,
            'is_canceled' => false,
            'price' => getLessonPrice($lessonTime->start, $lessonTime->end, $lessonTime->student->price),
            'lesson_time_id' => $lessonTime->id,
        ];
    }

    public function saveLesson(array $data): Lesson
    {
        return Lesson::create($data);
    }
}
