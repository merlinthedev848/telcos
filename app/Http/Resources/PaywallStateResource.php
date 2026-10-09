<?php

namespace App\Http\Resources;

use App\Modules\Paywall\Models\PaywallState;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin PaywallState
 */
class PaywallStateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'customer_id' => $this->customer_id,
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'status' => $this->status,
            'reason' => $this->reason,
            'dns_policy' => $this->dns_policy,
            'applied_at' => $this->applied_at?->toIso8601String(),
            'restored_at' => $this->restored_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
