<?php

namespace App\Services\Gateways;

use App\Services\PaymentGateway;

class StoneGateway implements PaymentGateway
{
    private array $cfg;

    public function __construct(array $cfg)
    {
        $this->cfg = $cfg;
    }

    public function charge(array $payload): array
    {
        $endpoint = $this->cfg['endpoint'] ?? '';
        $key = $this->cfg['api_key'] ?? ($_ENV['STONE_API_KEY'] ?? '');
        if (!$endpoint || !$key) {
            return ['success' => false, 'error' => 'missing_config'];
        }
        $valor = (float)($payload['valor_bruto'] ?? 0);
        if ($valor <= 0) return ['success' => false, 'error' => 'invalid_amount'];
        $res = $this->request('POST', $endpoint, [
            'Authorization: Bearer ' . $key,
            'Content-Type: application/json'
        ], json_encode([
            'amount' => $valor,
            'installments' => (int)($payload['parcelas'] ?? 1),
            'brand' => $payload['bandeira'] ?? null,
            'method' => $payload['forma'] ?? null,
            'description' => $payload['descricao'] ?? 'Pagamento'
        ]));
        if (($res['code'] ?? 500) >= 200 && ($res['code'] ?? 500) < 300) {
            $data = json_decode($res['body'] ?? '{}', true) ?: [];
            $feePercent = (float)($data['fee_percent'] ?? 0.0);
            $feeAmount = (float)($data['fee_amount'] ?? 0.0);
            return [
                'success' => true,
                'transaction_id' => $data['id'] ?? null,
                'provider_fee_percent' => $feePercent,
                'provider_fee_amount' => $feeAmount
            ];
        }
        return ['success' => false, 'error' => 'api_error', 'status' => $res['code'] ?? 500];
    }

    private function request(string $method, string $url, array $headers, ?string $body = null): array
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        if ($body !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        }
        $response = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return ['code' => $status, 'body' => $response];
    }
}
