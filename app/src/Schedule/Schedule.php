<?php

namespace App\src\Schedule;

use App\Models\LessonTime;
use App\Models\User;
use App\Repositories\LessonRepository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class Schedule
{
    private LessonRepository $lessonRepository;

    public function __construct(User $user)
    {
        $this->lessonRepository = new LessonRepository($user);
    }

    public function getWeekLessonsOnDays(array $weekDays): Collection
    {
        $lessonsOnDays = new Collection();
        foreach ($weekDays as $weekDayId => $date) {
            $lessonsOnDate = $this->getDateLessons($date);

            $this->lessonRepository->putToCacheLessonsOnDate($lessonsOnDate, $date);

            $lessonsOnDays->put($weekDayId, $lessonsOnDate->sortBy('start'));
        }
        return $lessonsOnDays;
    }

    public function getDateLessons(Carbon $date): \Illuminate\Database\Eloquent\Collection
    {
        $lessonsOnDate = $this->lessonRepository->getLessonsOnDate($date);

        if ($this->shouldGenerateLessons($date)) {
            $weekDayId = getWeekDayIndex($date);
            $lessonTimes = $this->lessonRepository->getWeekDayLessonTimes($weekDayId);

            $existingLessonTimeIds = $lessonsOnDate->pluck('lesson_time_id');

            foreach ($lessonTimes as $lessonTime) {
                if (!$existingLessonTimeIds->contains($lessonTime->id) && $this->createdBefore($lessonTime, $date)) {

                    $lessonData = $this->lessonRepository->generateLessonData($date, $lessonTime);

                    $lesson = $this->lessonRepository->saveLesson($lessonData);

                    $lessonsOnDate->push($lesson);
                }
            }
        }
        return $lessonsOnDate;
    }

    private function shouldGenerateLessons(Carbon $date): bool
    {
        return $date->isFuture() || $this->isAfterStartDate($date);
    }

    private function createdBefore(LessonTime $lessonTime, Carbon $date): bool
    {
        return $lessonTime->created_at->lte($date);
    }

    private function isAfterStartDate(Carbon $date): bool
    {
        $firstLesson = $this->lessonRepository->getLessonsOnDate($date->copy()->startOfDay())
            ->sortBy('created_at')
            ->first();

        if ($firstLesson) {
            return $firstLesson->created_at->lt($date);
        }
        return false;
    }
}
