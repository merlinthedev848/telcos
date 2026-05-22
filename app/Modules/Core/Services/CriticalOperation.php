<?php

namespace App\Modules\Core\Services;

use App\Events\CriticalOperationRecorded;
use Illuminate\Database\Eloquent\Model;

class CriticalOperation
{
    public function record(string $module, string $operation, Model|string $subject, array $payload = [], ?int $actorId = null): void
    {
        event(new CriticalOperationRecorded(
            module: $module,
            operation: $operation,
            subjectType: $subject instanceof Model ? $subject::class : $subject,
            subjectId: $subject instanceof Model ? $subject->getKey() : null,
            payload: $payload,
            actorId: $actorId,
        ));
    }
}
