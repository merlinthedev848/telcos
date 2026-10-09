<?php

namespace App\Modules\Provisioning\Adapters\Omada;

use App\Modules\Customers\Models\Customer;
use App\Modules\Provisioning\Contracts\ProvisioningAdapter;
use App\Modules\Provisioning\Contracts\ProvisioningResult;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class TpLinkOmadaAdapter implements ProvisioningAdapter
{
    private ?string $baseUrl;
    private ?string $siteId;
    private ?string $clientId;
    private ?string $clientSecret;

    public function __construct()
    {
        $this->baseUrl = config('services.omada.base_url');
        $this->siteId = config('services.omada.site_id');
        $this->clientId = config('services.omada.client_id');
        $this->clientSecret = config('services.omada.client_secret');
    }

    /**
     * Activate customer broadband / GPON / router profile on Omada controller.
     */
    public function activate(Customer $customer, array $serviceProfile): ProvisioningResult
    {
        if ($this->hasLiveConfiguration()) {
            return $this->executeLiveActivation($customer, $serviceProfile);
        }

        // High-fidelity sandbox / simulated provisioning
        $vlan = $serviceProfile['vlan_id'] ?? rand(100, 999);
        $down = $serviceProfile['download_speed'] ?? 1000;
        $up = $serviceProfile['upload_speed'] ?? 100;

        return new ProvisioningResult(true, "Successfully activated TP-Link Omada profile for {$customer->account_number}", [
            'mode' => 'sandbox',
            'customer' => $customer->account_number,
            'vlan_id' => $vlan,
            'bandwidth_profile' => [
                'download_mbps' => $down,
                'upload_mbps' => $up,
            ],
            'site_id' => $this->siteId ?? 'site_omada_default',
            'adapter' => 'tp-link-omada',
            'provisioned_at' => now()->toIso8601String(),
        ]);
    }

    /**
     * Suspend customer traffic via Omada dynamic rate limit or captive VLAN.
     */
    public function suspend(Customer $customer, string $reason): ProvisioningResult
    {
        if ($this->hasLiveConfiguration()) {
            return $this->executeLiveSuspension($customer, $reason);
        }

        return new ProvisioningResult(true, "Successfully suspended TP-Link Omada service for {$customer->account_number}", [
            'mode' => 'sandbox',
            'customer' => $customer->account_number,
            'reason' => $reason,
            'rate_limit' => '64kbps',
            'quarantine_vlan' => 999,
            'suspended_at' => now()->toIso8601String(),
        ]);
    }

    /**
     * Restore customer profile to normal operation.
     */
    public function restore(Customer $customer): ProvisioningResult
    {
        if ($this->hasLiveConfiguration()) {
            return $this->executeLiveRestore($customer);
        }

        return new ProvisioningResult(true, "Successfully restored TP-Link Omada service for {$customer->account_number}", [
            'mode' => 'sandbox',
            'customer' => $customer->account_number,
            'restored_at' => now()->toIso8601String(),
        ]);
    }

    /**
     * Test Omada Controller connectivity and health.
     */
    public function health(): ProvisioningResult
    {
        if (! $this->hasLiveConfiguration()) {
            return new ProvisioningResult(true, 'TP-Link Omada adapter active (Sandbox Mode - configured credentials not found)', [
                'mode' => 'sandbox',
                'adapter' => 'tp-link-omada',
                'status' => 'ready',
            ]);
        }

        try {
            $response = Http::timeout(5)->get("{$this->baseUrl}/api/v2/health");

            return new ProvisioningResult($response->successful(), 'Omada Controller Ping', [
                'mode' => 'live',
                'base_url' => $this->baseUrl,
                'status_code' => $response->status(),
            ]);
        } catch (Throwable $e) {
            Log::warning("Omada Controller health check failed: {$e->getMessage()}");

            return new ProvisioningResult(false, "Omada Controller connection error: {$e->getMessage()}", [
                'mode' => 'live',
                'base_url' => $this->baseUrl,
            ]);
        }
    }

    private function hasLiveConfiguration(): bool
    {
        return ! empty($this->baseUrl) && ! empty($this->siteId);
    }

    private function executeLiveActivation(Customer $customer, array $serviceProfile): ProvisioningResult
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders(['X-Omada-Site-Id' => $this->siteId])
                ->post("{$this->baseUrl}/api/v2/sites/{$this->siteId}/clients/bind", [
                    'account_number' => $customer->account_number,
                    'profile' => $serviceProfile,
                ]);

            if ($response->successful()) {
                return new ProvisioningResult(true, 'Live Omada activation succeeded', $response->json() ?? []);
            }

            return new ProvisioningResult(false, "Live Omada activation failed: HTTP {$response->status()}", [
                'response' => $response->body(),
            ]);
        } catch (Throwable $e) {
            Log::error("Omada live activation exception: {$e->getMessage()}");

            return new ProvisioningResult(false, "Live Omada activation exception: {$e->getMessage()}");
        }
    }

    private function executeLiveSuspension(Customer $customer, string $reason): ProvisioningResult
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders(['X-Omada-Site-Id' => $this->siteId])
                ->post("{$this->baseUrl}/api/v2/sites/{$this->siteId}/clients/suspend", [
                    'account_number' => $customer->account_number,
                    'reason' => $reason,
                ]);

            return new ProvisioningResult($response->successful(), 'Live Omada suspension dispatched', $response->json() ?? []);
        } catch (Throwable $e) {
            return new ProvisioningResult(false, "Live Omada suspension error: {$e->getMessage()}");
        }
    }

    private function executeLiveRestore(Customer $customer): ProvisioningResult
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders(['X-Omada-Site-Id' => $this->siteId])
                ->post("{$this->baseUrl}/api/v2/sites/{$this->siteId}/clients/restore", [
                    'account_number' => $customer->account_number,
                ]);

            return new ProvisioningResult($response->successful(), 'Live Omada restore dispatched', $response->json() ?? []);
        } catch (Throwable $e) {
            return new ProvisioningResult(false, "Live Omada restore error: {$e->getMessage()}");
        }
    }
}
