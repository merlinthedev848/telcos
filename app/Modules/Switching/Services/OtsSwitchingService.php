<?php

namespace App\Modules\Switching\Services;

use App\Modules\Core\Services\CriticalOperation;
use App\Modules\Customers\Models\Customer;
use App\Modules\Switching\Models\SwitchingWorkflow;
use Illuminate\Support\Str;

class OtsSwitchingService
{
    public function __construct(private readonly CriticalOperation $operations)
    {
    }

    public function start(Customer $customer, array $payload): SwitchingWorkflow
    {
        $workflow = SwitchingWorkflow::query()->create([
            'customer_id' => $customer->id,
            'ots_reference' => 'OTS-'.Str::upper(Str::random(10)),
            'status' => 'match_requested',
            'losing_provider' => $payload['losing_provider'] ?? null,
            'gaining_provider' => $payload['gaining_provider'] ?? 'TelecomOS',
            'requested_switch_date' => $payload['requested_switch_date'] ?? now()->addDays(10)->toDateString(),
            'payload' => $payload,
        ]);

        $this->operations->record('switching', 'ots.workflow_started', $workflow, ['status' => $workflow->status]);

        return $workflow;
    }

    public function advance(SwitchingWorkflow $workflow, string $status): SwitchingWorkflow
    {
        $workflow->update(['status' => $status, 'completed_at' => $status === 'completed' ? now() : null]);
        $this->operations->record('switching', 'ots.workflow_advanced', $workflow, ['status' => $status]);

        return $workflow;
    }
}
