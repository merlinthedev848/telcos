<?php

namespace App\Modules\Billing\Console;

use App\Modules\Billing\Models\Invoice;
use App\Modules\Billing\Services\DunningService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

class RunDunningCommand extends Command
{
    protected $signature = 'telecomos:dunning-run';

    protected $description = 'Advance dunning for invoices past their due date.';

    public function handle(DunningService $dunning): int
    {
        $this->info('Starting dunning execution cycle...');

        $processed = 0;
        $failed = 0;

        Invoice::query()
            ->where('balance_due', '>', 0)
            ->whereDate('due_date', '<', now())
            ->chunkById(100, function ($invoices) use ($dunning, &$processed, &$failed): void {
                /** @var Invoice $invoice */
                foreach ($invoices as $invoice) {
                    try {
                        $dunning->runForInvoice($invoice);
                        $processed++;
                    } catch (Throwable $e) {
                        $failed++;
                        Log::error("Failed running dunning for invoice #{$invoice->id}: {$e->getMessage()}", [
                            'invoice_id' => $invoice->id,
                            'exception' => $e,
                        ]);
                        $this->error("Error processing invoice #{$invoice->id}: {$e->getMessage()}");
                    }
                }
            });

        $this->info("Dunning cycle completed. Processed: {$processed}, Failures: {$failed}.");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}
