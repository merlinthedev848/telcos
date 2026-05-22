<?php

namespace App\Providers;

use App\Modules\Billing\Console\RunDunningCommand;
use App\Modules\Paywall\Console\SyncPaywallCommand;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                RunDunningCommand::class,
                SyncPaywallCommand::class,
            ]);
        }
    }
}
