<?php

namespace Tests\Feature;

use App\Jobs\SendOrderTrackEmailJob;
use App\Mail\OrderTrackNotificationMail;
use App\Models\OrderTrackEmail;
use App\Services\OrderTrackService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class OrderTrackEmailNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_track_creation_dispatches_the_created_email_after_response(): void
    {
        Bus::fake();

        $response = $this
            ->withHeader('tracking_token', (string) config('order_track.api_token'))
            ->postJson('/api/tracks', [
                'order_number' => 'MAIL-1001',
                'customer_name' => 'Maria Campos',
                'customer_email' => 'maria@example.com',
                'preferred_locale' => 'pt',
                'product_name' => 'Berlinger Haus Cookware Set',
                'shipping_address' => 'Rua da Logistica, Luanda',
            ]);

        $response->assertCreated();

        $this->assertDatabaseHas('order_tracks', [
            'order_number' => 'MAIL-1001',
            'preferred_locale' => 'pt',
        ]);

        $this->assertDatabaseHas('order_track_emails', [
            'recipient_email' => 'maria@example.com',
            'notification_type' => 'track_created',
            'status' => 'pending',
            'locale' => 'pt',
        ]);

        Bus::assertDispatchedAfterResponse(SendOrderTrackEmailJob::class, 1);
    }

    public function test_email_job_sends_the_created_notification_in_portuguese(): void
    {
        config(['queue.default' => 'sync']);
        config([
            'order_track.email_branding.logo_url' => 'https://cdn.example.com/logo.png',
            'order_track.email_branding.contact' => 'Support Team',
            'order_track.email_branding.email' => 'support@example.com',
            'order_track.email_branding.address' => 'Luanda, Angola',
        ]);
        Mail::fake();

        app(OrderTrackService::class)->create([
            'order_number' => 'MAIL-PT-2001',
            'customer_name' => 'Ana Silva',
            'customer_email' => 'ana@example.com',
            'preferred_locale' => 'pt',
            'product_name' => 'Smart TV',
            'shipping_address' => 'Maputo',
        ]);

        Mail::assertSent(OrderTrackNotificationMail::class, 1);

        $emailLog = OrderTrackEmail::query()->sole();

        $this->assertSame('sent', $emailLog->status->value);
        $this->assertStringContainsString('O seu codigo de tracking', $emailLog->subject);
        $this->assertStringContainsString('Parabens pela sua encomenda.', $emailLog->body_text);
        $this->assertStringContainsString('https://cdn.example.com/logo.png', $emailLog->body_html);
        $this->assertStringContainsString('Support Team', $emailLog->body_text);
        $this->assertStringContainsString('support@example.com', $emailLog->body_text);
        $this->assertStringContainsString('Luanda, Angola', $emailLog->body_text);
        $this->assertNotNull($emailLog->sent_at);
    }

    public function test_pending_notifications_can_be_processed_by_the_email_command(): void
    {
        Mail::fake();

        app(OrderTrackService::class)->create([
            'order_number' => 'MAIL-PENDING-4001',
            'customer_name' => 'Carlos Mendes',
            'customer_email' => 'carlos@example.com',
            'preferred_locale' => 'pt',
            'product_name' => 'Frigorifico',
        ], false);

        $this->assertDatabaseHas('order_track_emails', [
            'recipient_email' => 'carlos@example.com',
            'status' => 'pending',
        ]);

        $this->artisan('tracking:emails:process')
            ->expectsOutput('1 email(s) processado(s).')
            ->assertSuccessful();

        $emailLog = OrderTrackEmail::query()->sole();

        $this->assertSame('sent', $emailLog->status->value);
        $this->assertNotNull($emailLog->sent_at);
        Mail::assertSent(OrderTrackNotificationMail::class, 1);
    }

    public function test_automatic_progression_sends_an_email_for_each_due_stage(): void
    {
        config(['queue.default' => 'sync']);
        Mail::fake();

        $this->travelTo(now()->startOfMinute());

        app(OrderTrackService::class)->create([
            'order_number' => 'AUTO-MAIL-3001',
            'customer_name' => 'John Smith',
            'customer_email' => 'john@example.com',
            'preferred_locale' => 'en',
            'product_name' => 'Laptop',
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

        $this->artisan('tracking:advance')->assertSuccessful();

        $this->assertDatabaseHas('order_track_emails', [
            'notification_type' => 'stage_updated',
            'stage_key' => 'processing',
            'status' => 'sent',
        ]);

        $this->assertDatabaseHas('order_track_emails', [
            'notification_type' => 'stage_updated',
            'stage_key' => 'dispatched',
            'status' => 'sent',
        ]);

        $this->assertDatabaseHas('order_track_emails', [
            'notification_type' => 'stage_updated',
            'stage_key' => 'in_transit',
            'status' => 'sent',
        ]);

        $this->assertSame(3, OrderTrackEmail::query()->where('notification_type', 'stage_updated')->count());
        Mail::assertSent(OrderTrackNotificationMail::class, 4);
    }

    public function test_in_transit_last_day_adds_eight_days_and_sends_a_delay_email_in_a_loop(): void
    {
        config(['queue.default' => 'sync']);
        Mail::fake();

        $this->travelTo(now()->startOfMinute());

        $track = app(OrderTrackService::class)->create([
            'order_number' => 'DELAY-5001',
            'customer_name' => 'Pedro Gomes',
            'customer_email' => 'pedro@example.com',
            'preferred_locale' => 'pt',
            'product_name' => 'Air Fryer',
            'auto_progress' => true,
            'periods' => [
                'confirmed' => 1,
                'processing' => 1,
                'dispatched' => 1,
                'in_transit' => 24,
                'out_for_delivery' => 1,
                'delivered' => 0,
            ],
        ]);

        $this->travel(3)->hours();

        $this->artisan('tracking:advance')->assertSuccessful();

        $track = $track->fresh(['stages']);
        $originalOutForDelivery = $track->stages->firstWhere('stage_key', 'out_for_delivery')->planned_for_at->copy();
        $originalDelivered = $track->stages->firstWhere('stage_key', 'delivered')->planned_for_at->copy();

        $this->travel(2)->minutes();

        $this->artisan('tracking:advance')->assertSuccessful();

        $track = $track->fresh(['stages', 'emails']);

        $this->assertSame('in_transit', $track->current_stage->value);
        $this->assertTrue($track->estimated_delivery_at->equalTo($originalDelivered->copy()->addDays(8)));
        $this->assertTrue(
            $track->stages->firstWhere('stage_key', 'out_for_delivery')->planned_for_at->equalTo(
                $originalOutForDelivery->copy()->addDays(8)
            )
        );
        $this->assertDatabaseHas('order_track_emails', [
            'notification_type' => 'in_transit_delay',
            'recipient_email' => 'pedro@example.com',
            'locale' => 'pt',
        ]);

        $this->travel(8)->days();

        $this->artisan('tracking:advance')->assertSuccessful();

        $track = $track->fresh(['stages', 'emails']);

        $this->assertSame('in_transit', $track->current_stage->value);
        $this->assertTrue($track->estimated_delivery_at->equalTo($originalDelivered->copy()->addDays(16)));
        $this->assertSame(
            2,
            OrderTrackEmail::query()->where('notification_type', 'in_transit_delay')->count()
        );
        Mail::assertSent(OrderTrackNotificationMail::class, 6);
    }
}
