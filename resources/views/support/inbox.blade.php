@extends('layouts.app')

@section('content')
    <h1 class="mb-5 text-2xl font-semibold">Support Inbox</h1>
    <div class="grid gap-4 lg:grid-cols-[20rem_1fr]">
        <aside class="table-shell divide-y divide-line">
            @foreach($tickets as $ticket)
                <div class="p-4">
                    <div class="flex items-center justify-between">
                        <span class="font-semibold">{{ $ticket->ticket_number }}</span>
                        <span class="status-pill bg-slate-100 text-slate-700">{{ $ticket->status }}</span>
                    </div>
                    <div class="mt-1 text-sm font-medium">{{ $ticket->subject }}</div>
                    <div class="mt-1 text-xs text-slate-500">{{ $ticket->customer?->email }}</div>
                </div>
            @endforeach
        </aside>
        <section class="table-shell p-5">
            <h2 class="text-lg font-semibold">Unified Conversation</h2>
            <p class="mt-2 text-sm text-slate-600">Kayako-style ticket stream ready for email, portal, live chat, NOC, and field-service module channels.</p>
            @foreach($tickets->take(1) as $ticket)
                <div class="mt-5 space-y-3">
                    @foreach($ticket->messages as $message)
                        <div class="border border-line bg-panel p-3">
                            <div class="text-xs font-semibold uppercase text-slate-500">{{ $message->direction }} · {{ $message->sender }}</div>
                            <p class="mt-2 text-sm">{{ $message->body }}</p>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </section>
    </div>
@endsection
