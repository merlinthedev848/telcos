<?php

namespace App\Modules\Support\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Support\Models\SupportTicket;
use Illuminate\View\View;

class SupportInboxController extends Controller
{
    public function __invoke(): View
    {
        return view('support.inbox', [
            'tickets' => SupportTicket::query()
                ->with(['customer', 'messages'])
                ->orderByRaw("FIELD(status, 'new', 'open', 'pending', 'resolved', 'closed')")
                ->latest('last_activity_at')
                ->paginate(30),
        ]);
    }
}
