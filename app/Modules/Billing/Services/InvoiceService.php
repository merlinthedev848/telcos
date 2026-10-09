<?php

namespace App\Modules\Billing\Services;

use App\Enums\InvoiceStatus;
use App\Modules\Billing\Models\Invoice;
use App\Modules\Core\Services\CriticalOperation;
use App\Modules\Customers\Models\Customer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InvoiceService
{
    public function __construct(private readonly CriticalOperation $operations)
    {
    }

    /**
     * Issue a new invoice for a customer.
     *
     * @param Customer $customer
     * @param array<int, array{description: string, amount: float|int}> $lineItems
     * @param string|null $dueDate
     */
    public function issue(Customer $customer, array $lineItems, ?string $dueDate = null): Invoice
    {
        return DB::transaction(function () use ($customer, $lineItems, $dueDate): Invoice {
            $subtotal = collect($lineItems)->sum(fn (array $item) => (float) ($item['amount'] ?? 0));
            $tax = round($subtotal * 0.20, 2);
            $total = round($subtotal + $tax, 2);

            $invoice = Invoice::query()->create([
                'customer_id' => $customer->id,
                'invoice_number' => 'INV-'.now()->format('Y').'-'.Str::upper(Str::random(6)),
                'status' => InvoiceStatus::Issued->value,
                'issued_at' => now(),
                'due_date' => $dueDate ?? now()->addDays(14)->toDateString(),
                'subtotal' => $subtotal,
                'tax_total' => $tax,
                'total' => $total,
                'balance_due' => $total,
                'dunning_stage' => 0,
                'line_items' => $lineItems,
            ]);

            $this->operations->record('billing', 'invoice.issued', $invoice, [
                'invoice_number' => $invoice->invoice_number,
                'customer_id' => $customer->id,
                'total' => $total,
            ]);

            return $invoice;
        });
    }

    /**
     * Mark an invoice as fully paid.
     */
    public function markPaid(Invoice $invoice): Invoice
    {
        return DB::transaction(function () use ($invoice): Invoice {
            $invoice->update([
                'status' => InvoiceStatus::Paid->value,
                'balance_due' => 0,
            ]);

            $this->operations->record('billing', 'invoice.paid', $invoice, [
                'invoice_number' => $invoice->invoice_number,
                'paid_at' => now()->toIso8601String(),
            ]);

            return $invoice->refresh();
        });
    }

    /**
     * Void an invoice.
     */
    public function voidInvoice(Invoice $invoice, string $reason = 'Cancelled by administrator'): Invoice
    {
        return DB::transaction(function () use ($invoice, $reason): Invoice {
            $invoice->update([
                'status' => InvoiceStatus::Void->value,
                'balance_due' => 0,
            ]);

            $this->operations->record('billing', 'invoice.voided', $invoice, [
                'invoice_number' => $invoice->invoice_number,
                'reason' => $reason,
            ]);

            return $invoice->refresh();
        });
    }
}
