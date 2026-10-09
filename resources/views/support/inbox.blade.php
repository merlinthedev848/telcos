@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-ink">Unified Support Inbox</h1>
            <p class="text-sm text-slate-500">Manage omnichannel communications, customer tickets, and engineering escalations.</p>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-[22rem_1fr]">
        <!-- Ticket list -->
        <aside class="table-shell max-h-[750px] overflow-y-auto rounded-xl border border-line divide-y divide-line shadow-sm">
            <div class="bg-panel px-4 py-3 text-xs font-bold uppercase tracking-wider text-slate-500">
                Tickets Queue ({{ $tickets->total() }})
            </div>
            @forelse($tickets as $index => $ticket)
                <div class="p-4 transition hover:bg-slate-50/80 {{ $index === 0 ? 'bg-slate-50/90 border-l-4 border-action' : '' }}">
                    <div class="flex items-center justify-between">
                        <span class="font-mono text-xs font-bold text-slate-700">{{ $ticket->ticket_number }}</span>
                        @php
                            $ticketBadge = match($ticket->status) {
                                'new' => 'bg-sky-100 text-sky-800',
                                'open' => 'bg-emerald-100 text-emerald-800',
                                'pending' => 'bg-amber-100 text-amber-800',
                                'resolved', 'closed' => 'bg-slate-100 text-slate-600',
                                default => 'bg-slate-100 text-slate-700',
                            };
                        @endphp
                        <span class="status-pill rounded-full text-[10px] {{ $ticketBadge }}">{{ $ticket->status }}</span>
                    </div>
                    <div class="mt-1.5 text-sm font-bold text-ink line-clamp-1">{{ $ticket->subject }}</div>
                    <div class="mt-1 flex items-center justify-between text-xs text-slate-400">
                        <span>{{ $ticket->customer?->display_name ?? 'Guest' }}</span>
                        <span>{{ $ticket->last_activity_at?->diffForHumans() }}</span>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-xs text-slate-400">No support tickets found.</div>
            @endforelse
        </aside>

        <!-- Active Ticket Detail -->
        <section class="table-shell rounded-xl border border-line p-6 shadow-sm">
            @if($tickets->isNotEmpty())
                @php($activeTicket = $tickets->first())
                <div class="border-b border-line pb-4">
                    <div class="flex items-start justify-between">
                        <div>
                            <span class="font-mono text-xs font-semibold text-action">{{ $activeTicket->ticket_number }}</span>
                            <h2 class="mt-1 text-xl font-bold text-ink">{{ $activeTicket->subject }}</h2>
                            <p class="mt-1 text-xs text-slate-500">
                                Customer: <span class="font-semibold text-slate-700">{{ $activeTicket->customer?->display_name }}</span> ({{ $activeTicket->customer?->email }})
                                · Channel: <span class="font-medium uppercase text-slate-600">{{ $activeTicket->channel }}</span>
                                · Priority: <span class="font-medium uppercase text-slate-600">{{ $activeTicket->priority }}</span>
                            </p>
                        </div>
                        <span class="status-pill rounded-full bg-panel border border-line text-xs font-semibold uppercase text-slate-700">
                            Status: {{ $activeTicket->status }}
                        </span>
                    </div>
                </div>

                <div class="mt-6 space-y-4 max-h-[480px] overflow-y-auto pr-2">
                    @forelse($activeTicket->messages as $msg)
                        <div class="rounded-xl border p-4 shadow-xs {{ $msg->direction === 'inbound' ? 'border-slate-200 bg-panel' : 'border-teal-200 bg-teal-50/50 ml-6' }}">
                            <div class="flex items-center justify-between text-xs font-bold text-slate-500">
                                <span class="uppercase tracking-wider {{ $msg->direction === 'inbound' ? 'text-slate-600' : 'text-action' }}">
                                    {{ $msg->direction === 'inbound' ? 'Subscriber' : 'Support Agent' }} · {{ $msg->sender }}
                                </span>
                                <span>{{ $msg->created_at?->format('d M Y H:i') }}</span>
                            </div>
                            <div class="mt-2.5 text-sm text-ink whitespace-pre-line leading-relaxed">{{ $msg->body }}</div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400">No message history available.</p>
                    @endforelse
                </div>
            @else
                <div class="py-16 text-center text-sm text-slate-400">Select a ticket to inspect conversation history.</div>
            @endif
        </section>
    </div>
@endsection
