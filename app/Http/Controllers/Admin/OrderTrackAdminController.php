<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TrackingStage;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderTrackRequest;
use App\Http\Requests\UpdateOrderTrackStageRequest;
use App\Models\OrderTrack;
use App\Services\OrderTrackService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderTrackAdminController extends Controller
{
    public function index(Request $request): View
    {
        $query = OrderTrack::query()->latest();

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();

            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('tracking_code', 'like', "%{$search}%")
                    ->orWhere('order_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('product_name', 'like', "%{$search}%");
            });
        }

        return view('admin.tracks.index', [
            'tracks' => $query->paginate(12)->withQueryString(),
            'stageOptions' => TrackingStage::options(),
            'defaultPeriods' => TrackingStage::defaultDurations(),
            'localeOptions' => ['en' => 'EN', 'pt' => 'PT'],
        ]);
    }

    public function store(StoreOrderTrackRequest $request, OrderTrackService $orderTrackService): RedirectResponse
    {
        $track = $orderTrackService->create($request->validated());

        return redirect()
            ->route('admin.tracks.show', $track)
            ->with('status', __('tracking.flash.track_created'));
    }

    public function show(OrderTrack $track, OrderTrackService $orderTrackService): View
    {
        $track = $orderTrackService->syncCurrentStage($track);
        $track->load('emails');

        return view('admin.tracks.show', [
            'track' => $track,
            'stageOptions' => TrackingStage::options(),
        ]);
    }

    public function updateStage(
        UpdateOrderTrackStageRequest $request,
        OrderTrack $track,
        OrderTrackService $orderTrackService,
    ): RedirectResponse {
        $orderTrackService->updateStage(
            $track,
            TrackingStage::from($request->validated('stage')),
            $request->validated('notes'),
            $request->has('auto_progress') ? (bool) $request->boolean('auto_progress') : null,
        );

        return back()->with('status', __('tracking.flash.stage_updated'));
    }
}
