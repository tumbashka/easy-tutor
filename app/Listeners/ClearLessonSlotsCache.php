<?php

namespace App\Listeners;

use App\Events\FreeTime\FreeTimeAdded;
use App\Events\FreeTime\FreeTimeDeleted;
use App\Events\FreeTime\FreeTimeUpdated;
use App\Events\LessonTime\LessonTimeAdded;
use App\Events\LessonTime\LessonTimeDeleted;
use App\Events\LessonTime\LessonTimeUpdated;
use Illuminate\Support\Facades\Cache;

class ClearLessonSlotsCache
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
    public function handle(FreeTimeDeleted|FreeTimeUpdated|FreeTimeAdded|LessonTimeDeleted|LessonTimeUpdated|LessonTimeAdded $event): void
    {
        $user = $event->user;
        Cache::forget("all_lesson_slots_{$user->id}");
    }
}
