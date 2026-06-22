@php
    $customerName           = $track->customer_name ?: 'Cliente';
    $estimatedDelivery      = $track->estimated_delivery_at?->locale('pt')->translatedFormat('d \d\e F \d\e Y');
    $currentStageLabel      = $stage->label();
    $currentStageDescription = $stage->description();
    $isDelivered            = $stage === \App\Enums\TrackingStage::Delivered;
    $isDelay                = $emailType === \App\Enums\OrderTrackEmailType::InTransitDelay;
    $stageTime              = $isDelay ? null : $stageRecord?->planned_for_at?->locale('pt')->translatedFormat('d \d\e F \d\e Y');
    $logoUrl                = 'https://i.postimg.cc/qvRM64gc/worten-logo-removebg-preview.png';

    // Stage icon map (inline SVG as data URI embedded in style)
    $stageIconColor = '#E30613';
@endphp
<!DOCTYPE html>
<html lang="pt" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>
        @if ($isDelivered)
            A tua encomenda foi entregue! — Worten
        @elseif ($isDelay)
            Atualização importante da tua encomenda — Worten
        @elseif ($emailType === \App\Enums\OrderTrackEmailType::TrackCreated)
            Encomenda confirmada — Worten
        @else
            Atualização da encomenda — Worten
        @endif
    </title>
    <!--[if mso]>
    <noscript>
        <xml><o:OfficeDocumentSettings><o:PixelsPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml>
    </noscript>
    <![endif]-->
</head>
<body style="margin:0;padding:0;background-color:#f4f4f4;font-family:Arial,Helvetica,sans-serif;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;">

<!-- Preheader -->
<div style="display:none;max-height:0;overflow:hidden;mso-hide:all;">
    @if ($isDelivered)
        A tua encomenda #{{ $track->order_number }} foi entregue com sucesso!
    @elseif ($isDelay)
        Atualização importante sobre a tua encomenda #{{ $track->order_number }}.
    @elseif ($emailType === \App\Enums\OrderTrackEmailType::TrackCreated)
        A tua encomenda #{{ $track->order_number }} foi confirmada. Segue o teu envio aqui.
    @else
        A tua encomenda #{{ $track->order_number }} avançou para: {{ $currentStageLabel }}.
    @endif
    &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;
</div>

<!-- Wrapper -->
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f4f4f4;">
<tr><td align="center" style="padding:24px 16px;">

<!-- Main container -->
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width:600px;">

    <!-- ── HEADER ── -->
    <tr>
        <td style="background-color:#E30613;border-radius:8px 8px 0 0;padding:0;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td style="padding:20px 28px;">
                        <img src="{{ $logoUrl }}" alt="Worten" width="140" style="display:block;height:auto;max-height:48px;object-fit:contain;">
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <!-- ── HERO BANNER ── -->
    <tr>
        <td style="background-color:#E30613;padding:0 28px 28px;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td>
                        <p style="margin:0 0 6px;font-size:12px;color:rgba(255,255,255,0.8);text-transform:uppercase;letter-spacing:0.12em;font-family:Arial,sans-serif;">
                            Encomenda #{{ $track->order_number }}
                        </p>
                        @if ($isDelivered)
                            <h1 style="margin:0;font-size:26px;font-weight:700;color:#ffffff;line-height:1.2;font-family:Arial,sans-serif;">
                                ✓ &nbsp;A tua encomenda foi entregue!
                            </h1>
                        @elseif ($isDelay)
                            <h1 style="margin:0;font-size:26px;font-weight:700;color:#ffffff;line-height:1.2;font-family:Arial,sans-serif;">
                                Atualização da tua encomenda
                            </h1>
                        @elseif ($emailType === \App\Enums\OrderTrackEmailType::TrackCreated)
                            <h1 style="margin:0;font-size:26px;font-weight:700;color:#ffffff;line-height:1.2;font-family:Arial,sans-serif;">
                                Encomenda confirmada!
                            </h1>
                        @else
                            <h1 style="margin:0;font-size:26px;font-weight:700;color:#ffffff;line-height:1.2;font-family:Arial,sans-serif;">
                                Nova atualização da encomenda
                            </h1>
                        @endif
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <!-- ── WHITE BODY ── -->
    <tr>
        <td style="background-color:#ffffff;padding:28px 28px 0;">

            <!-- Greeting -->
            <p style="margin:0 0 18px;font-size:15px;color:#1a1a1a;font-family:Arial,sans-serif;">
                Olá <strong>{{ $customerName }}</strong>,
            </p>

            <!-- Main message -->
            @if ($emailType === \App\Enums\OrderTrackEmailType::TrackCreated)
                <p style="margin:0 0 8px;font-size:15px;color:#444;line-height:1.6;font-family:Arial,sans-serif;">
                    Obrigado pela tua compra na Worten! A tua encomenda foi recebida e está a ser tratada com o máximo cuidado.
                </p>
                <p style="margin:0 0 22px;font-size:15px;color:#444;line-height:1.6;font-family:Arial,sans-serif;">
                    Podes acompanhar todas as fases de entrega em tempo real com o código e link abaixo.
                </p>
            @elseif ($isDelay)
                <p style="margin:0 0 8px;font-size:15px;color:#444;line-height:1.6;font-family:Arial,sans-serif;">
                    Houve um imprevisto na entrega da tua encomenda devido à elevada procura. O teu produto continua em trânsito.
                </p>
                <p style="margin:0 0 22px;font-size:15px;color:#444;line-height:1.6;font-family:Arial,sans-serif;">
                    Pedimos desculpa pelo inconveniente. A janela de entrega foi prolongada por 8 dias. Agradecemos a tua compreensão.
                </p>
            @elseif ($isDelivered)
                <p style="margin:0 0 22px;font-size:15px;color:#444;line-height:1.6;font-family:Arial,sans-serif;">
                    A tua encomenda foi entregue com sucesso! Esperamos que estejas satisfeito com a tua compra. Obrigado por escolheres a Worten.
                </p>
            @else
                <p style="margin:0 0 22px;font-size:15px;color:#444;line-height:1.6;font-family:Arial,sans-serif;">
                    A tua encomenda avançou para uma nova fase de entrega. Mantemos-te informado em cada passo.
                </p>
            @endif

            <!-- ── PRODUCT BOX ── -->
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
                   style="background:#f9f9f9;border:1px solid #eeeeee;border-radius:8px;margin-bottom:22px;">
                <tr>
                    <td style="padding:18px 20px;">
                        <!-- Product name -->
                        <p style="margin:0 0 14px;font-size:13px;color:#999;text-transform:uppercase;letter-spacing:0.08em;font-family:Arial,sans-serif;">Produto</p>
                        <p style="margin:0 0 18px;font-size:15px;font-weight:700;color:#1a1a1a;line-height:1.4;font-family:Arial,sans-serif;">{{ $track->product_name }}</p>

                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td width="50%" style="vertical-align:top;padding-right:10px;">
                                    <p style="margin:0 0 4px;font-size:11px;color:#999;text-transform:uppercase;letter-spacing:0.08em;font-family:Arial,sans-serif;">Nº Encomenda</p>
                                    <p style="margin:0;font-size:14px;font-weight:700;color:#1a1a1a;font-family:Arial,sans-serif;">#{{ $track->order_number }}</p>
                                </td>
                                <td width="50%" style="vertical-align:top;">
                                    <p style="margin:0 0 4px;font-size:11px;color:#999;text-transform:uppercase;letter-spacing:0.08em;font-family:Arial,sans-serif;">Estado</p>
                                    <p style="margin:0;font-size:14px;font-weight:700;color:#E30613;font-family:Arial,sans-serif;">{{ $currentStageLabel }}</p>
                                </td>
                            </tr>
                        </table>

                        @if ($stageTime || $estimatedDelivery)
                            <hr style="border:none;border-top:1px solid #eeeeee;margin:14px 0;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    @if ($stageTime)
                                    <td width="50%" style="vertical-align:top;padding-right:10px;">
                                        <p style="margin:0 0 4px;font-size:11px;color:#999;text-transform:uppercase;letter-spacing:0.08em;font-family:Arial,sans-serif;">Fase prevista</p>
                                        <p style="margin:0;font-size:13px;font-weight:600;color:#1a1a1a;font-family:Arial,sans-serif;">{{ $stageTime }}</p>
                                    </td>
                                    @endif
                                    @if ($estimatedDelivery)
                                    <td width="50%" style="vertical-align:top;">
                                        <p style="margin:0 0 4px;font-size:11px;color:#999;text-transform:uppercase;letter-spacing:0.08em;font-family:Arial,sans-serif;">Entrega estimada</p>
                                        <p style="margin:0;font-size:13px;font-weight:600;color:#1a1a1a;font-family:Arial,sans-serif;">{{ $estimatedDelivery }}</p>
                                    </td>
                                    @endif
                                </tr>
                            </table>
                        @endif
                    </td>
                </tr>
            </table>

            <!-- ── TRACKING CODE ── -->
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
                   style="background:#fff3f3;border:2px solid #E30613;border-radius:8px;margin-bottom:22px;">
                <tr>
                    <td style="padding:16px 20px;">
                        <p style="margin:0 0 6px;font-size:11px;color:#E30613;text-transform:uppercase;letter-spacing:0.12em;font-weight:700;font-family:Arial,sans-serif;">
                            Código de rastreio
                        </p>
                        <p style="margin:0;font-size:24px;font-weight:700;letter-spacing:0.12em;color:#1a1a1a;font-family:Arial,sans-serif;">
                            {{ $track->tracking_code }}
                        </p>
                    </td>
                </tr>
            </table>

            @if ($notes)
            <!-- ── NOTES ── -->
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
                   style="background:#fff8e1;border-left:4px solid #FFC107;border-radius:0 6px 6px 0;margin-bottom:22px;">
                <tr>
                    <td style="padding:14px 16px;">
                        <p style="margin:0;font-size:14px;color:#5a4500;font-family:Arial,sans-serif;">
                            <strong>Nota:</strong> {{ $notes }}
                        </p>
                    </td>
                </tr>
            </table>
            @endif

            <!-- ── CTA BUTTON ── -->
            <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:10px;">
                <tr>
                    <td style="border-radius:6px;background-color:#E30613;">
                        <a href="{{ $trackingUrl }}"
                           target="_blank"
                           style="display:inline-block;padding:14px 28px;font-size:15px;font-weight:700;color:#ffffff;text-decoration:none;font-family:Arial,sans-serif;border-radius:6px;letter-spacing:0.02em;">
                            Seguir a minha encomenda &rarr;
                        </a>
                    </td>
                </tr>
            </table>

            <p style="margin:10px 0 0;font-size:12px;color:#999;font-family:Arial,sans-serif;">
                Ou copia este link: <a href="{{ $trackingUrl }}" style="color:#E30613;text-decoration:none;word-break:break-all;">{{ $trackingUrl }}</a>
            </p>

        </td>
    </tr>

    <!-- ── DIVIDER ── -->
    <tr>
        <td style="background-color:#ffffff;padding:24px 28px 0;">
            <hr style="border:none;border-top:1px solid #eeeeee;margin:0;">
        </td>
    </tr>

    <!-- ── SUPPORT BLOCK ── -->
    @if (filled($branding['contact'] ?? null) || filled($branding['email'] ?? null))
    <tr>
        <td style="background-color:#ffffff;padding:20px 28px;">
            <p style="margin:0 0 10px;font-size:13px;font-weight:700;color:#1a1a1a;font-family:Arial,sans-serif;">Precisas de ajuda?</p>
            @if (filled($branding['contact'] ?? null))
            <p style="margin:0 0 6px;font-size:13px;color:#555;font-family:Arial,sans-serif;">
                📞 &nbsp;<strong>{{ $branding['contact'] }}</strong>
            </p>
            @endif
            @if (filled($branding['email'] ?? null))
            <p style="margin:0;font-size:13px;color:#555;font-family:Arial,sans-serif;">
                ✉️ &nbsp;<a href="mailto:{{ $branding['email'] }}" style="color:#E30613;text-decoration:none;">{{ $branding['email'] }}</a>
            </p>
            @endif
        </td>
    </tr>
    @endif

    <!-- ── FOOTER ── -->
    <tr>
        <td style="background-color:#1a1a1a;border-radius:0 0 8px 8px;padding:20px 28px;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td>
                        <img src="{{ $logoUrl }}" alt="Worten" width="90" style="display:block;height:auto;max-height:30px;object-fit:contain;margin-bottom:12px;filter:brightness(0) invert(1);">
                        <p style="margin:0 0 6px;font-size:11px;color:#888;font-family:Arial,sans-serif;line-height:1.5;">
                            © {{ date('Y') }} Worten — Todos os direitos reservados.
                        </p>
                        <p style="margin:0;font-size:11px;color:#666;font-family:Arial,sans-serif;line-height:1.5;">
                            Este é um email automático relativo à tua encomenda. Por favor não respondas diretamente a este email.
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

</table>
<!-- /Main container -->

</td></tr>
</table>
<!-- /Wrapper -->

</body>
</html>
