<?php

namespace App\Jobs;

use App\Services\OrderTrackService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessOrderTrackAutomationJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 1;

    public function handle(OrderTrackService $orderTrackService): int
    {
        return $orderTrackService->advanceEligibleTracks();
    }
}
