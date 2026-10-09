@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-ink">Billing & Dunning Management</h1>
            <p class="text-sm text-slate-500">Track subscriber receivables, automated collections, overdue aging, and DNS captive paywall triggers.</p>
        </div>
    </div>

    <!-- Status Filter Tabs -->
    <div class="mb-4 flex flex-wrap gap-2 text-xs font-semibold">
        @php
            $statuses = [
                '' => 'All Invoices',
                'issued' => 'Issued',
                'overdue' => 'Overdue',
                'paid' => 'Paid',
            ];
        @endphp
        @foreach($statuses as $key => $label)
            <a
                href="{{ route('billing.invoices.index', $key ? ['status' => $key] : []) }}"
                class="rounded-lg px-3.5 py-2 transition {{ ($currentStatus ?? '') === $key ? 'bg-action text-white shadow-sm' : 'border border-line bg-white text-slate-600 hover:bg-slate-50' }}"
            >
                {{ $label }}
            </a>
        @endforeach
    </div>

    <div class="table-shell rounded-xl border border-line shadow-sm">
        <table class="min-w-full divide-y divide-line text-sm">
            <thead class="bg-panel text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                <tr>
                    <th class="px-5 py-3.5">Invoice #</th>
                    <th class="px-5 py-3.5">Subscriber</th>
                    <th class="px-5 py-3.5">Status</th>
                    <th class="px-5 py-3.5">Due Date</th>
                    <th class="px-5 py-3.5">Dunning Stage</th>
                    <th class="px-5 py-3.5">Total</th>
                    <th class="px-5 py-3.5">Balance Due</th>
                    <th class="px-5 py-3.5 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-line bg-white">
                @forelse($invoices as $invoice)
                    <tr class="transition hover:bg-slate-50/60">
                        <td class="px-5 py-3.5 font-mono text-xs font-bold text-ink">{{ $invoice->invoice_number }}</td>
                        <td class="px-5 py-3.5 font-medium text-slate-700">
                            @if($invoice->customer)
                                <a href="{{ route('customers.360', $invoice->customer) }}" class="text-action hover:underline">
                                    {{ $invoice->customer->display_name }}
                                </a>
                            @else
                                <span class="text-slate-400">N/A</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5">
                            @php
                                $badgeClass = match($invoice->status) {
                                    'paid' => 'bg-emerald-100 text-emerald-800',
                                    'overdue' => 'bg-rose-100 text-rose-800',
                                    'issued' => 'bg-amber-100 text-amber-800',
                                    default => 'bg-slate-100 text-slate-700',
                                };
                            @endphp
                            <span class="status-pill rounded-full text-[10px] {{ $badgeClass }}">{{ $invoice->status }}</span>
                        </td>
                        <td class="px-5 py-3.5 text-xs text-slate-500">{{ $invoice->due_date?->format('d M Y') ?? '—' }}</td>
                        <td class="px-5 py-3.5 font-mono text-xs text-slate-600">
                            @if($invoice->dunning_stage > 0)
                                <span class="font-bold text-amber-700">Stage {{ $invoice->dunning_stage }}</span>
                            @else
                                <span class="text-slate-400">Stage 0</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-xs text-slate-600">£{{ number_format($invoice->total, 2) }}</td>
                        <td class="px-5 py-3.5 font-bold text-ink">£{{ number_format($invoice->balance_due, 2) }}</td>
                        <td class="px-5 py-3.5 text-right">
                            @if($invoice->balance_due > 0)
                                <form method="post" action="{{ route('billing.invoices.mark-overdue', $invoice) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="rounded border border-amber-300 bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-800 transition hover:bg-amber-100">
                                        Trigger Dunning
                                    </button>
                                </form>
                            @else
                                <span class="text-xs text-emerald-600 font-semibold">Settled</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-5 py-10 text-center text-slate-400">
                            No invoices matching the selected criteria.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if($invoices->hasPages())
            <div class="border-t border-line bg-panel px-5 py-3">
                {{ $invoices->links() }}
            </div>
        @endif
    </div>
@endsection
