<?php

namespace App\Modules\Billing\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Billing\Models\Invoice;
use App\Modules\Billing\Services\DunningService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->string('status')->toString();

        $invoices = Invoice::query()
            ->with('customer')
            ->when($status, fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('billing.invoices', [
            'invoices' => $invoices,
            'currentStatus' => $status,
        ]);
    }

    public function markOverdue(Invoice $invoice, DunningService $dunning): RedirectResponse
    {
        $dunning->runForInvoice($invoice);

        return back()->with('status', "Dunning stage advanced for invoice {$invoice->invoice_number}.");
    }
}
