<?php

namespace App\Modules\Customers\Services;

use App\Modules\Core\Services\CriticalOperation;
use App\Modules\Customers\Models\Customer;
use Illuminate\Support\Str;

class CustomerService
{
    public function __construct(private readonly CriticalOperation $operations)
    {
    }

    public function create(array $data): Customer
    {
        $customer = Customer::query()->create([
            ...$data,
            'account_number' => $data['account_number'] ?? 'TEL'.Str::upper(Str::random(8)),
            'status' => $data['status'] ?? 'lead',
        ]);

        $this->operations->record('customers', 'customer.created', $customer, ['email' => $customer->email]);

        return $customer;
    }

    public function update(Customer $customer, array $data): Customer
    {
        $customer->update($data);
        $this->operations->record('customers', 'customer.updated', $customer, ['changes' => array_keys($data)]);

        return $customer;
    }
}
