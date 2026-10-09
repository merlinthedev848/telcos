<?php

namespace App\Modules\Paywall\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaywallStateResource;
use App\Modules\Customers\Models\Customer;
use App\Modules\Paywall\Services\DnsPaywallService;
use Illuminate\Http\Request;

class PaywallApiController extends Controller
{
    public function apply(Request $request, Customer $customer, DnsPaywallService $paywall): PaywallStateResource
    {
        $validated = $request->validate([
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        $state = $paywall->apply(
            $customer,
            $validated['reason'] ?? 'manual_restriction',
        );

        return new PaywallStateResource($state->loadMissing('customer'));
    }

    public function restore(Customer $customer, DnsPaywallService $paywall): PaywallStateResource
    {
        $state = $paywall->restore($customer);

        return new PaywallStateResource($state->loadMissing('customer'));
    }
}
