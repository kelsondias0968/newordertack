@extends('layouts.tracking')

@section('title', __('tracking.show.title', ['code' => $track->tracking_code]))

@section('content')
@php
    $marketplace = $track->marketplace ?? \App\Enums\Marketplace::Takealot;
    $branding    = $marketplace->branding();
    $brandColor  = $branding['color'];
    $currentPos  = $track->current_stage->position();
    $isDelivered = $track->current_stage === \App\Enums\TrackingStage::Delivered;
    $placeholder = strtoupper(substr($track->product_name, 0, 2));
@endphp

<style>
    :root {
        --mp-color: {{ $brandColor }};
        --mp-soft:  color-mix(in srgb, {{ $brandColor }} 10%, #fff);
        --mp-mid:   color-mix(in srgb, {{ $brandColor }} 30%, #fff);
        --stage-done:    #0EA5B0;
        --stage-current: #F97316;
        --stage-pending: #D1D5DB;
    }

    body { background: #f4f4f6; }

    .mp-shell {
        max-width: 520px;
        margin: 0 auto;
        padding: 0 0 48px;
    }

    /* ── Header ── */
    .mp-header {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 18px 20px;
        background: var(--mp-color);
        color: #fff;
    }

    .mp-header img {
        height: 36px;
        width: auto;
        filter: brightness(0) invert(1);
        object-fit: contain;
    }

    .mp-header-title {
        font-size: 1.05rem;
        font-weight: 700;
        letter-spacing: -0.01em;
    }

    /* ── Cards ── */
    .mp-card {
        margin: 14px 14px 0;
        background: #fff;
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.07);
    }

    .mp-card-head {
        padding: 14px 18px;
        border-bottom: 1px solid #f0f0f0;
        font-size: 0.78rem;
        font-weight: 700;
        color: #888;
        letter-spacing: 0.06em;
        text-transform: uppercase;
    }

    /* ── Product row ── */
    .mp-product {
        display: flex;
        gap: 14px;
        align-items: flex-start;
        padding: 18px;
    }

    .mp-product-img {
        width: 76px;
        height: 76px;
        border-radius: 10px;
        object-fit: cover;
        background: #f0f4f8;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        color: var(--mp-color);
        font-size: 1.1rem;
        overflow: hidden;
    }

    .mp-product-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .mp-product-info { flex: 1; }

    .mp-product-info h3 {
        margin: 0 0 6px;
        font-size: 0.97rem;
        font-weight: 600;
        color: #1a1a1a;
        line-height: 1.35;
    }

    .mp-product-meta {
        display: flex;
        flex-direction: column;
        gap: 4px;
        font-size: 0.84rem;
        color: #777;
    }

    .mp-product-meta span { display: block; }

    /* ── Seller row ── */
    .mp-seller {
        padding: 10px 18px 14px;
        font-size: 0.84rem;
        color: #555;
        border-top: 1px solid #f5f5f5;
    }
    .mp-seller strong { color: #1a1a1a; }

    /* ── Timeline toggle ── */
    .mp-timeline-toggle {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 18px;
        cursor: pointer;
        user-select: none;
    }

    .mp-timeline-toggle-left {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--mp-color);
    }

    .mp-timeline-toggle-left svg {
        width: 20px;
        height: 20px;
        flex-shrink: 0;
    }

    .mp-chevron {
        width: 20px;
        height: 20px;
        color: #aaa;
        transition: transform 0.25s;
    }

    .mp-chevron.is-open { transform: rotate(180deg); }

    /* ── Timeline ── */
    .mp-timeline {
        padding: 0 18px 18px;
        display: grid;
        gap: 0;
    }

    .mp-stage {
        display: grid;
        grid-template-columns: 32px 1fr;
        gap: 0 12px;
        position: relative;
    }

    .mp-stage:not(:last-child) .mp-dot-col::after {
        content: "";
        position: absolute;
        top: 22px;
        left: 10px;
        width: 2px;
        bottom: -4px;
        background: var(--stage-pending);
    }

    .mp-stage.is-done:not(:last-child) .mp-dot-col::after,
    .mp-stage.is-current:not(:last-child) .mp-dot-col::after {
        background: var(--stage-done);
    }

    .mp-dot-col {
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        padding-top: 4px;
    }

    .mp-dot {
        width: 22px;
        height: 22px;
        border-radius: 50%;
        background: var(--stage-pending);
        border: 3px solid #fff;
        box-shadow: 0 0 0 2px var(--stage-pending);
        flex-shrink: 0;
        z-index: 1;
    }

    .mp-stage.is-done .mp-dot {
        background: var(--stage-done);
        box-shadow: 0 0 0 2px var(--stage-done);
    }

    .mp-stage.is-current .mp-dot {
        background: var(--stage-current);
        box-shadow: 0 0 0 2px var(--stage-current);
    }

    .mp-stage-body {
        padding: 2px 0 20px;
    }

    .mp-stage-name {
        font-size: 0.94rem;
        font-weight: 600;
        color: #1a1a1a;
        line-height: 1.3;
    }

    .mp-stage.is-pending .mp-stage-name {
        color: #aaa;
        font-weight: 500;
    }

    .mp-stage-date {
        margin-top: 3px;
        font-size: 0.78rem;
        color: #aaa;
    }

    .mp-stage.is-done .mp-stage-date,
    .mp-stage.is-current .mp-stage-date {
        color: #888;
    }

    /* ── Summary ── */
    .mp-summary {
        display: grid;
        gap: 8px;
        padding: 16px 18px;
        font-size: 0.9rem;
    }

    .mp-summary-row {
        display: flex;
        justify-content: space-between;
        color: #555;
    }

    .mp-summary-row.is-total {
        font-weight: 700;
        color: #1a1a1a;
        padding-top: 8px;
        border-top: 1px solid #f0f0f0;
        margin-top: 4px;
    }

    /* ── Tracking code ── */
    .mp-code-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 18px;
        gap: 12px;
    }

    .mp-code-label {
        font-size: 0.78rem;
        color: #888;
        margin-bottom: 4px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .mp-code-value {
        font-size: 1.1rem;
        font-weight: 700;
        letter-spacing: 0.1em;
        color: var(--mp-color);
    }

    .mp-copy-btn {
        flex-shrink: 0;
        padding: 8px 16px;
        border: 1.5px solid var(--mp-color);
        border-radius: 8px;
        background: transparent;
        color: var(--mp-color);
        font-size: 0.82rem;
        font-weight: 700;
        cursor: pointer;
        transition: background 0.15s;
    }

    .mp-copy-btn:hover { background: var(--mp-soft); }

    /* ── Issue form ── */
    .mp-form { padding: 0 18px 18px; }
    .mp-form .field input,
    .mp-form .field textarea,
    .mp-form .field select {
        border-radius: 10px;
        padding: 12px 14px;
    }
    .mp-form .btn {
        width: 100%;
        border-radius: 10px;
        background: var(--mp-color);
        box-shadow: none;
    }

    /* ── Estimated delivery banner ── */
    .mp-eta {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 18px;
        background: var(--mp-soft);
        border-top: 1px solid var(--mp-mid);
        font-size: 0.87rem;
        color: color-mix(in srgb, var(--mp-color) 80%, #000);
        font-weight: 600;
    }

    .mp-eta svg { width: 18px; height: 18px; flex-shrink: 0; }
</style>

{{-- Header --}}
<div class="mp-header">
    <img src="{{ $branding['logo'] }}" alt="{{ $marketplace->label() }}">
    <span class="mp-header-title">{{ __('tracking.show.title', ['code' => $track->tracking_code]) }}</span>
</div>

<div class="mp-shell">

    {{-- Product card --}}
    <div class="mp-card">
        <div class="mp-card-head">{{ $marketplace->label() }}</div>

        <div class="mp-product">
            <div class="mp-product-img">
                @if ($track->product_image_url)
                    <img src="{{ $track->product_image_url }}" alt="{{ $track->product_name }}">
                @else
                    {{ $placeholder }}
                @endif
            </div>
            <div class="mp-product-info">
                <h3>{{ $track->product_name }}</h3>
                <div class="mp-product-meta">
                    <span>{{ __('tracking.show.order_badge', ['number' => $track->order_number]) }}</span>
                    @if ($track->customer_name)
                        <span>{{ $track->customer_name }}</span>
                    @endif
                    @if ($track->shipping_address)
                        <span>{{ $track->shipping_address }}</span>
                    @endif
                </div>
            </div>
        </div>

        @if ($track->estimated_delivery_at)
            <div class="mp-eta">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="1" y="3" width="15" height="13" rx="2"/>
                    <path d="M16 8h4l3 5v3h-7V8z"/>
                    <circle cx="5.5" cy="18.5" r="2.5"/>
                    <circle cx="18.5" cy="18.5" r="2.5"/>
                </svg>
                {{ $isDelivered
                    ? __('tracking.show.delivered')
                    : __('tracking.common.estimated_delivery', ['date' => $track->estimated_delivery_at->format('d/m/Y')]) }}
            </div>
        @endif
    </div>

    {{-- Tracking code --}}
    <div class="mp-card">
        <div class="mp-code-row">
            <div>
                <div class="mp-code-label">{{ __('tracking.common.tracking_code') }}</div>
                <div class="mp-code-value">{{ $track->tracking_code }}</div>
            </div>
            <button class="mp-copy-btn" data-copy-value="{{ $track->tracking_code }}">
                {{ __('tracking.actions.copy_code') }}
            </button>
        </div>
    </div>

    {{-- Timeline --}}
    <div class="mp-card">
        <div class="mp-timeline-toggle" id="timeline-toggle">
            <div class="mp-timeline-toggle-left">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M5 12h14M12 5l7 7-7 7"/>
                </svg>
                {{ __('tracking.show.status_heading') }}
            </div>
            <svg class="mp-chevron is-open" id="timeline-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M6 9l6 6 6-6"/>
            </svg>
        </div>

        <div id="mp-timeline-body" class="mp-timeline">
            @foreach ($track->stages as $stage)
                @php
                    $state = $stage->position < $currentPos
                        ? 'is-done'
                        : ($stage->position === $currentPos ? 'is-current' : 'is-pending');
                @endphp
                <div class="mp-stage {{ $state }}">
                    <div class="mp-dot-col">
                        <div class="mp-dot"></div>
                    </div>
                    <div class="mp-stage-body">
                        <div class="mp-stage-name">{{ $stage->title }}</div>
                        @if ($stage->reached_at)
                            <div class="mp-stage-date">{{ $stage->reached_at->format('d/m/Y · H:i') }}</div>
                        @elseif ($state === 'is-current' && $stage->planned_for_at)
                            <div class="mp-stage-date">{{ __('tracking.common.planned_for', ['date' => $stage->planned_for_at->format('d/m/Y')]) }}</div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Support form --}}
    <div class="mp-card">
        <div class="mp-card-head">{{ __('tracking.show.support_eyebrow') }}</div>
        <div class="mp-form">
            <div class="stack" style="gap:10px; padding: 14px 0 16px;">
                <p style="font-size:0.9rem;">{{ __('tracking.show.support_text') }}</p>
            </div>

            <form action="{{ route('tracking.issues.store', $track->tracking_code) }}" method="POST" class="stack" style="gap:12px;" data-issue-form novalidate>
                @csrf

                <div class="field">
                    <label for="full_name">{{ __('tracking.show.issue_name') }}</label>
                    <input id="full_name" name="full_name" type="text" value="{{ old('full_name') }}" placeholder="{{ __('tracking.show.issue_placeholder_name') }}" maxlength="120" required>
                    <span class="error" data-error-for="full_name">@error('full_name'){{ $message }}@enderror</span>
                </div>

                <div class="field">
                    <label for="email">{{ __('tracking.show.issue_email') }}</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="{{ __('tracking.show.issue_placeholder_email') }}" maxlength="180" required>
                    <span class="error" data-error-for="email">@error('email'){{ $message }}@enderror</span>
                </div>

                <div class="field">
                    <label for="phone">{{ __('tracking.show.issue_phone') }}</label>
                    <input id="phone" name="phone" type="text" value="{{ old('phone') }}" placeholder="{{ __('tracking.show.issue_placeholder_phone') }}" maxlength="40">
                </div>

                <div class="field">
                    <label for="issue_type">{{ __('tracking.show.issue_type') }}</label>
                    <select id="issue_type" name="issue_type" required>
                        <option value="">{{ __('tracking.show.issue_select') }}</option>
                        @foreach ($issueTypes as $value => $label)
                            <option value="{{ $value }}" @selected(old('issue_type') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <span class="error" data-error-for="issue_type">@error('issue_type'){{ $message }}@enderror</span>
                </div>

                <div class="field">
                    <label for="description">{{ __('tracking.show.issue_description') }}</label>
                    <textarea id="description" name="description" placeholder="{{ __('tracking.show.issue_placeholder_description') }}" minlength="10" maxlength="3000" required>{{ old('description') }}</textarea>
                    <span class="error" data-error-for="description">@error('description'){{ $message }}@enderror</span>
                </div>

                <div class="form-status" data-form-status></div>

                <button type="submit" class="btn"
                    data-submit-label="{{ __('tracking.actions.submit_complaint') }}"
                    data-submitting-label="{{ __('tracking.actions.submitting') }}">
                    {{ __('tracking.actions.submit_complaint') }}
                </button>
            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    // Timeline toggle
    const toggle = document.getElementById('timeline-toggle');
    const body   = document.getElementById('mp-timeline-body');
    const chevron = document.getElementById('timeline-chevron');

    toggle.addEventListener('click', function () {
        const isOpen = body.style.display !== 'none';
        body.style.display = isOpen ? 'none' : 'grid';
        chevron.classList.toggle('is-open', !isOpen);
    });

    // Copy buttons
    const copiedText = @json(__('tracking.actions.copied'));
    document.querySelectorAll('[data-copy-value]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            navigator.clipboard.writeText(btn.dataset.copyValue).then(function () {
                const orig = btn.textContent;
                btn.textContent = copiedText;
                setTimeout(function () { btn.textContent = orig; }, 1600);
            });
        });
    });

    // Issue form
    const issueForm = document.querySelector('[data-issue-form]');
    if (issueForm) {
        const statusBox    = issueForm.querySelector('[data-form-status]');
        const submitButton = issueForm.querySelector('button[type="submit"]');
        const requiredFields = ['full_name', 'email', 'issue_type', 'description'];
        const emailPattern   = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        const setStatus = function (message = '', type = '') {
            statusBox.textContent = message;
            statusBox.className   = 'form-status';
            if (message) statusBox.classList.add('is-visible', type === 'success' ? 'is-success' : 'is-error');
        };

        const validateField = function (field, show = true) {
            const value = field.value.trim();
            let message = '';
            if (requiredFields.includes(field.name) && value === '') {
                message = @json(__('tracking.validation.required'));
            } else if (field.name === 'email' && value !== '' && !emailPattern.test(value)) {
                message = @json(__('tracking.validation.invalid_email'));
            } else if (field.name === 'description' && value !== '' && value.length < 10) {
                message = @json(__('tracking.validation.description_min'));
            }
            const errorNode = issueForm.querySelector(`[data-error-for="${field.name}"]`);
            if (show && errorNode) {
                errorNode.textContent = message;
                field.classList.toggle('is-invalid', message !== '');
            }
            return message === '';
        };

        const updateSubmitState = function () {
            const isValid = Array.from(issueForm.elements)
                .filter(el => ['INPUT','TEXTAREA','SELECT'].includes(el.tagName) && el.name)
                .every(f => validateField(f, false));
            submitButton.disabled = !isValid;
        };

        Array.from(issueForm.elements)
            .filter(el => ['INPUT','TEXTAREA','SELECT'].includes(el.tagName) && el.name)
            .forEach(function (field) {
                field.addEventListener('input',  function () { validateField(field); updateSubmitState(); });
                field.addEventListener('change', function () { validateField(field); updateSubmitState(); });
                field.addEventListener('blur',   function () { validateField(field); });
            });

        updateSubmitState();

        issueForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            setStatus();
            const fields  = Array.from(issueForm.elements).filter(el => ['INPUT','TEXTAREA','SELECT'].includes(el.tagName) && el.name);
            const isValid = fields.every(f => validateField(f, true));
            if (!isValid) { updateSubmitState(); return; }

            submitButton.disabled    = true;
            submitButton.textContent = submitButton.dataset.submittingLabel;

            try {
                const response = await fetch(issueForm.action, {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    body: new FormData(issueForm),
                });
                const payload = await response.json().catch(() => ({}));
                if (response.status === 422 && payload.errors) {
                    Object.entries(payload.errors).forEach(([name, msgs]) => {
                        const f = issueForm.querySelector(`[name="${name}"]`);
                        const n = issueForm.querySelector(`[data-error-for="${name}"]`);
                        if (f && n) { f.classList.add('is-invalid'); n.textContent = msgs[0] ?? ''; }
                    });
                    setStatus(@json(__('tracking.validation.fix_errors')), 'error');
                    return;
                }
                if (!response.ok) { setStatus(@json(__('tracking.validation.submit_error')), 'error'); return; }
                issueForm.reset();
                issueForm.querySelectorAll('.is-invalid').forEach(f => f.classList.remove('is-invalid'));
                issueForm.querySelectorAll('[data-error-for]').forEach(n => n.textContent = '');
                setStatus(payload.message ?? @json(__('tracking.flash.issue_submitted')), 'success');
            } catch {
                setStatus(@json(__('tracking.validation.submit_error')), 'error');
            } finally {
                submitButton.textContent = submitButton.dataset.submitLabel;
                updateSubmitState();
            }
        });
    }
</script>
@endpush
