<?php

namespace App\Controllers;

use App\Models\PushSubscription;
use App\Core\Auth;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

class PushController extends BaseController
{
    public function subscribe()
    {
        header('Content-Type: application/json');
        
        // Aceita JSON
        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input || !isset($input['endpoint'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Dados inválidos']);
            return;
        }

        $endpoint = $input['endpoint'];
        $publicKey = $input['keys']['p256dh'] ?? null;
        $authToken = $input['keys']['auth'] ?? null;
        
        $userId = Auth::check() ? Auth::user()['id'] : null;

        $model = new PushSubscription();
        $result = $model->save($userId, $endpoint, $publicKey, $authToken);

        if ($result) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Erro ao salvar inscrição']);
        }
    }
    
    // Método de teste para enviar notificação (opcional, pode ser chamado via rota /push/test)
    public function testSend() 
    {
        if (!Auth::isAdmin()) {
            die('Acesso negado');
        }
        
        $model = new PushSubscription();
        $subs = $model->getAll();
        
        if (empty($subs)) {
            echo "Nenhuma inscrição encontrada.";
            return;
        }

        $auth = [
            'VAPID' => [
                'subject' => $_ENV['VAPID_SUBJECT'],
                'publicKey' => $_ENV['VAPID_PUBLIC_KEY'],
                'privateKey' => $_ENV['VAPID_PRIVATE_KEY'],
            ],
        ];

        try {
            $webPush = new WebPush($auth);

            foreach ($subs as $sub) {
                $webPush->queueNotification(
                    Subscription::create([
                        'endpoint' => $sub['endpoint'],
                        'publicKey' => $sub['public_key'],
                        'authToken' => $sub['auth_token'],
                        'contentEncoding' => $sub['content_encoding'],
                    ]),
                    json_encode([
                        'title' => 'Teste Web Push', 
                        'body' => 'Se você está vendo isso, as notificações estão funcionando!',
                        'url' => BASE_URL . 'dashboard'
                    ])
                );
            }

            foreach ($webPush->flush() as $report) {
                $endpoint = $report->getRequest()->getUri()->__toString();
                if ($report->isSuccess()) {
                    echo "[OK] Message sent to {$endpoint}<br>";
                } else {
                    echo "[ERR] Message failed to {$endpoint}: {$report->getReason()}<br>";
                    if ($report->isSubscriptionExpired()) {
                         $model->deleteByEndpoint($endpoint);
                         echo "Subscription expired and deleted.<br>";
                    }
                }
            }
        } catch (\Exception $e) {
            echo "Erro ao enviar: " . $e->getMessage();
        }
    }
}
