<?php

namespace App\Modules\Support\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Customers\Models\Customer;
use App\Modules\Support\Models\SupportTicket;
use App\Modules\Support\Services\SupportTicketService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SupportTicketApiController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(SupportTicket::query()->with('customer')->latest('last_activity_at')->paginate());
    }

    public function store(Request $request, SupportTicketService $tickets): JsonResponse
    {
        $data = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'subject' => ['required', 'string'],
            'body' => ['required', 'string'],
            'channel' => ['nullable', 'string'],
        ]);

        return response()->json($tickets->open(
            Customer::query()->findOrFail($data['customer_id']),
            $data['subject'],
            $data['body'],
            $data['channel'] ?? 'email',
        ), 201);
    }

    public function show(SupportTicket $ticket): JsonResponse
    {
        return response()->json($ticket->load(['customer', 'messages']));
    }
}
