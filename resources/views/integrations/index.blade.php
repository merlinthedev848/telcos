@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-ink">External Integrations & Connectors</h1>
        <p class="text-sm text-slate-500">Inspect status, verify API connectivity, and manage adapters for network hardware, OTS hubs, and email.</p>
    </div>

    <div class="grid gap-6 md:grid-cols-2">
        @foreach($integrations as $item)
            <div class="table-shell rounded-xl border border-line p-6 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <h2 class="text-base font-bold text-ink">{{ $item['name'] }}</h2>
                        <span class="status-pill rounded-full text-[10px] {{ $item['status'] === 'active' || $item['status'] === 'configured' ? 'bg-emerald-100 text-emerald-800' : 'bg-sky-100 text-sky-800' }}">
                            {{ $item['badge'] }}
                        </span>
                    </div>
                    <p class="mt-2 text-xs text-slate-600 leading-relaxed">{{ $item['description'] }}</p>
                    <div class="mt-4 rounded-lg border border-line bg-panel p-3 text-xs font-mono text-slate-500">
                        {{ $item['details'] }}
                    </div>
                </div>

                <div class="mt-6 border-t border-line pt-4 flex items-center justify-end">
                    <form method="post" action="{{ route('integrations.test', $item['key']) }}">
                        @csrf
                        <button type="submit" class="rounded-lg border border-line bg-white px-3.5 py-1.5 text-xs font-semibold text-slate-700 shadow-xs transition hover:bg-slate-50">
                            Test Connectivity
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
@endsection
