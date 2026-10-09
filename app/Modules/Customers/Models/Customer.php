<?php

namespace App\Modules\Customers\Models;

use App\Enums\CustomerStatus;
use App\Modules\Billing\Models\Invoice;
use App\Modules\Email\Models\EmailThread;
use App\Modules\Paywall\Models\PaywallState;
use App\Modules\Support\Models\SupportTicket;
use App\Modules\Switching\Models\SwitchingWorkflow;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory;
    use SoftDeletes;

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

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'status' => 'string',
        ];
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(\App\Modules\Billing\Models\Payment::class);
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

    public function paywallState(): HasOne
    {
        return $this->hasOne(PaywallState::class);
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->company_name ?: $this->contact_name;
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', CustomerStatus::Active->value);
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        return $query->where(function (Builder $sub) use ($term): void {
            $sub->where('account_number', 'like', "%{$term}%")
                ->orWhere('company_name', 'like', "%{$term}%")
                ->orWhere('contact_name', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%")
                ->orWhere('phone', 'like', "%{$term}%");
        });
    }
}
