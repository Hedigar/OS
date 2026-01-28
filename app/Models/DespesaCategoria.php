<?php

namespace App\Models;

use App\Core\Model;

class DespesaCategoria extends Model
{
    protected string $table = 'despesas_categorias';

    public function getAll(): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE ativo = 1 ORDER BY nome ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll() ?: [];
    }

    public function findByName(string $nome)
    {
        $sql = "SELECT * FROM {$this->table} WHERE nome = :nome AND ativo = 1 LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['nome' => $nome]);
        return $stmt->fetch();
    }
}
