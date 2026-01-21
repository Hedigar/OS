<?php

namespace App\Services;

use App\Services\Gateways\MercadoPagoGateway;
use App\Services\Gateways\StoneGateway;
use App\Services\Gateways\ModerninhaGateway;

class PaymentGatewayFactory
{
    public static function create(string $maquina, array $config): ?PaymentGateway
    {
        $maquina = trim($maquina);
        if ($maquina === '') return null;
        $providers = $config['maquinas'] ?? [];
        $providerCfg = null;
        foreach ($providers as $p) {
            if (($p['nome'] ?? '') === $maquina) { $providerCfg = $p; break; }
        }
        if (!$providerCfg) return null;
        $usarApi = (bool)($providerCfg['usar_api'] ?? false);
        if (!$usarApi) return null;
        if (stripos($maquina, 'mercado') !== false) {
            return new MercadoPagoGateway($providerCfg);
        }
        if (stripos($maquina, 'stone') !== false) {
            return new StoneGateway($providerCfg);
        }
        if (stripos($maquina, 'moderninha') !== false) {
            return new ModerninhaGateway($providerCfg);
        }
        return null;
    }
}
