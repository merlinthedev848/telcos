<?php

namespace App\Modules\Reports\Services;

use App\Modules\Billing\Models\Invoice;

class RevenueReportService
{
    public function summary(): array
    {
        return [
            'issued' => Invoice::query()->sum('total'),
            'outstanding' => Invoice::query()->sum('balance_due'),
            'overdue' => Invoice::query()->where('status', 'overdue')->sum('balance_due'),
        ];
    }
}
