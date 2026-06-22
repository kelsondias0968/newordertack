<?php

namespace App\Services;

use App\Enums\TrackingStage;
use App\Models\OrderTrack;
use App\Models\OrderTrackStage;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class OrderTrackService
{
    public function __construct(
        protected TrackingCodeGenerator $trackingCodeGenerator,
        protected OrderTrackEmailService $orderTrackEmailService,
    ) {
    }

    public function create(array $data, bool $dispatchCreatedNotification = true): OrderTrack
    {
        $track = DB::transaction(function () use ($data) {
            $placedAt = isset($data['placed_at']) ? Carbon::parse($data['placed_at']) : now();
            $currentStage = isset($data['current_stage'])
                ? TrackingStage::from($data['current_stage'])
                : TrackingStage::Confirmed;

            $track = OrderTrack::query()->create([
                'tracking_code' => $this->trackingCodeGenerator->normalize($data['tracking_code'] ?? null)
                    ?? $this->trackingCodeGenerator->generate($data['order_number'] ?? null),
                'order_number' => $data['order_number'],
                'customer_name' => $data['customer_name'] ?? null,
                'customer_email' => $data['customer_email'] ?? null,
                'customer_phone' => $data['customer_phone'] ?? null,
                'preferred_locale' => $data['preferred_locale'] ?? config('app.locale', 'en'),
                'notification_cc' => $data['notification_cc'] ?? null,
                'notification_bcc' => $data['notification_bcc'] ?? null,
                'product_name' => $data['product_name'],
                'product_image_url' => $data['product_image_url'] ?? null,
                'shipping_address' => $data['shipping_address'] ?? null,
                'notes' => $data['notes'] ?? null,
                'marketplace' => $data['marketplace'] ?? 'takealot',
                'current_stage' => TrackingStage::Confirmed,
                'auto_progress' => (bool) ($data['auto_progress'] ?? true),
                'placed_at' => $placedAt,
                'estimated_delivery_at' => $placedAt,
                'delivered_at' => null,
                'last_stage_change_at' => $placedAt,
            ]);

            $this->createStageTimeline($track, $placedAt, $this->resolvePeriods($data['periods'] ?? []));

            if ($currentStage !== TrackingStage::Confirmed) {
                $this->setStage($track->fresh(['stages']), $currentStage, now(), true, true, $data['notes'] ?? null);
            }

            $track->refresh();
            return $this->loadTrackRelations($track);
        });

        $this->orderTrackEmailService->queueTrackCreatedNotification($track, $dispatchCreatedNotification);

        return $track;
    }

    public function syncCurrentStage(OrderTrack $track): OrderTrack
    {
        $track = $this->loadTrackRelations($track);

        if ($track->auto_progress) {
            $this->advanceTrackIfDue($track);
            $track->refresh();
        }

        return $this->loadTrackRelations($track);
    }

    public function updateStage(
        OrderTrack $track,
        TrackingStage $stage,
        ?string $notes = null,
        ?bool $autoProgress = null,
    ): OrderTrack {
        $stageChanged = false;

        $updatedTrack = DB::transaction(function () use ($track, $stage, $notes, $autoProgress, &$stageChanged) {
            $track = $this->loadTrackRelations($track);

            if ($autoProgress !== null) {
                $track->forceFill([
                    'auto_progress' => $autoProgress,
                ])->save();
            }

            $stageChanged = $this->setStage($track, $stage, now(), true, true, $notes);

            return $this->loadTrackRelations($track->fresh());
        });

        if ($stageChanged) {
            $this->orderTrackEmailService->queueStageUpdatedNotification($updatedTrack, $stage, $notes);
        }

        return $updatedTrack;
    }

    public function advanceEligibleTracks(): int
    {
        $advanced = 0;

        OrderTrack::query()
            ->where('auto_progress', true)
            ->where('current_stage', '!=', TrackingStage::Delivered->value)
            ->with('stages')
            ->chunkById(50, function (EloquentCollection $tracks) use (&$advanced) {
                foreach ($tracks as $track) {
                    if ($this->advanceTrackIfDue($track)) {
                        $advanced++;
                    }
                }
            });

        return $advanced;
    }

    protected function advanceTrackIfDue(OrderTrack $track): bool
    {
        $track = $this->loadTrackRelations($track);

        if ($this->delayInTransitStageIfNeeded($track)) {
            return true;
        }

        $dueStages = $track->stages
            ->filter(fn (OrderTrackStage $stage) => $stage->position > $track->current_stage->position())
            ->filter(fn (OrderTrackStage $stage) => $stage->planned_for_at->lte(now()))
            ->sortBy('position')
            ->values();

        if ($dueStages->isEmpty()) {
            return false;
        }

        foreach ($dueStages as $dueStage) {
            $stageChanged = $this->setStage($track, $dueStage->stage_key, $dueStage->planned_for_at, false, false);
            $track = $this->loadTrackRelations($track->fresh());

            if ($stageChanged) {
                $this->orderTrackEmailService->queueStageUpdatedNotification($track, $dueStage->stage_key);
            }
        }

        return true;
    }

    protected function delayInTransitStageIfNeeded(OrderTrack $track): bool
    {
        if ($track->current_stage !== TrackingStage::InTransit) {
            return false;
        }

        $outForDeliveryStage = $track->stages->first(
            fn (OrderTrackStage $stage) => $stage->stage_key === TrackingStage::OutForDelivery
        );

        if (! $outForDeliveryStage || $outForDeliveryStage->reached_at !== null) {
            return false;
        }

        $lastDayHours = (int) config('order_track.automation.in_transit_last_day_hours', 24);

        if (! $outForDeliveryStage->planned_for_at->copy()->subHours($lastDayHours)->lte(now())) {
            return false;
        }

        $delayDays = (int) config('order_track.automation.in_transit_delay_days', 8);

        foreach ($track->stages->filter(fn (OrderTrackStage $stage) => $stage->position > TrackingStage::InTransit->position()) as $stageRecord) {
            $stageRecord->planned_for_at = $stageRecord->planned_for_at->copy()->addDays($delayDays);
            $stageRecord->save();
        }

        /** @var OrderTrackStage $deliveredStage */
        $deliveredStage = $track->stages->first(
            fn (OrderTrackStage $stage) => $stage->stage_key === TrackingStage::Delivered
        );

        $track->forceFill([
            'estimated_delivery_at' => $deliveredStage->fresh()->planned_for_at,
        ])->save();

        $refreshedTrack = $this->loadTrackRelations($track->fresh());

        $this->orderTrackEmailService->queueInTransitDelayNotification($refreshedTrack);

        return true;
    }

    protected function createStageTimeline(OrderTrack $track, CarbonInterface $placedAt, array $periods): void
    {
        $cursor = Carbon::instance($placedAt);

        foreach (TrackingStage::ordered() as $stage) {
            $durationHours = $periods[$stage->value];

            $track->stages()->create([
                'stage_key' => $stage,
                'position' => $stage->position(),
                'title' => $stage->label(),
                'description' => $stage->description(),
                'duration_hours' => $durationHours,
                'planned_for_at' => $cursor,
                'reached_at' => $stage === TrackingStage::Confirmed ? $placedAt : null,
                'manual_override' => false,
                'notes' => null,
            ]);

            $cursor = (clone $cursor)->addHours($durationHours);
        }

        $deliveredStage = $track->stages()->where('stage_key', TrackingStage::Delivered->value)->firstOrFail();

        $track->forceFill([
            'estimated_delivery_at' => $deliveredStage->planned_for_at,
        ])->save();
    }

    protected function setStage(
        OrderTrack $track,
        TrackingStage $targetStage,
        CarbonInterface $referenceTime,
        bool $manual,
        bool $rebuildFutureSchedule,
        ?string $notes = null,
    ): bool {
        $track->loadMissing('stages');

        $stageChanged = $track->current_stage !== $targetStage;
        $orderedStages = $track->stages->sortBy('position')->values();
        $targetPosition = $targetStage->position();
        $cursor = Carbon::instance($referenceTime);

        foreach ($orderedStages as $stageRecord) {
            $stageKey = $stageRecord->stage_key;

            if ($stageKey->position() < $targetPosition) {
                $stageRecord->reached_at = $stageRecord->reached_at ?? ($manual ? $referenceTime : $stageRecord->planned_for_at);
                $stageRecord->manual_override = $manual ?: $stageRecord->manual_override;
                $stageRecord->save();

                continue;
            }

            if ($stageKey === $targetStage) {
                if ($manual) {
                    $stageRecord->planned_for_at = $referenceTime;
                }

                $stageRecord->reached_at = $manual ? $referenceTime : ($stageRecord->reached_at ?? $stageRecord->planned_for_at);
                $stageRecord->manual_override = $manual;
                $stageRecord->notes = $notes ?: $stageRecord->notes;
                $stageRecord->save();

                if ($rebuildFutureSchedule) {
                    $cursor = (clone $cursor)->addHours($stageRecord->duration_hours);
                }

                continue;
            }

            if ($rebuildFutureSchedule) {
                $stageRecord->planned_for_at = $cursor;
                $stageRecord->reached_at = null;
                $stageRecord->manual_override = false;
                $stageRecord->save();

                $cursor = (clone $cursor)->addHours($stageRecord->duration_hours);
            }
        }

        /** @var OrderTrackStage $deliveredStage */
        $deliveredStage = $orderedStages->first(fn (OrderTrackStage $stage) => $stage->stage_key === TrackingStage::Delivered);

        $track->forceFill([
            'current_stage' => $targetStage,
            'estimated_delivery_at' => $deliveredStage->fresh()->planned_for_at,
            'delivered_at' => $targetStage === TrackingStage::Delivered ? $referenceTime : null,
            'last_stage_change_at' => $referenceTime,
        ])->save();

        return $stageChanged;
    }

    protected function resolvePeriods(array $periods): array
    {
        $resolved = TrackingStage::defaultDurations();

        foreach ($periods as $stage => $hours) {
            if (array_key_exists($stage, $resolved) && is_numeric($hours) && (int) $hours >= 0) {
                $resolved[$stage] = (int) $hours;
            }
        }

        return $resolved;
    }

    protected function loadTrackRelations(OrderTrack $track): OrderTrack
    {
        return $track->load([
            'stages' => fn ($query) => $query->orderBy('position'),
            'issues',
        ]);
    }
}
