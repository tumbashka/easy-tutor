<?php

namespace App\Listeners;

use App\Events\FreeTime\FreeTimeAdded;
use App\Events\FreeTime\FreeTimeDeleted;
use App\Events\FreeTime\FreeTimeUpdated;
use App\Events\LessonTime\LessonTimeAdded;
use App\Events\LessonTime\LessonTimeDeleted;
use App\Events\LessonTime\LessonTimeUpdated;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ClearUserAllLessonSlotsCache
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
        Log::info('Clear User All Lesson Slots Cache');
        if ($model instanceof \App\Models\FreeTime) {
            $user = $model->user;
        } elseif ($model instanceof \App\Models\LessonTime) {
            $user = $model->student->user;
        } else {
            return;
        }

        Log::info("очистка кэша таблицы занятий и окон у " . $user->email);
        Cache::forget("all_lesson_slots_{$user->id}");
    }
}
