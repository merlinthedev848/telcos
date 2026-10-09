@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-ink">Operational Analytics & Reports</h1>
            <p class="text-sm text-slate-500">Executive financial metrics, collection performance, customer churn, and OTS migration throughput.</p>
        </div>
        <a href="{{ route('reports.export.invoices') }}" class="inline-flex items-center gap-2 rounded-lg border border-line bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
            <span>&darr; Export Invoices (CSV)</span>
        </a>
    </div>

    <!-- Revenue KPIs -->
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
        <div class="metric rounded-xl border border-line p-5 shadow-sm">
            <div class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Billed</div>
            <div class="mt-2 text-2xl font-black text-ink">£{{ number_format($revenue['issued'], 2) }}</div>
            <div class="mt-1 text-xs text-slate-500">Lifetime revenue issued</div>
        </div>
        <div class="metric rounded-xl border border-emerald-200 bg-emerald-50/50 p-5 shadow-sm">
            <div class="text-xs font-bold uppercase tracking-wider text-emerald-700">Collected Revenue</div>
            <div class="mt-2 text-2xl font-black text-emerald-800">£{{ number_format($revenue['collected'], 2) }}</div>
            <div class="mt-1 text-xs text-emerald-600 font-semibold">{{ $revenue['collection_rate'] }}% collection rate</div>
        </div>
        <div class="metric rounded-xl border border-line p-5 shadow-sm">
            <div class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Outstanding</div>
            <div class="mt-2 text-2xl font-black text-ink">£{{ number_format($revenue['outstanding'], 2) }}</div>
            <div class="mt-1 text-xs text-slate-500">Current balance due</div>
        </div>
        <div class="metric rounded-xl border border-rose-200 bg-rose-50/50 p-5 shadow-sm">
            <div class="text-xs font-bold uppercase tracking-wider text-rose-700">Overdue Debt</div>
            <div class="mt-2 text-2xl font-black text-rose-800">£{{ number_format($revenue['overdue'], 2) }}</div>
            <div class="mt-1 text-xs text-rose-600">Active dunning accounts</div>
        </div>
        <div class="metric rounded-xl border border-line p-5 shadow-sm">
            <div class="text-xs font-bold uppercase tracking-wider text-slate-400">Active Subscribers</div>
            <div class="mt-2 text-2xl font-black text-action">{{ $customerStats['active'] }}</div>
            <div class="mt-1 text-xs text-slate-500">Out of {{ $customerStats['total'] }} accounts</div>
        </div>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-3">
        <!-- Subscriber Breakdown -->
        <div class="table-shell rounded-xl border border-line p-6 shadow-sm">
            <h2 class="text-base font-bold text-ink">Customer Account Health</h2>
            <div class="mt-4 space-y-3">
                <div class="flex items-center justify-between text-sm">
                    <span class="font-medium text-slate-700">Active Subscribers</span>
                    <span class="font-bold text-emerald-700">{{ $customerStats['active'] }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="font-medium text-slate-700">Sales Leads & Quotes</span>
                    <span class="font-bold text-sky-700">{{ $customerStats['leads'] }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="font-medium text-slate-700">Suspended (Paywall / Dunning)</span>
                    <span class="font-bold text-amber-700">{{ $customerStats['suspended'] }}</span>
                </div>
            </div>
        </div>

        <!-- Dunning Aging Breakdown -->
        <div class="table-shell rounded-xl border border-line p-6 shadow-sm">
            <h2 class="text-base font-bold text-ink">Dunning Escalation Funnel</h2>
            <div class="mt-4 space-y-3">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-slate-600">Stage 0 (Current Due)</span>
                    <span class="font-bold text-slate-800">{{ $dunningBreakdown['stage_0'] }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-slate-600">Stage 1 (1st Overdue Reminder)</span>
                    <span class="font-bold text-amber-600">{{ $dunningBreakdown['stage_1'] }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-slate-600">Stage 2 (Urgent Final Demand)</span>
                    <span class="font-bold text-orange-600">{{ $dunningBreakdown['stage_2'] }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-slate-600">Stage 3 (DNS Paywall Applied)</span>
                    <span class="font-bold text-rose-600">{{ $dunningBreakdown['stage_3'] }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-slate-600">Stage 4 (Terminal Suspension)</span>
                    <span class="font-bold text-red-800">{{ $dunningBreakdown['stage_4'] }}</span>
                </div>
            </div>
        </div>

        <!-- OTS Throughput -->
        <div class="table-shell rounded-xl border border-line p-6 shadow-sm">
            <h2 class="text-base font-bold text-ink">OTS Migration Volume</h2>
            <div class="mt-4 space-y-3">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-slate-600">Total Migrations</span>
                    <span class="font-bold text-slate-800">{{ $otsStats['total'] }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-slate-600">In-Flight Switches</span>
                    <span class="font-bold text-action">{{ $otsStats['active'] }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-slate-600">Completed Switches</span>
                    <span class="font-bold text-emerald-700">{{ $otsStats['completed'] }}</span>
                </div>
            </div>
        </div>
    </div>
@endsection
