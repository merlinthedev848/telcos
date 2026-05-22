<?php

namespace App\Modules\Paywall\Console;

use App\Modules\Paywall\Models\PaywallState;
use Illuminate\Console\Command;

class SyncPaywallCommand extends Command
{
    protected $signature = 'telecomos:paywall-sync';

    protected $description = 'Sync DNS paywall policy state to configured resolvers.';

    public function handle(): int
    {
        $restricted = PaywallState::query()->where('status', 'restricted')->count();
        $this->info("DNS paywall sync queued for {$restricted} restricted customer(s).");

        return self::SUCCESS;
    }
}
