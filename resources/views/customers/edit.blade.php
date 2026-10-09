@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-ink">Edit Customer: {{ $customer->display_name }}</h1>
            <p class="text-sm font-mono text-slate-500">Account: {{ $customer->account_number }}</p>
        </div>
        <form method="post" action="{{ route('customers.destroy', $customer) }}" onsubmit="return confirm('Are you sure you want to archive this customer?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="rounded-lg border border-rose-200 bg-rose-50 px-3.5 py-2 text-xs font-semibold text-rose-700 hover:bg-rose-100">
                Archive Customer
            </button>
        </form>
    </div>

    <div class="max-w-3xl rounded-xl border border-line bg-white p-6 shadow-sm">
        <form method="post" action="{{ route('customers.update', $customer) }}">
            @method('PUT')
            @include('customers._form')
        </form>
    </div>
@endsection
