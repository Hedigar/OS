<?php

namespace App\Services\Gateways;

use App\Services\PaymentGateway;

class MercadoPagoGateway implements PaymentGateway
{
    private array $cfg;

    public function __construct(array $cfg)
    {
        $this->cfg = $cfg;
    }

    public function charge(array $payload): array
    {
        $token = $this->cfg['access_token'] ?? ($_ENV['MP_ACCESS_TOKEN'] ?? '');
        if (!$token) return ['success' => false, 'error' => 'missing_token'];
        $forma = $payload['forma'] ?? '';
        $valor = (float)($payload['valor_bruto'] ?? 0);
        if ($valor <= 0) return ['success' => false, 'error' => 'invalid_amount'];
        if ($forma === 'pix') {
            $url = 'https://api.mercadopago.com/v1/payments';
            $body = [
                'transaction_amount' => $valor,
                'description' => $payload['descricao'] ?? 'Pagamento',
                'payment_method_id' => 'pix',
                'payer' => [
                    'email' => $payload['payer_email'] ?? 'cliente@example.com'
                ]
            ];
            $res = $this->request('POST', $url, [
                'Authorization: Bearer ' . $token,
                'Content-Type: application/json'
            ], json_encode($body));
            if (($res['code'] ?? 500) >= 200 && ($res['code'] ?? 500) < 300) {
                $data = json_decode($res['body'] ?? '{}', true) ?: [];
                return [
                    'success' => true,
                    'transaction_id' => $data['id'] ?? null,
                    'provider_fee_percent' => 0.0,
                    'provider_fee_amount' => 0.0
                ];
            }
            return ['success' => false, 'error' => 'api_error', 'status' => $res['code'] ?? 500];
        }
        return ['success' => false, 'error' => 'unsupported_forma'];
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

