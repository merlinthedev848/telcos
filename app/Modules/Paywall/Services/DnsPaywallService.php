<?php

namespace App\Modules\Paywall\Services;

use App\Enums\PaywallStatus;
use App\Modules\Core\Services\CriticalOperation;
use App\Modules\Customers\Models\Customer;
use App\Modules\Paywall\Models\PaywallState;
use Illuminate\Support\Facades\DB;

class DnsPaywallService
{
    public function __construct(private readonly CriticalOperation $operations)
    {
    }

    /**
     * Apply DNS captive portal / restriction policy for a customer.
     */
    public function apply(Customer $customer, string $reason): PaywallState
    {
        return DB::transaction(function () use ($customer, $reason): PaywallState {
            $state = PaywallState::query()->updateOrCreate(
                ['customer_id' => $customer->id],
                [
                    'status' => PaywallStatus::Restricted->value,
                    'reason' => $reason,
                    'dns_policy' => [
                        'mode' => 'redirect',
                        'portal' => '/paywall/'.$customer->account_number,
                        'allow' => ['payment-gateway', 'support'],
                    ],
                    'applied_at' => now(),
                    'restored_at' => null,
                ],
            );

            $this->operations->record('paywall', 'dns_paywall.applied', $state, [
                'customer_id' => $customer->id,
                'account_number' => $customer->account_number,
                'reason' => $reason,
            ]);

            return $state;
        });
    }

    /**
     * Restore unrestricted DNS resolution for a customer.
     */
    public function restore(Customer $customer): PaywallState
    {
        return DB::transaction(function () use ($customer): PaywallState {
            $state = PaywallState::query()->firstOrCreate(
                ['customer_id' => $customer->id],
                ['status' => PaywallStatus::Clear->value]
            );

            $state->update([
                'status' => PaywallStatus::Restored->value,
                'restored_at' => now(),
            ]);

            $this->operations->record('paywall', 'dns_paywall.restored', $state, [
                'customer_id' => $customer->id,
                'account_number' => $customer->account_number,
            ]);

            return $state;
        });
    }
}
