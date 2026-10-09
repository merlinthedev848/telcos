<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ActivateProvisioningRequest extends FormRequest
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
            'service_profile' => ['nullable', 'array'],
            'service_profile.package_name' => ['nullable', 'string', 'max:255'],
            'service_profile.download_speed' => ['nullable', 'integer', 'min:1'],
            'service_profile.upload_speed' => ['nullable', 'integer', 'min:1'],
            'service_profile.vlan_id' => ['nullable', 'integer', 'between:1,4094'],
        ];
    }
}
