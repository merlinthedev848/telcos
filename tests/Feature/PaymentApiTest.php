<?php

namespace Tests\Feature;

use App\Enums\InvoiceStatus;
use App\Enums\PaywallStatus;
use App\Modules\Billing\Services\InvoiceService;
use App\Modules\Customers\Models\Customer;
use App\Modules\Paywall\Models\PaywallState;
use App\Modules\Paywall\Services\DnsPaywallService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_record_payment_against_invoice_and_auto_settle(): void
    {
        $customer = Customer::query()->create([
            'account_number' => 'TEL-PAY1',
            'contact_name' => 'Payment User',
            'email' => 'payment@example.test',
            'status' => 'active',
        ]);

        $invoice = app(InvoiceService::class)->issue($customer, [
            ['description' => 'Gigabit Fibre', 'amount' => 100.00],
        ], now()->addDays(7)->toDateString());

        $this->assertEquals(120.00, $invoice->total); // 100 + 20% VAT
        $this->assertEquals(120.00, $invoice->balance_due);

        // Apply paywall to test auto-restoration upon settlement
        app(DnsPaywallService::class)->apply($customer, 'overdue');
        $this->assertSame(PaywallStatus::Restricted->value, $customer->paywallState->fresh()->status);

        // Record full payment
        $response = $this->postJson("/api/v1/invoices/{$invoice->id}/payments", [
            'amount' => 120.00,
            'provider' => 'stripe',
            'reference' => 'ch_123456789',
        ]);

        $response->assertCreated();
        $response->assertJsonPath('data.amount', 120.0);
        $response->assertJsonPath('data.provider', 'stripe');

        // Invoice must now be marked as paid with 0 balance
        $this->assertEquals(0, $invoice->fresh()->balance_due);
        $this->assertSame(InvoiceStatus::Paid->value, $invoice->fresh()->status);

        // DNS paywall must be automatically lifted
        $this->assertSame(PaywallStatus::Restored->value, $customer->paywallState->fresh()->status);
    }
}
