<?php

namespace App\Listeners;

use App\Events\LessonTime\LessonTimeUpdated;

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
    public function handle(LessonTimeUpdated $event): void
    {
        $lesson_time = $event->lessonTime;
        $lesson_time->updateLessons();
    }
}
