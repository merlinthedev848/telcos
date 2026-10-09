<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Customers\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_can_create_a_customer(): void
    {
        $response = $this->postJson('/api/v1/customers', [
            'contact_name' => 'Sam Carter',
            'email' => 'sam@example.test',
            'company_name' => 'Carter Telecom',
            'phone' => '+44 7700 900077',
        ]);

        $response->assertCreated();
        $response->assertJsonPath('data.email', 'sam@example.test');
        $response->assertJsonPath('data.company_name', 'Carter Telecom');

        $this->assertDatabaseHas('customers', ['email' => 'sam@example.test']);
        $this->assertDatabaseHas('audit_logs', [
            'module' => 'customers',
            'operation' => 'customer.created',
        ]);
    }

    public function test_api_validates_customer_email_uniqueness(): void
    {
        Customer::query()->create([
            'account_number' => 'TEL-EXISTING',
            'contact_name' => 'Existing User',
            'email' => 'existing@example.test',
            'status' => 'active',
        ]);

        $response = $this->postJson('/api/v1/customers', [
            'contact_name' => 'Duplicate User',
            'email' => 'existing@example.test',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    public function test_api_can_soft_delete_customer(): void
    {
        $customer = Customer::query()->create([
            'account_number' => 'TEL-DELETE1',
            'contact_name' => 'Delete Test',
            'email' => 'delete@example.test',
            'status' => 'lead',
        ]);

        $response = $this->deleteJson('/api/v1/customers/'.$customer->id);
        $response->assertNoContent();

        $this->assertSoftDeleted('customers', ['id' => $customer->id]);
        $this->assertDatabaseHas('audit_logs', [
            'module' => 'customers',
            'operation' => 'customer.deleted',
        ]);
    }
}
