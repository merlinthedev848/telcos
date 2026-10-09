<?php

namespace Tests\Feature;

use App\Enums\InvoiceStatus;
use App\Modules\Billing\Models\Invoice;
use App\Modules\Billing\Services\InvoiceService;
use App\Modules\Customers\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_and_filter_invoices(): void
    {
        $customer = Customer::query()->create([
            'account_number' => 'TEL-INVTEST',
            'contact_name' => 'Invoice Test User',
            'email' => 'invoicetest@example.test',
            'status' => 'active',
        ]);

        $invoiceService = app(InvoiceService::class);
        $invoice = $invoiceService->issue($customer, [
            ['description' => 'Business Broadband 500/50', 'amount' => 60.00],
        ], now()->addDays(7)->toDateString());

        $listRes = $this->getJson('/api/v1/invoices');
        $listRes->assertOk();
        $listRes->assertJsonPath('data.0.invoice_number', $invoice->invoice_number);

        $showRes = $this->getJson("/api/v1/invoices/{$invoice->id}");
        $showRes->assertOk();
        $showRes->assertJsonPath('data.total', 72.0); // 60 + 20% VAT
        $showRes->assertJsonPath('data.status', InvoiceStatus::Issued->value);
    }

    public function test_can_advance_dunning_via_api(): void
    {
        $customer = Customer::query()->create([
            'account_number' => 'TEL-DUNTEST',
            'contact_name' => 'Dunning Test User',
            'email' => 'dunningtest@example.test',
            'status' => 'active',
        ]);

        $invoiceService = app(InvoiceService::class);
        $invoice = $invoiceService->issue($customer, [
            ['description' => 'VoIP Trunk line', 'amount' => 20.00],
        ], now()->subDays(2)->toDateString());

        $dunningRes = $this->postJson("/api/v1/invoices/{$invoice->id}/dunning");
        $dunningRes->assertOk();
        $dunningRes->assertJsonPath('data.dunning_stage', 1);
        $dunningRes->assertJsonPath('data.status', InvoiceStatus::Overdue->value);

        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'dunning_stage' => 1,
            'status' => 'overdue',
        ]);
    }
}
