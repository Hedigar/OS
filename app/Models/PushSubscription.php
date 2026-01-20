<?php

namespace App\Models;

use PDO;
use App\Core\Database;

class PushSubscription
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance();
    }

    public function save($userId, $endpoint, $publicKey, $authToken, $contentEncoding = 'aes128gcm')
    {
        // Limita o tamanho do endpoint para evitar erro de index, se necessário, mas TEXT aguenta bastante
        // Verifica se já existe pelo endpoint
        $stmt = $this->pdo->prepare("SELECT id FROM push_subscriptions WHERE endpoint = ? LIMIT 1");
        $stmt->execute([$endpoint]);
        $existing = $stmt->fetch();

        if ($existing) {
            $stmt = $this->pdo->prepare("UPDATE push_subscriptions SET user_id = ?, public_key = ?, auth_token = ?, content_encoding = ?, updated_at = NOW() WHERE id = ?");
            return $stmt->execute([$userId, $publicKey, $authToken, $contentEncoding, $existing['id']]);
        } else {
            $stmt = $this->pdo->prepare("INSERT INTO push_subscriptions (user_id, endpoint, public_key, auth_token, content_encoding) VALUES (?, ?, ?, ?, ?)");
            return $stmt->execute([$userId, $endpoint, $publicKey, $authToken, $contentEncoding]);
        }
    }

    public function getAll()
    {
        $stmt = $this->pdo->query("SELECT * FROM push_subscriptions");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function deleteByEndpoint($endpoint)
    {
        $stmt = $this->pdo->prepare("DELETE FROM push_subscriptions WHERE endpoint = ?");
        return $stmt->execute([$endpoint]);
    }
}
