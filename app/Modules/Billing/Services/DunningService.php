<?php

namespace App\Modules\Billing\Services;

use App\Modules\Billing\Models\Invoice;
use App\Modules\Core\Services\CriticalOperation;
use App\Modules\Paywall\Services\DnsPaywallService;

class DunningService
{
    public function __construct(
        private readonly CriticalOperation $operations,
        private readonly DnsPaywallService $paywall,
    ) {
    }

    public function runForInvoice(Invoice $invoice): Invoice
    {
        if ($invoice->balance_due <= 0) {
            return $invoice;
        }

        $stage = min(((int) $invoice->dunning_stage) + 1, 4);
        $status = $stage >= 1 ? 'overdue' : $invoice->status;
        $invoice->update(['dunning_stage' => $stage, 'status' => $status]);

        $this->operations->record('billing', 'dunning.stage_advanced', $invoice, ['stage' => $stage]);

        if ($stage >= 3) {
            $this->paywall->apply($invoice->customer, 'invoice_overdue_stage_'.$stage);
        }

        return $invoice->refresh();
    }
}
