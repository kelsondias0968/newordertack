<?php

namespace App\Http\Controllers\Api;

use App\Enums\TrackingStage;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderTrackRequest;
use App\Models\OrderTrack;
use App\Models\OrderTrackStage;
use App\Services\OrderTrackEmailService;
use App\Services\OrderTrackService;
use App\Services\TrackingCodeGenerator;
use Illuminate\Http\JsonResponse;

class OrderTrackApiController extends Controller
{
    public function store(
        StoreOrderTrackRequest $request,
        OrderTrackService $orderTrackService,
        OrderTrackEmailService $orderTrackEmailService,
    ): JsonResponse
    {
        $track = $orderTrackService->create($request->validated(), dispatchCreatedNotification: false);
        $orderTrackEmailService->dispatchTrackCreatedNotificationAfterResponse($track);

        return response()->json([
            'message' => __('tracking.api.track_created'),
            'data' => $this->transformTrack($track),
        ], 201);
    }

    public function show(
        string $trackingCode,
        TrackingCodeGenerator $trackingCodeGenerator,
        OrderTrackService $orderTrackService,
    ): JsonResponse {
        $normalizedCode = $trackingCodeGenerator->normalize($trackingCode);

        abort_unless($normalizedCode, 404);

        $track = OrderTrack::query()
            ->where('tracking_code', $normalizedCode)
            ->firstOrFail();

        $track = $orderTrackService->syncCurrentStage($track);

        return response()->json([
            'data' => $this->transformTrack($track),
        ]);
    }

    protected function transformTrack(OrderTrack $track): array
    {
        $currentStagePosition = $track->current_stage->position();

        return [
            'tracking_code' => $track->tracking_code,
            'tracking_url' => route('tracking.show', $track->tracking_code),
            'order_number' => $track->order_number,
            'customer_name' => $track->customer_name,
            'customer_email' => $track->customer_email,
            'customer_phone' => $track->customer_phone,
            'preferred_locale' => $track->preferred_locale,
            'product_name' => $track->product_name,
            'product_image_url' => $track->product_image_url,
            'shipping_address' => $track->shipping_address,
            'auto_progress' => $track->auto_progress,
            'placed_at' => $track->placed_at?->toIso8601String(),
            'estimated_delivery_at' => $track->estimated_delivery_at?->toIso8601String(),
            'delivered_at' => $track->delivered_at?->toIso8601String(),
            'current_stage' => [
                'key' => $track->current_stage->value,
                'label' => $track->current_stage->label(),
                'description' => $track->current_stage->description(),
            ],
            'stages' => $track->stages->map(function (OrderTrackStage $stage) use ($currentStagePosition) {
                return [
                    'key' => $stage->stage_key->value,
                    'label' => $stage->title,
                    'description' => $stage->description,
                    'duration_hours' => $stage->duration_hours,
                    'planned_for_at' => $stage->planned_for_at?->toIso8601String(),
                    'reached_at' => $stage->reached_at?->toIso8601String(),
                    'state' => $this->stageState($stage, $currentStagePosition),
                    'manual_override' => $stage->manual_override,
                    'notes' => $stage->notes,
                ];
            })->values()->all(),
        ];
    }

    protected function stageState(OrderTrackStage $stage, int $currentStagePosition): string
    {
        if ($stage->position < $currentStagePosition) {
            return 'completed';
        }

        if ($stage->position === $currentStagePosition) {
            return 'current';
        }

        return 'pending';
    }
}
