<?php

namespace App\Modules\Provisioning\Contracts;

class ProvisioningResult
{
    public function __construct(
        public readonly bool $ok,
        public readonly string $message,
        public readonly array $payload = [],
    ) {
    }
}
