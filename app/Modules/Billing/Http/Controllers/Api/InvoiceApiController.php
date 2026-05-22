<?php

namespace App\Modules\Billing\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Billing\Models\Invoice;
use App\Modules\Billing\Services\DunningService;
use Illuminate\Http\JsonResponse;

class InvoiceApiController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Invoice::query()->with('customer')->latest()->paginate());
    }

    public function show(Invoice $invoice): JsonResponse
    {
        return response()->json($invoice->load('customer'));
    }

    public function runDunning(Invoice $invoice, DunningService $dunning): JsonResponse
    {
        return response()->json($dunning->runForInvoice($invoice));
    }
}
