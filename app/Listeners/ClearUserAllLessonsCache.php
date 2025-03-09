<?php

namespace App\Listeners;

use App\Events\LessonTime\LessonTimeAdded;
use App\Events\LessonTime\LessonTimeDeleted;
use App\Events\LessonTime\LessonTimeUpdated;
use App\Events\Student\StudentDeleted;
use App\Events\Student\StudentUpdated;
use Illuminate\Database\Eloquent\Model;
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
    public function handle(Model $model): void
    {
        Log::info('Clear User All Lessons Cache');
        if ($model instanceof \App\Models\LessonTime) {
            $user = $model->student->user;
        } else {
            return;
        }

        Log::info("очистка кэша занятий у " . $user->email);
        Cache::tags(["lessons_{$user->id}"])->flush();
    }
}
