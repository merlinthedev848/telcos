@extends('layouts.app')

@section('content')
    <div class="mb-5 flex items-center justify-between">
        <h1 class="text-2xl font-semibold">Customers</h1>
        <a href="{{ route('customers.create') }}" class="bg-action px-4 py-2 text-sm font-semibold text-white">Create</a>
    </div>

    <div class="table-shell">
        <table class="min-w-full divide-y divide-line text-sm">
            <thead class="bg-panel text-left text-xs uppercase text-slate-500">
                <tr>
                    <th class="px-4 py-3">Account</th>
                    <th class="px-4 py-3">Customer</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Email</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-line bg-white">
                @foreach($customers as $customer)
                    <tr>
                        <td class="px-4 py-3 font-mono text-xs">{{ $customer->account_number }}</td>
                        <td class="px-4 py-3 font-semibold">{{ $customer->company_name ?: $customer->contact_name }}</td>
                        <td class="px-4 py-3"><span class="status-pill bg-slate-100 text-slate-700">{{ $customer->status }}</span></td>
                        <td class="px-4 py-3">{{ $customer->email }}</td>
                        <td class="px-4 py-3 text-right"><a class="font-semibold text-action" href="{{ route('customers.360', $customer) }}">360</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
