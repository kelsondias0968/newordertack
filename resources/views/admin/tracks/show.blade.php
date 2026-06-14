@extends('layouts.tracking')

@section('title', __('tracking.admin.show_title', ['code' => $track->tracking_code]))

@section('content')
    <section class="stack" style="gap: 22px;">
        <div class="detail-grid">
            <article class="card card-section stack">
                <div class="inline-actions" style="justify-content: space-between;">
                    <div class="stack" style="gap: 8px;">
                        <span class="eyebrow">{{ __('tracking.admin.panel_eyebrow') }}</span>
                        <h2>{{ $track->product_name }}</h2>
                        <p>{{ __('tracking.admin.order_code_line', ['order' => $track->order_number, 'code' => $track->tracking_code]) }}</p>
                    </div>

                    <a href="{{ route('tracking.index', ['generated' => $track->tracking_code]) }}" class="btn-ghost">{{ __('tracking.actions.view_public_card') }}</a>
                </div>

                <div class="stats-grid two">
                    <div class="metric">
                        <strong>{{ $track->current_stage->label() }}</strong>
                        <p>{{ __('tracking.admin.current_stage_text') }}</p>
                    </div>
                    <div class="metric">
                        <strong>{{ $track->auto_progress ? __('tracking.states.on') : __('tracking.states.off') }}</strong>
                        <p>{{ __('tracking.admin.auto_text') }}</p>
                    </div>
                    <div class="metric">
                        <strong>{{ optional($track->placed_at)->format('d/m/Y H:i') }}</strong>
                        <p>{{ __('tracking.admin.base_date_text') }}</p>
                    </div>
                    <div class="metric">
                        <strong>{{ optional($track->estimated_delivery_at)->format('d/m/Y H:i') }}</strong>
                        <p>{{ __('tracking.admin.eta_text') }}</p>
                    </div>
                </div>

                <div class="note-box">
                    {{ __('tracking.common.public_link') }}:
                    <a href="{{ route('tracking.show', $track->tracking_code) }}" class="link-inline">{{ route('tracking.show', $track->tracking_code) }}</a>
                </div>

                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>{{ __('tracking.common.stage') }}</th>
                                <th>{{ __('tracking.common.period') }}</th>
                                <th>{{ __('tracking.common.scheduled') }}</th>
                                <th>{{ __('tracking.common.reached') }}</th>
                                <th>{{ __('tracking.common.type') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($track->stages as $stage)
                                <tr>
                                    <td>
                                        <strong>{{ $stage->title }}</strong><br>
                                        <span class="muted">{{ $stage->description }}</span>
                                    </td>
                                    <td>{{ $stage->duration_hours }}{{ __('tracking.time.hours_short') }}</td>
                                    <td>{{ $stage->planned_for_at->format('d/m/Y H:i') }}</td>
                                    <td>{{ $stage->reached_at?->format('d/m/Y H:i') ?? __('tracking.common.not_available') }}</td>
                                    <td>{{ $stage->manual_override ? __('tracking.states.manual') : __('tracking.states.auto') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </article>

            <aside class="stack">
                <div class="card card-section stack">
                    <span class="eyebrow">{{ __('tracking.admin.update_eyebrow') }}</span>
                    <form action="{{ route('admin.tracks.stage.update', $track) }}" method="POST" class="stack">
                        @csrf
                        @method('PATCH')

                        <div class="field">
                            <label for="stage">{{ __('tracking.admin.new_stage') }}</label>
                            <select id="stage" name="stage">
                                @foreach ($stageOptions as $value => $label)
                                    <option value="{{ $value }}" @selected($track->current_stage->value === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('stage')
                                <span class="error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="field">
                            <label for="notes">{{ __('tracking.admin.change_note') }}</label>
                            <textarea id="notes" name="notes" placeholder="{{ __('tracking.admin.change_note_placeholder') }}">{{ old('notes') }}</textarea>
                        </div>

                        <input type="hidden" name="auto_progress" value="0">
                        <label class="check">
                            <input type="checkbox" name="auto_progress" value="1" @checked($track->auto_progress)>
                            {{ __('tracking.admin.keep_auto') }}
                        </label>

                        <button type="submit" class="btn">{{ __('tracking.actions.save_state') }}</button>
                    </form>
                </div>

                <div class="card card-section stack">
                    <span class="eyebrow">{{ __('tracking.admin.order_data_eyebrow') }}</span>
                    <div class="stack">
                        @if ($track->customer_name)
                            <p><strong>{{ __('tracking.common.customer') }}:</strong> {{ $track->customer_name }}</p>
                        @endif
                        @if ($track->customer_email)
                            <p><strong>{{ __('tracking.common.email') }}:</strong> {{ $track->customer_email }}</p>
                        @endif
                        <p><strong>{{ __('tracking.common.language') }}:</strong> {{ strtoupper($track->preferred_locale) }}</p>
                        @if ($track->customer_phone)
                            <p><strong>{{ __('tracking.common.phone') }}:</strong> {{ $track->customer_phone }}</p>
                        @endif
                        @if ($track->notification_cc)
                            <p><strong>CC:</strong> {{ implode(', ', $track->notification_cc) }}</p>
                        @endif
                        @if ($track->notification_bcc)
                            <p><strong>BCC:</strong> {{ implode(', ', $track->notification_bcc) }}</p>
                        @endif
                        @if ($track->shipping_address)
                            <p><strong>{{ __('tracking.common.address') }}:</strong> {{ $track->shipping_address }}</p>
                        @endif
                        @if ($track->notes)
                            <p><strong>{{ __('tracking.common.notes') }}:</strong> {{ $track->notes }}</p>
                        @endif
                    </div>
                </div>
            </aside>
        </div>

        <article class="card card-section stack">
            <div class="stack" style="gap: 8px;">
                <span class="eyebrow">{{ __('tracking.admin.issues_eyebrow') }}</span>
                <h2>{{ __('tracking.admin.issues_heading') }}</h2>
            </div>

            <div class="stack">
                @forelse ($track->issues as $issue)
                    <div class="metric">
                        <div class="inline-actions" style="justify-content: space-between; margin-bottom: 8px;">
                            <strong>{{ \App\Http\Requests\StoreOrderTrackIssueRequest::issueTypes()[$issue->issue_type] ?? $issue->issue_type }}</strong>
                            <span class="status-badge {{ $issue->status === 'open' ? 'current' : 'completed' }}">{{ $issue->status === 'open' ? __('tracking.states.open') : __('tracking.states.closed') }}</span>
                        </div>
                        <p>{{ $issue->description }}</p>
                        <p class="helper" style="margin-top: 10px;">
                            {{ $issue->full_name }} | {{ $issue->email }}
                            @if ($issue->phone)
                                | {{ $issue->phone }}
                            @endif
                            | {{ $issue->created_at->format('d/m/Y H:i') }}
                        </p>
                    </div>
                @empty
                    <p>{{ __('tracking.admin.no_issues') }}</p>
                @endforelse
            </div>
        </article>

        <article class="card card-section stack">
            <div class="stack" style="gap: 8px;">
                <span class="eyebrow">{{ __('tracking.admin.emails_eyebrow') }}</span>
                <h2>{{ __('tracking.admin.emails_heading') }}</h2>
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>{{ __('tracking.admin.table_email_type') }}</th>
                            <th>{{ __('tracking.admin.table_email_stage') }}</th>
                            <th>{{ __('tracking.admin.table_email_recipient') }}</th>
                            <th>{{ __('tracking.admin.table_email_subject') }}</th>
                            <th>{{ __('tracking.admin.table_email_status') }}</th>
                            <th>{{ __('tracking.admin.table_email_queue') }}</th>
                            <th>{{ __('tracking.admin.table_email_sent') }}</th>
                            <th>{{ __('tracking.admin.table_email_error') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($track->emails as $email)
                            <tr>
                                <td>{{ $email->notification_type->label() }}</td>
                                <td>{{ $email->stage_key?->label() ?? __('tracking.common.not_available') }}</td>
                                <td>
                                    {{ $email->recipient_email }}
                                    @if ($email->cc)
                                        <div class="helper">CC: {{ implode(', ', $email->cc) }}</div>
                                    @endif
                                    @if ($email->bcc)
                                        <div class="helper">BCC: {{ implode(', ', $email->bcc) }}</div>
                                    @endif
                                </td>
                                <td>{{ $email->subject }}</td>
                                <td>
                                    <span class="status-badge {{ $email->status->value === 'sent' ? 'completed' : ($email->status->value === 'failed' ? 'failed' : ($email->status->value === 'processing' ? 'processing' : 'pending')) }}">
                                        {{ $email->status->label() }}
                                    </span>
                                </td>
                                <td>{{ $email->queued_at?->format('d/m/Y H:i') ?? __('tracking.common.not_available') }}</td>
                                <td>{{ $email->sent_at?->format('d/m/Y H:i') ?? __('tracking.common.not_available') }}</td>
                                <td>{{ $email->last_error ?: __('tracking.common.not_available') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">{{ __('tracking.admin.no_emails') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </article>
    </section>
@endsection
