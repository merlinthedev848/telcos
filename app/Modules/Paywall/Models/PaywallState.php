<?php

namespace App\Modules\Paywall\Models;

use App\Modules\Customers\Models\Customer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaywallState extends Model
{
    protected $fillable = [
        'customer_id',
        'status',
        'reason',
        'dns_policy',
        'applied_at',
        'restored_at',
    ];

    protected $casts = [
        'dns_policy' => 'array',
        'applied_at' => 'datetime',
        'restored_at' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
