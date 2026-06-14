<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderTrackIssueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:180'],
            'phone' => ['nullable', 'string', 'max:40'],
            'issue_type' => ['required', Rule::in(array_keys(self::issueTypes()))],
            'description' => ['required', 'string', 'min:10', 'max:3000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function issueTypes(): array
    {
        return [
            'delivery_delay' => __('tracking.issues.types.delivery_delay'),
            'wrong_status' => __('tracking.issues.types.wrong_status'),
            'damaged_item' => __('tracking.issues.types.damaged_item'),
            'missing_package' => __('tracking.issues.types.missing_package'),
            'other' => __('tracking.issues.types.other'),
        ];
    }
}
