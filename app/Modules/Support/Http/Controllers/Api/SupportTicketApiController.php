<?php

namespace App\Modules\Support\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSupportTicketRequest;
use App\Http\Resources\SupportTicketMessageResource;
use App\Http\Resources\SupportTicketResource;
use App\Modules\Customers\Models\Customer;
use App\Modules\Support\Models\SupportTicket;
use App\Modules\Support\Services\SupportTicketService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

class SupportTicketApiController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $status = $request->string('status')->toString();

        $tickets = SupportTicket::query()
            ->with(['customer', 'assignee'])
            ->when($status, fn ($q) => $q->where('status', $status))
            ->latest('last_activity_at')
            ->paginate($request->integer('per_page', 15));

        return SupportTicketResource::collection($tickets);
    }

    public function store(StoreSupportTicketRequest $request, SupportTicketService $tickets): JsonResponse
    {
        $validated = $request->validated();
        $customer = Customer::query()->findOrFail($validated['customer_id']);

        $ticket = $tickets->open(
            customer: $customer,
            subject: $validated['subject'],
            body: $validated['body'],
            channel: $validated['channel'] ?? 'email',
            priority: $validated['priority'] ?? 'normal',
        );

        return (new SupportTicketResource($ticket->loadMissing(['customer', 'messages'])))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(SupportTicket $ticket): SupportTicketResource
    {
        return new SupportTicketResource($ticket->loadMissing(['customer', 'assignee', 'messages']));
    }

    public function reply(Request $request, SupportTicket $ticket, SupportTicketService $tickets): JsonResponse
    {
        $validated = $request->validate([
            'body' => ['required', 'string'],
            'sender' => ['required', 'string', 'max:255'],
            'direction' => ['nullable', 'string', 'in:inbound,outbound'],
        ]);

        $message = $tickets->reply(
            ticket: $ticket,
            body: $validated['body'],
            sender: $validated['sender'],
            direction: $validated['direction'] ?? 'outbound',
        );

        return (new SupportTicketMessageResource($message))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }
}
