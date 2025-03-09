<?php

namespace App\Listeners;

use App\Events\LessonTime\LessonTimeAdded;
use App\Events\LessonTime\LessonTimeDeleted;
use App\Events\LessonTime\LessonTimeUpdated;
use App\Events\Student\StudentDeleted;
use App\Events\Student\StudentUpdated;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ClearUserAllLessonsCache
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
    public function handle(LessonTimeDeleted|LessonTimeUpdated|LessonTimeAdded|StudentDeleted|StudentUpdated $event): void
    {
        $user = $event->user;
        Cache::tags(["lessons_{$user->id}"])->flush();
    }
}
