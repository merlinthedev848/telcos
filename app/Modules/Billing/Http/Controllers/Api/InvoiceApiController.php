<?php

namespace App\Modules\Billing\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\InvoiceResource;
use App\Modules\Billing\Models\Invoice;
use App\Modules\Billing\Services\DunningService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class InvoiceApiController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $status = $request->string('status')->toString();

        $invoices = Invoice::query()
            ->with('customer')
            ->when($status, fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return InvoiceResource::collection($invoices);
    }

    public function show(Invoice $invoice): InvoiceResource
    {
        return new InvoiceResource($invoice->loadMissing('customer'));
    }

    public function runDunning(Invoice $invoice, DunningService $dunning): InvoiceResource
    {
        $updated = $dunning->runForInvoice($invoice);

        return new InvoiceResource($updated->loadMissing('customer'));
    }
}
