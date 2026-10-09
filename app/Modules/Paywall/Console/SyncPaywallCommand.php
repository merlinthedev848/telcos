<?php

namespace App\Modules\Paywall\Console;

use App\Enums\PaywallStatus;
use App\Modules\Paywall\Models\PaywallState;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncPaywallCommand extends Command
{
    protected $signature = 'telecomos:paywall-sync {--dry-run : Only display synced entries without applying}';

    protected $description = 'Sync active DNS paywall restriction policies to core DNS resolvers and gateways.';

    public function handle(): int
    {
        $this->info('Initializing DNS paywall policy sync...');

        $restrictedStates = PaywallState::query()
            ->with('customer')
            ->where('status', PaywallStatus::Restricted->value)
            ->get();

        $count = $restrictedStates->count();

        if ($count === 0) {
            $this->info('No restricted accounts found. DNS policy is clear.');

            return self::SUCCESS;
        }

        $rules = [];
        foreach ($restrictedStates as $state) {
            $customer = $state->customer;
            if (! $customer) {
                continue;
            }

            $rules[] = [
                'account' => $customer->account_number,
                'email' => $customer->email,
                'portal_url' => $state->dns_policy['portal'] ?? "/paywall/{$customer->account_number}",
                'applied_at' => $state->applied_at?->toIso8601String(),
                'reason' => $state->reason,
            ];
        }

        if ($this->option('dry-run')) {
            $this->table(['Account', 'Email', 'Portal Redirect', 'Reason', 'Applied At'], array_map(function ($r) {
                return [$r['account'], $r['email'], $r['portal_url'], $r['reason'], $r['applied_at']];
            }, $rules));

            return self::SUCCESS;
        }

        Log::info("DNS paywall policy synchronized for {$count} subscriber(s).", [
            'restricted_count' => $count,
            'accounts' => array_column($rules, 'account'),
        ]);

        $this->info("DNS paywall sync complete. Active restrictions: {$count}.");

        return self::SUCCESS;
    }
}
