<?php

namespace App\Modules\Switching\Http\Controllers\Api;

use App\Enums\SwitchingStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSwitchingWorkflowRequest;
use App\Http\Resources\SwitchingWorkflowResource;
use App\Modules\Customers\Models\Customer;
use App\Modules\Switching\Models\SwitchingWorkflow;
use App\Modules\Switching\Services\OtsSwitchingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class SwitchingApiController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $status = $request->string('status')->toString();

        $workflows = SwitchingWorkflow::query()
            ->with('customer')
            ->when($status, fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return SwitchingWorkflowResource::collection($workflows);
    }

    public function store(StoreSwitchingWorkflowRequest $request, OtsSwitchingService $switching): JsonResponse
    {
        $validated = $request->validated();
        $customer = Customer::query()->findOrFail($validated['customer_id']);

        $workflow = $switching->start($customer, $validated);

        return (new SwitchingWorkflowResource($workflow->loadMissing('customer')))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(SwitchingWorkflow $workflow): SwitchingWorkflowResource
    {
        return new SwitchingWorkflowResource($workflow->loadMissing('customer'));
    }

    public function advance(Request $request, SwitchingWorkflow $workflow, OtsSwitchingService $switching): SwitchingWorkflowResource
    {
        $validated = $request->validate([
            'status' => ['required', 'string', Rule::in(SwitchingStatus::values())],
        ]);

        $advanced = $switching->advance($workflow, $validated['status']);

        return new SwitchingWorkflowResource($advanced->loadMissing('customer'));
    }
}
