<?php

namespace App\Modules\Provisioning\Adapters\Omada;

use App\Modules\Customers\Models\Customer;
use App\Modules\Provisioning\Contracts\ProvisioningAdapter;
use App\Modules\Provisioning\Contracts\ProvisioningResult;

class TpLinkOmadaAdapter implements ProvisioningAdapter
{
    public function activate(Customer $customer, array $serviceProfile): ProvisioningResult
    {
        return new ProvisioningResult(true, 'Queued TP-Link Omada activation', [
            'customer' => $customer->account_number,
            'service_profile' => $serviceProfile,
            'adapter' => 'tp-link-omada',
        ]);
    }

    public function suspend(Customer $customer, string $reason): ProvisioningResult
    {
        return new ProvisioningResult(true, 'Queued TP-Link Omada suspension', compact('reason'));
    }

    public function restore(Customer $customer): ProvisioningResult
    {
        return new ProvisioningResult(true, 'Queued TP-Link Omada restore');
    }

    public function health(): ProvisioningResult
    {
        return new ProvisioningResult(true, 'TP-Link Omada adapter configured', [
            'base_url' => config('services.omada.base_url'),
            'site_id' => config('services.omada.site_id'),
        ]);
    }
}
