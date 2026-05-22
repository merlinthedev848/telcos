<?php

namespace App\Providers;

use App\Events\CriticalOperationRecorded;
use App\Listeners\WriteCriticalOperationAuditLog;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        CriticalOperationRecorded::class => [
            WriteCriticalOperationAuditLog::class,
        ],
    ];
}
