<?php

namespace App\Modules\Billing\Console;

use App\Modules\Billing\Models\Invoice;
use App\Modules\Billing\Services\DunningService;
use Illuminate\Console\Command;

class RunDunningCommand extends Command
{
    protected $signature = 'telecomos:dunning-run';

    protected $description = 'Advance dunning for invoices past their due date.';

    public function handle(DunningService $dunning): int
    {
        Invoice::query()
            ->where('balance_due', '>', 0)
            ->whereDate('due_date', '<', now())
            ->each(fn (Invoice $invoice) => $dunning->runForInvoice($invoice));

        $this->info('Dunning run complete.');

        return self::SUCCESS;
    }
}
