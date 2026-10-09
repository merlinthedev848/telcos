<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'TelecomOS') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#eef3f8] text-ink antialiased">
    <div class="min-h-screen">
        <header class="border-b border-line bg-white text-ink shadow-sm">
            <div class="flex h-14 items-center justify-between px-6">
                <div class="flex items-center gap-4">
                    <a href="{{ route('dashboard') }}" class="text-xl font-black tracking-tight text-action">TelecomOS</a>
                    <span class="rounded bg-slate-100 px-2 py-0.5 font-mono text-[10px] font-bold uppercase tracking-wider text-slate-500">OSS/BSS</span>
                </div>
                <div class="flex items-center gap-4 text-sm">
                    @auth
                        <div class="flex items-center gap-2 text-slate-700">
                            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                            <span class="font-medium">{{ auth()->user()->name }}</span>
                            <span class="text-xs text-slate-400">({{ auth()->user()->email }})</span>
                        </div>
                        <form method="post" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="rounded border border-line bg-panel px-2.5 py-1 text-xs font-semibold text-slate-600 transition hover:bg-slate-200 hover:text-ink">
                                Sign Out
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-xs font-semibold text-action hover:underline">Sign In</a>
                    @endauth
                </div>
            </div>
        </header>

        <div class="flex">
            <aside class="min-h-[calc(100vh-3.5rem)] w-64 bg-gradient-to-b from-emerald-600 via-teal-700 to-slate-900 text-white shadow-xl">
                <div class="p-4">
                    <div class="text-[11px] font-bold uppercase tracking-wider text-emerald-200/80">Navigation</div>
                </div>
                <nav class="space-y-1 px-3 text-sm">
                    @php($nav = app(\App\Modules\Core\Support\ModuleRegistry::class)->nav())
                    @foreach($nav as $module)
                        @php
                            $routePrefix = \Illuminate\Support\Str::before($module['nav'], '.');
                            $isActive = request()->routeIs($module['nav']) || request()->routeIs($routePrefix . '.*');
                        @endphp
                        <a class="flex items-center justify-between rounded-md border-l-4 px-3 py-2.5 font-medium transition {{ $isActive ? 'border-white bg-white/20 text-white font-semibold shadow-sm' : 'border-transparent text-white/85 hover:bg-white/10 hover:text-white' }}" href="{{ route($module['nav']) }}">
                            <span>{{ $module['name'] }}</span>
                            @if($isActive)
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-300"></span>
                            @endif
                        </a>
                    @endforeach
                </nav>
                <div class="mt-8 border-t border-white/10 p-5 text-xs leading-relaxed text-white/70">
                    <p class="font-semibold text-white/90">TelecomOS Platform</p>
                    <p class="mt-1 text-[11px]">One Touch Switching, billing, automated dunning, TP-Link Omada provisioning, DNS captive paywall, and omnichannel support.</p>
                </div>
            </aside>

            <main class="flex-1 p-8">
                @if(session('status'))
                    <div class="mb-6 flex items-center justify-between rounded-lg border border-emerald-300 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800 shadow-sm">
                        <div class="flex items-center gap-2">
                            <span class="inline-block h-2 w-2 rounded-full bg-emerald-500"></span>
                            <span>{{ session('status') }}</span>
                        </div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-6 rounded-lg border border-rose-300 bg-rose-50 p-4 text-sm text-rose-800 shadow-sm">
                        <div class="font-semibold">Please correct the following errors:</div>
                        <ul class="mt-2 list-inside list-disc space-y-1 text-xs">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
