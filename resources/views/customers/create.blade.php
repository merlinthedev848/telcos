@extends('layouts.app')

@section('content')
    <h1 class="mb-5 text-2xl font-semibold">Create Customer</h1>
    <form method="post" action="{{ route('customers.store') }}" class="table-shell p-5">
        @include('customers._form')
    </form>
@endsection
