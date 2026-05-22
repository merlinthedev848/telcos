<?php

namespace Tests\Unit;

use App\Modules\Core\Support\ModuleRegistry;
use PHPUnit\Framework\TestCase;

class ModuleRegistryTest extends TestCase
{
    public function test_it_exposes_enabled_navigation_modules(): void
    {
        $registry = new ModuleRegistry([
            ['key' => 'core', 'name' => 'Core', 'enabled' => true, 'nav' => 'dashboard'],
            ['key' => 'lab', 'name' => 'Lab', 'enabled' => false, 'nav' => 'lab.index'],
        ]);

        $this->assertCount(1, $registry->nav());
        $this->assertSame('core', $registry->nav()->first()['key']);
    }
}
