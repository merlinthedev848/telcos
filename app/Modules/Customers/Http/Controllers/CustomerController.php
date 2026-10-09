<?php

namespace App\Modules\Customers\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Modules\Customers\Models\Customer;
use App\Modules\Customers\Services\Customer360Service;
use App\Modules\Customers\Services\CustomerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $customers = Customer::query()
            ->search($request->string('q')->toString())
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('customers.index', [
            'customers' => $customers,
            'searchTerm' => $request->string('q')->toString(),
        ]);
    }

    public function create(): View
    {
        return view('customers.create', [
            'customer' => new Customer(),
        ]);
    }

    public function store(StoreCustomerRequest $request, CustomerService $customers): RedirectResponse
    {
        $customer = $customers->create($request->validated());

        return redirect()
            ->route('customers.360', $customer)
            ->with('status', 'Customer created successfully.');
    }

    public function show(Customer $customer): RedirectResponse
    {
        return redirect()->route('customers.360', $customer);
    }

    public function show360(Customer $customer, Customer360Service $customer360): View
    {
        return view('customers.show', $customer360->snapshot($customer));
    }

    public function edit(Customer $customer): View
    {
        return view('customers.edit', [
            'customer' => $customer,
        ]);
    }

    public function update(UpdateCustomerRequest $request, Customer $customer, CustomerService $customers): RedirectResponse
    {
        $customers->update($customer, $request->validated());

        return redirect()
            ->route('customers.360', $customer)
            ->with('status', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer, CustomerService $customers): RedirectResponse
    {
        $customers->delete($customer);

        return redirect()
            ->route('customers.index')
            ->with('status', 'Customer archived successfully.');
    }
}
