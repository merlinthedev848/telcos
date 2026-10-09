<?php

namespace App\Modules\Automations\Console;

use App\Modules\Automations\Services\AutomationRunService;
use Illuminate\Console\Command;

class RunAutomationsCommand extends Command
{
    protected $signature = 'telecomos:automations-run';

    protected $description = 'Execute scheduled automated telecom OSS/BSS workflows.';

    public function handle(AutomationRunService $automations): int
    {
        $this->info('Starting TelecomOS automated sweep...');

        $stats = $automations->runAll();

        $this->info("Automations complete: Dunning processed: {$stats['dunning_processed']}, Paywalls reconciled: {$stats['paywall_reconciled']}.");

        return self::SUCCESS;
    }
}
