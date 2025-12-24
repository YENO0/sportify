<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\EventStatusService;

class RefreshEventStatuses extends Command
{
    protected $signature = 'events:refresh-statuses';

    protected $description = 'Refresh event lifecycle and registration statuses based on dates and capacity.';

    public function handle(): int
    {
        $this->info('Refreshing event statuses...');
        EventStatusService::syncAll();
        $this->info('Done.');

        return self::SUCCESS;
    }
}

