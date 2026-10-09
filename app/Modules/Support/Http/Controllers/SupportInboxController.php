<?php

namespace App\Modules\Support\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Support\Models\SupportTicket;
use Illuminate\View\View;

class SupportInboxController extends Controller
{
    public function __invoke(): View
    {
        $statusOrderSql = "CASE status
            WHEN 'new' THEN 1
            WHEN 'open' THEN 2
            WHEN 'pending' THEN 3
            WHEN 'resolved' THEN 4
            WHEN 'closed' THEN 5
            ELSE 6
        END";

        $tickets = SupportTicket::query()
            ->with(['customer', 'messages'])
            ->orderByRaw($statusOrderSql)
            ->latest('last_activity_at')
            ->paginate(30);

        return view('support.inbox', [
            'tickets' => $tickets,
        ]);
    }
}
