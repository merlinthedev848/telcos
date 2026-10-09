<?php

namespace App\Modules\Switching\Services;

use App\Enums\SwitchingStatus;
use App\Modules\Core\Services\CriticalOperation;
use App\Modules\Customers\Models\Customer;
use App\Modules\Switching\Models\SwitchingWorkflow;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OtsSwitchingService
{
    public function __construct(private readonly CriticalOperation $operations)
    {
    }

    /**
     * Start a One Touch Switching (OTS) process for a customer.
     *
     * @param Customer $customer
     * @param array<string, mixed> $payload
     */
    public function start(Customer $customer, array $payload): SwitchingWorkflow
    {
        return DB::transaction(function () use ($customer, $payload): SwitchingWorkflow {
            $workflow = SwitchingWorkflow::query()->create([
                'customer_id' => $customer->id,
                'ots_reference' => 'OTS-'.Str::upper(Str::random(10)),
                'status' => SwitchingStatus::MatchRequested->value,
                'losing_provider' => $payload['losing_provider'] ?? null,
                'gaining_provider' => $payload['gaining_provider'] ?? config('app.name', 'TelecomOS'),
                'requested_switch_date' => $payload['requested_switch_date'] ?? now()->addDays(10)->toDateString(),
                'payload' => $payload,
            ]);

            $this->operations->record('switching', 'ots.workflow_started', $workflow, [
                'ots_reference' => $workflow->ots_reference,
                'status' => $workflow->status,
                'customer_id' => $customer->id,
            ]);

            return $workflow;
        });
    }

    /**
     * Advance the OTS workflow status.
     */
    public function advance(SwitchingWorkflow $workflow, SwitchingStatus|string $status): SwitchingWorkflow
    {
        return DB::transaction(function () use ($workflow, $status): SwitchingWorkflow {
            $statusValue = $status instanceof SwitchingStatus ? $status->value : $status;

            $workflow->update([
                'status' => $statusValue,
                'completed_at' => $statusValue === SwitchingStatus::Completed->value ? now() : null,
            ]);

            $this->operations->record('switching', 'ots.workflow_advanced', $workflow, [
                'ots_reference' => $workflow->ots_reference,
                'status' => $statusValue,
            ]);

            return $workflow->refresh();
        });
    }
}
