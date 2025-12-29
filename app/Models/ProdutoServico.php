<?php

namespace App\Models;

use App\Core\Model;

class ProdutoServico extends Model
{
    protected $table = 'produtos_servicos';

    /**
     * Busca todos os itens ativos.
     */
    public function getAll()
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE ativo = 1 ORDER BY nome ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Busca um item pelo ID.
     */
    public function findById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id AND ativo = 1");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
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
