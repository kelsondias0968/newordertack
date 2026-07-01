@php
    $currentPosition = $track->current_stage->position();
    $totalStages     = $track->stages->count();
    $isDelivered     = $track->current_stage === \App\Enums\TrackingStage::Delivered;
    $placeholder     = strtoupper(substr($track->product_name, 0, 2));
    $eta             = $track->estimated_delivery_at;

    // Amazon teal
    $teal  = '#007185';
    $tealBg = '#E8F4F5';
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Track Order — Amazon</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Amazon+Ember:wght@400;700&family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
        body{font-family:'Open Sans',Arial,sans-serif;background:#EAEDED;color:#0F1111;font-size:14px;}
        a{color:#007185;text-decoration:none;}
        a:hover{text-decoration:underline;color:#C7511F;}
        img{max-width:100%;display:block;}

        /* ── Top nav ── */
        .az-nav{
            background:#232F3E;
            padding:8px 16px;
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:12px;
        }
        .az-logo{
            height:32px;
            object-fit:contain;
            filter:brightness(0) invert(1);
        }
        .az-search{
            flex:1;
            max-width:600px;
            display:flex;
            border-radius:4px;
            overflow:hidden;
        }
        .az-search input{
            flex:1;
            padding:8px 12px;
            border:none;
            font-size:14px;
            outline:none;
            background:#fff;
        }
        .az-search-btn{
            background:#FEBD69;
            border:none;
            padding:0 14px;
            cursor:pointer;
            display:flex;
            align-items:center;
        }
        .az-search-btn svg{width:18px;height:18px;stroke:#111;fill:none;stroke-width:2;}
        .az-nav-links{
            display:flex;
            gap:16px;
        }
        .az-nav-links a{
            color:#fff;
            font-size:13px;
            white-space:nowrap;
        }

        /* ── Shell ── */
        .az-shell{
            max-width:700px;
            margin:0 auto;
            padding:0 0 48px;
        }

        /* ── Hero ── */
        .az-hero{
            background: linear-gradient(170deg, #C8E6E8 0%, #EAF4F5 60%, #EAEDED 100%);
            padding:22px 20px 18px;
        }
        .az-hero-row{
            display:flex;
            align-items:flex-start;
            justify-content:space-between;
            gap:12px;
            margin-bottom:16px;
        }
        .az-hero-title{
            font-size:1.45rem;
            font-weight:700;
            color:#0F1111;
            line-height:1.2;
        }
        .az-hero-title span{color:#007185;}
        .az-see-all{
            font-size:13px;
            color:#007185;
            white-space:nowrap;
            margin-top:4px;
        }
        .az-product-row{
            display:flex;
            align-items:center;
            gap:14px;
        }
        .az-product-thumb{
            width:80px;
            height:80px;
            background:#fff;
            border:1px solid #ddd;
            border-radius:4px;
            display:flex;
            align-items:center;
            justify-content:center;
            font-weight:700;
            color:#007185;
            font-size:1.1rem;
            flex-shrink:0;
            overflow:hidden;
        }
        .az-product-thumb img{width:100%;height:100%;object-fit:contain;padding:4px;}

        /* ── Progress bar ── */
        .az-progress-wrap{
            background:#fff;
            padding:22px 20px 20px;
            border-bottom:1px solid #ddd;
        }
        .az-stage-label{
            font-size:1.05rem;
            font-weight:700;
            text-align:center;
            margin-bottom:18px;
            color:#0F1111;
        }
        .az-progress{
            position:relative;
            display:flex;
            align-items:center;
            justify-content:space-between;
            padding:0 16px;
            margin-bottom:12px;
        }
        /* Connecting line behind dots */
        .az-progress::before{
            content:"";
            position:absolute;
            left:32px;
            right:32px;
            top:14px;
            height:3px;
            background:#D5D9D9;
            z-index:0;
        }
        .az-progress-fill{
            position:absolute;
            left:32px;
            top:14px;
            height:3px;
            background:#007185;
            z-index:1;
            transition:width .4s;
        }
        .az-step{
            display:flex;
            flex-direction:column;
            align-items:center;
            gap:8px;
            z-index:2;
            flex:1;
        }
        .az-dot{
            width:28px;
            height:28px;
            border-radius:50%;
            background:#D5D9D9;
            display:flex;
            align-items:center;
            justify-content:center;
            flex-shrink:0;
        }
        .az-dot.done{background:#007185;}
        .az-dot.done svg{display:block;}
        .az-dot svg{display:none;width:15px;height:15px;stroke:#fff;stroke-width:2.5;fill:none;}
        .az-dot.current{
            background:#fff;
            border:3px solid #007185;
        }
        .az-step-label{
            font-size:11px;
            font-weight:600;
            color:#565959;
            text-align:center;
            line-height:1.3;
        }
        .az-step.is-done .az-step-label,
        .az-step.is-current .az-step-label{
            color:#0F1111;
            font-weight:700;
        }
        .az-step-date{
            font-size:10px;
            color:#007185;
            text-align:center;
        }

        /* ── Cards ── */
        .az-card{
            background:#fff;
            margin:10px 0 0;
            border:1px solid #ddd;
        }
        .az-card-title{
            padding:14px 20px 0;
            font-size:1rem;
            font-weight:700;
            color:#0F1111;
        }
        .az-divider{
            border:none;
            border-top:1px solid #eee;
            margin:14px 0;
        }

        /* ── Info rows ── */
        .az-info-row{
            display:flex;
            justify-content:space-between;
            align-items:center;
            padding:10px 20px;
            font-size:13px;
            border-bottom:1px solid #f5f5f5;
        }
        .az-info-row:last-child{border-bottom:none;}
        .az-info-label{color:#565959;}
        .az-info-value{font-weight:600;color:#0F1111;}
        .az-info-value.teal{color:#007185;}

        /* ── CTA button ── */
        .az-btn{
            display:inline-block;
            padding:8px 18px;
            background:linear-gradient(to bottom,#f7dfa5,#f0c14b);
            border:1px solid #a88734;
            border-radius:3px;
            font-size:13px;
            font-weight:600;
            color:#111;
            cursor:pointer;
            text-align:center;
            text-decoration:none;
        }
        .az-btn:hover{background:linear-gradient(to bottom,#f5d78e,#eeb933);text-decoration:none;color:#111;}
        .az-btn-outline{
            display:block;
            width:calc(100% - 40px);
            margin:12px 20px 16px;
            padding:10px;
            background:#fff;
            border:1px solid #D5D9D9;
            border-radius:8px;
            font-size:14px;
            font-weight:600;
            color:#0F1111;
            cursor:pointer;
            text-align:center;
        }
        .az-btn-outline:hover{background:#F7F8F8;border-color:#999;}

        /* ── Code box ── */
        .az-code-box{
            margin:0 20px 16px;
            padding:14px 16px;
            background:#F0F2F2;
            border:1px solid #ddd;
            border-radius:4px;
        }
        .az-code-label{
            font-size:11px;
            color:#565959;
            text-transform:uppercase;
            letter-spacing:.06em;
            margin-bottom:6px;
        }
        .az-code-value{
            font-size:1.3rem;
            font-weight:700;
            letter-spacing:.1em;
            color:#007185;
        }
        .az-copy-btn{
            margin-top:8px;
            padding:5px 14px;
            background:#fff;
            border:1px solid #D5D9D9;
            border-radius:3px;
            font-size:12px;
            font-weight:600;
            cursor:pointer;
            color:#0F1111;
        }

        /* ── Form ── */
        .az-form-section{padding:14px 20px 18px;}
        .az-field{display:grid;gap:5px;margin-bottom:12px;}
        .az-field label{font-size:13px;font-weight:700;color:#0F1111;}
        .az-field input,
        .az-field textarea,
        .az-field select{
            width:100%;
            padding:8px 10px;
            border:1px solid #a6a6a6;
            border-radius:3px;
            font:inherit;
            font-size:13px;
            outline:none;
            background:#fff;
        }
        .az-field input:focus,
        .az-field textarea:focus,
        .az-field select:focus{
            border-color:#e77600;
            box-shadow:0 0 0 3px rgba(228,121,17,.25);
        }
        .az-field input.is-invalid,
        .az-field textarea.is-invalid,
        .az-field select.is-invalid{
            border-color:#c40000;
            box-shadow:0 0 0 3px rgba(196,0,0,.1);
        }
        .az-field textarea{min-height:90px;resize:vertical;}
        .az-error{color:#c40000;font-size:12px;font-weight:600;}
        .az-submit{
            width:100%;
            padding:10px;
            background:linear-gradient(to bottom,#f7dfa5,#f0c14b);
            border:1px solid #a88734;
            border-radius:3px;
            font:inherit;
            font-size:14px;
            font-weight:600;
            color:#111;
            cursor:pointer;
        }
        .az-submit:disabled{opacity:.5;cursor:not-allowed;}
        .az-form-status{
            display:none;
            padding:10px 12px;
            border-radius:3px;
            font-size:13px;
            font-weight:600;
            margin-bottom:12px;
            border:1px solid;
        }
        .az-form-status.is-visible{display:block;}
        .az-form-status.is-success{background:#EAF5EA;color:#167516;border-color:#00a020;}
        .az-form-status.is-error{background:#FFF0F0;color:#c40000;border-color:#c40000;}

        /* ── Footer ── */
        .az-footer{
            background:#232F3E;
            padding:18px 20px;
            margin-top:10px;
            text-align:center;
        }
        .az-footer img{margin:0 auto 10px;height:24px;filter:brightness(0) invert(1);}
        .az-footer p{font-size:11px;color:#aaa;}
    </style>
</head>
<body>

{{-- ── Nav ── --}}
<nav class="az-nav">
    <img class="az-logo"
         src="https://upload.wikimedia.org/wikipedia/commons/a/a9/Amazon_logo.svg"
         alt="Amazon">
    <div class="az-search">
        <input type="text" placeholder="Search Amazon">
        <button class="az-search-btn" aria-label="Search">
            <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
        </button>
    </div>
    <div class="az-nav-links">
        <a href="#">Returns &amp; Orders</a>
    </div>
</nav>

<div class="az-shell">

    {{-- ── Hero ── --}}
    <div class="az-hero">
        <div class="az-hero-row">
            <div>
                @if ($isDelivered)
                    <div class="az-hero-title">Your order was <span>delivered</span></div>
                @elseif ($eta)
                    <div class="az-hero-title">
                        Arriving
                        @if ($eta->isToday())
                            <span>today by {{ $eta->format('g A') }}</span>
                        @elseif ($eta->isTomorrow())
                            <span>tomorrow by {{ $eta->format('g A') }}</span>
                        @else
                            <span>{{ $eta->format('M j') }}</span>
                        @endif
                    </div>
                @else
                    <div class="az-hero-title">Order <span>#{{ $track->order_number }}</span></div>
                @endif
            </div>
            <a href="#" class="az-see-all">See all orders</a>
        </div>

        <div class="az-product-row">
            <div class="az-product-thumb">
                @if ($track->product_image_url)
                    <img src="{{ $track->product_image_url }}" alt="{{ $track->product_name }}">
                @else
                    {{ $placeholder }}
                @endif
            </div>
        </div>
    </div>

    {{-- ── Progress bar ── --}}
    @php
        $stages = $track->stages;
        $stageCount = $stages->count();
        $fillPct = $stageCount > 1
            ? min(100, round(($currentPosition - 1) / ($stageCount - 1) * 100))
            : 100;
    @endphp
    <div class="az-progress-wrap">
        <div class="az-stage-label">{{ $track->current_stage->label() }}</div>

        <div class="az-progress" id="az-progress-bar">
            <div class="az-progress-fill" style="width: {{ $fillPct }}%"></div>

            @foreach ($stages as $stage)
                @php
                    $isDone    = $stage->position < $currentPosition;
                    $isCurrent = $stage->position === $currentPosition;
                    $stepClass = $isDone ? 'is-done' : ($isCurrent ? 'is-current' : 'is-pending');
                    $dotClass  = $isDone ? 'done' : ($isCurrent ? 'current' : '');
                @endphp
                <div class="az-step {{ $stepClass }}">
                    <div class="az-dot {{ $dotClass }}">
                        <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                    <div class="az-step-label">{{ $stage->title }}</div>
                    @if ($stage->reached_at)
                        <div class="az-step-date">{{ $stage->reached_at->format('M j') }}</div>
                    @elseif ($isCurrent && $stage->planned_for_at)
                        <div class="az-step-date">Est. {{ $stage->planned_for_at->format('M j') }}</div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- ── Order info ── --}}
    <div class="az-card">
        <div class="az-card-title">Order details</div>
        <hr class="az-divider">
        <div class="az-info-row">
            <span class="az-info-label">Order number</span>
            <span class="az-info-value">#{{ $track->order_number }}</span>
        </div>
        @if ($track->customer_name)
        <div class="az-info-row">
            <span class="az-info-label">Ordered by</span>
            <span class="az-info-value">{{ $track->customer_name }}</span>
        </div>
        @endif
        <div class="az-info-row">
            <span class="az-info-label">Product</span>
            <span class="az-info-value">{{ Str::limit($track->product_name, 50) }}</span>
        </div>
        @if ($track->shipping_address)
        <div class="az-info-row">
            <span class="az-info-label">Shipping to</span>
            <span class="az-info-value">{{ $track->shipping_address }}</span>
        </div>
        @endif
        @if ($eta && !$isDelivered)
        <div class="az-info-row">
            <span class="az-info-label">Estimated delivery</span>
            <span class="az-info-value teal">{{ $eta->format('M j, Y') }}</span>
        </div>
        @endif
    </div>

    {{-- ── Tracking code ── --}}
    <div class="az-card" style="padding-top:14px;">
        <div class="az-card-title">Tracking information</div>
        <hr class="az-divider">
        <div class="az-code-box">
            <div class="az-code-label">Tracking code</div>
            <div class="az-code-value">{{ $track->tracking_code }}</div>
            <button class="az-copy-btn" data-copy-value="{{ $track->tracking_code }}">Copy code</button>
        </div>
        <button class="az-btn-outline" data-copy-value="{{ route('tracking.show', $track->tracking_code) }}">
            Copy tracking link
        </button>
    </div>

    {{-- ── Support form ── --}}
    <div class="az-card" style="padding-top:14px;">
        <div class="az-card-title">Need help with your order?</div>
        <hr class="az-divider">
        <div class="az-form-section">
            <form action="{{ route('tracking.issues.store', $track->tracking_code) }}"
                  method="POST" data-issue-form novalidate>
                @csrf

                <div class="az-field">
                    <label>Full name</label>
                    <input name="full_name" type="text" value="{{ old('full_name') }}"
                           placeholder="Your name" maxlength="120" required>
                    <span class="az-error" data-error-for="full_name">@error('full_name'){{ $message }}@enderror</span>
                </div>

                <div class="az-field">
                    <label>Email address</label>
                    <input name="email" type="email" value="{{ old('email') }}"
                           placeholder="name@example.com" maxlength="180" required>
                    <span class="az-error" data-error-for="email">@error('email'){{ $message }}@enderror</span>
                </div>

                <div class="az-field">
                    <label>Phone <span style="font-weight:400;color:#888;">(optional)</span></label>
                    <input name="phone" type="text" value="{{ old('phone') }}"
                           placeholder="+1 555 000 0000" maxlength="40">
                </div>

                <div class="az-field">
                    <label>Issue type</label>
                    <select name="issue_type" required>
                        <option value="">Select issue type</option>
                        @foreach ($issueTypes as $value => $label)
                            <option value="{{ $value }}" @selected(old('issue_type') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <span class="az-error" data-error-for="issue_type">@error('issue_type'){{ $message }}@enderror</span>
                </div>

                <div class="az-field">
                    <label>Describe your issue</label>
                    <textarea name="description" placeholder="Please describe your issue in detail..."
                              minlength="10" maxlength="3000" required>{{ old('description') }}</textarea>
                    <span class="az-error" data-error-for="description">@error('description'){{ $message }}@enderror</span>
                </div>

                <div class="az-form-status" data-form-status></div>

                <button type="submit" class="az-submit"
                        data-submit-label="Submit"
                        data-submitting-label="Submitting...">
                    Submit
                </button>
            </form>
        </div>
    </div>

    {{-- ── Footer ── --}}
    <div class="az-footer">
        <img src="https://upload.wikimedia.org/wikipedia/commons/a/a9/Amazon_logo.svg" alt="Amazon">
        <p>© {{ date('Y') }}, Amazon.com, Inc. or its affiliates</p>
    </div>

</div>

<script>
    // Copy buttons
    document.querySelectorAll('[data-copy-value]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            navigator.clipboard.writeText(btn.dataset.copyValue).then(function () {
                const orig = btn.textContent;
                btn.textContent = 'Copied!';
                setTimeout(function () { btn.textContent = orig; }, 1600);
            });
        });
    });

    // Issue form
    const issueForm = document.querySelector('[data-issue-form]');
    if (issueForm) {
        const statusBox = issueForm.querySelector('[data-form-status]');
        const submitBtn = issueForm.querySelector('[type="submit"]');
        const required  = ['full_name', 'email', 'issue_type', 'description'];
        const emailRe   = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        const setStatus = function (msg, type) {
            statusBox.textContent = msg || '';
            statusBox.className = 'az-form-status' + (msg ? ' is-visible ' + (type === 'success' ? 'is-success' : 'is-error') : '');
        };

        const validateField = function (f, show) {
            const v = f.value.trim();
            let msg = '';
            if (required.includes(f.name) && !v) msg = 'Required field.';
            else if (f.name === 'email' && v && !emailRe.test(v)) msg = 'Invalid email address.';
            else if (f.name === 'description' && v && v.length < 10) msg = 'Minimum 10 characters.';
            if (show !== false) {
                const node = issueForm.querySelector('[data-error-for="' + f.name + '"]');
                if (node) node.textContent = msg;
                f.classList.toggle('is-invalid', !!msg);
            }
            return !msg;
        };

        const updateBtn = function () {
            submitBtn.disabled = !Array.from(issueForm.elements)
                .filter(el => ['INPUT','TEXTAREA','SELECT'].includes(el.tagName) && el.name)
                .every(f => validateField(f, false));
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
            const fields = Array.from(issueForm.elements).filter(el => ['INPUT','TEXTAREA','SELECT'].includes(el.tagName) && el.name);
            if (!fields.every(f => validateField(f, true))) { updateBtn(); return; }

            submitBtn.disabled = true;
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
                    setStatus('Please fix the errors above.', 'error');
                    return;
                }
                if (!res.ok) { setStatus('Something went wrong. Please try again.', 'error'); return; }
                issueForm.reset();
                issueForm.querySelectorAll('.is-invalid').forEach(f => f.classList.remove('is-invalid'));
                issueForm.querySelectorAll('[data-error-for]').forEach(n => n.textContent = '');
                setStatus(payload.message || 'Your request was submitted successfully.', 'success');
            } catch {
                setStatus('Something went wrong. Please try again.', 'error');
            } finally {
                submitBtn.textContent = submitBtn.dataset.submitLabel;
                updateBtn();
            }
        });
    }
</script>
</body>
</html>
