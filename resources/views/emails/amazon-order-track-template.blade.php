@php
    $customerName            = $track->customer_name ?: 'Customer';
    $estimatedDelivery       = $track->estimated_delivery_at?->translatedFormat('l, F j, Y');
    $currentStageLabel       = $stage->label();
    $currentStageDescription = $stage->description();
    $isDelivered             = $stage === \App\Enums\TrackingStage::Delivered;
    $isDelay                 = $emailType === \App\Enums\OrderTrackEmailType::InTransitDelay;
    $stageTime               = $isDelay ? null : $stageRecord?->planned_for_at?->translatedFormat('l, F j, Y');
    $logoUrl                 = 'https://upload.wikimedia.org/wikipedia/commons/a/a9/Amazon_logo.svg';

    $stages        = $track->stages;
    $currentPos    = $track->current_stage->position();
    $totalStages   = $stages->count();
    $fillPct       = $totalStages > 1 ? min(100, round(($currentPos - 1) / ($totalStages - 1) * 100)) : 100;
@endphp
<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>
        @if ($isDelivered)
            Your package was delivered — Amazon
        @elseif ($isDelay)
            Important update about your order — Amazon
        @elseif ($emailType === \App\Enums\OrderTrackEmailType::TrackCreated)
            Order confirmed — Amazon
        @else
            Order update: {{ $currentStageLabel }} — Amazon
        @endif
    </title>
</head>
<body style="margin:0;padding:0;background-color:#EAEDED;font-family:Arial,Helvetica,sans-serif;-webkit-text-size-adjust:100%;">

<!-- Preheader -->
<div style="display:none;max-height:0;overflow:hidden;mso-hide:all;">
    @if ($isDelivered)
        Your order #{{ $track->order_number }} has been delivered. Thank you for shopping with Amazon.
    @elseif ($isDelay)
        An important update regarding your order #{{ $track->order_number }}.
    @elseif ($emailType === \App\Enums\OrderTrackEmailType::TrackCreated)
        Your order #{{ $track->order_number }} is confirmed. Track your package here.
    @else
        Your order #{{ $track->order_number }} is now: {{ $currentStageLabel }}.
    @endif
    &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;
</div>

<!-- Wrapper -->
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#EAEDED;">
<tr><td align="center" style="padding:20px 12px;">

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width:600px;">

    <!-- ── HEADER ── -->
    <tr>
        <td style="background-color:#232F3E;border-radius:6px 6px 0 0;padding:14px 24px;">
            <img src="{{ $logoUrl }}" alt="Amazon" width="96"
                 style="display:block;height:auto;filter:brightness(0) invert(1);">
        </td>
    </tr>

    <!-- ── ORANGE ACCENT BAR ── -->
    <tr>
        <td style="background-color:#FF9900;height:4px;font-size:0;line-height:0;">&nbsp;</td>
    </tr>

    <!-- ── HERO ── -->
    <tr>
        <td style="background-color:#ffffff;padding:26px 24px 20px;border-left:1px solid #ddd;border-right:1px solid #ddd;">
            @if ($isDelivered)
                <h1 style="margin:0 0 8px;font-size:22px;font-weight:700;color:#0F1111;font-family:Arial,sans-serif;">
                    Your package was delivered! ✓
                </h1>
                <p style="margin:0;font-size:14px;color:#565959;font-family:Arial,sans-serif;">
                    We hope you enjoy your purchase. Thank you for choosing Amazon.
                </p>
            @elseif ($isDelay)
                <h1 style="margin:0 0 8px;font-size:22px;font-weight:700;color:#0F1111;font-family:Arial,sans-serif;">
                    Important update about your delivery
                </h1>
                <p style="margin:0;font-size:14px;color:#565959;font-family:Arial,sans-serif;">
                    We're working hard to get your package to you as soon as possible.
                </p>
            @elseif ($emailType === \App\Enums\OrderTrackEmailType::TrackCreated)
                <h1 style="margin:0 0 8px;font-size:22px;font-weight:700;color:#0F1111;font-family:Arial,sans-serif;">
                    Order confirmed — thank you!
                </h1>
                <p style="margin:0;font-size:14px;color:#565959;font-family:Arial,sans-serif;">
                    Your order has been placed and is being prepared. You can track every step below.
                </p>
            @else
                <h1 style="margin:0 0 8px;font-size:22px;font-weight:700;color:#0F1111;font-family:Arial,sans-serif;">
                    Your order has been updated
                </h1>
                <p style="margin:0;font-size:14px;color:#565959;font-family:Arial,sans-serif;">
                    Good news — your order just moved to a new stage.
                </p>
            @endif
        </td>
    </tr>

    <!-- ── PROGRESS BAR ── -->
    <tr>
        <td style="background-color:#ffffff;padding:20px 24px 24px;border-left:1px solid #ddd;border-right:1px solid #ddd;">

            <!-- Current stage label -->
            <p style="margin:0 0 16px;font-size:16px;font-weight:700;color:#0F1111;text-align:center;font-family:Arial,sans-serif;">
                {{ $currentStageLabel }}
            </p>

            <!-- Progress track container -->
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    @foreach ($stages as $i => $s)
                        @php
                            $isDone    = $s->position < $currentPos;
                            $isCurrent = $s->position === $currentPos;
                            $dotBg     = $isDone ? '#007185' : ($isCurrent ? '#ffffff' : '#D5D9D9');
                            $dotBorder = $isCurrent ? '3px solid #007185' : 'none';
                            $labelColor = ($isDone || $isCurrent) ? '#0F1111' : '#888';
                            $labelWeight = ($isDone || $isCurrent) ? '700' : '400';
                        @endphp
                        <td align="center" style="vertical-align:top;position:relative;">
                            @if (! $loop->first)
                                <!-- Line before dot -->
                                <div style="display:inline-block;width:100%;height:3px;background:{{ $isDone ? '#007185' : '#D5D9D9' }};vertical-align:middle;margin-bottom:13px;"></div>
                            @else
                                <div style="height:16px;"></div>
                            @endif

                            <!-- Dot -->
                            <div style="width:28px;height:28px;border-radius:50%;background:{{ $dotBg }};border:{{ $dotBorder }};margin:0 auto 8px;display:flex;align-items:center;justify-content:center;">
                                @if ($isDone)
                                    <span style="color:#ffffff;font-size:14px;font-weight:700;line-height:1;">✓</span>
                                @endif
                            </div>

                            <!-- Label -->
                            <p style="margin:0 2px;font-size:11px;font-weight:{{ $labelWeight }};color:{{ $labelColor }};font-family:Arial,sans-serif;text-align:center;line-height:1.3;">
                                {{ $s->title }}
                            </p>

                            @if ($s->reached_at)
                                <p style="margin:4px 0 0;font-size:10px;color:#007185;font-family:Arial,sans-serif;text-align:center;">
                                    {{ $s->reached_at->format('M j') }}
                                </p>
                            @elseif ($isCurrent && $s->planned_for_at)
                                <p style="margin:4px 0 0;font-size:10px;color:#007185;font-family:Arial,sans-serif;text-align:center;">
                                    Est. {{ $s->planned_for_at->format('M j') }}
                                </p>
                            @endif
                        </td>
                    @endforeach
                </tr>
            </table>
        </td>
    </tr>

    <!-- ── ORDER DETAILS BOX ── -->
    <tr>
        <td style="background-color:#ffffff;padding:0 24px 24px;border-left:1px solid #ddd;border-right:1px solid #ddd;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
                   style="background:#F0F2F2;border:1px solid #ddd;border-radius:4px;">
                <tr>
                    <td style="padding:16px 18px;">

                        <p style="margin:0 0 12px;font-size:13px;color:#888;text-transform:uppercase;letter-spacing:.07em;font-family:Arial,sans-serif;">Order details</p>

                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td style="padding-bottom:10px;vertical-align:top;" width="50%">
                                    <p style="margin:0 0 3px;font-size:11px;color:#888;text-transform:uppercase;letter-spacing:.06em;font-family:Arial,sans-serif;">Order number</p>
                                    <p style="margin:0;font-size:14px;font-weight:700;color:#0F1111;font-family:Arial,sans-serif;">#{{ $track->order_number }}</p>
                                </td>
                                <td style="padding-bottom:10px;vertical-align:top;" width="50%">
                                    <p style="margin:0 0 3px;font-size:11px;color:#888;text-transform:uppercase;letter-spacing:.06em;font-family:Arial,sans-serif;">Status</p>
                                    <p style="margin:0;font-size:14px;font-weight:700;color:#007185;font-family:Arial,sans-serif;">{{ $currentStageLabel }}</p>
                                </td>
                            </tr>
                        </table>

                        <hr style="border:none;border-top:1px solid #ddd;margin:10px 0;">

                        <p style="margin:0 0 3px;font-size:11px;color:#888;text-transform:uppercase;letter-spacing:.06em;font-family:Arial,sans-serif;">Product</p>
                        <p style="margin:0 0 12px;font-size:14px;font-weight:600;color:#0F1111;font-family:Arial,sans-serif;">{{ $track->product_name }}</p>

                        @if ($track->customer_name)
                        <p style="margin:0 0 3px;font-size:11px;color:#888;text-transform:uppercase;letter-spacing:.06em;font-family:Arial,sans-serif;">Ordered by</p>
                        <p style="margin:0 0 12px;font-size:14px;color:#0F1111;font-family:Arial,sans-serif;">{{ $track->customer_name }}</p>
                        @endif

                        @if ($estimatedDelivery && !$isDelivered)
                        <hr style="border:none;border-top:1px solid #ddd;margin:10px 0;">
                        <p style="margin:0 0 3px;font-size:11px;color:#888;text-transform:uppercase;letter-spacing:.06em;font-family:Arial,sans-serif;">Estimated delivery</p>
                        <p style="margin:0;font-size:14px;font-weight:700;color:#007185;font-family:Arial,sans-serif;">{{ $estimatedDelivery }}</p>
                        @endif

                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <!-- ── TRACKING CODE ── -->
    <tr>
        <td style="background-color:#ffffff;padding:0 24px 24px;border-left:1px solid #ddd;border-right:1px solid #ddd;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
                   style="border:2px solid #007185;border-radius:4px;">
                <tr>
                    <td style="padding:14px 18px;">
                        <p style="margin:0 0 4px;font-size:11px;color:#007185;text-transform:uppercase;letter-spacing:.1em;font-weight:700;font-family:Arial,sans-serif;">Tracking code</p>
                        <p style="margin:0;font-size:22px;font-weight:700;letter-spacing:.1em;color:#0F1111;font-family:Arial,sans-serif;">{{ $track->tracking_code }}</p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    @if ($notes)
    <!-- ── NOTES ── -->
    <tr>
        <td style="background-color:#ffffff;padding:0 24px 20px;border-left:1px solid #ddd;border-right:1px solid #ddd;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
                   style="background:#FFFBF0;border-left:4px solid #FF9900;border-radius:0 4px 4px 0;">
                <tr>
                    <td style="padding:12px 14px;">
                        <p style="margin:0;font-size:13px;color:#5a4500;font-family:Arial,sans-serif;">
                            <strong>Note:</strong> {{ $notes }}
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    @endif

    <!-- ── CTA ── -->
    <tr>
        <td style="background-color:#ffffff;padding:0 24px 28px;border-left:1px solid #ddd;border-right:1px solid #ddd;">
            <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td style="border-radius:3px;background:linear-gradient(to bottom,#f7dfa5,#f0c14b);border:1px solid #a88734;">
                        <a href="{{ $trackingUrl }}" target="_blank"
                           style="display:inline-block;padding:10px 22px;font-size:14px;font-weight:700;color:#111111;text-decoration:none;font-family:Arial,sans-serif;">
                            Track your package &rarr;
                        </a>
                    </td>
                </tr>
            </table>
            <p style="margin:12px 0 0;font-size:12px;color:#888;font-family:Arial,sans-serif;">
                Or copy this link: <a href="{{ $trackingUrl }}" style="color:#007185;word-break:break-all;">{{ $trackingUrl }}</a>
            </p>
        </td>
    </tr>

    @if ($isDelay)
    <!-- ── DELAY MESSAGE ── -->
    <tr>
        <td style="background-color:#ffffff;padding:0 24px 24px;border-left:1px solid #ddd;border-right:1px solid #ddd;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
                   style="background:#FFF8F0;border:1px solid #FF9900;border-radius:4px;">
                <tr>
                    <td style="padding:16px 18px;">
                        <p style="margin:0 0 8px;font-size:14px;font-weight:700;color:#0F1111;font-family:Arial,sans-serif;">Delivery update</p>
                        <p style="margin:0;font-size:13px;color:#565959;line-height:1.6;font-family:Arial,sans-serif;">
                            Your package encountered a delay due to high demand in your area. We've extended the delivery window by 8 days. We apologize for the inconvenience and appreciate your patience.
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    @endif

    <!-- ── SUPPORT ── -->
    @if (filled($branding['contact'] ?? null) || filled($branding['email'] ?? null))
    <tr>
        <td style="background-color:#ffffff;padding:0 24px 24px;border-left:1px solid #ddd;border-right:1px solid #ddd;">
            <hr style="border:none;border-top:1px solid #eee;margin:0 0 16px;">
            <p style="margin:0 0 10px;font-size:13px;font-weight:700;color:#0F1111;font-family:Arial,sans-serif;">Need help?</p>
            @if (filled($branding['contact'] ?? null))
            <p style="margin:0 0 6px;font-size:13px;color:#565959;font-family:Arial,sans-serif;">
                📞 &nbsp;<strong>{{ $branding['contact'] }}</strong>
            </p>
            @endif
            @if (filled($branding['email'] ?? null))
            <p style="margin:0;font-size:13px;color:#565959;font-family:Arial,sans-serif;">
                ✉️ &nbsp;<a href="mailto:{{ $branding['email'] }}" style="color:#007185;text-decoration:none;">{{ $branding['email'] }}</a>
            </p>
            @endif
        </td>
    </tr>
    @endif

    <!-- ── FOOTER ── -->
    <tr>
        <td style="background-color:#232F3E;border-radius:0 0 6px 6px;padding:18px 24px;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td>
                        <img src="{{ $logoUrl }}" alt="Amazon" width="80"
                             style="display:block;height:auto;filter:brightness(0) invert(1);margin-bottom:10px;">
                        <p style="margin:0 0 4px;font-size:11px;color:#aaa;font-family:Arial,sans-serif;">
                            © {{ date('Y') }}, Amazon.com, Inc. or its affiliates. All rights reserved.
                        </p>
                        <p style="margin:0;font-size:11px;color:#777;font-family:Arial,sans-serif;">
                            This is an automated message. Please do not reply directly to this email.
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

</table>

</td></tr>
</table>

</body>
</html>
