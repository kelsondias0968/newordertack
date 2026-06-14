<?php

namespace Tests\Feature;

use App\Enums\TrackingStage;
use App\Models\OrderTrack;
use App\Services\OrderTrackService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTrackProgressTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_tracking_command_advances_due_tracks(): void
    {
        $this->travelTo(now()->startOfMinute());

        $track = app(OrderTrackService::class)->create([
            'order_number' => 'AUTO-1001',
            'product_name' => 'Smart TV',
            'auto_progress' => true,
            'periods' => [
                'confirmed' => 1,
                'processing' => 1,
                'dispatched' => 1,
                'in_transit' => 2,
                'out_for_delivery' => 1,
                'delivered' => 0,
            ],
        ]);

        $this->travel(3)->hours();

        $this->artisan('tracking:advance')
            ->expectsOutput('1 track(s) processado(s).')
            ->assertSuccessful();

        $this->assertEquals(
            TrackingStage::InTransit,
            OrderTrack::query()->findOrFail($track->id)->current_stage
        );
    }
}
