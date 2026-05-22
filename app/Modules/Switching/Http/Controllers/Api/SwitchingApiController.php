<?php

namespace App\Modules\Switching\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Customers\Models\Customer;
use App\Modules\Switching\Models\SwitchingWorkflow;
use App\Modules\Switching\Services\OtsSwitchingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SwitchingApiController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(SwitchingWorkflow::query()->with('customer')->latest()->paginate());
    }

    public function store(Request $request, OtsSwitchingService $switching): JsonResponse
    {
        $data = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'losing_provider' => ['nullable', 'string'],
            'gaining_provider' => ['nullable', 'string'],
            'requested_switch_date' => ['nullable', 'date'],
        ]);

        return response()->json($switching->start(Customer::query()->findOrFail($data['customer_id']), $data), 201);
    }

    public function show(SwitchingWorkflow $workflow): JsonResponse
    {
        return response()->json($workflow->load('customer'));
    }
}
