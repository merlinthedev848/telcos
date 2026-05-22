@extends('layouts.app')

@section('content')
    <div class="mb-5 flex items-start justify-between">
        <div>
            <h1 class="text-2xl font-semibold">{{ $customer->company_name ?: $customer->contact_name }}</h1>
            <p class="text-sm text-slate-600">{{ $customer->account_number }} · {{ $customer->email }} · {{ $customer->status }}</p>
        </div>
        <a href="{{ route('customers.edit', $customer) }}" class="border border-line bg-white px-4 py-2 text-sm font-semibold">Edit</a>
    </div>

    <div class="grid gap-4 md:grid-cols-4">
        <div class="metric"><div class="text-xs uppercase text-slate-500">Balance Due</div><div class="mt-2 text-2xl font-bold">£{{ number_format($balance_due, 2) }}</div></div>
        <div class="metric"><div class="text-xs uppercase text-slate-500">Open Tickets</div><div class="mt-2 text-2xl font-bold">{{ $open_tickets->count() }}</div></div>
        <div class="metric"><div class="text-xs uppercase text-slate-500">OTS Status</div><div class="mt-2 text-lg font-bold">{{ $switching?->status ?? 'none' }}</div></div>
        <div class="metric"><div class="text-xs uppercase text-slate-500">Latest Invoice</div><div class="mt-2 text-lg font-bold">{{ $latest_invoice?->invoice_number ?? 'none' }}</div></div>
    </div>

    <div class="mt-6 grid gap-4 lg:grid-cols-2">
        <section class="table-shell p-5">
            <h2 class="text-lg font-semibold">Billing</h2>
            <div class="mt-3 space-y-2 text-sm">
                @forelse($customer->invoices as $invoice)
                    <div class="flex justify-between border-b border-line py-2">
                        <span>{{ $invoice->invoice_number }} · {{ $invoice->status }}</span>
                        <span class="font-semibold">£{{ number_format($invoice->balance_due, 2) }}</span>
                    </div>
                @empty
                    <p class="text-slate-500">No invoices yet.</p>
                @endforelse
            </div>
        </section>
        <section class="table-shell p-5">
            <h2 class="text-lg font-semibold">Support and Communications</h2>
            <div class="mt-3 space-y-2 text-sm">
                @forelse($open_tickets as $ticket)
                    <div class="border-b border-line py-2">
                        <div class="font-semibold">{{ $ticket->subject }}</div>
                        <div class="text-xs uppercase text-slate-500">{{ $ticket->ticket_number }} · {{ $ticket->status }}</div>
                    </div>
                @empty
                    <p class="text-slate-500">No open tickets.</p>
                @endforelse
            </div>
        </section>
    </div>
@endsection
