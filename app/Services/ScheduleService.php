<?php

namespace App\Services;

use App\DTO\Lesson\WeekDTO;
use App\Models\LessonTime;
use App\Models\User;
use App\Repositories\LessonRepository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class ScheduleService
{
    private LessonRepository $lessonRepository;

    public function __construct(?User $user = null, ?LessonRepository $lessonRepository = null)
    {
        $user = $user ?? auth()->user();
        $this->lessonRepository = $lessonRepository ?? new LessonRepository($user);
    }

    /**
     * Получение данных на неделю.
     * Можно передать отступ в неделях.
     *
     * @param int $weekOffset
     * @return WeekDTO
     */
    public function getWeekData(int $weekOffset = 0): WeekDTO
    {
        $weekDays = $this->getWeekDays($weekOffset);
        $previous = $this->getPreviousWeeks($weekOffset, 10);
        $next = $this->getNextWeeks($weekOffset, 10);
        $lessonsOnDays = $this->getWeekLessonsOnDays($weekDays);
        $weekBorders = $this->getWeekBorders($weekOffset);

        return new WeekDTO(
            $weekOffset,
            $weekDays,
            $previous,
            $next,
            $lessonsOnDays,
            $weekBorders,
        );
    }

    /**
     * Получение массива дней недели
     * ['0-6' => Carbon obj]
     *
     * @return array<Carbon>
     */
    public function getWeekDays(int $weekOffset = 0): array
    {
        $startDate = now();
        $currentWeekDay = $startDate->addWeeks($weekOffset)->startOfWeek()->endOfDay();
        $weekDays = [];
        for ($i = 0; $i <= 6; $i++) {
            $weekDays[] = $currentWeekDay->copy()->addDays($i);
        }

        return $weekDays;
    }

    /**
     * Получение массива строк с границами предыдущих недель
     *
     * @return array<string>
     */
    public function getPreviousWeeks(int $weekOffset = 0, int $count = 5): array
    {
        $res = [];
        for ($i = -1; -$i <= $count; $i--) {
            $res[$i + $weekOffset] = $this->getWeekBorders($weekOffset + $i);
        }

        return $res;
    }

    /**
     * Получение массива строк с границами будущих недель
     *
     * @return array<string>
     */
    public function getNextWeeks(int $weekOffset = 0, int $count = 5): array
    {
        $res = [];
        for ($i = 1; $i <= $count; $i++) {
            $res[$i + $weekOffset] = $this->getWeekBorders($weekOffset + $i);
        }

        return $res;
    }

    /**
     * Получение строки, вида:
     * `{дата начала недели} - {дата конца недели}`
     */
    public function getWeekBorders(int $weekOffset = 0): string
    {
        $weekOffsetDate = now()->addWeeks($weekOffset);
        $mon = $weekOffsetDate->startOfWeek()->format('d.m.y');
        $sun = $weekOffsetDate->endOfWeek()->format('d.m.y');

        return "{$mon} - {$sun}";
    }

    /**
     * Возвращает коллекцию, где ключ - индекс дня недели,
     * а значение - коллекция с занятиями этого дня
     */
    public function getWeekLessonsOnDays(array $weekDays): Collection
    {
        $lessonsOnDays = new Collection;
        foreach ($weekDays as $weekDayId => $date) {
            $lessonsOnDate = $this->getActualLessonsOnDate($date);

            $this->lessonRepository->putToCacheLessonsOnDate($lessonsOnDate, $date);

            $lessonsOnDays->put($weekDayId, $lessonsOnDate->sortBy('start'));
        }

        return $lessonsOnDays;
    }

    /**
     * Получить актуальные занятия в определенную дату.
     * Генерирует новые занятие, если необходимо.
     *
     * @param Carbon $date
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActualLessonsOnDate(Carbon $date): \Illuminate\Database\Eloquent\Collection
    {
        $lessonsOnDate = $this->lessonRepository->getLessonsOnDate($date);

        if ($this->shouldGenerateLessons($date)) {
            $weekDayId = getWeekDayIndex($date);
            $lessonTimes = $this->lessonRepository->getWeekDayLessonTimes($weekDayId);

            $existingLessonTimeIds = $lessonsOnDate->pluck('lesson_time_id');

            foreach ($lessonTimes as $lessonTime) {
                if (! $existingLessonTimeIds->contains($lessonTime->id) && $this->createdBefore($lessonTime, $date)) {
                    $lessonData = $this->lessonRepository->generateLessonData($date, $lessonTime);

                    $lesson = $this->lessonRepository->saveLesson($lessonData);

                    $lessonsOnDate->push($lesson);
                }
            }
        }

        return $lessonsOnDate->sortBy('start');
    }

    /**
     * Нужно ли генерировать уроки в указанный день
     *
     * @param Carbon $date
     * @return bool
     */
    private function shouldGenerateLessons(Carbon $date): bool
    {
        return $date->isFuture() || $this->isAfterFirstLesson($date);
    }

    /**
     * Время занятия появилось до указанного дня.
     *
     * @param LessonTime $lessonTime
     * @param Carbon $date
     * @return bool
     */
    private function createdBefore(LessonTime $lessonTime, Carbon $date): bool
    {
        return $lessonTime->created_at->lte($date);
    }

    /**
     * Дата после первого занятия репетитора
     *
     * @param Carbon $date
     * @return bool
     */
    private function isAfterFirstLesson(Carbon $date): bool
    {
        $firstLesson = $this->lessonRepository->getUserFirstLesson();

        if ($firstLesson) {
            return $firstLesson->created_at->lt($date);
        }

        return false;
    }
}
