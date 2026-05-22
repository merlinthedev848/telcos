<?php

namespace App\Modules\Switching\Models;

use App\Modules\Customers\Models\Customer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SwitchingWorkflow extends Model
{
    protected $fillable = [
        'customer_id',
        'ots_reference',
        'status',
        'losing_provider',
        'gaining_provider',
        'requested_switch_date',
        'completed_at',
        'payload',
    ];

    protected $casts = [
        'requested_switch_date' => 'date',
        'completed_at' => 'datetime',
        'payload' => 'array',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
