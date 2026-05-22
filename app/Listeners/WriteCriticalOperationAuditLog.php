<?php

namespace App\Listeners;

use App\Events\CriticalOperationRecorded;
use App\Modules\Core\Models\AuditLog;
use Illuminate\Contracts\Queue\ShouldQueue;

class WriteCriticalOperationAuditLog implements ShouldQueue
{
    public string $queue = 'critical';

    public function handle(CriticalOperationRecorded $event): void
    {
        AuditLog::query()->create([
            'module' => $event->module,
            'operation' => $event->operation,
            'subject_type' => $event->subjectType,
            'subject_id' => $event->subjectId,
            'actor_id' => $event->actorId,
            'payload' => $event->payload,
        ]);
    }
}
