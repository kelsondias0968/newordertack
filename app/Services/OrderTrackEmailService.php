<?php

namespace App\Services;

use App\Enums\OrderTrackEmailStatus;
use App\Enums\OrderTrackEmailType;
use App\Enums\TrackingStage;
use App\Jobs\SendOrderTrackEmailJob;
use App\Models\OrderTrack;
use App\Models\OrderTrackEmail;
use App\Models\OrderTrackStage;
use Illuminate\Support\Facades\App;
use Throwable;

class OrderTrackEmailService
{
    public function queueTrackCreatedNotification(OrderTrack $track, bool $dispatch = true): ?OrderTrackEmail
    {
        return $this->queueNotification($track, OrderTrackEmailType::TrackCreated, $track->current_stage, $track->notes, $dispatch);
    }

    public function queueStageUpdatedNotification(
        OrderTrack $track,
        TrackingStage $stage,
        ?string $notes = null,
        bool $dispatch = true,
    ): ?OrderTrackEmail {
        return $this->queueNotification($track, OrderTrackEmailType::StageUpdated, $stage, $notes, $dispatch);
    }

    public function queueInTransitDelayNotification(
        OrderTrack $track,
        bool $dispatch = true,
    ): ?OrderTrackEmail {
        return $this->queueNotification($track, OrderTrackEmailType::InTransitDelay, TrackingStage::InTransit, null, $dispatch);
    }

    public function dispatchQueuedEmailAsync(OrderTrackEmail $emailLog): void
    {
        SendOrderTrackEmailJob::dispatch($emailLog->id)
            ->onConnection((string) config('order_track.email_dispatch.connection', config('queue.default')));
    }

    public function processPendingEmails(int $limit = 50): int
    {
        $emailIds = OrderTrackEmail::query()
            ->whereIn('status', [
                OrderTrackEmailStatus::Pending->value,
                OrderTrackEmailStatus::Failed->value,
            ])
            ->orderBy('id')
            ->limit($limit)
            ->pluck('id');

        foreach ($emailIds as $emailId) {
            try {
                SendOrderTrackEmailJob::dispatchSync($emailId);
            } catch (Throwable $exception) {
                report($exception);
            }
        }

        return $emailIds->count();
    }

    protected function queueNotification(
        OrderTrack $track,
        OrderTrackEmailType $type,
        TrackingStage $stage,
        ?string $notes = null,
        bool $dispatch = true,
    ): ?OrderTrackEmail {
        $track->loadMissing('stages');

        if (! $track->customer_email) {
            return null;
        }

        $locale = $this->resolveLocale($track->preferred_locale);
        $trackingUrl = route('tracking.show', [
            'trackingCode' => $track->tracking_code,
            'lang' => $locale,
        ]);
        $stageRecord = $track->stages->first(
            fn (OrderTrackStage $item) => $item->stage_key === $stage
        );
        $branding = $this->resolveEmailBranding($track);

        $content = $this->renderContent($track, $type, $stage, $stageRecord, $trackingUrl, $locale, $notes, $branding);

        $emailLog = $track->emails()->create([
            'order_track_stage_id' => $stageRecord?->id,
            'notification_type' => $type,
            'stage_key' => $stage,
            'locale' => $locale,
            'status' => OrderTrackEmailStatus::Pending,
            'mailer' => config('mail.default'),
            'recipient_email' => $track->customer_email,
            'recipient_name' => $track->customer_name,
            'cc' => $track->notification_cc,
            'bcc' => $track->notification_bcc,
            'subject' => $content['subject'],
            'body_html' => $content['body_html'],
            'body_text' => $content['body_text'],
            'meta' => [
                'tracking_code' => $track->tracking_code,
                'tracking_url' => $trackingUrl,
                'order_number' => $track->order_number,
                'product_name' => $track->product_name,
                'stage_label' => $this->withLocale($locale, fn () => $stage->label()),
                'stage_description' => $this->withLocale($locale, fn () => $stage->description()),
                'notes' => $notes,
                'branding' => $branding,
            ],
            'queued_at' => now(),
        ]);

        if ($dispatch) {
            $this->dispatchQueuedEmailAsync($emailLog);
        }

        return $emailLog;
    }

    protected function renderContent(
        OrderTrack $track,
        OrderTrackEmailType $type,
        TrackingStage $stage,
        ?OrderTrackStage $stageRecord,
        string $trackingUrl,
        string $locale,
        ?string $notes = null,
        array $branding = [],
    ): array {
        return $this->withLocale($locale, function () use ($track, $type, $stage, $stageRecord, $trackingUrl, $locale, $notes, $branding) {
            $subject = match ($type) {
                OrderTrackEmailType::TrackCreated => __('tracking.emails.track_created.subject', [
                    'order' => $track->order_number,
                ]),
                OrderTrackEmailType::StageUpdated => __('tracking.emails.stage_updated.subject', [
                    'order' => $track->order_number,
                    'stage' => $stage->label(),
                ]),
                OrderTrackEmailType::InTransitDelay => __('tracking.emails.in_transit_delay.subject', [
                    'order' => $track->order_number,
                ]),
            };

            $viewData = [
                'emailType' => $type,
                'track' => $track,
                'stage' => $stage,
                'stageRecord' => $stageRecord,
                'trackingUrl' => $trackingUrl,
                'locale' => $locale,
                'notes' => $notes,
                'branding' => $branding,
            ];

            return [
                'subject' => $subject,
                'body_html' => view('emails.order-track-template', $viewData)->render(),
                'body_text' => view('emails.order-track-template-text', $viewData)->render(),
            ];
        });
    }

    protected function resolveLocale(?string $locale): string
    {
        return in_array($locale, ['en', 'pt'], true)
            ? $locale
            : config('app.locale', 'en');
    }

    protected function withLocale(string $locale, callable $callback): mixed
    {
        $originalLocale = App::currentLocale();

        App::setLocale($locale);

        try {
            return $callback();
        } finally {
            App::setLocale($originalLocale);
        }
    }

    protected function resolveEmailBranding(OrderTrack $track): array
    {
        $marketplace = $track->marketplace ?? \App\Enums\Marketplace::Takealot;
        $branding = $marketplace->branding();

        return [
            'logo_url' => $branding['logo'] ?: rtrim((string) config('app.url'), '/').'/assets/logod.png',
            'contact'  => $branding['contact'],
            'email'    => $branding['email'],
            'address'  => $branding['address'],
            'color'    => $branding['color'],
            'name'     => $branding['name'],
        ];
    }
}
