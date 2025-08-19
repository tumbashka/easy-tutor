<?php

namespace App\Listeners;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ClearUserDateLessonsCache
{
    public function handle(Model $model): void
    {
        Log::info('Clear User Date Lesson Cache');
        if ($model instanceof \App\Models\Lesson) {
            $lesson = $model;
        } else {
            return;
        }

        Log::info("очистка кэша занятий на {$lesson->date} у {$lesson->user->email}");
        $res = Cache::tags("lessons_{$lesson->user->id}")->forget("lessons_{$lesson->user->id}_{$lesson->date->format('Y-m-d')}");
    }
}
