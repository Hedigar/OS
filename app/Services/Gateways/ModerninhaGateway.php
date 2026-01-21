<?php

namespace App\Services\Gateways;

use App\Services\PaymentGateway;

class ModerninhaGateway implements PaymentGateway
{
    private array $cfg;

    public function __construct(array $cfg)
    {
        $this->cfg = $cfg;
    }

    public function charge(array $payload): array
    {
        $merchant = $this->cfg['merchant_id'] ?? '';
        $key = $this->cfg['api_key'] ?? '';
        if (!$merchant || !$key) {
            return ['success' => false, 'error' => 'missing_config'];
        }
        return ['success' => false, 'error' => 'unsupported_api'];
    }
}

