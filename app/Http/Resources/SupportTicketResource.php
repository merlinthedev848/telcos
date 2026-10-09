<?php

namespace App\Http\Resources;

use App\Modules\Support\Models\SupportTicket;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin SupportTicket
 */
class SupportTicketResource extends JsonResource
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
            'ticket_number' => $this->ticket_number,
            'subject' => $this->subject,
            'status' => $this->status,
            'priority' => $this->priority,
            'channel' => $this->channel,
            'assigned_to' => $this->assigned_to,
            'last_activity_at' => $this->last_activity_at?->toIso8601String(),
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'messages' => SupportTicketMessageResource::collection($this->whenLoaded('messages')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
