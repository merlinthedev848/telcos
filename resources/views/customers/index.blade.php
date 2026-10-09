@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-ink">Customer Management</h1>
            <p class="text-sm text-slate-500">Manage subscribers, service locations, contact details, and account 360 profiles.</p>
        </div>
        <a href="{{ route('customers.create') }}" class="inline-flex items-center justify-center rounded-lg bg-action px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:opacity-90">
            + Create Customer
        </a>
    </div>

    <div class="mb-4">
        <form method="get" action="{{ route('customers.index') }}" class="flex max-w-md gap-2">
            <input
                type="search"
                name="q"
                value="{{ $searchTerm ?? '' }}"
                placeholder="Search account, name, email, phone..."
                class="w-full rounded-lg border border-line px-3.5 py-2 text-sm text-ink placeholder-slate-400 shadow-sm focus:border-action focus:outline-none focus:ring-1 focus:ring-action"
            >
            <button type="submit" class="rounded-lg border border-line bg-panel px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-200">
                Search
            </button>
            @if(!empty($searchTerm))
                <a href="{{ route('customers.index') }}" class="flex items-center rounded-lg border border-line px-3 py-2 text-xs font-semibold text-slate-500 hover:text-slate-800">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <div class="table-shell rounded-xl border border-line shadow-sm">
        <table class="min-w-full divide-y divide-line text-sm">
            <thead class="bg-panel text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                <tr>
                    <th class="px-5 py-3.5">Account</th>
                    <th class="px-5 py-3.5">Subscriber</th>
                    <th class="px-5 py-3.5">Status</th>
                    <th class="px-5 py-3.5">Email</th>
                    <th class="px-5 py-3.5">Phone</th>
                    <th class="px-5 py-3.5 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-line bg-white">
                @forelse($customers as $customer)
                    <tr class="transition hover:bg-slate-50/60">
                        <td class="px-5 py-3.5 font-mono text-xs font-semibold text-slate-600">{{ $customer->account_number }}</td>
                        <td class="px-5 py-3.5">
                            <div class="font-bold text-ink">{{ $customer->display_name }}</div>
                            @if($customer->company_name && $customer->contact_name)
                                <div class="text-xs text-slate-400">{{ $customer->contact_name }}</div>
                            @endif
                        </td>
                        <td class="px-5 py-3.5">
                            @php
                                $statusClasses = match($customer->status) {
                                    'active' => 'bg-emerald-100 text-emerald-800',
                                    'suspended' => 'bg-amber-100 text-amber-800',
                                    'cancelled' => 'bg-rose-100 text-rose-800',
                                    default => 'bg-slate-100 text-slate-700',
                                };
                            @endphp
                            <span class="status-pill rounded-full {{ $statusClasses }}">{{ $customer->status }}</span>
                        </td>
                        <td class="px-5 py-3.5 text-slate-600">{{ $customer->email }}</td>
                        <td class="px-5 py-3.5 text-slate-600">{{ $customer->phone ?? '—' }}</td>
                        <td class="px-5 py-3.5 text-right font-medium">
                            <div class="flex items-center justify-end gap-3">
                                <a class="font-semibold text-action hover:underline" href="{{ route('customers.360', $customer) }}">360° View</a>
                                <a class="text-xs text-slate-500 hover:text-slate-800" href="{{ route('customers.edit', $customer) }}">Edit</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-10 text-center text-slate-400">
                            No customers found. Click "+ Create Customer" to register a new account.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if($customers->hasPages())
            <div class="border-t border-line bg-panel px-5 py-3">
                {{ $customers->links() }}
            </div>
        @endif
    </div>
@endsection
