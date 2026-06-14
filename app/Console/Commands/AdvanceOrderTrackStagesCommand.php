<?php

namespace App\Console\Commands;

use App\Jobs\ProcessOrderTrackAutomationJob;
use Illuminate\Console\Command;

class AdvanceOrderTrackStagesCommand extends Command
{
    protected $signature = 'tracking:advance';

    protected $description = 'Advance automatic tracking stages whose planned period has elapsed.';

    public function handle(): int
    {
        $advanced = (int) app()->call([new ProcessOrderTrackAutomationJob(), 'handle']);

        $this->info("{$advanced} track(s) processado(s).");

        return self::SUCCESS;
    }
}
