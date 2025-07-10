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
        return $this->user->lessons()->oldest()->first();
    }

    public function getFromCacheLessonsOnDate(Carbon $date): mixed
    {
        return Cache::tags("lessons_{$this->user->id}")
            ->get("lessons_{$this->user->id}_{$date->format('Y-m-d')}");
    }

    public function putToCacheLessonsOnDate(Collection $lessonsOnDate, Carbon $date): void
    {
        Cache::tags("lessons_{$this->user->id}")
            ->put("lessons_{$this->user->id}_{$date->format('Y-m-d')}", $lessonsOnDate, 3600);
    }

    public function getFromCacheLessonTimesOnWeekDay(int $weekDayId): mixed
    {
        return Cache::tags("lesson_dates_{$this->user->id}")
            ->get("lesson_dates_{$this->user->id}_{$weekDayId}");
    }

    public function putToCacheLessonTimesOnWeekDay(Collection $lessonTimesOnWeekDay, int $weekDayId): void
    {
        Cache::tags("lesson_dates_{$this->user->id}")
            ->put("lesson_dates_{$this->user->id}_{$weekDayId}", $lessonTimesOnWeekDay, 3600);
    }

    public function getWeekDayLessonTimes(int $weekDayId): Collection
    {
        $lessonTimesOnWeekDay =  $this->getFromCacheLessonTimesOnWeekDay($weekDayId);

        if ($lessonTimesOnWeekDay == null || $lessonTimesOnWeekDay->isEmpty()) {
            $lessonTimesOnWeekDay = $this->user->lessonTimes()->where('week_day', $weekDayId)->get();
            $this->putToCacheLessonTimesOnWeekDay($lessonTimesOnWeekDay, $weekDayId);
        }

        return $lessonTimesOnWeekDay;
    }


}
