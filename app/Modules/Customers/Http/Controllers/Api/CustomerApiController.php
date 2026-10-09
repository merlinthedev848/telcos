<?php

namespace App\Modules\Customers\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Modules\Customers\Models\Customer;
use App\Modules\Customers\Services\CustomerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

class CustomerApiController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $customers = Customer::query()
            ->search($request->string('q')->toString())
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return CustomerResource::collection($customers);
    }

    public function store(StoreCustomerRequest $request, CustomerService $customers): JsonResponse
    {
        $customer = $customers->create($request->validated());

        return (new CustomerResource($customer))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Customer $customer): CustomerResource
    {
        return new CustomerResource($customer->loadMissing(['invoices', 'tickets', 'paywallState']));
    }

    public function update(UpdateCustomerRequest $request, Customer $customer, CustomerService $customers): CustomerResource
    {
        $updated = $customers->update($customer, $request->validated());

        return new CustomerResource($updated);
    }

    public function destroy(Customer $customer, CustomerService $customers): JsonResponse
    {
        $customers->delete($customer);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
