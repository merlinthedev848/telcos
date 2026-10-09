<?php

namespace App\Modules\Paywall\Models;

use App\Modules\Customers\Models\Customer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaywallState extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'status',
        'reason',
        'dns_policy',
        'applied_at',
        'restored_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'dns_policy' => 'array',
            'applied_at' => 'datetime',
            'restored_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
