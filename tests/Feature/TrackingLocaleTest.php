<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrackingLocaleTest extends TestCase
{
    use RefreshDatabase;

    public function test_tracking_pages_default_to_english(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSeeText('Track your order');
        $response->assertDontSeeText('Rastrear encomenda');
    }

    public function test_tracking_pages_can_switch_to_portuguese(): void
    {
        $response = $this->get('/?lang=pt');

        $response->assertOk();
        $response->assertSeeText('Rastrear encomenda');
        $response->assertSessionHas('locale', 'pt');
    }
}
