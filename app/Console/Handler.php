<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Log;

class Handler
{
    public function __invoke(Schedule $schedule): void
    {
//        $schedule->command('telegram:check-reminders')->everyMinute();
//        Log::debug('Выполняется schedule');
    }
}
