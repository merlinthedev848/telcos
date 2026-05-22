@extends('layouts.app')

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold">Operations Dashboard</h1>
            <p class="text-sm text-slate-600">Acquire, switch, provision, bill, collect, restrict, restore, and support from one console.</p>
        </div>
        <a href="{{ route('customers.create') }}" class="bg-action px-4 py-2 text-sm font-semibold text-white">New Customer</a>
    </div>

    <div class="grid gap-4 md:grid-cols-4">
        <div class="border border-emerald-200 bg-gradient-to-br from-emerald-400 to-teal-500 p-4 text-white shadow-sm">
            <div class="text-xs font-semibold uppercase text-white/80">Customers</div>
            <div class="mt-2 text-3xl font-bold">{{ $customerCount }}</div>
        </div>
        <div class="border border-amber-200 bg-gradient-to-br from-amber-400 to-orange-500 p-4 text-white shadow-sm">
            <div class="text-xs font-semibold uppercase text-white/80">Overdue Invoices</div>
            <div class="mt-2 text-3xl font-bold">{{ $overdueInvoices }}</div>
        </div>
        <div class="border border-blue-200 bg-gradient-to-br from-blue-500 to-indigo-600 p-4 text-white shadow-sm">
            <div class="text-xs font-semibold uppercase text-white/80">Open Tickets</div>
            <div class="mt-2 text-3xl font-bold">{{ $openTickets }}</div>
        </div>
        <div class="border border-sky-200 bg-white p-4 shadow-sm">
            <div class="text-xs font-semibold uppercase text-slate-500">Active OTS</div>
            <div class="mt-2 text-3xl font-bold text-action">{{ $activeSwitches }}</div>
        </div>
    </div>

    <section class="mt-6 grid gap-4 lg:grid-cols-3">
        <div class="table-shell p-5 lg:col-span-2">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold">Revenue and Service Activity</h2>
                <span class="text-xs font-semibold uppercase text-slate-400">Live module feed</span>
            </div>
            <div class="mt-6 h-56 border-b border-l border-line">
                <div class="relative h-full">
                    <div class="absolute inset-x-0 bottom-0 h-[38%] bg-cyan-100"></div>
                    <div class="absolute inset-x-0 bottom-0 h-[52%] bg-blue-100" style="clip-path: polygon(0 70%, 14% 48%, 28% 62%, 42% 28%, 56% 43%, 70% 20%, 84% 36%, 100% 12%, 100% 100%, 0 100%)"></div>
                    <svg class="absolute inset-0 h-full w-full" viewBox="0 0 640 220" preserveAspectRatio="none" aria-hidden="true">
                        <polyline points="0,154 90,112 180,136 270,70 360,96 450,52 540,78 640,28" fill="none" stroke="#126a71" stroke-width="4"/>
                        <polyline points="0,178 90,152 180,166 270,122 360,142 450,104 540,126 640,88" fill="none" stroke="#f59e0b" stroke-width="3"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4 grid gap-3 text-sm md:grid-cols-4">
                @foreach(['Acquire', 'OTS', 'Provision', 'Bill', 'Collect', 'Paywall', 'Restore', 'Support'] as $step)
                    <div class="border border-line bg-panel p-3 font-medium">{{ $step }}</div>
                @endforeach
            </div>
        </div>
        <div class="table-shell p-5">
            <h2 class="text-lg font-semibold">Module Mix</h2>
            <div class="mx-auto mt-5 grid h-36 w-36 place-items-center rounded-full" style="background: conic-gradient(#126a71 0 31%, #2563eb 31% 54%, #f59e0b 54% 73%, #10b981 73% 88%, #cbd5e1 88% 100%)">
                <div class="grid h-20 w-20 place-items-center rounded-full bg-white text-sm font-bold text-action">v1</div>
            </div>
            <div class="mt-5 space-y-2 text-sm">
                @foreach(app(\App\Modules\Core\Support\ModuleRegistry::class)->enabled() as $module)
                    <div class="flex items-center justify-between border-b border-line py-2">
                        <span>{{ $module['name'] }}</span>
                        <span class="text-xs font-semibold uppercase text-emerald-700">Enabled</span>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection
