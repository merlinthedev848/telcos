<?php

namespace App\Modules\Automations\Services;

use App\Modules\Core\Services\CriticalOperation;

class AutomationRunService
{
    public function __construct(private readonly CriticalOperation $operations)
    {
    }

    public function record(string $name, array $payload = []): void
    {
        $this->operations->record('automations', 'automation.run', 'automation', ['name' => $name, ...$payload]);
    }
}
