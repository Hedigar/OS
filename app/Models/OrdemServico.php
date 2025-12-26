<?php

namespace App\Models;

use App\Core\Model;
use App\Models\Cliente;
use App\Models\StatusOS;

class OrdemServico extends Model
{
    protected $table = 'ordens_servico';
    
    // Métodos específicos para Ordem de Serviço podem ser adicionados aqui

    /**
     * Retorna uma OS com dados do cliente e status.
     * @param int $id ID da Ordem de Serviço
     * @return array|null
     */
    public function findWithDetails(int $id): ?array
    {
        $sql = "SELECT os.*, c.nome_completo as cliente_nome, s.nome as status_nome, s.cor as status_cor
                FROM {$this->table} os
                JOIN clientes c ON os.cliente_id = c.id
                JOIN status_os s ON os.status_atual_id = s.id
                WHERE os.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Retorna todas as OS com dados do cliente e status.
     * @return array
     */
    public function getAllWithDetails(): array
    {
        $sql = "SELECT os.*, c.nome_completo as cliente_nome, s.nome as status_nome, s.cor as status_cor
                FROM {$this->table} os
                JOIN clientes c ON os.cliente_id = c.id
                JOIN status_os s ON os.status_atual_id = s.id
                ORDER BY os.id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Calcula e atualiza os totais da OS.
     * @param int $osId ID da Ordem de Serviço
     * @param array $itens Itens da OS
     * @return bool
     */
    public function updateTotals(int $osId, array $itens): bool
    {
        $totalProdutos = 0.00;
        $totalServicos = 0.00;

        foreach ($itens as $item) {
            if ($item['tipo'] === 'produto') {
                $totalProdutos += $item['valor_total'];
            } elseif ($item['tipo'] === 'servico') {
                $totalServicos += $item['valor_total'];
            }
        }

        $totalOS = $totalProdutos + $totalServicos;

        $sql = "UPDATE {$this->table} SET
                valor_total_produtos = :vtp,
                valor_total_servicos = :vts,
                valor_total_os = :vto
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'vtp' => $totalProdutos,
            'vts' => $totalServicos,
            'vto' => $totalOS,
            'id' => $osId
        ]);
    }

    /**
     * Retorna todas as OS de um cliente.
     * @param int $clienteId ID do Cliente
     * @return array
     */
    public function findByClienteId(int $clienteId): array
    {
        $sql = "SELECT os.*, s.nome as status_nome, s.cor as status_cor
                FROM {$this->table} os
                JOIN status_os s ON os.status_atual_id = s.id
                WHERE os.cliente_id = :cliente_id
                ORDER BY os.id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['cliente_id' => $clienteId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
