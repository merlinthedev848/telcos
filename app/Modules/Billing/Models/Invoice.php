<?php

namespace App\Modules\Billing\Models;

use App\Enums\InvoiceStatus;
use App\Modules\Customers\Models\Customer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    use HasFactory;

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

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'issued_at' => 'datetime',
            'due_date' => 'date',
            'subtotal' => 'decimal:2',
            'tax_total' => 'decimal:2',
            'total' => 'decimal:2',
            'balance_due' => 'decimal:2',
            'dunning_stage' => 'integer',
            'line_items' => 'array',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function payments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('status', InvoiceStatus::Overdue->value)
            ->orWhere(function (Builder $sub): void {
                $sub->where('balance_due', '>', 0)
                    ->whereDate('due_date', '<', now());
            });
    }

    public function scopeUnpaid(Builder $query): Builder
    {
        return $query->where('balance_due', '>', 0);
    }
}
