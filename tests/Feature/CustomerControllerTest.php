<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Customers\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::query()->create([
            'name' => 'Support Manager',
            'email' => 'manager@telecomos.local',
            'password' => bcrypt('secret123'),
        ]);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/customers');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_customers_list(): void
    {
        Customer::query()->create([
            'account_number' => 'TEL-WEB1',
            'contact_name' => 'Web Customer',
            'email' => 'web@example.test',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->user)->get('/customers');
        $response->assertOk();
        $response->assertSee('Web Customer');
    }

    public function test_authenticated_user_can_create_customer(): void
    {
        $response = $this->actingAs($this->user)->post('/customers', [
            'contact_name' => 'Fresh Lead',
            'email' => 'fresh@example.test',
            'company_name' => 'Fresh Tech',
            'status' => 'lead',
        ]);

        $customer = Customer::query()->where('email', 'fresh@example.test')->first();
        $this->assertNotNull($customer);

        $response->assertRedirect(route('customers.360', $customer));
    }

    public function test_authenticated_user_can_view_customer_360(): void
    {
        $customer = Customer::query()->create([
            'account_number' => 'TEL-360TEST',
            'contact_name' => 'Profile User',
            'email' => 'profile@example.test',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->user)->get(route('customers.360', $customer));
        $response->assertOk();
        $response->assertSee('TEL-360TEST');
        $response->assertSee('DNS Paywall');
    }
}
