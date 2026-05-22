<?php

namespace App\Modules\Switching\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Switching\Models\SwitchingWorkflow;
use Illuminate\View\View;

class SwitchingController extends Controller
{
    public function __invoke(): View
    {
        return view('switching.workflows', [
            'workflows' => SwitchingWorkflow::query()->with('customer')->latest()->paginate(25),
        ]);
    }
}
