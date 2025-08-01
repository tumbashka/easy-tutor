<?php

namespace App\Listeners;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ClearUserAllLessonsCache
{
    public function handle(Model $model): void
    {
        Log::info('Clear User All Lessons Cache');
        if ($model instanceof \App\Models\LessonTime) {
            $user = $model->student->user;
        } elseif ($model instanceof \App\Models\Student) {
            $user = $model->user;
        } else {
            return;
        }

        Log::info('очистка кэша занятий у '.$user->email);
        Cache::tags(["lessons_{$user->id}"])->flush();
    }
}
