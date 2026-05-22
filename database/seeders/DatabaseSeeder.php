<?php

namespace Database\Seeders;

use App\Models\User;
use App\Modules\Billing\Services\InvoiceService;
use App\Modules\Customers\Services\CustomerService;
use App\Modules\Support\Services\SupportTicketService;
use App\Modules\Switching\Services\OtsSwitchingService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'core.view',
            'customers.view',
            'customers.manage',
            'billing.view',
            'billing.manage',
            'paywall.manage',
            'provisioning.manage',
            'support.view',
            'support.manage',
            'switching.view',
            'switching.manage',
            'reports.view',
            'integrations.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        $adminRole = Role::findOrCreate('super-admin');
        $adminRole->syncPermissions($permissions);

        $admin = User::query()->firstOrCreate(
            ['email' => 'admin@telecomos.local'],
            ['name' => 'TelecomOS Admin', 'password' => Hash::make('telecomos')],
        );
        $admin->assignRole($adminRole);

        $customers = app(CustomerService::class);
        $billing = app(InvoiceService::class);
        $support = app(SupportTicketService::class);
        $switching = app(OtsSwitchingService::class);

        $customer = $customers->create([
            'company_name' => 'Northbank Fibre Ltd',
            'contact_name' => 'Avery Morris',
            'email' => 'avery@example.test',
            'phone' => '+44 20 7946 0101',
            'status' => 'active',
            'service_address' => '12 Market Street, Manchester, M1 1AA',
            'billing_address' => '12 Market Street, Manchester, M1 1AA',
        ]);

        $billing->issue($customer, [
            ['description' => 'FTTP 1000/100 managed service', 'amount' => 79.00],
            ['description' => 'Static IPv4 address', 'amount' => 5.00],
        ], now()->subDays(5)->toDateString());

        $support->open($customer, 'Router adoption question', 'Can you confirm whether the TP-Link unit has adopted into our site?');
        $switching->start($customer, ['losing_provider' => 'Legacy ISP', 'requested_switch_date' => now()->addDays(7)->toDateString()]);
    }
}
