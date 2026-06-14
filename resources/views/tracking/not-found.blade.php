@extends('layouts.tracking')

@section('title', __('tracking.not_found.title'))

@section('content')
    <section class="card card-section stack" style="max-width: 760px; margin: 0 auto;">
        <span class="eyebrow">{{ __('tracking.not_found.eyebrow') }}</span>

        <div class="stack">
            <h1 style="font-size: clamp(2rem, 4vw, 3.5rem);">{{ __('tracking.not_found.heading') }}</h1>
            <p>{{ __('tracking.not_found.text') }}</p>
        </div>

        <div class="code-box stack">
            <div>
                <div class="code-label">{{ __('tracking.common.tracking_code') }}</div>
                <div class="tracking-code" style="font-size: clamp(1.4rem, 3vw, 2.1rem); letter-spacing: 0.08em;">
                    {{ $trackingCode }}
                </div>
            </div>
            <p>{{ __('tracking.not_found.help') }}</p>
        </div>

        <div class="inline-actions">
            <a href="{{ route('tracking.index') }}" class="btn">{{ __('tracking.not_found.back_home') }}</a>
        </div>
    </section>
@endsection
