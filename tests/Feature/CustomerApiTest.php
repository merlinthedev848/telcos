<?php

namespace Tests\Feature;

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
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('customers', ['email' => 'sam@example.test']);
    }
}
