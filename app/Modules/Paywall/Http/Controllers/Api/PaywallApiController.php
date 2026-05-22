<?php

namespace App\Modules\Paywall\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Customers\Models\Customer;
use App\Modules\Paywall\Services\DnsPaywallService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaywallApiController extends Controller
{
    public function apply(Request $request, Customer $customer, DnsPaywallService $paywall): JsonResponse
    {
        return response()->json($paywall->apply($customer, $request->string('reason', 'manual_restriction')->toString()));
    }

    public function restore(Customer $customer, DnsPaywallService $paywall): JsonResponse
    {
        return response()->json($paywall->restore($customer));
    }
}
