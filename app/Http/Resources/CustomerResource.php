<?php

namespace App\Http\Resources;

use App\Modules\Customers\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Customer
 */
class CustomerResource extends JsonResource
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
            'account_number' => $this->account_number,
            'company_name' => $this->company_name,
            'contact_name' => $this->contact_name,
            'display_name' => $this->display_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'status' => $this->status,
            'service_address' => $this->service_address,
            'billing_address' => $this->billing_address,
            'metadata' => $this->metadata,
            'invoices' => InvoiceResource::collection($this->whenLoaded('invoices')),
            'tickets' => SupportTicketResource::collection($this->whenLoaded('tickets')),
            'paywall' => new PaywallStateResource($this->whenLoaded('paywallState')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
