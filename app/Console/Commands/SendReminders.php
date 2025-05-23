<?php

namespace App\Console\Commands;

use App\Jobs\SendTelegramReminder;
use Illuminate\Console\Command;

class SendReminders extends Command
{
    protected $signature = 'reminders:send';

    protected $description = 'Dispatch send reminders';

    public function handle()
    {
        $this->info('Sending reminders...');
        SendTelegramReminder::dispatch();
    }
}
