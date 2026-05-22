<?php

namespace App\Modules\Billing\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Billing\Models\Invoice;
use App\Modules\Billing\Services\DunningService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function index(): View
    {
        return view('billing.invoices', ['invoices' => Invoice::query()->with('customer')->latest()->paginate(25)]);
    }

    public function markOverdue(Invoice $invoice, DunningService $dunning): RedirectResponse
    {
        $dunning->runForInvoice($invoice);

        return back();
    }
}
