<?php

namespace App\Models;

use App\Core\Model;

class ItemOS extends Model
{
    protected $table = 'itens_os';

    /**
     * Retorna todos os itens de uma Ordem de Serviço.
     * @param int $osId ID da Ordem de Serviço
     * @return array
     */
    public function findByOsId(int $osId)
    {
        $sql = "SELECT * FROM {$this->table} WHERE os_id = :os_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['os_id' => $osId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Deleta todos os itens de uma Ordem de Serviço.
     * @param int $osId ID da Ordem de Serviço
     * @return bool
     */
    public function deleteByOsId(int $osId): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE os_id = :os_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['os_id' => $osId]);
    }
}
