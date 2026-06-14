<?php

namespace App\Http\Requests;

use App\Enums\TrackingStage;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderTrackRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'notification_cc' => $this->normalizeEmailList($this->input('notification_cc')),
            'notification_bcc' => $this->normalizeEmailList($this->input('notification_bcc')),
        ]);
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'tracking_code' => ['nullable', 'string', 'max:40', 'regex:/^[A-Za-z0-9\-]+$/', 'unique:order_tracks,tracking_code'],
            'order_number' => ['required', 'string', 'max:100', 'unique:order_tracks,order_number'],
            'customer_name' => ['nullable', 'string', 'max:120'],
            'customer_email' => ['required', 'email', 'max:180'],
            'customer_phone' => ['nullable', 'string', 'max:40'],
            'preferred_locale' => ['nullable', 'string', Rule::in(['en', 'pt'])],
            'notification_cc' => ['nullable', 'array'],
            'notification_cc.*' => ['email', 'max:180'],
            'notification_bcc' => ['nullable', 'array'],
            'notification_bcc.*' => ['email', 'max:180'],
            'product_name' => ['required', 'string', 'max:255'],
            'product_image_url' => ['nullable', 'url', 'max:2048'],
            'shipping_address' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'placed_at' => ['nullable', 'date'],
            'current_stage' => ['nullable', Rule::enum(TrackingStage::class)],
            'auto_progress' => ['nullable', 'boolean'],
            'periods' => ['nullable', 'array'],
        ];

        foreach (TrackingStage::ordered() as $stage) {
            $rules["periods.{$stage->value}"] = ['nullable', 'integer', 'min:0', 'max:720'];
        }

        return $rules;
    }

    protected function normalizeEmailList(mixed $value): ?array
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_string($value)) {
            $value = explode(',', $value);
        }

        if (! is_array($value)) {
            return null;
        }

        $emails = array_values(array_filter(array_map(
            static fn (mixed $item) => is_string($item) ? trim($item) : null,
            $value
        )));

        return $emails === [] ? null : $emails;
    }
}
