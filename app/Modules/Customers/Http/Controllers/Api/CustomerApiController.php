<?php

namespace App\Modules\Customers\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Customers\Models\Customer;
use App\Modules\Customers\Services\CustomerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerApiController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Customer::query()->latest()->paginate());
    }

    public function store(Request $request, CustomerService $customers): JsonResponse
    {
        $customer = $customers->create($request->validate([
            'company_name' => ['nullable', 'string'],
            'contact_name' => ['required', 'string'],
            'email' => ['required', 'email'],
            'phone' => ['nullable', 'string'],
            'service_address' => ['nullable', 'string'],
            'billing_address' => ['nullable', 'string'],
        ]));

        return response()->json($customer, 201);
    }

    public function show(Customer $customer): JsonResponse
    {
        return response()->json($customer->load(['invoices', 'tickets']));
    }

    public function update(Request $request, Customer $customer, CustomerService $customers): JsonResponse
    {
        return response()->json($customers->update($customer, $request->all()));
    }

    public function destroy(Customer $customer): JsonResponse
    {
        $customer->delete();

        return response()->json(status: 204);
    }
}
