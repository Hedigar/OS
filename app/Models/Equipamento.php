<?php

namespace App\Models;

use App\Core\Model;

class Equipamento extends Model
{
    protected string $table = 'equipamentos';

    /**
     * Busca todos os equipamentos de um cliente.
     */
    public function findByClienteId(int $clienteId): array
    {
        try {
            // Se o erro persistir, verifique se na sua tabela a coluna é 'cliente_id' ou 'ordem_servico_id'
            $sql = "SELECT * FROM {$this->table} WHERE cliente_id = :cliente_id AND ativo = 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['cliente_id' => $clienteId]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
        } catch (\PDOException $e) {
            return [];
        }
    }

    /**
     * Verifica se um equipamento já existe para o cliente.
     */
    public function findBySerial(int $clienteId, string $serial): ?array
    {
        if (empty($serial)) return null;
        
        try {
            $sql = "SELECT * FROM {$this->table} WHERE cliente_id = :cliente_id AND serial = :serial AND ativo = 1 LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'cliente_id' => $clienteId,
                'serial' => $serial
            ]);
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $result ?: null;
        } catch (\PDOException $e) {
            return null;
        }
    }
}
