<?php

namespace App\Models;

use App\Core\Model;

class CRMCampanha extends Model
{
    protected string $table = 'crm_campanhas';

    public function getAtivas(): array
    {
        $sql = "SELECT c.*, u.nome as usuario_nome,
                (SELECT COUNT(*) FROM cliente_interacoes ci WHERE ci.campanha_id = c.id) as total_enviados
                FROM {$this->table} c
                LEFT JOIN usuarios u ON c.usuario_id = u.id
                WHERE c.status != 'finalizada'
                ORDER BY c.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }

    public function findById(int $id): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($result) {
            $result['filtros'] = json_decode($result['filtros'], true);
        }
        return $result ?: null;
    }
}
