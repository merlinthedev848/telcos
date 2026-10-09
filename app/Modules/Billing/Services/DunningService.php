<?php

namespace App\Modules\Billing\Services;

use App\Enums\InvoiceStatus;
use App\Modules\Billing\Models\Invoice;
use App\Modules\Core\Services\CriticalOperation;
use App\Modules\Paywall\Services\DnsPaywallService;
use Illuminate\Support\Facades\DB;

class DunningService
{
    public function __construct(
        private readonly CriticalOperation $operations,
        private readonly DnsPaywallService $paywall,
    ) {
    }

    /**
     * Advance the dunning escalation stage for an overdue invoice.
     */
    public function runForInvoice(Invoice $invoice): Invoice
    {
        if ($invoice->balance_due <= 0) {
            return $invoice;
        }

        return DB::transaction(function () use ($invoice): Invoice {
            $stage = min(((int) $invoice->dunning_stage) + 1, 4);
            $status = $stage >= 1 ? InvoiceStatus::Overdue->value : $invoice->status;

            $invoice->update([
                'dunning_stage' => $stage,
                'status' => $status,
            ]);

            $this->operations->record('billing', 'dunning.stage_advanced', $invoice, [
                'stage' => $stage,
                'invoice_number' => $invoice->invoice_number,
                'balance_due' => (float) $invoice->balance_due,
            ]);

            if ($stage >= 3) {
                $invoice->loadMissing('customer');
                if ($invoice->customer) {
                    $this->paywall->apply($invoice->customer, 'invoice_overdue_stage_'.$stage);
                }
            }

            return $invoice->refresh();
        });
    }
}
