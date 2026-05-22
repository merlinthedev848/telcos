<?php

namespace App\Modules\Billing\Models;

use App\Modules\Customers\Models\Customer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    protected $fillable = [
        'customer_id',
        'invoice_number',
        'status',
        'issued_at',
        'due_date',
        'subtotal',
        'tax_total',
        'total',
        'balance_due',
        'dunning_stage',
        'line_items',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_total' => 'decimal:2',
        'total' => 'decimal:2',
        'balance_due' => 'decimal:2',
        'line_items' => 'array',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
