<?php

namespace App\Modules\Core\Services;

use App\Events\CriticalOperationRecorded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CriticalOperation
{
    /**
     * Record a critical domain operation for auditing.
     *
     * @param string $module
     * @param string $operation
     * @param Model|string $subject
     * @param array<string, mixed> $payload
     * @param int|null $actorId
     */
    public function record(
        string $module,
        string $operation,
        Model|string $subject,
        array $payload = [],
        ?int $actorId = null,
    ): void {
        $resolvedActorId = $actorId ?? Auth::id();

        event(new CriticalOperationRecorded(
            module: $module,
            operation: $operation,
            subjectType: $subject instanceof Model ? $subject->getMorphClass() : $subject,
            subjectId: $subject instanceof Model ? $subject->getKey() : null,
            payload: $payload,
            actorId: $resolvedActorId ? (int) $resolvedActorId : null,
        ));
    }
}
