<?php

namespace Tests\Feature;

use App\Modules\Customers\Models\Customer;
use App\Modules\Switching\Models\SwitchingWorkflow;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SwitchingApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_start_and_advance_ots_switching_workflow(): void
    {
        $customer = Customer::query()->create([
            'account_number' => 'TEL-OTS1',
            'contact_name' => 'Bob Switch',
            'email' => 'bob@ots.test',
            'status' => 'lead',
        ]);

        $createResponse = $this->postJson('/api/v1/switching/workflows', [
            'customer_id' => $customer->id,
            'losing_provider' => 'Legacy Broadband Inc',
            'gaining_provider' => 'TelecomOS Gigabit',
            'requested_switch_date' => now()->addDays(14)->toDateString(),
        ]);

        $createResponse->assertCreated();
        $workflowId = $createResponse->json('data.id');

        $advanceResponse = $this->postJson("/api/v1/switching/workflows/{$workflowId}/advance", [
            'status' => 'completed',
        ]);

        $advanceResponse->assertOk();
        $advanceResponse->assertJsonPath('data.status', 'completed');

        $this->assertDatabaseHas('switching_workflows', [
            'id' => $workflowId,
            'status' => 'completed',
        ]);
        $this->assertNotNull(SwitchingWorkflow::find($workflowId)->completed_at);
    }
}
