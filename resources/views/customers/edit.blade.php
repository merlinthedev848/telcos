@extends('layouts.app')

@section('content')
    <h1 class="mb-5 text-2xl font-semibold">Edit Customer</h1>
    <form method="post" action="{{ route('customers.update', $customer) }}" class="table-shell p-5">
        @method('put')
        @include('customers._form')
    </form>
@endsection
