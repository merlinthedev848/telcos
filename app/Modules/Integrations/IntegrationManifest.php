<?php

namespace App\Modules\Integrations;

class IntegrationManifest
{
    public function supported(): array
    {
        return [
            'tp-link-omada' => ['module' => 'Provisioning', 'status' => 'skeleton'],
            'mailpit' => ['module' => 'Email', 'status' => 'local'],
            'meilisearch' => ['module' => 'Reports', 'status' => 'optional'],
            'payment-gateway' => ['module' => 'Billing', 'status' => 'planned'],
            'noc-monitoring' => ['module' => 'Future', 'status' => 'planned'],
            'gis' => ['module' => 'Future', 'status' => 'planned'],
            'field-service' => ['module' => 'Future', 'status' => 'planned'],
            'live-chat' => ['module' => 'Future', 'status' => 'planned'],
        ];
    }
}
