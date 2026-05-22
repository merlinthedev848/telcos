<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login · TelecomOS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 text-ink antialiased">
    <main class="grid min-h-screen place-items-center p-6">
        <form method="post" action="{{ route('login.attempt') }}" class="w-full max-w-sm border border-line bg-white p-6 shadow-sm">
            @csrf
            <h1 class="text-2xl font-bold">TelecomOS</h1>
            <p class="mt-1 text-sm text-slate-600">Admin sign in</p>

            @if($errors->any())
                <div class="mt-4 border border-red-200 bg-red-50 p-3 text-sm text-danger">{{ $errors->first() }}</div>
            @endif

            <label class="mt-5 block text-sm font-medium">Email
                <input name="email" type="email" required autofocus class="mt-1 w-full border-line" value="{{ old('email', 'admin@telecomos.local') }}">
            </label>
            <label class="mt-4 block text-sm font-medium">Password
                <input name="password" type="password" required class="mt-1 w-full border-line" value="telecomos">
            </label>
            <label class="mt-4 flex items-center gap-2 text-sm">
                <input name="remember" type="checkbox" class="border-line">
                Remember this session
            </label>
            <button class="mt-5 w-full bg-action px-4 py-2 text-sm font-semibold text-white">Sign in</button>
        </form>
    </main>
</body>
</html>
