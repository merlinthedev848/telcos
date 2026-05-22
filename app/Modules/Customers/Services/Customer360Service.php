<?php

namespace App\Modules\Customers\Services;

use App\Modules\Customers\Models\Customer;

class Customer360Service
{
    public function snapshot(Customer $customer): array
    {
        $customer->load(['invoices', 'tickets', 'emailThreads', 'switchingWorkflows']);

        return [
            'customer' => $customer,
            'balance_due' => $customer->invoices->whereIn('status', ['issued', 'overdue'])->sum('balance_due'),
            'latest_invoice' => $customer->invoices->sortByDesc('due_date')->first(),
            'open_tickets' => $customer->tickets->whereIn('status', ['new', 'open', 'pending'])->values(),
            'switching' => $customer->switchingWorkflows->sortByDesc('created_at')->first(),
            'communications' => $customer->emailThreads->sortByDesc('last_message_at')->take(5),
        ];
    }
}
