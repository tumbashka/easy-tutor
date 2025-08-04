<?php

namespace App\Listeners;

use App\Models\Lesson;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ClearUserFirstLessonCache
{
    public function handle(Lesson $firstLesson): void
    {
        $user = $firstLesson->user;
        Cache::forget("user_first_lesson_{$user->id}}");
    }
}
