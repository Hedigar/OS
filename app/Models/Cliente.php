<?php

namespace App\Models;

use App\Core\Model;

class Cliente extends Model
{
    protected string $table = 'clientes';

    /**
     * Busca clientes por nome ou documento.
     * @param string $termo
     * @return array
     */
    public function buscarPorTermo(string $termo): array
    {
        try {
            $termo = trim($termo);
            if ($termo === '') return [];

            $limit = 10;
            $digits = preg_replace('/\D/', '', $termo);

            if ($digits !== '' && ctype_digit($digits)) {
                $sql = "SELECT * FROM {$this->table} 
                        WHERE ativo = 1 
                        AND REPLACE(REPLACE(REPLACE(documento, '.', ''), '-', ''), '/', '') LIKE :doc 
                        LIMIT :limit";
                $stmt = $this->db->prepare($sql);
                $stmt->bindValue(':doc', "%{$digits}%");
                $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
            } else {
                $sql = "SELECT * FROM {$this->table} 
                        WHERE ativo = 1 
                        AND nome_completo LIKE :nome 
                        LIMIT :limit";
                $stmt = $this->db->prepare($sql);
                $stmt->bindValue(':nome', "%" . $termo . "%");
                $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
            }

            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
        } catch (\PDOException $e) {
            error_log("Erro na busca de cliente: " . $e->getMessage());
            return [];
        }
    }
}
