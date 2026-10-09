<?php

namespace App\Http\Resources;

use App\Modules\Switching\Models\SwitchingWorkflow;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin SwitchingWorkflow
 */
class SwitchingWorkflowResource extends JsonResource
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
            'ots_reference' => $this->ots_reference,
            'status' => $this->status,
            'losing_provider' => $this->losing_provider,
            'gaining_provider' => $this->gaining_provider,
            'requested_switch_date' => $this->requested_switch_date?->toDateString(),
            'completed_at' => $this->completed_at?->toIso8601String(),
            'payload' => $this->payload,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
