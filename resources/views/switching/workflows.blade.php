@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-ink">One Touch Switching (OTS) Workflows</h1>
            <p class="text-sm text-slate-500">Automated UK residential and business broadband migration coordination under OTS compliance.</p>
        </div>
    </div>

    <div class="table-shell rounded-xl border border-line shadow-sm">
        <table class="min-w-full divide-y divide-line text-sm">
            <thead class="bg-panel text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                <tr>
                    <th class="px-5 py-3.5">OTS Reference</th>
                    <th class="px-5 py-3.5">Subscriber</th>
                    <th class="px-5 py-3.5">Status</th>
                    <th class="px-5 py-3.5">Losing Provider</th>
                    <th class="px-5 py-3.5">Gaining Provider</th>
                    <th class="px-5 py-3.5">Requested Date</th>
                    <th class="px-5 py-3.5">Completed At</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-line bg-white">
                @forelse($workflows as $workflow)
                    <tr class="transition hover:bg-slate-50/60">
                        <td class="px-5 py-3.5 font-mono text-xs font-bold text-action">{{ $workflow->ots_reference }}</td>
                        <td class="px-5 py-3.5 font-medium text-slate-800">
                            @if($workflow->customer)
                                <a href="{{ route('customers.360', $workflow->customer) }}" class="hover:underline">
                                    {{ $workflow->customer->display_name }}
                                </a>
                            @else
                                <span class="text-slate-400">N/A</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5">
                            @php
                                $otsBadge = match($workflow->status) {
                                    'completed' => 'bg-emerald-100 text-emerald-800',
                                    'match_requested', 'matched' => 'bg-sky-100 text-sky-800',
                                    'switch_ordered' => 'bg-amber-100 text-amber-800',
                                    'cancelled' => 'bg-rose-100 text-rose-800',
                                    default => 'bg-slate-100 text-slate-700',
                                };
                            @endphp
                            <span class="status-pill rounded-full text-[10px] {{ $otsBadge }}">
                                {{ str_replace('_', ' ', $workflow->status) }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-xs text-slate-600">{{ $workflow->losing_provider ?? '—' }}</td>
                        <td class="px-5 py-3.5 text-xs text-slate-600 font-semibold">{{ $workflow->gaining_provider ?? 'TelecomOS' }}</td>
                        <td class="px-5 py-3.5 text-xs text-slate-500">{{ $workflow->requested_switch_date?->format('d M Y') ?? '—' }}</td>
                        <td class="px-5 py-3.5 text-xs text-slate-500">{{ $workflow->completed_at?->format('d M Y H:i') ?? 'Pending' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-10 text-center text-slate-400">
                            No OTS switching workflows active at this time.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if($workflows->hasPages())
            <div class="border-t border-line bg-panel px-5 py-3">
                {{ $workflows->links() }}
            </div>
        @endif
    </div>
@endsection
