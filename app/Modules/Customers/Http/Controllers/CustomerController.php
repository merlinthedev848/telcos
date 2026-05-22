<?php

namespace App\Modules\Customers\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Customers\Models\Customer;
use App\Modules\Customers\Services\Customer360Service;
use App\Modules\Customers\Services\CustomerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(): View
    {
        return view('customers.index', ['customers' => Customer::query()->latest()->paginate(20)]);
    }

    public function create(): View
    {
        return view('customers.create');
    }

    public function store(Request $request, CustomerService $customers): RedirectResponse
    {
        $customer = $customers->create($this->validated($request));

        return redirect()->route('customers.360', $customer);
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
        return view('customers.edit', ['customer' => $customer]);
    }

    public function update(Request $request, Customer $customer, CustomerService $customers): RedirectResponse
    {
        $customers->update($customer, $this->validated($request));

        return redirect()->route('customers.360', $customer);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'company_name' => ['nullable', 'string', 'max:255'],
            'contact_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'service_address' => ['nullable', 'string'],
            'billing_address' => ['nullable', 'string'],
            'status' => ['nullable', 'string', 'max:40'],
        ]);
    }
}
