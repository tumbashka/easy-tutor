<?php

namespace App\src\Schedule;

use App\Models\Lesson;
use App\Models\LessonTime;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class Schedule
{
    private array $week_days;
    private array $lessons_on_days = [];
    private User $user;

    public function __construct(int $weekOffset)
    {
        $this->week_days = getWeekDays($weekOffset); // ['0-6' => Carbon obj]
        $this->user = auth()->user();
    }

    public function getWeekLessonsOnDays(): array
    {
        $this->calculate();
        return $this->lessons_on_days;
    }

    public function calculate(): void
    {
        foreach ($this->week_days as $week_day_id => $date) {

            if (!$lessons_on_date = Cache::tags("lessons_{$this->user->id}")->get("lessons_{$this->user->id}_{$date->format('Y-m-d')}")) {
                $week_day_lesson_times = $this->user->getWeekDayLessonTimes($week_day_id);
                $lessons_on_date = $this->user->getLessonsOnDate($date);

                if ($lessons_on_date->isNotEmpty()) {
                    if ($date->isFuture()) {
                        $lesson_times_ids = $lessons_on_date->pluck('lesson_time_id');
                        foreach ($week_day_lesson_times as $lesson_time) {
                            if (!$lesson_times_ids->contains($lesson_time->id)) {
                                $lessons_on_date[] = $this->createLesson($date, $lesson_time);
                            }
                        }
                    }
                } else {
                    if ($date->isFuture() || $this->isPastButDontFirst($date)) {
                        foreach ($week_day_lesson_times as $lesson_time) {
                            $lessons_on_date[] = $this->createLesson($date, $lesson_time);
                        }
                    }
                }

                $lessons_on_date = $lessons_on_date->sort(function ($a, $b) {
                    return $a['start'] <=> $b['start'];
                });

                Cache::tags("lessons_{$this->user->id}")->put("lessons_{$this->user->id}_{$date->format('Y-m-d')}", $lessons_on_date, 3600);
            }
            $this->lessons_on_days[$week_day_id] = $lessons_on_date;
        }
    }

    private function isPastButDontFirst(Carbon $date): bool
    {
        $first_lesson_time = $this->user->lessons()->orderBy('created_at')->first('created_at');
        if ($first_lesson_time) {
            return $first_lesson_time->created_at->lt($date);
        }
        return false;
    }

    private function createLesson(Carbon $date, LessonTime $lesson_time)
    {
        return Lesson::create([
            'student_id' => $lesson_time->student_id,
            'user_id' => $this->user->id,
            'student_name' => $lesson_time->student->name,
            'date' => $date,
            'start' => $lesson_time->start,
            'end' => $lesson_time->end,
            'is_paid' => false,
            'price' => getLessonPrice($lesson_time->start, $lesson_time->end, $lesson_time->student->price),
            'lesson_time_id' => $lesson_time->id,
        ]);
    }
}
