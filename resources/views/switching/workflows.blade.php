@extends('layouts.app')

@section('content')
    <h1 class="mb-5 text-2xl font-semibold">One Touch Switching</h1>
    <div class="table-shell">
        <table class="min-w-full divide-y divide-line text-sm">
            <thead class="bg-panel text-left text-xs uppercase text-slate-500">
                <tr>
                    <th class="px-4 py-3">Reference</th>
                    <th class="px-4 py-3">Customer</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Losing Provider</th>
                    <th class="px-4 py-3">Switch Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-line bg-white">
                @foreach($workflows as $workflow)
                    <tr>
                        <td class="px-4 py-3 font-mono text-xs">{{ $workflow->ots_reference }}</td>
                        <td class="px-4 py-3">{{ $workflow->customer?->company_name ?: $workflow->customer?->contact_name }}</td>
                        <td class="px-4 py-3"><span class="status-pill bg-slate-100 text-slate-700">{{ $workflow->status }}</span></td>
                        <td class="px-4 py-3">{{ $workflow->losing_provider }}</td>
                        <td class="px-4 py-3">{{ $workflow->requested_switch_date?->format('d M Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
