<?php

namespace App\Listeners;

use App\Events\Lesson\LessonAdded;
use App\Events\Lesson\LessonUpdated;
use Illuminate\Support\Facades\Cache;

class ClearUserDateLessonsCache
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
    public function handle(LessonAdded|LessonUpdated $event): void
    {
        $lesson = $event->lesson;
        $user = $event->user;
        $res = Cache::tags("lessons_{$user->id}")->forget("lessons_{$user->id}_{$lesson->date}");
    }
}
