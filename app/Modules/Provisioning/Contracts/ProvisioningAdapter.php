<?php

namespace App\Modules\Provisioning\Contracts;

use App\Modules\Customers\Models\Customer;

interface ProvisioningAdapter
{
    public function activate(Customer $customer, array $serviceProfile): ProvisioningResult;

    public function suspend(Customer $customer, string $reason): ProvisioningResult;

    public function restore(Customer $customer): ProvisioningResult;

    public function health(): ProvisioningResult;
}
