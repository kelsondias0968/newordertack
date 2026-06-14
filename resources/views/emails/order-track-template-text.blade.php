@php
    $customerName = $track->customer_name ?: __('tracking.emails.shared.generic_customer');
    $estimatedDelivery = $track->estimated_delivery_at?->locale($locale)->translatedFormat('l, d F Y H:i');
    $currentStageLabel = $stage->label();
    $currentStageDescription = $stage->description();
    $stageTime = $emailType === \App\Enums\OrderTrackEmailType::InTransitDelay
        ? null
        : $stageRecord?->planned_for_at?->locale($locale)->translatedFormat('l, d F Y H:i');
    $hasSupportDetails = filled($branding['contact'] ?? null) || filled($branding['email'] ?? null) || filled($branding['address'] ?? null);
@endphp
{{ __('tracking.emails.shared.greeting', ['name' => $customerName]) }}

@if ($emailType === \App\Enums\OrderTrackEmailType::TrackCreated)
{{ __('tracking.emails.track_created.congrats') }}
{{ __('tracking.emails.track_created.processing_message') }}
@elseif ($emailType === \App\Enums\OrderTrackEmailType::InTransitDelay)
{{ __('tracking.emails.in_transit_delay.problem_message') }}
{{ __('tracking.emails.in_transit_delay.wait_message') }}
@elseif ($stage === \App\Enums\TrackingStage::Delivered)
{{ __('tracking.emails.stage_updated.delivered_message') }}
@else
{{ __('tracking.emails.stage_updated.progress_message', ['stage' => $currentStageLabel]) }}
@endif

{{ __('tracking.common.order') }}: {{ $track->order_number }}
{{ __('tracking.common.product') }}: {{ $track->product_name }}
{{ __('tracking.common.tracking_code') }}: {{ $track->tracking_code }}
{{ __('tracking.emails.shared.current_stage') }}: {{ $currentStageLabel }}
{{ $currentStageDescription }}

@if ($stageTime)
{{ __('tracking.emails.shared.stage_time', ['date' => $stageTime]) }}
@endif
@if ($estimatedDelivery)
{{ __('tracking.emails.shared.estimated_delivery', ['date' => $estimatedDelivery]) }}
@endif
@if ($notes)
{{ __('tracking.emails.shared.note_label') }} {{ $notes }}
@endif

{{ __('tracking.emails.shared.track_button') }}: {{ $trackingUrl }}

@if ($hasSupportDetails)

{{ __('tracking.emails.shared.support_details') }}
@if (! empty($branding['contact']))
{{ __('tracking.emails.shared.contact_label') }}: {{ $branding['contact'] }}
@endif
@if (! empty($branding['email']))
{{ __('tracking.emails.shared.email_label') }}: {{ $branding['email'] }}
@endif
@if (! empty($branding['address']))
{{ __('tracking.emails.shared.address_label') }}: {{ $branding['address'] }}
@endif
@endif
