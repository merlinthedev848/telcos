<?php

namespace App\Modules\Support\Services;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Modules\Core\Services\CriticalOperation;
use App\Modules\Customers\Models\Customer;
use App\Modules\Support\Models\SupportTicket;
use App\Modules\Support\Models\SupportTicketMessage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SupportTicketService
{
    public function __construct(private readonly CriticalOperation $operations)
    {
    }

    /**
     * Open a new support ticket with initial message.
     */
    public function open(
        Customer $customer,
        string $subject,
        string $body,
        string $channel = 'email',
        string $priority = 'normal',
    ): SupportTicket {
        return DB::transaction(function () use ($customer, $subject, $body, $channel, $priority): SupportTicket {
            $ticket = SupportTicket::query()->create([
                'customer_id' => $customer->id,
                'ticket_number' => 'TIC-'.Str::upper(Str::random(8)),
                'subject' => $subject,
                'status' => TicketStatus::New->value,
                'priority' => TicketPriority::tryFrom($priority)?->value ?? TicketPriority::Normal->value,
                'channel' => $channel,
                'last_activity_at' => now(),
            ]);

            $ticket->messages()->create([
                'direction' => 'inbound',
                'sender' => $customer->email,
                'body' => $body,
            ]);

            $this->operations->record('support', 'ticket.opened', $ticket, [
                'ticket_number' => $ticket->ticket_number,
                'customer_id' => $customer->id,
                'channel' => $channel,
            ]);

            return $ticket;
        });
    }

    /**
     * Post a reply to an existing support ticket.
     */
    public function reply(
        SupportTicket $ticket,
        string $body,
        string $sender,
        string $direction = 'outbound',
        array $metadata = [],
    ): SupportTicketMessage {
        return DB::transaction(function () use ($ticket, $body, $sender, $direction, $metadata): SupportTicketMessage {
            $message = $ticket->messages()->create([
                'direction' => $direction,
                'sender' => $sender,
                'body' => $body,
                'metadata' => $metadata,
            ]);

            $newStatus = $direction === 'outbound' ? TicketStatus::Pending->value : TicketStatus::Open->value;
            $ticket->update([
                'status' => $newStatus,
                'last_activity_at' => now(),
            ]);

            $this->operations->record('support', 'ticket.replied', $ticket, [
                'ticket_number' => $ticket->ticket_number,
                'direction' => $direction,
                'sender' => $sender,
            ]);

            return $message;
        });
    }

    /**
     * Update ticket lifecycle status.
     */
    public function updateStatus(SupportTicket $ticket, TicketStatus|string $status): SupportTicket
    {
        return DB::transaction(function () use ($ticket, $status): SupportTicket {
            $statusValue = $status instanceof TicketStatus ? $status->value : $status;
            $ticket->update([
                'status' => $statusValue,
                'last_activity_at' => now(),
            ]);

            $this->operations->record('support', 'ticket.status_changed', $ticket, [
                'ticket_number' => $ticket->ticket_number,
                'new_status' => $statusValue,
            ]);

            return $ticket->refresh();
        });
    }
}
