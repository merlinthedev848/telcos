<?php

namespace App\Modules\Billing\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentResource;
use App\Modules\Billing\Models\Invoice;
use App\Modules\Billing\Models\Payment;
use App\Modules\Billing\Services\PaymentService;
use App\Modules\Customers\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

class PaymentApiController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $payments = Payment::query()
            ->with(['customer', 'invoice'])
            ->latest('received_at')
            ->paginate($request->integer('per_page', 15));

        return PaymentResource::collection($payments);
    }

    public function storeForInvoice(Request $request, Invoice $invoice, PaymentService $payments): JsonResponse
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'provider' => ['nullable', 'string', 'max:50'],
            'reference' => ['nullable', 'string', 'max:255'],
            'payload' => ['nullable', 'array'],
        ]);

        $customer = $invoice->customer;

        $payment = $payments->recordPayment(
            customer: $customer,
            amount: (float) $validated['amount'],
            invoice: $invoice,
            provider: $validated['provider'] ?? 'manual',
            reference: $validated['reference'] ?? null,
            payload: $validated['payload'] ?? [],
        );

        return (new PaymentResource($payment->loadMissing(['customer', 'invoice'])))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function storeForCustomer(Request $request, Customer $customer, PaymentService $payments): JsonResponse
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'invoice_id' => ['nullable', 'integer', 'exists:invoices,id'],
            'provider' => ['nullable', 'string', 'max:50'],
            'reference' => ['nullable', 'string', 'max:255'],
            'payload' => ['nullable', 'array'],
        ]);

        $invoice = ! empty($validated['invoice_id']) ? Invoice::query()->find($validated['invoice_id']) : null;

        $payment = $payments->recordPayment(
            customer: $customer,
            amount: (float) $validated['amount'],
            invoice: $invoice,
            provider: $validated['provider'] ?? 'manual',
            reference: $validated['reference'] ?? null,
            payload: $validated['payload'] ?? [],
        );

        return (new PaymentResource($payment->loadMissing(['customer', 'invoice'])))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }
}
