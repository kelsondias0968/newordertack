<?php

namespace App\Http\Controllers;

use App\Models\OrderTrack;
use App\Services\OrderTrackService;
use App\Services\TrackingCodeGenerator;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderTrackingController extends Controller
{
    public function index(Request $request): View
    {
        $generatedTrack = null;

        if ($request->filled('generated')) {
            $generatedTrack = OrderTrack::query()
                ->where('tracking_code', app(TrackingCodeGenerator::class)->normalize($request->string('generated')->toString()))
                ->first();
        }

        return view('tracking.index', [
            'generatedTrack' => $generatedTrack,
        ]);
    }

    public function lookup(Request $request, TrackingCodeGenerator $trackingCodeGenerator): RedirectResponse
    {
        $validated = $request->validate([
            'tracking_code' => ['required', 'string', 'max:40'],
        ]);

        return redirect()->route('tracking.show', $trackingCodeGenerator->normalize($validated['tracking_code']));
    }

    public function show(
        string $trackingCode,
        TrackingCodeGenerator $trackingCodeGenerator,
        OrderTrackService $orderTrackService,
    ) {
        $normalizedCode = $trackingCodeGenerator->normalize($trackingCode);

        if (! $normalizedCode) {
            return $this->notFoundResponse($trackingCode);
        }

        $track = OrderTrack::query()
            ->where('tracking_code', $normalizedCode)
            ->first();

        if (! $track) {
            return $this->notFoundResponse($normalizedCode);
        }

        $track = $orderTrackService->syncCurrentStage($track);

        return view('tracking.show', [
            'track' => $track,
            'issueTypes' => \App\Http\Requests\StoreOrderTrackIssueRequest::issueTypes(),
        ]);
    }

    protected function notFoundResponse(string $trackingCode): Response
    {
        return response()->view('tracking.not-found', [
            'trackingCode' => $trackingCode,
        ], 404);
    }
}
