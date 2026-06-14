@extends('layouts.tracking')

@section('title', __('tracking.show.title', ['code' => $track->tracking_code]))

@section('content')
    @php
        $currentPosition = $track->current_stage->position();
        $isDelivered = $track->current_stage === \App\Enums\TrackingStage::Delivered;
        $placeholder = strtoupper(substr($track->product_name, 0, 2));
    @endphp

    <section class="stack" style="gap: 22px;">
        <div class="content-grid">
            <article class="card card-section stack">
                <div class="stack" style="gap: 10px;">
                    <span class="eyebrow">{{ __('tracking.show.order_badge', ['number' => $track->order_number]) }}</span>
                    <h2>{{ $isDelivered ? __('tracking.show.delivered') : __('tracking.show.on_the_way') }}</h2>
                    <p>{{ __('tracking.show.code_line', ['code' => $track->tracking_code]) }}</p>
                </div>

                <div class="product">
                    @if ($track->product_image_url)
                        <img src="{{ $track->product_image_url }}" alt="{{ $track->product_name }}" class="product-image">
                    @else
                        <div class="placeholder-image">{{ $placeholder }}</div>
                    @endif

                    <div class="stack" style="gap: 10px;">
                        <h3>{{ $track->product_name }}</h3>
                        @if ($track->customer_name)
                            <p>{{ __('tracking.show.customer_line', ['value' => $track->customer_name]) }}</p>
                        @endif
                        @if ($track->shipping_address)
                            <p>{{ __('tracking.show.destination_line', ['value' => $track->shipping_address]) }}</p>
                        @endif
                    </div>
                </div>

                <div class="stack">
                    <h3>{{ __('tracking.show.arrival_heading') }}</h3>
                    <div class="countdown" data-countdown="{{ optional($track->estimated_delivery_at)->toIso8601String() }}">
                        <div class="count-box">
                            <strong data-days>00</strong>
                            <span>{{ __('tracking.time.days') }}</span>
                        </div>
                        <div class="count-box">
                            <strong data-hours>00</strong>
                            <span>{{ __('tracking.time.hours') }}</span>
                        </div>
                        <div class="count-box">
                            <strong data-minutes>00</strong>
                            <span>{{ __('tracking.time.minutes') }}</span>
                        </div>
                        <div class="count-box">
                            <strong data-seconds>00</strong>
                            <span>{{ __('tracking.time.seconds') }}</span>
                        </div>
                    </div>

                    <div class="pill">
                        {{ $track->estimated_delivery_at
                            ? __('tracking.common.estimated_delivery', ['date' => $track->estimated_delivery_at->format('d/m/Y H:i')])
                            : __('tracking.common.no_estimate') }}
                    </div>
                </div>

                <div class="stack">
                    <h3>{{ __('tracking.show.status_heading') }}</h3>
                    <div class="timeline">
                        @foreach ($track->stages as $stage)
                            @php
                                $state = $stage->position < $currentPosition ? 'completed' : ($stage->position === $currentPosition ? 'current' : 'pending');
                            @endphp
                            <div class="timeline-item {{ $state }}">
                                <div class="inline-actions" style="justify-content: space-between; margin-bottom: 6px;">
                                    <h3>{{ $stage->title }}</h3>
                                    <span class="status-badge {{ $state }}">
                                        {{ $state === 'completed' ? __('tracking.states.completed') : ($state === 'current' ? __('tracking.states.current') : __('tracking.states.pending')) }}
                                    </span>
                                </div>
                                <p>{{ $stage->description }}</p>
                                <p class="helper" style="margin-top: 6px;">
                                    {{ __('tracking.common.planned_for', ['date' => $stage->planned_for_at->format('d/m/Y H:i')]) }}
                                    @if ($stage->reached_at)
                                        | {{ __('tracking.common.reached_at', ['date' => $stage->reached_at->format('d/m/Y H:i')]) }}
                                    @endif
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </article>

            <aside class="stack">
                <div class="card card-section stack">
                    <span class="eyebrow">{{ __('tracking.show.summary_eyebrow') }}</span>
                    <div class="code-box stack">
                        <div>
                            <div class="code-label">{{ __('tracking.common.tracking_code') }}</div>
                            <div class="tracking-code" style="font-size: 2rem;">{{ $track->tracking_code }}</div>
                        </div>
                        <button type="button" class="btn-secondary" data-copy-value="{{ $track->tracking_code }}">{{ __('tracking.actions.copy_code') }}</button>
                    </div>

                    <div class="stats-grid two">
                        <div class="metric">
                            <strong>{{ $track->current_stage->label() }}</strong>
                            <p>{{ __('tracking.show.current_stage_title') }}</p>
                        </div>
                        <div class="metric">
                            <strong>{{ $track->auto_progress ? __('tracking.states.auto') : __('tracking.states.manual') }}</strong>
                            <p>{{ __('tracking.show.progress_mode_title') }}</p>
                        </div>
                    </div>
                </div>

                <div class="card card-section stack">
                    <span class="eyebrow">{{ __('tracking.show.share_eyebrow') }}</span>
                    <div class="field">
                        <label for="public_link">{{ __('tracking.common.public_link') }}</label>
                        <input id="public_link" type="text" readonly value="{{ route('tracking.show', $track->tracking_code) }}">
                    </div>
                    <button type="button" class="btn-ghost" data-copy-value="{{ route('tracking.show', $track->tracking_code) }}">{{ __('tracking.actions.copy_link') }}</button>
                </div>
            </aside>
        </div>

        <article class="card card-section stack">
            <span class="eyebrow">{{ __('tracking.show.support_eyebrow') }}</span>
            <div class="stack" style="gap: 8px;">
                <h2>{{ __('tracking.show.support_heading') }}</h2>
                <p>{{ __('tracking.show.support_text') }}</p>
            </div>

            <form action="{{ route('tracking.issues.store', $track->tracking_code) }}" method="POST" class="form-grid two" data-issue-form novalidate>
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
                    <span class="error" data-error-for="phone">@error('phone'){{ $message }}@enderror</span>
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

                <div class="field" style="grid-column: 1 / -1;">
                    <label for="description">{{ __('tracking.show.issue_description') }}</label>
                    <textarea id="description" name="description" placeholder="{{ __('tracking.show.issue_placeholder_description') }}" minlength="10" maxlength="3000" required>{{ old('description') }}</textarea>
                    <span class="error" data-error-for="description">@error('description'){{ $message }}@enderror</span>
                </div>

                <div class="form-status" style="grid-column: 1 / -1;" data-form-status></div>

                <div style="grid-column: 1 / -1;">
                    <button type="submit" class="btn" data-submit-label="{{ __('tracking.actions.submit_complaint') }}" data-submitting-label="{{ __('tracking.actions.submitting') }}">{{ __('tracking.actions.submit_complaint') }}</button>
                </div>
            </form>
        </article>
    </section>
@endsection

@push('scripts')
    <script>
        const countdown = document.querySelector('[data-countdown]');

        if (countdown) {
            const target = new Date(countdown.dataset.countdown).getTime();
            const days = countdown.querySelector('[data-days]');
            const hours = countdown.querySelector('[data-hours]');
            const minutes = countdown.querySelector('[data-minutes]');
            const seconds = countdown.querySelector('[data-seconds]');

            const render = function () {
                const now = Date.now();
                let diff = Math.max(0, target - now);

                const dayValue = Math.floor(diff / 86400000);
                diff -= dayValue * 86400000;

                const hourValue = Math.floor(diff / 3600000);
                diff -= hourValue * 3600000;

                const minuteValue = Math.floor(diff / 60000);
                diff -= minuteValue * 60000;

                const secondValue = Math.floor(diff / 1000);

                days.textContent = String(dayValue).padStart(2, '0');
                hours.textContent = String(hourValue).padStart(2, '0');
                minutes.textContent = String(minuteValue).padStart(2, '0');
                seconds.textContent = String(secondValue).padStart(2, '0');
            };

            render();
            setInterval(render, 1000);
        }

        const issueForm = document.querySelector('[data-issue-form]');

        if (issueForm) {
            const statusBox = issueForm.querySelector('[data-form-status]');
            const submitButton = issueForm.querySelector('button[type="submit"]');
            const requiredFields = ['full_name', 'email', 'issue_type', 'description'];
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            const setStatus = function (message = '', type = '') {
                statusBox.textContent = message;
                statusBox.className = 'form-status';

                if (message) {
                    statusBox.classList.add('is-visible', type === 'success' ? 'is-success' : 'is-error');
                }
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

                if (show) {
                    errorNode.textContent = message;
                    field.classList.toggle('is-invalid', message !== '');
                }

                return message === '';
            };

            const updateSubmitState = function () {
                const isValid = Array.from(issueForm.elements)
                    .filter((element) => ['INPUT', 'TEXTAREA', 'SELECT'].includes(element.tagName) && element.name)
                    .every((field) => validateField(field, false));

                submitButton.disabled = !isValid;
            };

            Array.from(issueForm.elements)
                .filter((element) => ['INPUT', 'TEXTAREA', 'SELECT'].includes(element.tagName) && element.name)
                .forEach((field) => {
                    field.addEventListener('input', function () {
                        validateField(field);
                        updateSubmitState();
                    });

                    field.addEventListener('change', function () {
                        validateField(field);
                        updateSubmitState();
                    });

                    field.addEventListener('blur', function () {
                        validateField(field);
                    });
                });

            updateSubmitState();

            issueForm.addEventListener('submit', async function (event) {
                event.preventDefault();
                setStatus();

                const fields = Array.from(issueForm.elements)
                    .filter((element) => ['INPUT', 'TEXTAREA', 'SELECT'].includes(element.tagName) && element.name);

                const isValid = fields.every((field) => validateField(field, true));

                if (!isValid) {
                    updateSubmitState();
                    return;
                }

                submitButton.disabled = true;
                submitButton.textContent = submitButton.dataset.submittingLabel;

                try {
                    const response = await fetch(issueForm.action, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: new FormData(issueForm),
                    });

                    const payload = await response.json().catch(() => ({}));

                    if (response.status === 422 && payload.errors) {
                        Object.entries(payload.errors).forEach(([fieldName, messages]) => {
                            const field = issueForm.querySelector(`[name="${fieldName}"]`);
                            const errorNode = issueForm.querySelector(`[data-error-for="${fieldName}"]`);

                            if (field && errorNode) {
                                field.classList.add('is-invalid');
                                errorNode.textContent = messages[0] ?? '';
                            }
                        });

                        setStatus(@json(__('tracking.validation.fix_errors')), 'error');
                        return;
                    }

                    if (!response.ok) {
                        setStatus(@json(__('tracking.validation.submit_error')), 'error');
                        return;
                    }

                    issueForm.reset();
                    issueForm.querySelectorAll('.is-invalid').forEach((field) => field.classList.remove('is-invalid'));
                    issueForm.querySelectorAll('[data-error-for]').forEach((node) => node.textContent = '');
                    setStatus(payload.message ?? @json(__('tracking.flash.issue_submitted')), 'success');
                } catch (error) {
                    setStatus(@json(__('tracking.validation.submit_error')), 'error');
                } finally {
                    submitButton.textContent = submitButton.dataset.submitLabel;
                    updateSubmitState();
                }
            });
        }
    </script>
@endpush
