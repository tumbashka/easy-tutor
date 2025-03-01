<?php

namespace App\Listeners;

use App\Events\Lesson\LessonAdded;
use App\Events\Lesson\LessonUpdated;
use Illuminate\Support\Facades\Cache;

class ClearDateLessonsCache
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
        Cache::forget("lessons_{$user->id}_{$lesson->date->format('Y-m-d')}");
    }
}
