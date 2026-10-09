<?php

namespace Tests\Feature;

use App\Modules\Customers\Models\Customer;
use App\Modules\Support\Models\SupportTicket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmailInboundApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_inbound_email_creates_new_subscriber_and_ticket(): void
    {
        $response = $this->postJson('/api/v1/email/inbound', [
            'from' => 'Jane Inbound <jane@inbound.test>',
            'subject' => 'Broadband installation enquiry',
            'body' => 'I would like to check when full fibre will be installed in our area.',
        ]);

        $response->assertOk();
        $response->assertJsonPath('status', 'processed');

        $this->assertDatabaseHas('customers', ['email' => 'jane@inbound.test']);
        $this->assertDatabaseHas('support_tickets', ['subject' => 'Broadband installation enquiry']);
    }

    public function test_inbound_email_from_existing_customer_attaches_to_open_ticket(): void
    {
        $customer = Customer::query()->create([
            'account_number' => 'TEL-INBOUND1',
            'contact_name' => 'Existing Customer',
            'email' => 'existing@inbound.test',
            'status' => 'active',
        ]);

        $ticket = SupportTicket::query()->create([
            'customer_id' => $customer->id,
            'ticket_number' => 'TIC-INB001',
            'subject' => 'Router Delivery Status',
            'status' => 'open',
            'channel' => 'email',
            'last_activity_at' => now(),
        ]);

        $response = $this->postJson('/api/v1/email/inbound', [
            'from' => 'existing@inbound.test',
            'subject' => 'Re: Router Delivery Status',
            'body' => 'Has the tracking number been generated yet?',
        ]);

        $response->assertOk();
        $response->assertJsonPath('ticket_id', $ticket->id);
        $this->assertSame(1, $ticket->messages()->count());
    }
}
