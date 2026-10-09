<?php

namespace App\Modules\Provisioning\Services;

use App\Modules\Core\Services\CriticalOperation;
use App\Modules\Customers\Models\Customer;
use App\Modules\Provisioning\Contracts\ProvisioningAdapter;
use App\Modules\Provisioning\Contracts\ProvisioningResult;

class ProvisioningService
{
    public function __construct(
        private readonly CriticalOperation $operations,
        private readonly ProvisioningAdapter $adapter,
    ) {
    }

    /**
     * Activate network service for a customer.
     *
     * @param Customer $customer
     * @param array<string, mixed> $serviceProfile
     */
    public function activate(Customer $customer, array $serviceProfile = []): ProvisioningResult
    {
        $result = $this->adapter->activate($customer, $serviceProfile);

        $this->operations->record(
            'provisioning',
            'service.activation_requested',
            $customer,
            ['result' => $result->ok, 'message' => $result->message, ...$result->payload],
        );

        return $result;
    }

    /**
     * Suspend network service for a customer.
     */
    public function suspend(Customer $customer, string $reason): ProvisioningResult
    {
        $result = $this->adapter->suspend($customer, $reason);

        $this->operations->record(
            'provisioning',
            'service.suspension_requested',
            $customer,
            ['reason' => $reason, 'result' => $result->ok, ...$result->payload],
        );

        return $result;
    }

    /**
     * Restore network service for a customer.
     */
    public function restore(Customer $customer): ProvisioningResult
    {
        $result = $this->adapter->restore($customer);

        $this->operations->record(
            'provisioning',
            'service.restoration_requested',
            $customer,
            ['result' => $result->ok, ...$result->payload],
        );

        return $result;
    }

    /**
     * Check health status of active provisioning adapter.
     */
    public function health(): ProvisioningResult
    {
        return $this->adapter->health();
    }
}
