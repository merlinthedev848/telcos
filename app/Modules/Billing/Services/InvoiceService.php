<?php

namespace App\Modules\Billing\Services;

use App\Modules\Billing\Models\Invoice;
use App\Modules\Core\Services\CriticalOperation;
use App\Modules\Customers\Models\Customer;
use Illuminate\Support\Str;

class InvoiceService
{
    public function __construct(private readonly CriticalOperation $operations)
    {
    }

    public function issue(Customer $customer, array $lineItems, ?string $dueDate = null): Invoice
    {
        $subtotal = collect($lineItems)->sum(fn (array $item) => (float) $item['amount']);
        $tax = round($subtotal * 0.2, 2);
        $total = $subtotal + $tax;

        $invoice = Invoice::query()->create([
            'customer_id' => $customer->id,
            'invoice_number' => 'INV-'.now()->format('Y').'-'.Str::upper(Str::random(6)),
            'status' => 'issued',
            'issued_at' => now(),
            'due_date' => $dueDate ?? now()->addDays(14)->toDateString(),
            'subtotal' => $subtotal,
            'tax_total' => $tax,
            'total' => $total,
            'balance_due' => $total,
            'dunning_stage' => 0,
            'line_items' => $lineItems,
        ]);

        $this->operations->record('billing', 'invoice.issued', $invoice, ['total' => $total]);

        return $invoice;
    }

    public function markPaid(Invoice $invoice): Invoice
    {
        $invoice->update(['status' => 'paid', 'balance_due' => 0]);
        $this->operations->record('billing', 'invoice.paid', $invoice);

        return $invoice;
    }
}
