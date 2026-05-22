<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CriticalOperationRecorded
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly string $module,
        public readonly string $operation,
        public readonly string $subjectType,
        public readonly string|int|null $subjectId,
        public readonly array $payload = [],
        public readonly ?int $actorId = null,
    ) {
    }
}
