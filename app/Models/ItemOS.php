<?php

namespace App\Models;

use App\Core\Model;

class ItemOS extends Model
{
    protected string $table = 'itens_ordem_servico';

    public function findByOsId(int $ordemServicoId): array
    {
        try {
            $sql = "SELECT *
                    FROM {$this->table}
                    WHERE ordem_servico_id = :ordem_servico_id AND ativo = 1
                    ORDER BY id ASC";

            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':ordem_servico_id', $ordemServicoId);
            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
        } catch (\PDOException $e) {
            error_log('Erro ao buscar itens da OS: ' . $e->getMessage());
            return [];
        }
    }
}
