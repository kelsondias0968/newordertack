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
<!DOCTYPE html>
<html lang="{{ $locale }}">
<head>
    <meta charset="utf-8">
    <title>{{ $emailType->label() }}</title>
</head>
<body style="margin:0; padding:24px; background:#eef6fb; font-family:Arial, Helvetica, sans-serif; color:#24313d;">
    <div style="max-width:640px; margin:0 auto;">
        @if (! empty($branding['logo_url']))
            <div style="margin:0 0 16px; padding:22px 28px; background:#ffffff; border-radius:24px; border:1px solid #d4e5f2; text-align:center;">
                <img src="{{ $branding['logo_url'] }}" alt="{{ __('tracking.app_name') }}" style="display:inline-block; max-width:180px; max-height:56px;">
            </div>
        @endif

        <div style="background:#ffffff; border-radius:24px; border:1px solid #d4e5f2; overflow:hidden;">
            <div style="padding:28px 28px 18px; background:linear-gradient(135deg, {{ $branding['color'] ?? '#0b79bf' }} 0%, {{ $branding['color'] ?? '#0b79bf' }}bb 100%); color:#ffffff;">
                <p style="margin:0 0 10px; font-size:12px; letter-spacing:0.14em; text-transform:uppercase; opacity:0.86;">{{ $branding['name'] ?? __('tracking.app_name') }}</p>
                @if ($emailType === \App\Enums\OrderTrackEmailType::TrackCreated)
                    <h1 style="margin:0; font-size:28px; line-height:1.15;">{{ __('tracking.emails.track_created.heading') }}</h1>
                @elseif ($emailType === \App\Enums\OrderTrackEmailType::InTransitDelay)
                    <h1 style="margin:0; font-size:28px; line-height:1.15;">{{ __('tracking.emails.in_transit_delay.heading') }}</h1>
                @elseif ($stage === \App\Enums\TrackingStage::Delivered)
                    <h1 style="margin:0; font-size:28px; line-height:1.15;">{{ __('tracking.emails.stage_updated.delivered_heading') }}</h1>
                @else
                    <h1 style="margin:0; font-size:28px; line-height:1.15;">{{ __('tracking.emails.stage_updated.heading') }}</h1>
                @endif
            </div>

            <div style="padding:28px;">
                <p style="margin:0 0 16px; color:#24313d;">{{ __('tracking.emails.shared.greeting', ['name' => $customerName]) }}</p>

                @if ($emailType === \App\Enums\OrderTrackEmailType::TrackCreated)
                    <p style="margin:0 0 14px; color:#617284;">{{ __('tracking.emails.track_created.congrats') }}</p>
                    <p style="margin:0 0 20px; color:#617284;">{{ __('tracking.emails.track_created.processing_message') }}</p>
                @elseif ($emailType === \App\Enums\OrderTrackEmailType::InTransitDelay)
                    <p style="margin:0 0 14px; color:#617284;">{{ __('tracking.emails.in_transit_delay.problem_message') }}</p>
                    <p style="margin:0 0 20px; color:#617284;">{{ __('tracking.emails.in_transit_delay.wait_message') }}</p>
                @elseif ($stage === \App\Enums\TrackingStage::Delivered)
                    <p style="margin:0 0 20px; color:#617284;">{{ __('tracking.emails.stage_updated.delivered_message') }}</p>
                @else
                    <p style="margin:0 0 20px; color:#617284;">{{ __('tracking.emails.stage_updated.progress_message', ['stage' => $currentStageLabel]) }}</p>
                @endif

                <div style="padding:20px; border-radius:20px; background:#f5fbff; border:1px solid #d7eaf7; margin-bottom:20px;">
                    <p style="margin:0 0 8px; color:#617284; font-size:13px; text-transform:uppercase; letter-spacing:0.08em;">{{ __('tracking.common.order') }}</p>
                    <p style="margin:0 0 16px; font-size:18px; font-weight:700; color:#24313d;">{{ $track->order_number }}</p>

                    <p style="margin:0 0 8px; color:#617284; font-size:13px; text-transform:uppercase; letter-spacing:0.08em;">{{ __('tracking.common.product') }}</p>
                    <p style="margin:0 0 16px; color:#24313d;">{{ $track->product_name }}</p>

                    <p style="margin:0 0 8px; color:#617284; font-size:13px; text-transform:uppercase; letter-spacing:0.08em;">{{ __('tracking.common.tracking_code') }}</p>
                    <p style="margin:0 0 16px; font-size:24px; font-weight:700; letter-spacing:0.08em; color:#0b79bf;">{{ $track->tracking_code }}</p>

                    <p style="margin:0 0 8px; color:#617284; font-size:13px; text-transform:uppercase; letter-spacing:0.08em;">{{ __('tracking.emails.shared.current_stage') }}</p>
                    <p style="margin:0 0 4px; color:#24313d; font-weight:700;">{{ $currentStageLabel }}</p>
                    <p style="margin:0; color:#617284;">{{ $currentStageDescription }}</p>
                </div>

                @if ($stageTime)
                    <p style="margin:0 0 14px; color:#617284;">{{ __('tracking.emails.shared.stage_time', ['date' => $stageTime]) }}</p>
                @endif

                @if ($estimatedDelivery)
                    <p style="margin:0 0 14px; color:#617284;">{{ __('tracking.emails.shared.estimated_delivery', ['date' => $estimatedDelivery]) }}</p>
                @endif

                @if ($notes)
                    <p style="margin:0 0 18px; padding:14px 16px; border-radius:16px; background:#fff7e8; color:#6f5a2a;">
                        <strong>{{ __('tracking.emails.shared.note_label') }}</strong> {{ $notes }}
                    </p>
                @endif

                <a href="{{ $trackingUrl }}" style="display:inline-block; padding:14px 24px; border-radius:16px; background:#0b79bf; color:#ffffff; text-decoration:none; font-weight:700;">
                    {{ __('tracking.emails.shared.track_button') }}
                </a>

                <p style="margin:18px 0 0; color:#617284;">{{ __('tracking.emails.shared.link_fallback') }}</p>
                <p style="margin:8px 0 0; word-break:break-all;">
                    <a href="{{ $trackingUrl }}" style="color:#0b79bf; text-decoration:none;">{{ $trackingUrl }}</a>
                </p>

                @if ($hasSupportDetails)
                    <div style="margin-top:24px; padding:18px 20px; border-radius:18px; background:#f8fbfd; border:1px solid #dceaf4;">
                        <p style="margin:0 0 12px; color:#24313d; font-size:14px; font-weight:700;">{{ __('tracking.emails.shared.support_details') }}</p>

                        @if (! empty($branding['contact']))
                            <p style="margin:0 0 8px; color:#617284;">
                                <strong style="color:#24313d;">{{ __('tracking.emails.shared.contact_label') }}:</strong> {{ $branding['contact'] }}
                            </p>
                        @endif

                        @if (! empty($branding['email']))
                            <p style="margin:0 0 8px; color:#617284;">
                                <strong style="color:#24313d;">{{ __('tracking.emails.shared.email_label') }}:</strong> {{ $branding['email'] }}
                            </p>
                        @endif

                        @if (! empty($branding['address']))
                            <p style="margin:0; color:#617284;">
                                <strong style="color:#24313d;">{{ __('tracking.emails.shared.address_label') }}:</strong> {{ $branding['address'] }}
                            </p>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
