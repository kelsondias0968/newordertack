<?php

namespace Tests\Feature;

use App\Services\OrderTrackService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTrackIssueTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_customer_can_submit_an_issue_for_a_track(): void
    {
        $track = app(OrderTrackService::class)->create([
            'order_number' => 'ISSUE-2002',
            'product_name' => 'Wireless Headphones',
        ]);

        $response = $this->post("/track/{$track->tracking_code}/issues", [
            'full_name' => 'Carlos Tavares',
            'email' => 'carlos@example.com',
            'phone' => '923000000',
            'issue_type' => 'delivery_delay',
            'description' => 'A encomenda ainda nao chegou e o prazo inicial ja terminou.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('order_track_issues', [
            'order_track_id' => $track->id,
            'issue_type' => 'delivery_delay',
            'status' => 'open',
        ]);
    }

    public function test_a_customer_can_submit_an_issue_for_a_track_via_json(): void
    {
        $track = app(OrderTrackService::class)->create([
            'order_number' => 'ISSUE-2003',
            'product_name' => 'Wireless Speaker',
        ]);

        $response = $this->postJson("/track/{$track->tracking_code}/issues", [
            'full_name' => 'Ana Reis',
            'email' => 'ana@example.com',
            'phone' => '923000123',
            'issue_type' => 'wrong_status',
            'description' => 'O estado apresentado nao corresponde ao estado real da entrega.',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('message', 'Your complaint has been submitted successfully.');

        $this->assertDatabaseHas('order_track_issues', [
            'order_track_id' => $track->id,
            'issue_type' => 'wrong_status',
            'status' => 'open',
        ]);
    }

    public function test_issue_submission_returns_json_validation_errors_for_ajax_requests(): void
    {
        $track = app(OrderTrackService::class)->create([
            'order_number' => 'ISSUE-2004',
            'product_name' => 'Smart Watch',
        ]);

        $response = $this->postJson("/track/{$track->tracking_code}/issues", [
            'full_name' => '',
            'email' => 'invalid-email',
            'issue_type' => '',
            'description' => 'short',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['full_name', 'email', 'issue_type', 'description']);
    }
}
