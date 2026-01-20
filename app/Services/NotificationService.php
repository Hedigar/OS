<?php

namespace App\Services;

use App\Models\PushSubscription;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

class NotificationService
{
    public static function sendToAll($title, $body, $url = '/')
    {
        // Garante que as chaves existam
        if (empty($_ENV['VAPID_PUBLIC_KEY']) || empty($_ENV['VAPID_PRIVATE_KEY'])) {
            return;
        }

        $model = new PushSubscription();
        $subs = $model->getAll();

        if (empty($subs)) return;

        $auth = [
            'VAPID' => [
                'subject' => $_ENV['VAPID_SUBJECT'] ?? 'mailto:admin@localhost',
                'publicKey' => $_ENV['VAPID_PUBLIC_KEY'],
                'privateKey' => $_ENV['VAPID_PRIVATE_KEY'],
            ],
        ];

        try {
            $webPush = new WebPush($auth);

            foreach ($subs as $sub) {
                // Validar dados da subscription
                if (empty($sub['endpoint'])) continue;

                $webPush->queueNotification(
                    Subscription::create([
                        'endpoint' => $sub['endpoint'],
                        'publicKey' => $sub['public_key'],
                        'authToken' => $sub['auth_token'],
                        'contentEncoding' => $sub['content_encoding'] ?? 'aes128gcm',
                    ]),
                    json_encode([
                        'title' => $title,
                        'body' => $body,
                        'url' => $url
                    ])
                );
            }

            foreach ($webPush->flush() as $report) {
                if (!$report->isSuccess() && $report->isSubscriptionExpired()) {
                     $model->deleteByEndpoint($report->getRequest()->getUri()->__toString());
                }
            }
        } catch (\Exception $e) {
            error_log("Erro ao enviar push notification: " . $e->getMessage());
        }
    }
}
