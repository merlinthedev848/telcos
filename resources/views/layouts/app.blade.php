<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'TelecomOS') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#eef3f8] text-ink antialiased">
    <div class="min-h-screen">
        <header class="border-b border-white/20 bg-white text-ink shadow-sm">
            <div class="flex h-14 items-center justify-between px-5">
                <div class="flex items-center gap-4">
                    <a href="{{ route('dashboard') }}" class="text-lg font-bold text-action">TelecomOS</a>
                    <span class="text-xs uppercase text-slate-400">OSS/BSS</span>
                </div>
                <div class="flex items-center gap-3 text-sm text-slate-600">
                    <span>Admin</span>
                    <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                </div>
            </div>
        </header>

        <div class="flex">
            <aside class="min-h-[calc(100vh-3.5rem)] w-64 bg-gradient-to-b from-emerald-400 via-cyan-500 to-blue-700 text-white shadow-xl">
                <nav class="space-y-1 p-3 text-sm">
                    @php($nav = app(\App\Modules\Core\Support\ModuleRegistry::class)->nav())
                    @foreach($nav as $module)
                        <a class="block border-l-4 px-3 py-2 font-medium {{ request()->routeIs($module['nav']) ? 'border-white bg-white/20 text-white' : 'border-transparent text-white/85 hover:bg-white/10' }}" href="{{ route($module['nav']) }}">
                            {{ $module['name'] }}
                        </a>
                    @endforeach
                </nav>
                <div class="border-t border-white/20 p-4 text-xs leading-5 text-white/75">
                    Built for OTS, billing, provisioning, DNS paywall, support, and module expansion.
                </div>
            </aside>

            <main class="flex-1 p-6">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
