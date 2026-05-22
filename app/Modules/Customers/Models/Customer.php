<?php

namespace App\Modules\Customers\Models;

use App\Modules\Billing\Models\Invoice;
use App\Modules\Email\Models\EmailThread;
use App\Modules\Support\Models\SupportTicket;
use App\Modules\Switching\Models\SwitchingWorkflow;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $fillable = [
        'account_number',
        'company_name',
        'contact_name',
        'email',
        'phone',
        'status',
        'service_address',
        'billing_address',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(SupportTicket::class);
    }

    public function emailThreads(): HasMany
    {
        return $this->hasMany(EmailThread::class);
    }

    public function switchingWorkflows(): HasMany
    {
        return $this->hasMany(SwitchingWorkflow::class);
    }
}
