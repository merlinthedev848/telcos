@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold text-ink">{{ $customer->display_name }}</h1>
                @php
                    $statusClasses = match($customer->status) {
                        'active' => 'bg-emerald-100 text-emerald-800',
                        'suspended' => 'bg-amber-100 text-amber-800',
                        'cancelled' => 'bg-rose-100 text-rose-800',
                        default => 'bg-slate-100 text-slate-700',
                    };
                @endphp
                <span class="status-pill rounded-full {{ $statusClasses }}">{{ $customer->status }}</span>
            </div>
            <p class="mt-1 text-sm text-slate-500 font-mono">
                Account: {{ $customer->account_number }} · Email: {{ $customer->email }} · Phone: {{ $customer->phone ?? 'N/A' }}
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('customers.edit', $customer) }}" class="rounded-lg border border-line bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                Edit Details
            </a>
            <a href="{{ route('customers.index') }}" class="rounded-lg border border-transparent px-3 py-2 text-sm font-medium text-slate-500 hover:text-slate-800">
                Back to List
            </a>
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="metric rounded-xl border border-line p-5 shadow-sm">
            <div class="text-xs font-bold uppercase tracking-wider text-slate-400">Balance Due</div>
            <div class="mt-2 text-2xl font-black text-ink">£{{ number_format($balance_due, 2) }}</div>
            <div class="mt-1 text-xs text-slate-500">Unpaid issued / overdue</div>
        </div>

        <div class="metric rounded-xl border border-line p-5 shadow-sm">
            <div class="text-xs font-bold uppercase tracking-wider text-slate-400">Open Tickets</div>
            <div class="mt-2 text-2xl font-black text-ink">{{ $open_tickets->count() }}</div>
            <div class="mt-1 text-xs text-slate-500">Active support threads</div>
        </div>

        <div class="metric rounded-xl border border-line p-5 shadow-sm">
            <div class="text-xs font-bold uppercase tracking-wider text-slate-400">OTS Switching</div>
            <div class="mt-2 text-lg font-bold text-action">
                {{ $switching?->status ? ucwords(str_replace('_', ' ', $switching->status)) : 'None Active' }}
            </div>
            <div class="mt-1 text-xs font-mono text-slate-400">{{ $switching?->ots_reference ?? 'No active switch' }}</div>
        </div>

        <div class="metric rounded-xl border border-line p-5 shadow-sm">
            <div class="text-xs font-bold uppercase tracking-wider text-slate-400">DNS Paywall</div>
            <div class="mt-2 text-lg font-bold {{ ($paywall?->status === 'restricted') ? 'text-rose-600' : 'text-emerald-600' }}">
                {{ $paywall?->status ? ucfirst($paywall->status) : 'Clear' }}
            </div>
            <div class="mt-1 text-xs text-slate-500">{{ $paywall?->reason ? 'Reason: '.$paywall->reason : 'Unrestricted network access' }}</div>
        </div>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        <!-- Billing History -->
        <section class="table-shell rounded-xl border border-line p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <h2 class="text-base font-bold text-ink">Invoices & Billing</h2>
                <span class="text-xs font-semibold text-slate-400">Total Invoices: {{ $customer->invoices->count() }}</span>
            </div>
            <div class="mt-4 divide-y divide-line text-sm">
                @forelse($customer->invoices as $invoice)
                    <div class="flex items-center justify-between py-3">
                        <div>
                            <div class="font-bold text-ink">{{ $invoice->invoice_number }}</div>
                            <div class="text-xs text-slate-400">Due: {{ $invoice->due_date?->format('d M Y') ?? 'N/A' }} · Dunning Stage: {{ $invoice->dunning_stage }}</div>
                        </div>
                        <div class="text-right">
                            <div class="font-bold text-ink">£{{ number_format($invoice->balance_due, 2) }}</div>
                            <span class="status-pill rounded-full text-[10px] {{ $invoice->status === 'paid' ? 'bg-emerald-100 text-emerald-800' : ($invoice->status === 'overdue' ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800') }}">
                                {{ $invoice->status }}
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="py-6 text-center text-xs text-slate-400">No invoices have been issued yet.</p>
                @endforelse
            </div>
        </section>

        <!-- Support Tickets & Comms -->
        <section class="table-shell rounded-xl border border-line p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <h2 class="text-base font-bold text-ink">Support Cases</h2>
                <a href="{{ route('support.inbox') }}" class="text-xs font-semibold text-action hover:underline">Support Inbox &rarr;</a>
            </div>
            <div class="mt-4 divide-y divide-line text-sm">
                @forelse($open_tickets as $ticket)
                    <div class="py-3">
                        <div class="flex items-center justify-between">
                            <div class="font-bold text-ink">{{ $ticket->subject }}</div>
                            <span class="status-pill rounded-full bg-slate-100 text-[10px] text-slate-700">{{ $ticket->status }}</span>
                        </div>
                        <div class="mt-1 text-xs text-slate-400">
                            {{ $ticket->ticket_number }} · Priority: {{ ucfirst($ticket->priority) }} · Last Activity: {{ $ticket->last_activity_at?->diffForHumans() }}
                        </div>
                    </div>
                @empty
                    <p class="py-6 text-center text-xs text-slate-400">No active tickets for this customer.</p>
                @endforelse
            </div>
        </section>
    </div>

    <!-- Location & Metadata -->
    <div class="mt-6 rounded-xl border border-line bg-white p-6 shadow-sm">
        <h2 class="text-base font-bold text-ink">Service & Billing Addresses</h2>
        <div class="mt-4 grid gap-4 md:grid-cols-2 text-sm">
            <div class="rounded-lg border border-line bg-panel p-4">
                <div class="text-xs font-bold uppercase tracking-wider text-slate-400">Service Location</div>
                <div class="mt-2 text-slate-700 whitespace-pre-line">{{ $customer->service_address ?: 'Not provided' }}</div>
            </div>
            <div class="rounded-lg border border-line bg-panel p-4">
                <div class="text-xs font-bold uppercase tracking-wider text-slate-400">Billing Address</div>
                <div class="mt-2 text-slate-700 whitespace-pre-line">{{ $customer->billing_address ?: 'Same as service address' }}</div>
            </div>
        </div>
    </div>
@endsection
