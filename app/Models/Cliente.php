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
            $termoLike = "%" . $termo . "%";
            // Query com todos os campos necessários para o endereço
            $sql = "SELECT * 
                    FROM {$this->table} 
                    WHERE ativo = 1 
                    AND (nome_completo LIKE :termo 
                    OR documento LIKE :termo) 
                    LIMIT 10";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':termo', $termoLike);
            $stmt->execute();
            
            $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            return $results ?: [];
        } catch (\PDOException $e) {
            // Em caso de erro, retorna um array vazio ou loga o erro
            error_log("Erro na busca de cliente: " . $e->getMessage());
            return [];
        }
    }
}
