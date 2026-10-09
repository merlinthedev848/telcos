<?php

namespace App\Modules\Reports\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Billing\Models\Invoice;
use App\Modules\Customers\Models\Customer;
use App\Modules\Reports\Services\RevenueReportService;
use App\Modules\Switching\Models\SwitchingWorkflow;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportsController extends Controller
{
    public function index(RevenueReportService $reports): View
    {
        $revenue = $reports->summary();

        $customerStats = [
            'total' => Customer::query()->count(),
            'active' => Customer::query()->where('status', 'active')->count(),
            'leads' => Customer::query()->where('status', 'lead')->count(),
            'suspended' => Customer::query()->where('status', 'suspended')->count(),
        ];

        $dunningBreakdown = [
            'stage_0' => Invoice::query()->where('dunning_stage', 0)->where('balance_due', '>', 0)->count(),
            'stage_1' => Invoice::query()->where('dunning_stage', 1)->count(),
            'stage_2' => Invoice::query()->where('dunning_stage', 2)->count(),
            'stage_3' => Invoice::query()->where('dunning_stage', 3)->count(),
            'stage_4' => Invoice::query()->where('dunning_stage', 4)->count(),
        ];

        $otsStats = [
            'total' => SwitchingWorkflow::query()->count(),
            'active' => SwitchingWorkflow::query()->active()->count(),
            'completed' => SwitchingWorkflow::query()->where('status', 'completed')->count(),
        ];

        return view('reports.index', [
            'revenue' => $revenue,
            'customerStats' => $customerStats,
            'dunningBreakdown' => $dunningBreakdown,
            'otsStats' => $otsStats,
        ]);
    }

    public function exportInvoicesCsv(): StreamedResponse
    {
        $fileName = 'invoices-report-'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function (): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Invoice Number', 'Account Number', 'Subscriber', 'Status', 'Due Date', 'Total', 'Balance Due', 'Dunning Stage']);

            Invoice::query()->with('customer')->chunkById(200, function ($invoices) use ($handle): void {
                /** @var Invoice $inv */
                foreach ($invoices as $inv) {
                    fputcsv($handle, [
                        $inv->invoice_number,
                        $inv->customer?->account_number ?? 'N/A',
                        $inv->customer?->display_name ?? 'N/A',
                        $inv->status,
                        $inv->due_date?->toDateString(),
                        $inv->total,
                        $inv->balance_due,
                        $inv->dunning_stage,
                    ]);
                }
            });

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv',
        ]);
    }
}
