<?php

namespace App\Modules\Paywall\Services;

use App\Modules\Core\Services\CriticalOperation;
use App\Modules\Customers\Models\Customer;
use App\Modules\Paywall\Models\PaywallState;

class DnsPaywallService
{
    public function __construct(private readonly CriticalOperation $operations)
    {
    }

    public function apply(Customer $customer, string $reason): PaywallState
    {
        $state = PaywallState::query()->updateOrCreate(
            ['customer_id' => $customer->id],
            [
                'status' => 'restricted',
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

        $this->operations->record('paywall', 'dns_paywall.applied', $state, ['reason' => $reason]);

        return $state;
    }

    public function restore(Customer $customer): PaywallState
    {
        $state = PaywallState::query()->firstOrCreate(['customer_id' => $customer->id]);
        $state->update(['status' => 'restored', 'restored_at' => now()]);

        $this->operations->record('paywall', 'dns_paywall.restored', $state);

        return $state;
    }
}
