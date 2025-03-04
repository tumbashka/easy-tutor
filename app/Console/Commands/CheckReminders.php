<?php

namespace App\Console\Commands;

use App\Jobs\SendEventReminder;
use Illuminate\Console\Command;

class CheckReminders extends Command
{
    protected $signature = 'telegram:check-reminders';
    protected $description = 'Check events and dispatch reminders';

    public function handle()
    {
        $this->info('Checking reminders...');
//        SendEventReminder::dispatch();
    }
}
