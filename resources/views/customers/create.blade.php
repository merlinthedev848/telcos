@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-ink">New Customer Registration</h1>
        <p class="text-sm text-slate-500">Register a new subscriber account into TelecomOS OSS/BSS.</p>
    </div>

    <div class="max-w-3xl rounded-xl border border-line bg-white p-6 shadow-sm">
        <form method="post" action="{{ route('customers.store') }}">
            @include('customers._form')
        </form>
    </div>
@endsection
