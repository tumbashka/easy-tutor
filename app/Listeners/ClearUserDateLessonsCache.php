<?php

namespace App\Listeners;

use App\Events\Lesson\LessonAdded;
use App\Events\Lesson\LessonUpdated;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

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
    public function handle(Model $model): void
    {
        Log::info('Clear User Date Lesson Cache');
        if ($model instanceof \App\Models\Lesson) {
            $lesson = $model;
        } else {
            return;
        }

        Log::info("очистка кэша занятий на {$lesson->date} у {$lesson->user->email}");
        Cache::tags("lessons_{$lesson->user->id}")->forget("lessons_{$lesson->user->id}_{$lesson->date}");
    }
}
