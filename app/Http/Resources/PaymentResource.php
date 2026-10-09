<?php

namespace App\Http\Resources;

use App\Modules\Billing\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Payment
 */
class PaymentResource extends JsonResource
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
            'invoice_id' => $this->invoice_id,
            'provider' => $this->provider,
            'reference' => $this->reference,
            'amount' => (float) $this->amount,
            'status' => $this->status,
            'received_at' => $this->received_at?->toIso8601String(),
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'invoice' => new InvoiceResource($this->whenLoaded('invoice')),
            'payload' => $this->payload,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
