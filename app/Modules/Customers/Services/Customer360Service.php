<?php

namespace App\Modules\Customers\Services;

use App\Enums\InvoiceStatus;
use App\Modules\Customers\Models\Customer;

class Customer360Service
{
    /**
     * Build an optimized 360-degree operational view for a customer.
     *
     * @param Customer $customer
     * @return array<string, mixed>
     */
    public function snapshot(Customer $customer): array
    {
        $customer->loadMissing(['paywallState']);

        $balanceDue = (float) $customer->invoices()
            ->whereIn('status', [InvoiceStatus::Issued->value, InvoiceStatus::Overdue->value])
            ->sum('balance_due');

        $latestInvoice = $customer->invoices()
            ->latest('due_date')
            ->first();

        $openTickets = $customer->tickets()
            ->open()
            ->with(['assignee', 'messages'])
            ->latest('last_activity_at')
            ->get();

        $switching = $customer->switchingWorkflows()
            ->latest('created_at')
            ->first();

        $communications = $customer->emailThreads()
            ->with(['messages' => fn ($q) => $q->latest()->limit(2)])
            ->latest('last_message_at')
            ->take(5)
            ->get();

        return [
            'customer' => $customer,
            'balance_due' => $balanceDue,
            'latest_invoice' => $latestInvoice,
            'open_tickets' => $openTickets,
            'switching' => $switching,
            'communications' => $communications,
            'paywall' => $customer->paywallState,
        ];
    }
}
