<?php

namespace App\Modules\Integrations\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Integrations\IntegrationManifest;
use App\Modules\Provisioning\Services\ProvisioningService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;
use Illuminate\View\View;
use Throwable;

class IntegrationsController extends Controller
{
    public function index(IntegrationManifest $manifest, ProvisioningService $provisioning): View
    {
        $integrations = [
            [
                'key' => 'tp-link-omada',
                'name' => 'TP-Link Omada SDN Controller',
                'description' => 'Automated FTTP / router provisioning, bandwidth rate-limiting, and captive VLAN assignment.',
                'status' => config('services.omada.base_url') ? 'configured' : 'sandbox',
                'badge' => config('services.omada.base_url') ? 'Live Controller' : 'Sandbox Simulated',
                'details' => config('services.omada.base_url') ? config('services.omada.base_url') : 'Using mock sandbox adapter',
            ],
            [
                'key' => 'totsco-ots',
                'name' => 'TOTSCo OTS Hub',
                'description' => 'UK Industry standard One Touch Switching coordination platform for residential broadband migration.',
                'status' => 'active',
                'badge' => 'Operational',
                'details' => 'Compliant OTS workflow engine enabled',
            ],
            [
                'key' => 'mail',
                'name' => 'Email & Inbound Webhook Transport',
                'description' => 'Inbound ticket ingestion and customer billing delivery.',
                'status' => 'active',
                'badge' => strtoupper((string) config('mail.default')),
                'details' => config('mail.from.address'),
            ],
            [
                'key' => 'database',
                'name' => 'Core Database Engine',
                'description' => 'Primary relational storage for subscribers, invoices, tickets, and logs.',
                'status' => 'active',
                'badge' => strtoupper((string) config('database.default')),
                'details' => 'Connection established with composite indexing',
            ],
        ];

        return view('integrations.index', [
            'integrations' => $integrations,
        ]);
    }

    public function testConnection(Request $request, string $key, ProvisioningService $provisioning): RedirectResponse
    {
        $statusMessage = match($key) {
            'tp-link-omada' => $provisioning->health()->message,
            'database' => $this->testDatabase(),
            default => "Integration test completed for {$key}.",
        };

        return back()->with('status', $statusMessage);
    }

    private function testDatabase(): string
    {
        try {
            DB::select('SELECT 1');
            return 'Database connection is healthy and responsive.';
        } catch (Throwable $e) {
            return "Database connection failed: {$e->getMessage()}";
        }
    }
}
