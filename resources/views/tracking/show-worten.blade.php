@php
    $currentPosition = $track->current_stage->position();
    $isDelivered     = $track->current_stage === \App\Enums\TrackingStage::Delivered;
    $placeholder     = strtoupper(substr($track->product_name, 0, 2));

    // Stage icons (SVG paths)
    $stageIcons = [
        'confirmed'        => '<path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>',
        'processing'       => '<path d="M20 7H4a1 1 0 00-1 1v10a1 1 0 001 1h16a1 1 0 001-1V8a1 1 0 00-1-1z"/><path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2"/>',
        'dispatched'       => '<path d="M12 3H5a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2V8"/><polyline points="15 3 20 8 15 8"/>',
        'in_transit'       => '<rect x="1" y="3" width="15" height="13" rx="1"/><path d="M16 8h4l3 5v3h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/>',
        'out_for_delivery' => '<path d="M5 17H3a2 2 0 01-2-2V5a2 2 0 012-2h11a2 2 0 012 2v3"/><rect x="9" y="11" width="14" height="10" rx="1"/><circle cx="12" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>',
        'delivered'        => '<path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>',
    ];
@endphp
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rastrear Encomenda — Worten</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
        body{font-family:'Open Sans',Arial,sans-serif;background:#f2f2f2;color:#1a1a1a;font-size:15px;}
        a{color:inherit;text-decoration:none;}
        img{max-width:100%;display:block;}

        /* ── Top nav ── */
        .w-nav{
            background:#E30613;
            padding:14px 20px;
            display:flex;
            align-items:center;
            justify-content:space-between;
        }
        .w-nav-logo{
            height:28px;
            filter:brightness(0) invert(1);
            object-fit:contain;
        }
        .w-nav-icons{
            display:flex;
            gap:18px;
        }
        .w-nav-icons svg{
            width:24px;height:24px;
            stroke:#fff;
            fill:none;
            stroke-width:2;
            stroke-linecap:round;
            stroke-linejoin:round;
        }

        /* ── Shell ── */
        .w-shell{
            max-width:480px;
            margin:0 auto;
            padding-bottom:48px;
        }

        /* ── Cards ── */
        .w-card{
            background:#fff;
            margin:10px 0;
            border-bottom:1px solid #e8e8e8;
            border-top:1px solid #e8e8e8;
        }

        /* ── Sold by row ── */
        .w-soldby{
            padding:12px 16px;
            font-size:0.88rem;
            color:#555;
            border-bottom:1px solid #f0f0f0;
        }
        .w-soldby strong{color:#1a1a1a;}

        /* ── Product row ── */
        .w-product{
            display:flex;
            gap:14px;
            align-items:flex-start;
            padding:16px;
        }
        .w-product-thumb{
            width:72px;
            height:72px;
            border-radius:6px;
            background:#f5f5f5;
            border:1px solid #e8e8e8;
            flex-shrink:0;
            display:flex;
            align-items:center;
            justify-content:center;
            font-weight:700;
            color:#E30613;
            font-size:1rem;
            overflow:hidden;
        }
        .w-product-thumb img{width:100%;height:100%;object-fit:cover;}
        .w-product-info{flex:1;}
        .w-product-name{
            font-size:0.92rem;
            font-weight:600;
            line-height:1.4;
            margin-bottom:10px;
            color:#1a1a1a;
        }
        .w-product-row{
            display:flex;
            justify-content:space-between;
            font-size:0.88rem;
            color:#555;
            padding:3px 0;
        }
        .w-product-row strong{color:#1a1a1a;}

        /* ── Tracking toggle ── */
        .w-track-toggle{
            display:flex;
            align-items:center;
            gap:10px;
            padding:16px;
            cursor:pointer;
            border-top:1px solid #f0f0f0;
            user-select:none;
        }
        .w-truck-icon{
            width:22px;height:22px;
            flex-shrink:0;
        }
        .w-track-label{
            flex:1;
            font-size:0.95rem;
            font-weight:700;
            color:#E30613;
        }
        .w-chevron{
            width:18px;height:18px;
            stroke:#aaa;
            fill:none;
            stroke-width:2.5;
            stroke-linecap:round;
            transition:transform .25s;
        }
        .w-chevron.is-up{transform:rotate(180deg);}

        /* ── Timeline ── */
        .w-timeline{
            padding:8px 16px 16px 10px;
        }
        .w-stage{
            display:grid;
            grid-template-columns:36px 1fr;
            align-items:flex-start;
            position:relative;
        }
        .w-stage:not(:last-child) .w-dot-col::after{
            content:"";
            position:absolute;
            top:26px;
            left:17px;
            width:2px;
            bottom:-4px;
            background:#D1D5DB;
        }
        .w-stage.is-done:not(:last-child) .w-dot-col::after{background:#00A0A0;}

        .w-dot-col{
            position:relative;
            display:flex;
            flex-direction:column;
            align-items:center;
            padding-top:2px;
        }
        .w-icon-wrap{
            width:34px;height:34px;
            border-radius:50%;
            display:flex;
            align-items:center;
            justify-content:center;
            background:#f0f0f0;
            flex-shrink:0;
            z-index:1;
        }
        .w-icon-wrap svg{
            width:16px;height:16px;
            stroke:#aaa;
            fill:none;
            stroke-width:1.8;
            stroke-linecap:round;
            stroke-linejoin:round;
        }
        .w-stage.is-done .w-icon-wrap{
            background:#e6f7f7;
        }
        .w-stage.is-done .w-icon-wrap svg{stroke:#00A0A0;}
        .w-stage.is-current .w-icon-wrap{
            background:#fff3e6;
        }
        .w-stage.is-current .w-icon-wrap svg{stroke:#FF6B00;}

        /* Dot below icon */
        .w-dot{
            width:10px;height:10px;
            border-radius:50%;
            background:#D1D5DB;
            margin-top:5px;
            flex-shrink:0;
        }
        .w-stage.is-done .w-dot{background:#00A0A0;}
        .w-stage.is-current .w-dot{background:#FF6B00;}

        .w-stage-body{
            padding:6px 0 22px 8px;
        }
        .w-stage-name{
            font-size:0.92rem;
            font-weight:600;
            color:#aaa;
            line-height:1.3;
        }
        .w-stage.is-done .w-stage-name,
        .w-stage.is-current .w-stage-name{
            color:#1a1a1a;
        }
        .w-stage-date{
            font-size:0.78rem;
            color:#aaa;
            margin-top:2px;
        }
        .w-stage.is-done .w-stage-date,
        .w-stage.is-current .w-stage-date{
            color:#777;
        }

        /* ── Order info section ── */
        .w-info-row{
            display:flex;
            justify-content:space-between;
            align-items:center;
            padding:12px 16px;
            border-bottom:1px solid #f5f5f5;
            font-size:0.9rem;
        }
        .w-info-label{color:#777;}
        .w-info-value{font-weight:600;color:#1a1a1a;}
        .w-info-value.is-red{color:#E30613;}

        /* ── Copy button ── */
        .w-copy-btn{
            background:transparent;
            border:1.5px solid #E30613;
            color:#E30613;
            font-size:0.8rem;
            font-weight:700;
            padding:5px 12px;
            border-radius:4px;
            cursor:pointer;
            font-family:inherit;
        }

        /* ── Support form ── */
        .w-form-section{padding:16px;}
        .w-form-title{
            font-size:0.95rem;
            font-weight:700;
            margin-bottom:14px;
            color:#1a1a1a;
        }
        .w-field{
            display:grid;
            gap:6px;
            margin-bottom:14px;
        }
        .w-field label{
            font-size:0.82rem;
            font-weight:700;
            color:#555;
            text-transform:uppercase;
            letter-spacing:0.04em;
        }
        .w-field input,
        .w-field textarea,
        .w-field select{
            width:100%;
            padding:11px 13px;
            border:1.5px solid #ddd;
            border-radius:6px;
            font:inherit;
            font-size:0.92rem;
            color:#1a1a1a;
            outline:none;
            background:#fff;
            transition:border-color .2s;
        }
        .w-field input:focus,
        .w-field textarea:focus,
        .w-field select:focus{border-color:#E30613;}
        .w-field input.is-invalid,
        .w-field textarea.is-invalid,
        .w-field select.is-invalid{border-color:#c00;box-shadow:0 0 0 3px rgba(200,0,0,.08);}
        .w-field textarea{min-height:100px;resize:vertical;}
        .w-error{color:#c00;font-size:0.8rem;font-weight:600;min-height:1em;}

        .w-submit-btn{
            width:100%;
            padding:14px;
            background:#E30613;
            color:#fff;
            border:none;
            border-radius:6px;
            font:inherit;
            font-size:0.95rem;
            font-weight:700;
            cursor:pointer;
            transition:background .2s;
        }
        .w-submit-btn:hover{background:#c0000f;}
        .w-submit-btn:disabled{opacity:.5;cursor:not-allowed;}

        .w-form-status{
            display:none;
            padding:12px 14px;
            border-radius:6px;
            font-size:0.88rem;
            font-weight:600;
            margin-bottom:12px;
        }
        .w-form-status.is-visible{display:block;}
        .w-form-status.is-success{background:#e6f4ea;color:#1a6e2e;border:1px solid #b7dfc1;}
        .w-form-status.is-error{background:#fdecea;color:#9c1c1c;border:1px solid #f5c2c2;}

        /* ── Section divider label ── */
        .w-section-label{
            padding:10px 16px;
            font-size:0.76rem;
            font-weight:700;
            color:#999;
            text-transform:uppercase;
            letter-spacing:0.07em;
            background:#f9f9f9;
            border-top:1px solid #eee;
            border-bottom:1px solid #eee;
        }
    </style>
</head>
<body>

{{-- ── Top nav ── --}}
<nav class="w-nav">
    <img class="w-nav-logo"
         src="https://i.postimg.cc/VkxV8N88/worten-desktop-Dl-N-JMO0.jpg"
         alt="Worten">
    <div class="w-nav-icons">
        <svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        <svg viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
    </div>
</nav>

<div class="w-shell">

    {{-- ── Product card ── --}}
    <div class="w-card" style="margin-top:10px;">
        <div class="w-soldby">Vendido por: <strong>Worten</strong></div>

        <div class="w-product">
            <div class="w-product-thumb">
                @if ($track->product_image_url)
                    <img src="{{ $track->product_image_url }}" alt="{{ $track->product_name }}">
                @else
                    {{ $placeholder }}
                @endif
            </div>
            <div class="w-product-info">
                <div class="w-product-name">{{ $track->product_name }}</div>
                <div class="w-product-row">
                    <span>Encomenda</span>
                    <strong>#{{ $track->order_number }}</strong>
                </div>
                @if ($track->customer_name)
                <div class="w-product-row">
                    <span>Cliente</span>
                    <strong>{{ $track->customer_name }}</strong>
                </div>
                @endif
                @if ($track->shipping_address)
                <div class="w-product-row">
                    <span>Destino</span>
                    <strong>{{ $track->shipping_address }}</strong>
                </div>
                @endif
                @if ($track->estimated_delivery_at && ! $isDelivered)
                <div class="w-product-row" style="margin-top:6px;">
                    <span>Entrega estimada</span>
                    <strong style="color:#E30613;">{{ $track->estimated_delivery_at->format('d/m/Y') }}</strong>
                </div>
                @elseif ($isDelivered)
                <div class="w-product-row" style="margin-top:6px;">
                    <span></span>
                    <strong style="color:#00A0A0;">✓ Entregue</strong>
                </div>
                @endif
            </div>
        </div>

        {{-- ── Tracking timeline toggle ── --}}
        <div class="w-track-toggle" id="worten-track-toggle">
            <svg class="w-truck-icon" viewBox="0 0 24 24" fill="none" stroke="#FF6B00" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="1" y="3" width="15" height="13" rx="1"/>
                <path d="M16 8h4l3 5v3h-7V8z"/>
                <circle cx="5.5" cy="18.5" r="2.5"/>
                <circle cx="18.5" cy="18.5" r="2.5"/>
            </svg>
            <span class="w-track-label">Segue aqui o teu envio</span>
            <svg class="w-chevron is-up" id="worten-chevron" viewBox="0 0 24 24">
                <path d="M6 9l6 6 6-6"/>
            </svg>
        </div>

        <div id="worten-timeline" class="w-timeline">
            @foreach ($track->stages as $stage)
                @php
                    $state   = $stage->position < $currentPosition ? 'is-done'
                             : ($stage->position === $currentPosition ? 'is-current' : 'is-pending');
                    $iconKey = $stage->stage_key->value;
                    $icon    = $stageIcons[$iconKey] ?? $stageIcons['confirmed'];
                @endphp
                <div class="w-stage {{ $state }}">
                    <div class="w-dot-col">
                        <div class="w-icon-wrap">
                            <svg viewBox="0 0 24 24">{!! $icon !!}</svg>
                        </div>
                        @if (! $loop->last)
                            <div class="w-dot"></div>
                        @endif
                    </div>
                    <div class="w-stage-body">
                        <div class="w-stage-name">{{ $stage->title }}</div>
                        @if ($stage->reached_at)
                            <div class="w-stage-date">{{ $stage->reached_at->format('d/m/Y') }}</div>
                        @elseif ($state === 'is-current' && $stage->planned_for_at)
                            <div class="w-stage-date">Previsto {{ $stage->planned_for_at->format('d/m/Y') }}</div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- ── Tracking code card ── --}}
    <div class="w-card">
        <div class="w-section-label">Código de Rastreio</div>
        <div class="w-info-row">
            <div>
                <div style="font-size:0.78rem;color:#999;margin-bottom:4px;text-transform:uppercase;letter-spacing:.05em;">Código</div>
                <div class="w-info-value is-red" style="font-size:1.2rem;letter-spacing:0.1em;">{{ $track->tracking_code }}</div>
            </div>
            <button class="w-copy-btn" data-copy-value="{{ $track->tracking_code }}">Copiar</button>
        </div>
        <div class="w-info-row">
            <span class="w-info-label">Estado atual</span>
            <span class="w-info-value">{{ $track->current_stage->label() }}</span>
        </div>
        <div class="w-info-row">
            <span class="w-info-label">Link de rastreio</span>
            <button class="w-copy-btn" data-copy-value="{{ route('tracking.show', $track->tracking_code) }}">Copiar link</button>
        </div>
    </div>

    {{-- ── Support form ── --}}
    <div class="w-card">
        <div class="w-section-label">Suporte</div>
        <div class="w-form-section">
            <div class="w-form-title">Tens algum problema com a tua encomenda?</div>

            <form action="{{ route('tracking.issues.store', $track->tracking_code) }}"
                  method="POST" data-issue-form novalidate>
                @csrf

                <div class="w-field">
                    <label>Nome completo</label>
                    <input name="full_name" type="text" value="{{ old('full_name') }}"
                           placeholder="O teu nome" maxlength="120" required>
                    <span class="w-error" data-error-for="full_name">@error('full_name'){{ $message }}@enderror</span>
                </div>

                <div class="w-field">
                    <label>E-mail</label>
                    <input name="email" type="email" value="{{ old('email') }}"
                           placeholder="email@exemplo.com" maxlength="180" required>
                    <span class="w-error" data-error-for="email">@error('email'){{ $message }}@enderror</span>
                </div>

                <div class="w-field">
                    <label>Telefone <span style="font-weight:400;color:#aaa;">(opcional)</span></label>
                    <input name="phone" type="text" value="{{ old('phone') }}"
                           placeholder="+351 9XX XXX XXX" maxlength="40">
                </div>

                <div class="w-field">
                    <label>Tipo de problema</label>
                    <select name="issue_type" required>
                        <option value="">Seleciona o tipo de problema</option>
                        @foreach ($issueTypes as $value => $label)
                            <option value="{{ $value }}" @selected(old('issue_type') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <span class="w-error" data-error-for="issue_type">@error('issue_type'){{ $message }}@enderror</span>
                </div>

                <div class="w-field">
                    <label>Descrição</label>
                    <textarea name="description" placeholder="Descreve o teu problema em detalhe..."
                              minlength="10" maxlength="3000" required>{{ old('description') }}</textarea>
                    <span class="w-error" data-error-for="description">@error('description'){{ $message }}@enderror</span>
                </div>

                <div class="w-form-status" data-form-status></div>

                <button type="submit" class="w-submit-btn"
                        data-submit-label="Enviar pedido de suporte"
                        data-submitting-label="A enviar...">
                    Enviar pedido de suporte
                </button>
            </form>
        </div>
    </div>

</div>

<script>
    // Timeline toggle
    document.getElementById('worten-track-toggle').addEventListener('click', function () {
        const body    = document.getElementById('worten-timeline');
        const chevron = document.getElementById('worten-chevron');
        const isOpen  = body.style.display !== 'none';
        body.style.display = isOpen ? 'none' : 'block';
        chevron.classList.toggle('is-up', !isOpen);
    });

    // Copy buttons
    document.querySelectorAll('[data-copy-value]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            navigator.clipboard.writeText(btn.dataset.copyValue).then(function () {
                const orig = btn.textContent;
                btn.textContent = 'Copiado!';
                setTimeout(function () { btn.textContent = orig; }, 1600);
            });
        });
    });

    // Issue form
    const issueForm = document.querySelector('[data-issue-form]');
    if (issueForm) {
        const statusBox    = issueForm.querySelector('[data-form-status]');
        const submitBtn    = issueForm.querySelector('[type="submit"]');
        const requiredFields = ['full_name', 'email', 'issue_type', 'description'];
        const emailPattern   = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        const setStatus = function (msg, type) {
            statusBox.textContent = msg || '';
            statusBox.className   = 'w-form-status' + (msg ? ' is-visible ' + (type === 'success' ? 'is-success' : 'is-error') : '');
        };

        const validateField = function (field, show) {
            const value = field.value.trim();
            let msg = '';
            if (requiredFields.includes(field.name) && !value) msg = 'Campo obrigatório.';
            else if (field.name === 'email' && value && !emailPattern.test(value)) msg = 'E-mail inválido.';
            else if (field.name === 'description' && value && value.length < 10) msg = 'Mínimo 10 caracteres.';

            if (show !== false) {
                const node = issueForm.querySelector('[data-error-for="' + field.name + '"]');
                if (node) node.textContent = msg;
                field.classList.toggle('is-invalid', !!msg);
            }
            return !msg;
        };

        const updateBtn = function () {
            const ok = Array.from(issueForm.elements)
                .filter(el => ['INPUT','TEXTAREA','SELECT'].includes(el.tagName) && el.name)
                .every(f => validateField(f, false));
            submitBtn.disabled = !ok;
        };

        Array.from(issueForm.elements)
            .filter(el => ['INPUT','TEXTAREA','SELECT'].includes(el.tagName) && el.name)
            .forEach(function (f) {
                f.addEventListener('input',  function () { validateField(f); updateBtn(); });
                f.addEventListener('change', function () { validateField(f); updateBtn(); });
                f.addEventListener('blur',   function () { validateField(f); });
            });

        updateBtn();

        issueForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            setStatus();
            const fields  = Array.from(issueForm.elements).filter(el => ['INPUT','TEXTAREA','SELECT'].includes(el.tagName) && el.name);
            if (!fields.every(f => validateField(f, true))) { updateBtn(); return; }

            submitBtn.disabled    = true;
            submitBtn.textContent = submitBtn.dataset.submittingLabel;

            try {
                const res = await fetch(issueForm.action, {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    body: new FormData(issueForm),
                });
                const payload = await res.json().catch(() => ({}));

                if (res.status === 422 && payload.errors) {
                    Object.entries(payload.errors).forEach(([name, msgs]) => {
                        const f = issueForm.querySelector('[name="' + name + '"]');
                        const n = issueForm.querySelector('[data-error-for="' + name + '"]');
                        if (f && n) { f.classList.add('is-invalid'); n.textContent = msgs[0] || ''; }
                    });
                    setStatus('Corrija os erros antes de enviar.', 'error');
                    return;
                }
                if (!res.ok) { setStatus('Erro ao enviar. Tente novamente.', 'error'); return; }

                issueForm.reset();
                issueForm.querySelectorAll('.is-invalid').forEach(f => f.classList.remove('is-invalid'));
                issueForm.querySelectorAll('[data-error-for]').forEach(n => n.textContent = '');
                setStatus(payload.message || 'Pedido enviado com sucesso!', 'success');
            } catch {
                setStatus('Erro ao enviar. Tente novamente.', 'error');
            } finally {
                submitBtn.textContent = submitBtn.dataset.submitLabel;
                updateBtn();
            }
        });
    }
</script>
</body>
</html>
