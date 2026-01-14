<?php

namespace App\Models;

use App\Core\Model;

class OrdemServico extends Model
{
    protected string $table = 'ordens_servico';
    
    /**
     * Retorna uma OS com dados do cliente, status e equipamento.
     * @param int $id ID da Ordem de Serviço
     * @return array|null
     */
    public function findWithDetails(int $id): ?array
    {
        $sql = "SELECT os.*, 
                       c.nome_completo as cliente_nome, 
                       c.documento as cliente_documento,
                       c.telefone_principal as cliente_telefone,
                       s.nome as status_nome, 
                       s.cor as status_cor,
                       e.tipo as equipamento_tipo,
                       e.marca as equipamento_marca,
                       e.modelo as equipamento_modelo,
                       e.serial as equipamento_serial,
                       e.senha as equipamento_senha,
                       e.acessorios as equipamento_acessorios,
                       e.possui_fonte as equipamento_fonte,
                       e.sn_fonte as equipamento_sn_fonte
	                FROM {$this->table} os
	                JOIN clientes c ON os.cliente_id = c.id
	                JOIN status_os s ON os.status_atual_id = s.id
	                LEFT JOIN equipamentos e ON os.equipamento_id = e.id
                WHERE os.id = :id AND os.ativo = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Retorna todas as OS com dados do cliente e status.
     * @return array
     */
	    public function getAllWithDetails(string $search = ''): array
	    {
	        $sql = "SELECT os.*, c.nome_completo as cliente_nome, s.nome as status_nome, s.cor as status_cor,
	                       e.modelo as equipamento_modelo
	                FROM {$this->table} os
	                JOIN clientes c ON os.cliente_id = c.id
	                JOIN status_os s ON os.status_atual_id = s.id
	                LEFT JOIN equipamentos e ON os.equipamento_id = e.id
	                WHERE os.ativo = 1";
        
        $params = [];
        if (!empty($search)) {
            if (is_numeric($search)) {
                $sql .= " AND (os.id = :search_id OR c.nome_completo LIKE :search_nome)";
                $params['search_id'] = $search;
                $params['search_nome'] = "%{$search}%";
            } else {
                $sql .= " AND c.nome_completo LIKE :search_nome";
                $params['search_nome'] = "%{$search}%";
            }
        }

        $sql .= " ORDER BY os.id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Calcula e atualiza os totais da OS.
     */
    public function updateTotals(int $osId, array $itens): bool
    {
        $totalProdutos = 0.00;
        $totalServicos = 0.00;

        foreach ($itens as $item) {
            $tipo = $item['tipo_item'] ?? $item['tipo'] ?? '';
            if ($tipo === 'produto') {
                $totalProdutos += $item['valor_total'];
            } elseif ($tipo === 'servico') {
                $totalServicos += $item['valor_total'];
            }
        }

        $totalDesconto = 0.00;
        foreach ($itens as $item) {
            $totalDesconto += (float)($item['desconto'] ?? 0);
        }

        $totalOS = $totalProdutos + $totalServicos;

        $sql = "UPDATE {$this->table} SET
                valor_total_produtos = :vtp,
                valor_total_servicos = :vts,
                valor_total_os = :vto,
                valor_desconto = :vd
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'vtp' => $totalProdutos,
            'vts' => $totalServicos,
            'vto' => $totalOS,
            'vd'  => $totalDesconto,
            'id'  => $osId
        ]);
    }

    /**
     * Retorna todas as OS de um cliente.
     */
    public function findByClienteId(int $clienteId): array
    {
        $sql = "SELECT os.*, s.nome as status_nome, s.cor as status_cor
                FROM {$this->table} os
                JOIN status_os s ON os.status_atual_id = s.id
                WHERE os.cliente_id = :cliente_id AND os.ativo = 1
                ORDER BY os.id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['cliente_id' => $clienteId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Retorna todas as OS de um equipamento.
     */
    public function findByEquipamentoId(int $equipamentoId): array
    {
        $sql = "SELECT os.*, s.nome as status_nome, s.cor as status_cor
                FROM {$this->table} os
                JOIN status_os s ON os.status_atual_id = s.id
                WHERE os.equipamento_id = :equipamento_id AND os.ativo = 1
                ORDER BY os.id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['equipamento_id' => $equipamentoId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
