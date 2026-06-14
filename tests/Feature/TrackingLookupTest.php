<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrackingLookupTest extends TestCase
{
    use RefreshDatabase;

    public function test_unknown_tracking_code_shows_custom_not_found_page(): void
    {
        $response = $this->get('/track/UNKNOWN-404');

        $response->assertNotFound();
        $response->assertSeeText('Tracking code not found.');
        $response->assertSeeText('UNKNOWN-404');
    }

    public function test_tracking_lookup_requires_the_code_field(): void
    {
        $response = $this->from('/')->post('/track/lookup', [
            'tracking_code' => '',
        ]);

        $response->assertRedirect('/');
        $response->assertSessionHasErrors('tracking_code');
    }
}
