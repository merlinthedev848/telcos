<?php

namespace App\Modules\Core\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Billing\Models\Invoice;
use App\Modules\Customers\Models\Customer;
use App\Modules\Support\Models\SupportTicket;
use App\Modules\Switching\Models\SwitchingWorkflow;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('dashboard.index', [
            'customerCount' => Customer::query()->count(),
            'overdueInvoices' => Invoice::query()->where('status', 'overdue')->count(),
            'openTickets' => SupportTicket::query()->whereIn('status', ['new', 'open', 'pending'])->count(),
            'activeSwitches' => SwitchingWorkflow::query()->whereNotIn('status', ['completed', 'cancelled'])->count(),
        ]);
    }
}
