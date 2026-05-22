@extends('layouts.app')

@section('content')
    <h1 class="mb-5 text-2xl font-semibold">Billing and Dunning</h1>
    <div class="table-shell">
        <table class="min-w-full divide-y divide-line text-sm">
            <thead class="bg-panel text-left text-xs uppercase text-slate-500">
                <tr>
                    <th class="px-4 py-3">Invoice</th>
                    <th class="px-4 py-3">Customer</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Due</th>
                    <th class="px-4 py-3">Balance</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-line bg-white">
                @foreach($invoices as $invoice)
                    <tr>
                        <td class="px-4 py-3 font-semibold">{{ $invoice->invoice_number }}</td>
                        <td class="px-4 py-3">{{ $invoice->customer?->company_name ?: $invoice->customer?->contact_name }}</td>
                        <td class="px-4 py-3"><span class="status-pill bg-amber-100 text-warn">{{ $invoice->status }}</span></td>
                        <td class="px-4 py-3">{{ $invoice->due_date?->format('d M Y') }}</td>
                        <td class="px-4 py-3 font-semibold">£{{ number_format($invoice->balance_due, 2) }}</td>
                        <td class="px-4 py-3 text-right">
                            <form method="post" action="{{ route('billing.invoices.mark-overdue', $invoice) }}">
                                @csrf
                                <button class="text-sm font-semibold text-action">Run Dunning</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
