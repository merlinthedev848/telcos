<?php

namespace App\Modules\Switching\Models;

use App\Enums\SwitchingStatus;
use App\Modules\Customers\Models\Customer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SwitchingWorkflow extends Model
{
    use HasFactory;

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

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'requested_switch_date' => 'date',
            'completed_at' => 'datetime',
            'payload' => 'array',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNotIn('status', [
            SwitchingStatus::Completed->value,
            SwitchingStatus::Cancelled->value,
        ]);
    }
}
