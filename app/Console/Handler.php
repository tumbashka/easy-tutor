<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;

class Handler
{
    public function __invoke(Schedule $schedule): void
    {
        $schedule->command('reminders:create')->everyFiveMinutes();
        $schedule->command('reminders:send')->everyFiveMinutes();
        $schedule->command('backup:run', ['--only-db' => true, '--disable-notifications' => true])->twiceDaily();
        $schedule->command('backup:clean', ['--disable-notifications' => true])->daily();
    }
}
