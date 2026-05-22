<?php

namespace App\Modules\Provisioning\Services;

use App\Modules\Core\Services\CriticalOperation;
use App\Modules\Customers\Models\Customer;
use App\Modules\Provisioning\Adapters\Omada\TpLinkOmadaAdapter;
use App\Modules\Provisioning\Contracts\ProvisioningResult;

class ProvisioningService
{
    public function __construct(
        private readonly CriticalOperation $operations,
        private readonly TpLinkOmadaAdapter $adapter,
    ) {
    }

    public function activate(Customer $customer, array $serviceProfile): ProvisioningResult
    {
        $result = $this->adapter->activate($customer, $serviceProfile);
        $this->operations->record('provisioning', 'service.activation_requested', $customer, $result->payload);

        return $result;
    }
}
