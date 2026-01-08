<?php

namespace App\Models;

use App\Core\Model;

class StatusHistorico extends Model
{
    protected string $table = 'ordens_servico_status_historico';

    public function findByOsId(int $osId): array
    {
        $sql = "SELECT h.*, s.nome as status_nome, s.cor as status_cor, u.nome as usuario_nome
                FROM {$this->table} h
                JOIN status_os s ON h.status_id = s.id
                LEFT JOIN usuarios u ON h.usuario_id = u.id
                WHERE h.ordem_servico_id = :os_id
                ORDER BY h.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['os_id' => $osId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }
}
