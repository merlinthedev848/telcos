<?php

namespace App\Modules\Customers\Services;

use App\Enums\CustomerStatus;
use App\Modules\Core\Services\CriticalOperation;
use App\Modules\Customers\Models\Customer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CustomerService
{
    public function __construct(private readonly CriticalOperation $operations)
    {
    }

    /**
     * Create a new customer record.
     *
     * @param array<string, mixed> $data
     */
    public function create(array $data): Customer
    {
        return DB::transaction(function () use ($data): Customer {
            $customer = Customer::query()->create([
                ...$data,
                'account_number' => $data['account_number'] ?? 'TEL-'.Str::upper(Str::random(8)),
                'status' => $data['status'] ?? CustomerStatus::Lead->value,
            ]);

            $this->operations->record('customers', 'customer.created', $customer, [
                'account_number' => $customer->account_number,
                'email' => $customer->email,
            ]);

            return $customer;
        });
    }

    /**
     * Update an existing customer record.
     *
     * @param Customer $customer
     * @param array<string, mixed> $data
     */
    public function update(Customer $customer, array $data): Customer
    {
        return DB::transaction(function () use ($customer, $data): Customer {
            $customer->update($data);

            $this->operations->record('customers', 'customer.updated', $customer, [
                'changes' => array_keys($data),
            ]);

            return $customer;
        });
    }

    /**
     * Soft delete a customer record.
     */
    public function delete(Customer $customer): bool
    {
        return DB::transaction(function () use ($customer): bool {
            $this->operations->record('customers', 'customer.deleted', $customer, [
                'account_number' => $customer->account_number,
                'email' => $customer->email,
            ]);

            return (bool) $customer->delete();
        });
    }
}
