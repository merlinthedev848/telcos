<?php

namespace App\Providers;

use App\Modules\Core\Support\ModuleRegistry;
use App\Modules\Provisioning\Adapters\Omada\TpLinkOmadaAdapter;
use App\Modules\Provisioning\Contracts\ProvisioningAdapter;
use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ModuleRegistry::class, fn () => new ModuleRegistry(config('modules', [])));

        $this->app->bind(ProvisioningAdapter::class, TpLinkOmadaAdapter::class);
    }
}
