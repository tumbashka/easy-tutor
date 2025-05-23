<?php

namespace App\Listeners;

use App\Services\LessonTimeService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class UpdateLessonTimeLessons
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Model $model): void
    {
        Log::info('Update Lesson Time Lessons');
        if ($model instanceof \App\Models\LessonTime) {
            $lessonTime = $model;
        } else {
            return;
        }

        Log::info("обновление времени занятия {$lessonTime->id} у {$lessonTime->student->user->email}");
        $service = app(LessonTimeService::class);
        $service->updateFutureLessons($lessonTime);
    }
}
