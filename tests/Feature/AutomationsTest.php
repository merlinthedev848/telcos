<?php

namespace Tests\Feature;

use App\Enums\PaywallStatus;
use App\Modules\Automations\Services\AutomationRunService;
use App\Modules\Billing\Services\InvoiceService;
use App\Modules\Customers\Models\Customer;
use App\Modules\Paywall\Services\DnsPaywallService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AutomationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_automations_reconciles_settled_paywalls(): void
    {
        $customer = Customer::query()->create([
            'account_number' => 'TEL-AUTO1',
            'contact_name' => 'Auto User',
            'email' => 'auto@example.test',
            'status' => 'active',
        ]);

        $invoice = app(InvoiceService::class)->issue($customer, [
            ['description' => 'Managed Wifi', 'amount' => 30],
        ], now()->subDays(5)->toDateString());

        // Apply paywall
        app(DnsPaywallService::class)->apply($customer, 'overdue');
        $this->assertSame(PaywallStatus::Restricted->value, $customer->paywallState->fresh()->status);

        // Mark invoice as paid
        app(InvoiceService::class)->markPaid($invoice);

        // Run automation sweep
        $stats = app(AutomationRunService::class)->runAll();

        $this->assertEquals(1, $stats['paywall_reconciled']);
        $this->assertSame(PaywallStatus::Restored->value, $customer->paywallState->fresh()->status);
        $this->assertDatabaseHas('audit_logs', [
            'module' => 'automations',
            'operation' => 'automation.run',
        ]);
    }
}
