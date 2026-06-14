<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderTrackIssueRequest;
use App\Models\OrderTrack;
use App\Services\TrackingCodeGenerator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class OrderTrackIssueController extends Controller
{
    public function store(
        string $trackingCode,
        StoreOrderTrackIssueRequest $request,
        TrackingCodeGenerator $trackingCodeGenerator,
    ): JsonResponse|RedirectResponse {
        $normalizedCode = $trackingCodeGenerator->normalize($trackingCode);

        abort_unless($normalizedCode, 404);

        $track = OrderTrack::query()
            ->where('tracking_code', $normalizedCode)
            ->firstOrFail();

        $track->issues()->create([
            ...$request->validated(),
            'status' => 'open',
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => __('tracking.flash.issue_submitted'),
            ], 201);
        }

        return back()->with('status', __('tracking.flash.issue_submitted'));
    }
}
