<?php

namespace App\Console\Commands;

use App\Jobs\ProcessTelegramUpdate;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Update;

class TelegramPolling extends Command
{
    protected $signature = 'telegram:poll';
    protected $description = 'Poll Telegram updates and dispatch them to queue';

    public function handle(Api $telegram): void
    {
        $this->info('Starting Telegram polling...');
        $lastUpdateId = 0;
        while (true) {
            try {
                $updates = $telegram->getUpdates([
                    'offset' => $lastUpdateId + 1,
                    'timeout' => 30,
                ]);

                if (empty($updates)) {
                    sleep(1);
                    continue;
                }

                foreach ($updates as $update) {
                    $this->info('Dispatch update');
                    ProcessTelegramUpdate::dispatch($update);
                    $lastUpdateId = $update->updateId;
                }

                sleep(1);
            } catch (\Exception $e) {
                $this->error('Error: ' . $e->getMessage());
                sleep(5);
            }
        }
    }
}
