<?php

namespace App\Modules\Provisioning\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Customers\Models\Customer;
use App\Modules\Provisioning\Services\ProvisioningService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProvisioningApiController extends Controller
{
    public function activate(Request $request, Customer $customer, ProvisioningService $provisioning): JsonResponse
    {
        return response()->json($provisioning->activate($customer, $request->input('service_profile', [])));
    }
}
