<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTrackApiTest extends TestCase
{
    use RefreshDatabase;

    protected string $trackingToken;

    protected function setUp(): void
    {
        parent::setUp();

        $this->trackingToken = (string) config('order_track.api_token');
    }

    public function test_it_creates_a_track_via_api_and_returns_the_public_link(): void
    {
        $response = $this
            ->withHeader('tracking_token', $this->trackingToken)
            ->postJson('/api/tracks', [
                'order_number' => 'PG-411759',
                'customer_name' => 'Maria Campos',
                'customer_email' => 'maria@example.com',
                'product_name' => 'Berlinger Haus Cookware Set',
                'shipping_address' => 'Rua da Logistica, Luanda',
                'current_stage' => 'processing',
                'periods' => [
                    'confirmed' => 2,
                    'processing' => 6,
                    'dispatched' => 8,
                    'in_transit' => 24,
                    'out_for_delivery' => 4,
                    'delivered' => 0,
                ],
            ]);

        $response
            ->assertCreated()
            ->assertJsonPath('message', 'Track created successfully.')
            ->assertJsonPath('data.order_number', 'PG-411759')
            ->assertJsonPath('data.current_stage.key', 'processing')
            ->assertJsonPath('data.current_stage.label', 'Processing');

        $this->assertDatabaseHas('order_tracks', [
            'order_number' => 'PG-411759',
            'current_stage' => 'processing',
        ]);

        $this->assertDatabaseCount('order_track_stages', 6);
        $this->assertNotEmpty($response->json('data.tracking_url'));
    }

    public function test_it_rejects_api_requests_without_a_valid_tracking_token(): void
    {
        $this->postJson('/api/tracks', [])
            ->assertUnauthorized()
            ->assertJsonPath('message', 'Invalid tracking token.');

        $this->withHeader('tracking_token', 'wrong-token')
            ->getJson('/api/tracks/PG-411759')
            ->assertUnauthorized()
            ->assertJsonPath('message', 'Invalid tracking token.');
    }
}
