<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSwitchingWorkflowRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'losing_provider' => ['nullable', 'string', 'max:255'],
            'gaining_provider' => ['nullable', 'string', 'max:255'],
            'requested_switch_date' => ['nullable', 'date', 'after_or_equal:today'],
            'payload' => ['nullable', 'array'],
        ];
    }
}
