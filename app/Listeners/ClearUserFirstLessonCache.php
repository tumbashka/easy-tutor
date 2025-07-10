<?php

namespace App\Listeners;

use App\Models\Lesson;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ClearUserFirstLessonCache
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
    public function handle(Lesson $firstLesson): void
    {
        $user = $firstLesson->user;
        Cache::forget("user_first_lesson_{$user->id}}");
    }
}
