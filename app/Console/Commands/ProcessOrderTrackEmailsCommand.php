<?php

namespace App\Console\Commands;

use App\Services\OrderTrackEmailService;
use Illuminate\Console\Command;

class ProcessOrderTrackEmailsCommand extends Command
{
    protected $signature = 'tracking:emails:process {--limit=50}';

    protected $description = 'Process pending and failed tracking notification emails.';

    public function handle(OrderTrackEmailService $orderTrackEmailService): int
    {
        $processed = $orderTrackEmailService->processPendingEmails((int) $this->option('limit'));

        $this->info("{$processed} email(s) processado(s).");

        return self::SUCCESS;
    }
}
