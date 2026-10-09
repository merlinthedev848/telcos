<?php

namespace Tests\Feature;

use App\Modules\Customers\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProvisioningApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_invoke_provisioning_endpoints(): void
    {
        $customer = Customer::query()->create([
            'account_number' => 'TEL-PROV1',
            'contact_name' => 'Carol Network',
            'email' => 'carol@prov.test',
            'status' => 'active',
        ]);

        $healthRes = $this->getJson('/api/v1/provisioning/health');
        $healthRes->assertOk();

        $activateRes = $this->postJson("/api/v1/provisioning/{$customer->id}/activate", [
            'service_profile' => [
                'package_name' => 'Ultrafast 900',
                'download_speed' => 900,
                'upload_speed' => 110,
            ],
        ]);
        $activateRes->assertOk();

        $suspendRes = $this->postJson("/api/v1/provisioning/{$customer->id}/suspend", [
            'reason' => 'non_payment',
        ]);
        $suspendRes->assertOk();

        $restoreRes = $this->postJson("/api/v1/provisioning/{$customer->id}/restore");
        $restoreRes->assertOk();
    }
}
