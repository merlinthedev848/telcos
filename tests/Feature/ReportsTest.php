<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Billing\Services\InvoiceService;
use App\Modules\Customers\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportsTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::query()->create([
            'name' => 'Report Viewer',
            'email' => 'viewer@telecomos.local',
            'password' => bcrypt('password123'),
        ]);
    }

    public function test_can_view_reports_dashboard(): void
    {
        $response = $this->actingAs($this->user)->get('/reports');
        $response->assertOk();
        $response->assertSee('Operational Analytics');
        $response->assertSee('Total Billed');
    }

    public function test_can_stream_invoices_csv(): void
    {
        $customer = Customer::query()->create([
            'account_number' => 'TEL-CSV1',
            'contact_name' => 'CSV User',
            'email' => 'csv@example.test',
            'status' => 'active',
        ]);

        app(InvoiceService::class)->issue($customer, [
            ['description' => 'Business Leased Line', 'amount' => 250],
        ]);

        $response = $this->actingAs($this->user)->get('/reports/export/invoices');
        $response->assertOk();
        $this->assertStringContainsString('text/csv', (string) $response->headers->get('content-type'));
    }
}
