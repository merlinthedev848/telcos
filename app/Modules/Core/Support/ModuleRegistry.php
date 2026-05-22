<?php

namespace App\Modules\Core\Support;

use Illuminate\Support\Collection;

class ModuleRegistry
{
    public function __construct(private readonly array $modules)
    {
    }

    public function all(): Collection
    {
        return collect($this->modules);
    }

    public function enabled(): Collection
    {
        return $this->all()->where('enabled', true)->values();
    }

    public function nav(): Collection
    {
        return $this->enabled()->filter(fn (array $module) => isset($module['nav']))->values();
    }
}
