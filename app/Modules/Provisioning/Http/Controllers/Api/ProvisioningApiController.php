<?php

namespace App\Modules\Provisioning\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ActivateProvisioningRequest;
use App\Modules\Customers\Models\Customer;
use App\Modules\Provisioning\Services\ProvisioningService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProvisioningApiController extends Controller
{
    public function activate(ActivateProvisioningRequest $request, Customer $customer, ProvisioningService $provisioning): JsonResponse
    {
        $result = $provisioning->activate(
            $customer,
            $request->validated('service_profile') ?? [],
        );

        return response()->json($result, $result->ok ? 200 : 422);
    }

    public function suspend(Request $request, Customer $customer, ProvisioningService $provisioning): JsonResponse
    {
        $validated = $request->validate([
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        $result = $provisioning->suspend(
            $customer,
            $validated['reason'] ?? 'manual_suspension',
        );

        return response()->json($result, $result->ok ? 200 : 422);
    }

    public function restore(Customer $customer, ProvisioningService $provisioning): JsonResponse
    {
        $result = $provisioning->restore($customer);

        return response()->json($result, $result->ok ? 200 : 422);
    }

    public function health(ProvisioningService $provisioning): JsonResponse
    {
        $result = $provisioning->health();

        return response()->json($result, $result->ok ? 200 : 503);
    }
}
