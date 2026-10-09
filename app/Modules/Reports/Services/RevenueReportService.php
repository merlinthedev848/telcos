<?php

namespace App\Modules\Reports\Services;

use App\Enums\InvoiceStatus;
use App\Modules\Billing\Models\Invoice;

class RevenueReportService
{
    /**
     * Generate an executive revenue and billing metrics summary.
     *
     * @return array<string, mixed>
     */
    public function summary(): array
    {
        $issued = (float) Invoice::query()->sum('total');
        $outstanding = (float) Invoice::query()->sum('balance_due');
        $overdue = (float) Invoice::query()->where('status', InvoiceStatus::Overdue->value)->sum('balance_due');
        $collected = round(max(0, $issued - $outstanding), 2);
        $collectionRate = $issued > 0 ? round(($collected / $issued) * 100, 1) : 100.0;

        return [
            'issued' => $issued,
            'collected' => $collected,
            'outstanding' => $outstanding,
            'overdue' => $overdue,
            'collection_rate' => $collectionRate,
        ];
    }
}
