<?php

namespace App\Console\Commands;

use App\Models\Lesson;
use App\Models\User;
use App\Notifications\LessonStarted;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class CheckLessonStart extends Command
{
    protected $signature = 'lessons:check-start';

    protected $description = 'Check for lessons starting now and broadcast notifications';

    public function handle(): void
    {
        $now = Carbon::now();
        $lessons = Lesson::query()
            ->with('user')
            ->whereRelation('user', 'is_active', true)
            ->whereToday('date')
            ->where('start', '<=', $now->copy()->addMinutes(1))
            ->where('start', '>=', $now->toTimeString())
                ->orderBy('start')
            ->get();

        foreach ($lessons as $lesson) {
            $user = $lesson->user;
            /** @var $user User*/

            $user->notify(new LessonStarted($lesson));
//            event(new LessonStarted($lesson));
        }

        $this->info('Checked for starting lessons at ' . $now. '. Found lessons: ' . $lessons->count());
        Log::info('Checked for starting lessons at ' . $now. '. Found lessons: ' . $lessons->count());
    }
}
