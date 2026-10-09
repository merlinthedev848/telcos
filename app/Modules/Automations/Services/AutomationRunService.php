<?php

namespace App\Modules\Automations\Services;

use App\Enums\InvoiceStatus;
use App\Enums\PaywallStatus;
use App\Modules\Billing\Models\Invoice;
use App\Modules\Billing\Services\DunningService;
use App\Modules\Core\Services\CriticalOperation;
use App\Modules\Paywall\Models\PaywallState;
use App\Modules\Paywall\Services\DnsPaywallService;
use Illuminate\Support\Facades\Log;
use Throwable;

class AutomationRunService
{
    public function __construct(
        private readonly CriticalOperation $operations,
        private readonly DunningService $dunning,
        private readonly DnsPaywallService $paywall,
    ) {
    }

    /**
     * Run all scheduled system automations.
     *
     * @return array<string, int>
     */
    public function runAll(): array
    {
        $dunningCount = $this->runDunningSweep();
        $paywallCleaned = $this->runPaywallReconciliation();

        $stats = [
            'dunning_processed' => $dunningCount,
            'paywall_reconciled' => $paywallCleaned,
        ];

        $this->record('daily_automation_sweep', $stats);

        return $stats;
    }

    /**
     * Sweep through overdue invoices and advance dunning stages.
     */
    public function runDunningSweep(): int
    {
        $processed = 0;

        Invoice::query()
            ->where('balance_due', '>', 0)
            ->whereDate('due_date', '<', now())
            ->chunkById(50, function ($invoices) use (&$processed): void {
                /** @var Invoice $invoice */
                foreach ($invoices as $invoice) {
                    try {
                        $this->dunning->runForInvoice($invoice);
                        $processed++;
                    } catch (Throwable $e) {
                        Log::error("Automation error in dunning for invoice #{$invoice->id}: {$e->getMessage()}");
                    }
                }
            });

        return $processed;
    }

    /**
     * Ensure customers who have fully paid off balances have their DNS paywalls released.
     */
    public function runPaywallReconciliation(): int
    {
        $reconciled = 0;

        $restrictedStates = PaywallState::query()
            ->with('customer.invoices')
            ->where('status', PaywallStatus::Restricted->value)
            ->get();

        foreach ($restrictedStates as $state) {
            $customer = $state->customer;
            if (! $customer) {
                continue;
            }

            $hasOverdue = $customer->invoices
                ->whereIn('status', [InvoiceStatus::Issued->value, InvoiceStatus::Overdue->value])
                ->where('balance_due', '>', 0)
                ->isNotEmpty();

            if (! $hasOverdue) {
                $this->paywall->restore($customer);
                $reconciled++;
            }
        }

        return $reconciled;
    }

    public function record(string $name, array $payload = []): void
    {
        $this->operations->record('automations', 'automation.run', 'automation', [
            'name' => $name,
            'executed_at' => now()->toIso8601String(),
            ...$payload,
        ]);
    }
}
