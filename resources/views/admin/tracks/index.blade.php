@extends('layouts.tracking')

@section('title', __('tracking.admin.index_title'))

@section('content')
    <section class="stack" style="gap: 22px;">
        <div class="admin-grid">
            <article class="card card-section stack">
                <span class="eyebrow">{{ __('tracking.admin.new_eyebrow') }}</span>
                <div class="stack" style="gap: 8px;">
                    <h2>{{ __('tracking.admin.new_heading') }}</h2>
                    <p>{{ __('tracking.admin.new_text') }}</p>
                </div>

                <form action="{{ route('admin.tracks.store') }}" method="POST" class="stack">
                    @csrf

                    <div class="form-grid two">
                        <div class="field">
                            <label for="order_number">{{ __('tracking.admin.order_number') }}</label>
                            <input id="order_number" name="order_number" type="text" value="{{ old('order_number') }}" placeholder="Ex.: PG-411759">
                            @error('order_number')
                                <span class="error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="field">
                            <label for="tracking_code">{{ __('tracking.admin.tracking_code_optional') }}</label>
                            <input id="tracking_code" name="tracking_code" type="text" value="{{ old('tracking_code') }}" placeholder="{{ __('tracking.common.optional') }}">
                            @error('tracking_code')
                                <span class="error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="field">
                            <label for="customer_name">{{ __('tracking.admin.customer_name') }}</label>
                            <input id="customer_name" name="customer_name" type="text" value="{{ old('customer_name') }}">
                        </div>

                        <div class="field">
                            <label for="customer_phone">{{ __('tracking.admin.customer_phone') }}</label>
                            <input id="customer_phone" name="customer_phone" type="text" value="{{ old('customer_phone') }}">
                        </div>

                        <div class="field">
                            <label for="customer_email">{{ __('tracking.admin.customer_email') }}</label>
                            <input id="customer_email" name="customer_email" type="email" value="{{ old('customer_email') }}">
                            @error('customer_email')
                                <span class="error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="field">
                            <label for="placed_at">{{ __('tracking.admin.placed_at') }}</label>
                            <input id="placed_at" name="placed_at" type="datetime-local" value="{{ old('placed_at') }}">
                        </div>

                        <div class="field" style="grid-column: 1 / -1;">
                            <label for="product_name">{{ __('tracking.admin.product_name') }}</label>
                            <input id="product_name" name="product_name" type="text" value="{{ old('product_name') }}">
                            @error('product_name')
                                <span class="error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="field">
                            <label for="product_image_url">{{ __('tracking.admin.product_image') }}</label>
                            <input id="product_image_url" name="product_image_url" type="url" value="{{ old('product_image_url') }}" placeholder="https://...">
                        </div>

                        <div class="field">
                            <label for="current_stage">{{ __('tracking.admin.current_stage') }}</label>
                            <select id="current_stage" name="current_stage">
                                @foreach ($stageOptions as $value => $label)
                                    <option value="{{ $value }}" @selected(old('current_stage', 'confirmed') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="field">
                            <label for="preferred_locale">{{ __('tracking.admin.preferred_locale') }}</label>
                            <select id="preferred_locale" name="preferred_locale">
                                @foreach ($localeOptions as $value => $label)
                                    <option value="{{ $value }}" @selected(old('preferred_locale', 'en') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="field" style="grid-column: 1 / -1;">
                            <label for="shipping_address">{{ __('tracking.admin.shipping_address') }}</label>
                            <textarea id="shipping_address" name="shipping_address" placeholder="{{ __('tracking.admin.address_placeholder') }}">{{ old('shipping_address') }}</textarea>
                        </div>

                        <div class="field" style="grid-column: 1 / -1;">
                            <label for="notes">{{ __('tracking.admin.internal_notes') }}</label>
                            <textarea id="notes" name="notes" placeholder="{{ __('tracking.admin.notes_placeholder') }}">{{ old('notes') }}</textarea>
                        </div>

                        <div class="field">
                            <label for="notification_cc">{{ __('tracking.admin.notification_cc') }}</label>
                            <input id="notification_cc" name="notification_cc" type="text" value="{{ is_array(old('notification_cc')) ? implode(', ', old('notification_cc')) : old('notification_cc') }}" placeholder="ops@example.com, qa@example.com">
                            <small>{{ __('tracking.admin.notification_emails_help') }}</small>
                            @error('notification_cc.*')
                                <span class="error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="field">
                            <label for="notification_bcc">{{ __('tracking.admin.notification_bcc') }}</label>
                            <input id="notification_bcc" name="notification_bcc" type="text" value="{{ is_array(old('notification_bcc')) ? implode(', ', old('notification_bcc')) : old('notification_bcc') }}" placeholder="audit@example.com, archive@example.com">
                            <small>{{ __('tracking.admin.notification_emails_help') }}</small>
                            @error('notification_bcc.*')
                                <span class="error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <input type="hidden" name="auto_progress" value="0">
                    <label class="check">
                        <input type="checkbox" name="auto_progress" value="1" @checked(old('auto_progress', '1') == '1')>
                        {{ __('tracking.admin.auto_progress') }}
                    </label>

                    <div class="stack">
                        <h3>{{ __('tracking.admin.period_heading') }}</h3>
                        <div class="input-grid two">
                            @foreach ($defaultPeriods as $stage => $hours)
                                <div class="field">
                                    <label for="periods_{{ $stage }}">{{ $stageOptions[$stage] }}</label>
                                    <input
                                        id="periods_{{ $stage }}"
                                        name="periods[{{ $stage }}]"
                                        type="number"
                                        min="0"
                                        value="{{ old('periods.'.$stage, $hours) }}">
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <button type="submit" class="btn">{{ __('tracking.actions.create_track') }}</button>
                </form>
            </article>

            <article class="card card-section stack">
                <div class="inline-actions" style="justify-content: space-between;">
                    <div class="stack" style="gap: 8px;">
                        <span class="eyebrow">{{ __('tracking.admin.list_eyebrow') }}</span>
                        <h2>{{ __('tracking.admin.list_heading') }}</h2>
                    </div>

                    <form method="GET" action="{{ route('admin.tracks.index') }}" class="inline-actions">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('tracking.admin.search_placeholder') }}" style="padding: 14px 16px; min-width: 260px; border-radius: 16px; border: 1px solid var(--line); background: rgba(255,255,255,0.84);">
                        <button type="submit" class="btn-ghost">{{ __('tracking.actions.filter') }}</button>
                    </form>
                </div>

                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>{{ __('tracking.admin.table_tracking') }}</th>
                                <th>{{ __('tracking.admin.table_order') }}</th>
                                <th>{{ __('tracking.admin.table_product') }}</th>
                                <th>{{ __('tracking.admin.table_status') }}</th>
                                <th>{{ __('tracking.admin.table_eta') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($tracks as $track)
                                <tr>
                                    <td>{{ $track->tracking_code }}</td>
                                    <td>{{ $track->order_number }}</td>
                                    <td>{{ $track->product_name }}</td>
                                    <td>{{ $track->current_stage->label() }}</td>
                                    <td>{{ optional($track->estimated_delivery_at)->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <a href="{{ route('admin.tracks.show', $track) }}" class="link-inline">{{ __('tracking.actions.open') }}</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">{{ __('tracking.admin.no_tracks') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="pagination">
                    {{ $tracks->links() }}
                </div>
            </article>
        </div>
    </section>
@endsection
