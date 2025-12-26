<?php

namespace App\Models;

use App\Core\Model;

class Cliente extends Model
{
    protected $table = 'clientes';

    // Métodos específicos para Cliente podem ser adicionados aqui

    public function getTable()
    {
        return $this->table;
    }

    public function getConnection()
    {
        return $this->db;
    }

    /**
     * Busca clientes por nome ou documento.
     * @param string $termo
     * @return array
     */
    public function buscarPorTermo(string $termo): array
    {
        $termo = "%" . $termo . "%";
        $sql = "SELECT id, nome_completo, documento FROM {$this->table} WHERE nome_completo LIKE :termo OR documento LIKE :termo";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':termo', $termo);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
