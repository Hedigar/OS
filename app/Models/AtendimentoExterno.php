<?php

namespace App\Models;

use App\Core\Model;

class AtendimentoExterno extends Model
{
    protected string $table = 'atendimentos_externos';

    /**
     * Busca atendimentos externos por cliente.
     * @param int $clienteId
     * @return array
     */
    public function findByClienteId(int $clienteId): array
    {
        $sql = "SELECT ae.*, u.nome as tecnico_nome 
                FROM {$this->table} ae 
                LEFT JOIN usuarios u ON ae.usuario_id = u.id 
                WHERE ae.cliente_id = :cliente_id 
                ORDER BY ae.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['cliente_id' => $clienteId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Sobrescreve find() pois esta tabela não tem coluna 'ativo'.
     */
    public function find(int $id): mixed
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Sobrescreve o find para incluir dados relacionados.
     */
    public function findWithDetails(int $id): ?array
    {
        $sql = "SELECT ae.*, c.nome_completo as cliente_nome, c.documento as cliente_documento, 
                       c.telefone_principal as cliente_telefone, u.nome as tecnico_nome 
                FROM {$this->table} ae 
                JOIN clientes c ON ae.cliente_id = c.id 
                LEFT JOIN usuarios u ON ae.usuario_id = u.id 
                WHERE ae.id = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ?: null;
    }



    public function listarItens($atendimentoId)
{
    // Buscamos na tabela de itens, mas filtrando pelo atendimento externo
    $sql = "SELECT * FROM itens_ordem_servico 
            WHERE atendimento_externo_id = :id 
            AND ativo = 1";
    $stmt = $this->db->prepare($sql);
    $stmt->execute(['id' => $atendimentoId]);
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
}

    /**
     * Sobrescreve o delete pois esta tabela não tem coluna 'ativo'.
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
    
    /**
     * Sobrescreve o countAll pois esta tabela não tem coluna 'ativo'.
     */
    public function countAll(string $whereClause = '', array $params = []): int
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} ae JOIN clientes c ON ae.cliente_id = c.id";
        if (!empty($whereClause)) {
            $sql .= " WHERE " . $whereClause;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Sobrescreve o getPaginated pois esta tabela não tem coluna 'ativo'.
     */
    public function getPaginated(int $limit, int $offset, string $whereClause = '', array $params = []): array
    {
        $sql = "SELECT ae.*, c.nome_completo as cliente_nome 
                FROM {$this->table} ae 
                JOIN clientes c ON ae.cliente_id = c.id";
        
        if (!empty($whereClause)) {
            $sql .= " WHERE " . $whereClause;
        }
        
        $sql .= " ORDER BY ae.created_at DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Query Scope: Retorna atendimentos concluídos em um período.
     */
    public function concluidosNoPeriodo(string $dataInicio, string $dataFim): array
    {
        $sql = "SELECT 
                    ae.id,
                    (COALESCE(ae.valor_total, 0) + COALESCE(ae.valor_deslocamento, 0)) as valor_total,
                    ae.valor_taxa_nf,
                    ae.descricao_problema,
                    c.nome_completo as cliente,
                    DATE(COALESCE(MAX(pt.created_at), ae.created_at)) as data_finalizacao
                FROM {$this->table} ae
                JOIN clientes c ON ae.cliente_id = c.id
                LEFT JOIN pagamentos_transacoes pt ON pt.tipo_origem = 'atendimento' AND pt.origem_id = ae.id
                WHERE ae.ativo = 1 
                AND ae.status = 'concluido'
                AND (COALESCE(ae.valor_total, 0) + COALESCE(ae.valor_deslocamento, 0)) > 0
                GROUP BY ae.id, ae.created_at, ae.valor_total, ae.valor_deslocamento, ae.valor_taxa_nf, ae.descricao_problema, c.nome_completo
                HAVING DATE(COALESCE(MAX(pt.created_at), ae.created_at)) BETWEEN ? AND ?
                ORDER BY data_finalizacao DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$dataInicio, $dataFim]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Query Scope: Retorna atendimentos com valores pendentes.
     */
    public function comPendencias(): array
    {
        $sql = "SELECT 
                    ae.id,
                    (COALESCE(ae.valor_total, 0) + COALESCE(ae.valor_deslocamento, 0)) as valor_total,
                    ae.valor_taxa_nf,
                    ae.descricao_problema,
                    DATE(ae.created_at) as data,
                    c.nome_completo as cliente,
                    COALESCE((SELECT SUM(valor_bruto) FROM pagamentos_transacoes WHERE tipo_origem = 'atendimento' AND origem_id = ae.id AND ativo = 1), 0) as valor_pago
                FROM {$this->table} ae
                JOIN clientes c ON ae.cliente_id = c.id
                WHERE ae.ativo = 1 
                AND (COALESCE(ae.valor_total, 0) + COALESCE(ae.valor_deslocamento, 0)) > 0
                HAVING valor_pago < valor_total OR valor_pago = 0
                ORDER BY ae.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }

    public function updateTotals(int $atendimentoId, array $itens): bool
    {
        $totalProdutos = 0.00;
        $totalServicos = 0.00;

        if (!empty($itens)) {
            foreach ($itens as $item) {
                $tipo = $item['tipo_item'] ?? $item['tipo'] ?? 'servico';
                $valorItem = ($item['quantidade'] * ($item['valor_unitario'] + ($item['valor_mao_de_obra'] ?? 0)));
                $descontoItem = (float)($item['desconto'] ?? 0);
                
                if ($tipo === 'produto') {
                    $totalProdutos += ($valorItem - $descontoItem);
                } else {
                    $totalServicos += ($valorItem - $descontoItem);
                }
            }
        }

        // Busca diretamente via SQL para evitar quaisquer problemas
        $sqlBusca = "SELECT valor_deslocamento, emitir_nf FROM {$this->table} WHERE id = :id";
        $stmtBusca = $this->db->prepare($sqlBusca);
        $stmtBusca->execute(['id' => $atendimentoId]);
        $atendimento = $stmtBusca->fetch(\PDO::FETCH_ASSOC);
        
        if (!$atendimento) {
            return false;
        }
        
        $valorDeslocamento = (float)($atendimento['valor_deslocamento'] ?? 0);
        $emitirNF = (int)($atendimento['emitir_nf'] ?? 0);
        $valorTotal = $totalProdutos + $totalServicos;

        // Cálculo da Taxa NF
        $valorTaxaNF = 0.00;
        if ($emitirNF) {
            $configModel = new \App\Models\ConfiguracaoGeral();
            $percProdutos = (float)$configModel->getValor('nf_porcentagem_produtos') ?: 3;
            $percServicos = (float)$configModel->getValor('nf_porcentagem_servicos') ?: 6;
            
            $valorTaxaNF = ($totalProdutos * ($percProdutos / 100)) + (($totalServicos + $valorDeslocamento) * ($percServicos / 100));
        }

        // Atualização diretamente via SQL
        $sqlUpdate = "UPDATE {$this->table} SET
                valor_total = :valor_total,
                valor_taxa_nf = :valor_taxa_nf
                WHERE id = :id";

        $stmtUpdate = $this->db->prepare($sqlUpdate);
        return $stmtUpdate->execute([
            'valor_total' => $valorTotal,
            'valor_taxa_nf' => $valorTaxaNF,
            'id' => $atendimentoId
        ]);
    }
}
