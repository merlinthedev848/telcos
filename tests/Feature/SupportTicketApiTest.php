<?php

namespace Tests\Feature;

use App\Modules\Customers\Models\Customer;
use App\Modules\Support\Models\SupportTicket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupportTicketApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_open_and_reply_to_support_ticket(): void
    {
        $customer = Customer::query()->create([
            'account_number' => 'TEL-TICKET1',
            'contact_name' => 'Alice Support',
            'email' => 'alice@support.test',
            'status' => 'active',
        ]);

        $createResponse = $this->postJson('/api/v1/support/tickets', [
            'customer_id' => $customer->id,
            'subject' => 'Fibre Drop Out',
            'body' => 'ONT red LOS light is blinking.',
            'priority' => 'high',
        ]);

        $createResponse->assertCreated();
        $createResponse->assertJsonPath('data.subject', 'Fibre Drop Out');
        $createResponse->assertJsonPath('data.status', 'new');

        $ticketId = $createResponse->json('data.id');

        $replyResponse = $this->postJson("/api/v1/support/tickets/{$ticketId}/reply", [
            'body' => 'We are dispatching an engineer to check the splice box.',
            'sender' => 'engineer@telecomos.local',
            'direction' => 'outbound',
        ]);

        $replyResponse->assertCreated();
        $replyResponse->assertJsonPath('data.direction', 'outbound');

        $this->assertDatabaseHas('support_tickets', [
            'id' => $ticketId,
            'status' => 'pending',
        ]);
        $this->assertDatabaseHas('support_ticket_messages', [
            'support_ticket_id' => $ticketId,
            'direction' => 'outbound',
        ]);
    }
}
