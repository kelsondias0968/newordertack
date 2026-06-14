@extends('layouts.tracking')

@section('title', __('tracking.index.title'))

@section('content')
    <section class="hero-grid" style="margin-bottom: 22px;">
        <article class="card card-section stack">
            <span class="eyebrow">{{ __('tracking.index.eyebrow') }}</span>
            <div class="stack">
                <h1>{{ __('tracking.index.heading') }}</h1>
                <p>{{ __('tracking.index.description') }}</p>
            </div>

            <form action="{{ route('tracking.lookup') }}" method="POST" class="stack" data-track-form>
                @csrf
                <div class="field">
                    <label for="tracking_code">{{ __('tracking.common.tracking_code') }}</label>
                    <div class="snake-input-wrap">
                        <input
                            id="tracking_code"
                            name="tracking_code"
                            type="text"
                            required
                            value="{{ old('tracking_code', $generatedTrack?->tracking_code) }}"
                            placeholder="{{ __('tracking.index.placeholder') }}">
                    </div>
                    @error('tracking_code')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="btn" data-track-submit disabled>{{ __('tracking.actions.track_order') }}</button>
            </form>

            <div class="stats-grid two">
                <div class="metric">
                    <strong>{{ __('tracking.index.auto_title') }}</strong>
                    <p>{{ __('tracking.index.auto_text') }}</p>
                </div>
                <div class="metric">
                    <strong>{{ __('tracking.index.manual_title') }}</strong>
                    <p>{{ __('tracking.index.manual_text') }}</p>
                </div>
            </div>
        </article>

        <aside class="card card-section stack">
            @if ($generatedTrack)
                <span class="eyebrow">{{ __('tracking.index.generated_eyebrow') }}</span>
                <div class="code-box stack">
                    <div>
                        <div class="code-label">{{ __('tracking.index.generated_label') }}</div>
                        <div class="tracking-code">{{ $generatedTrack->tracking_code }}</div>
                    </div>

                    <div class="inline-actions">
                        <button type="button" class="btn-secondary" data-copy-value="{{ $generatedTrack->tracking_code }}">{{ __('tracking.actions.copy_code') }}</button>
                        <a href="{{ route('tracking.show', $generatedTrack->tracking_code) }}" class="btn-ghost">{{ __('tracking.actions.open_tracking') }}</a>
                    </div>
                </div>
            @else
                <span class="eyebrow">{{ __('tracking.index.structure_eyebrow') }}</span>
                <div class="stack">
                    <h2>{{ __('tracking.index.structure_heading') }}</h2>
                    <p>{{ __('tracking.index.structure_text') }}</p>
                </div>
            @endif
        </aside>
    </section>

    <section class="card card-section hero-grid">
        <article class="stack" style="align-content: center;">
            <span class="eyebrow">{{ __('tracking.index.delivery_eyebrow') }}</span>
            <div class="stack">
                <h2>{{ __('tracking.index.delivery_heading') }}</h2>
                <p>{{ __('tracking.index.delivery_text') }}</p>
            </div>
        </article>

        <aside>
            <img
                src="{{ asset('assets/Free-Delivery.webp') }}"
                alt="{{ __('tracking.index.delivery_heading') }}"
                style="width: 100%; max-height: 280px; object-fit: contain; object-position: center;">
        </aside>
    </section>
@endsection

@push('scripts')
    <script>
        const trackForm = document.querySelector('[data-track-form]');

        if (trackForm) {
            const trackInput = trackForm.querySelector('#tracking_code');
            const trackSubmit = trackForm.querySelector('[data-track-submit]');

            const toggleSubmit = function () {
                trackSubmit.disabled = trackInput.value.trim() === '';
            };

            toggleSubmit();
            trackInput.addEventListener('input', toggleSubmit);
            trackInput.addEventListener('change', toggleSubmit);
        }
    </script>
@endpush
