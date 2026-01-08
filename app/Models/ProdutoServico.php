<?php

namespace App\Models;

use App\Core\Model;

class ProdutoServico extends Model
{
    protected string $table = 'produtos_servicos';

    /**
     * Busca todos os itens ativos.
     */
    public function getAll(): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE ativo = 1 ORDER BY nome ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Busca um item pelo ID.
     */
    public function findById(int $id): mixed
    {
        return $this->find($id);
    }

    /**
     * Atualiza o valor de venda de todos os produtos com base em uma nova porcentagem.
     * @param float $porcentagem
     * @return bool
     */
    public function atualizarPrecosEmMassa(float $porcentagem)
    {
        $sql = "UPDATE {$this->table} 
                SET valor_venda = custo * (1 + (:porcentagem / 100)) 
                WHERE tipo = 'produto' AND custo > 0 AND ativo = 1";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['porcentagem' => $porcentagem]);
    }
}
