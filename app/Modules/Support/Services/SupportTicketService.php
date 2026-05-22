<?php

namespace App\Modules\Support\Services;

use App\Modules\Core\Services\CriticalOperation;
use App\Modules\Customers\Models\Customer;
use App\Modules\Support\Models\SupportTicket;
use Illuminate\Support\Str;

class SupportTicketService
{
    public function __construct(private readonly CriticalOperation $operations)
    {
    }

    public function open(Customer $customer, string $subject, string $body, string $channel = 'email'): SupportTicket
    {
        $ticket = SupportTicket::query()->create([
            'customer_id' => $customer->id,
            'ticket_number' => 'TIC-'.Str::upper(Str::random(8)),
            'subject' => $subject,
            'status' => 'new',
            'priority' => 'normal',
            'channel' => $channel,
            'last_activity_at' => now(),
        ]);

        $ticket->messages()->create([
            'direction' => 'inbound',
            'sender' => $customer->email,
            'body' => $body,
        ]);

        $this->operations->record('support', 'ticket.opened', $ticket, ['channel' => $channel]);

        return $ticket;
    }
}
