<?php

namespace App\Listeners;

use App\Models\LessonTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ClearUserLessonTimesCache
{
    public function handle(LessonTime $lessonTime): void
    {
        $weekDayId = $lessonTime->week_day;
        $student = $lessonTime->student;
        $user = $student->user;

        Log::info('очистка кэша LessonTime');
        Cache::tags(["lesson_dates_{$user->id}"])->flush();
    }
}
