<?php

namespace App\Modules\Email\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Customers\Models\Customer;
use App\Modules\Customers\Services\CustomerService;
use App\Modules\Email\Services\EmailThreadingService;
use App\Modules\Support\Models\SupportTicket;
use App\Modules\Support\Services\SupportTicketService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmailInboundApiController extends Controller
{
    public function handle(
        Request $request,
        EmailThreadingService $emailThreading,
        CustomerService $customerService,
        SupportTicketService $supportTickets,
    ): JsonResponse {
        $validated = $request->validate([
            'from' => ['required', 'string', 'max:255'],
            'to' => ['nullable', 'string', 'max:255'],
            'subject' => ['required', 'string', 'max:500'],
            'body' => ['required', 'string'],
            'message_id' => ['nullable', 'string', 'max:255'],
            'headers' => ['nullable', 'array'],
        ]);

        // Extract raw email from sender string e.g. "John Doe <john@example.com>"
        $rawEmail = $validated['from'];
        if (preg_match('/<([^>]+)>/', $rawEmail, $matches)) {
            $rawEmail = $matches[1];
        }

        // 1. Locate or create customer
        $customer = Customer::query()->where('email', $rawEmail)->first();
        if (! $customer) {
            $customer = $customerService->create([
                'contact_name' => $validated['from'],
                'email' => $rawEmail,
                'status' => 'lead',
            ]);
        }

        // 2. Thread the email message
        $thread = $emailThreading->recordInbound($customer, $validated);

        // 3. Link or create a support ticket
        $openTicket = SupportTicket::query()
            ->where('customer_id', $customer->id)
            ->open()
            ->latest('last_activity_at')
            ->first();

        if ($openTicket) {
            $supportTickets->reply(
                ticket: $openTicket,
                body: $validated['body'],
                sender: $validated['from'],
                direction: 'inbound',
            );
        } else {
            $openTicket = $supportTickets->open(
                customer: $customer,
                subject: $validated['subject'],
                body: $validated['body'],
                channel: 'email',
            );
        }

        return response()->json([
            'status' => 'processed',
            'thread_id' => $thread->id,
            'ticket_id' => $openTicket->id,
            'ticket_number' => $openTicket->ticket_number,
        ]);
    }
}
