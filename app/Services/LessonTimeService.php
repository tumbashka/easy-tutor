<?php

namespace App\Services;

use App\Models\LessonTime;
use App\Models\User;
use Illuminate\Support\Carbon;

class LessonTimeService
{
    private User $user;

    public function __construct(?User $user = null)
    {
        $this->user = $user ?? auth()->user();
    }

    public function updateFutureLessons(LessonTime $lessonTime): void
    {
        $lessonTime->load('subject');

        $futureLessons = $this->getFutureLessonTimeLessons($lessonTime);
        $futureLessons->each(function ($lesson) use ($lessonTime) {
            $lesson->start = $lessonTime->start;
            $lesson->end = $lessonTime->end;
            $lesson->price = getLessonPrice($lessonTime->start, $lessonTime->end, $lesson->student->price);
            $lesson->subject_id = $lessonTime->subject?->id;
            $lesson->subject_name = $lessonTime->subject?->name;
            $lesson->save();
        });
    }

    public function getFutureLessonTimeLessons(LessonTime $lessonTime)
    {
        $fromTomorrowFutureLessons = $lessonTime->lessons()
            ->where('date', '>', now())
            ->where('user_id', auth()->user()->id)
            ->get();

        $todayFutureLessons = $lessonTime->lessons()
            ->where('date', now()->format('Y-m-d'))
            ->where('start', '>', now()->format('H:i:s'))
            ->where('user_id', auth()->user()->id)
            ->get();

        return $fromTomorrowFutureLessons->concat($todayFutureLessons);
    }
}
