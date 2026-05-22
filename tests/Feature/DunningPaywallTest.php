<?php

namespace Tests\Feature;

use App\Modules\Billing\Services\DunningService;
use App\Modules\Billing\Services\InvoiceService;
use App\Modules\Customers\Services\CustomerService;
use App\Modules\Paywall\Models\PaywallState;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DunningPaywallTest extends TestCase
{
    use RefreshDatabase;

    public function test_stage_three_dunning_applies_dns_paywall(): void
    {
        $customer = app(CustomerService::class)->create([
            'contact_name' => 'Jordan Reed',
            'email' => 'jordan@example.test',
        ]);

        $invoice = app(InvoiceService::class)->issue($customer, [
            ['description' => 'FTTP service', 'amount' => 50],
        ], now()->subDay()->toDateString());

        $dunning = app(DunningService::class);
        $dunning->runForInvoice($invoice);
        $dunning->runForInvoice($invoice->refresh());
        $dunning->runForInvoice($invoice->refresh());

        $this->assertDatabaseHas('paywall_states', [
            'customer_id' => $customer->id,
            'status' => 'restricted',
        ]);

        $this->assertSame('restricted', PaywallState::query()->first()->status);
    }
}
