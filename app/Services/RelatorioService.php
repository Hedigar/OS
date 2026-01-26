<?php

namespace App\Services;

use App\Models\OrdemServico;

class RelatorioService
{
    private OrdemServico $osModel;

    public function __construct()
    {
        $this->osModel = new OrdemServico();
    }

    public function resumoFinanceiro(string $dataInicio, string $dataFim): array
    {
        $db = $this->osModel->getConnection();
        $sql = "SELECT 
                    SUM(valor_total_os) as total_bruto,
                    SUM(valor_total_produtos) as total_produtos,
                    SUM(valor_total_servicos) as total_servicos,
                    (SELECT SUM(i.quantidade * COALESCE(NULLIF(i.valor_custo, 0), NULLIF(i.custo, 0), 0)) 
                     FROM itens_ordem_servico i 
                     JOIN ordens_servico o ON i.ordem_servico_id = o.id 
                     WHERE o.status_atual_id = 5 AND o.ativo = 1 AND i.ativo = 1
                     AND DATE(o.created_at) BETWEEN :sub_start AND :sub_end) as total_custo
                FROM ordens_servico 
                WHERE status_atual_id = 5 AND ativo = 1 
                AND DATE(created_at) BETWEEN :start AND :end";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            'start' => $dataInicio,
            'end' => $dataFim,
            'sub_start' => $dataInicio,
            'sub_end' => $dataFim
        ]);
        return $stmt->fetch() ?: [];
    }

    public function osPorStatus(string $dataInicio, string $dataFim): array
    {
        $db = $this->osModel->getConnection();
        $sql = "SELECT s.nome, COUNT(os.id) as total, s.cor 
                FROM status_os s
                LEFT JOIN ordens_servico os ON os.status_atual_id = s.id AND os.ativo = 1 AND DATE(os.created_at) BETWEEN :start AND :end
                GROUP BY s.id
                ORDER BY s.ordem ASC";
        $stmt = $db->prepare($sql);
        $stmt->execute(['start' => $dataInicio, 'end' => $dataFim]);
        return $stmt->fetchAll() ?: [];
    }

    public function atendimentosResumo(string $dataInicio, string $dataFim): array
    {
        $db = $this->osModel->getConnection();
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(valor_total) as valor_total,
                    SUM(valor_deslocamento) as valor_deslocamento
                FROM atendimentos_externos 
                WHERE ativo = 1 AND DATE(created_at) BETWEEN :start AND :end";
        $stmt = $db->prepare($sql);
        $stmt->execute(['start' => $dataInicio, 'end' => $dataFim]);
        return $stmt->fetch() ?: [];
    }

    public function custosPorOS(string $dataInicio, string $dataFim): array
    {
        $db = $this->osModel->getConnection();
        $sql = "SELECT 
                    os.id as os_id,
                    c.nome_completo as cliente_nome,
                    SUM(i.quantidade * COALESCE(NULLIF(i.valor_custo, 0), NULLIF(i.custo, 0), 0)) as custo_total
                FROM ordens_servico os
                JOIN clientes c ON c.id = os.cliente_id
                JOIN itens_ordem_servico i ON i.ordem_servico_id = os.id AND i.ativo = 1
                WHERE os.status_atual_id = 5 
                  AND os.ativo = 1 
                  AND DATE(os.created_at) BETWEEN :start AND :end
                GROUP BY os.id, c.nome_completo
                HAVING custo_total > 0
                ORDER BY os.id DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute(['start' => $dataInicio, 'end' => $dataFim]);
        $lista = $stmt->fetchAll() ?: [];

        if (empty($lista)) return [];

        $ids = array_map(static fn($r) => (int)$r['os_id'], $lista);
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sqlItens = "SELECT ordem_servico_id, descricao, tipo_item, quantidade, COALESCE(NULLIF(valor_custo, 0), NULLIF(custo, 0), 0) as valor_custo
                     FROM itens_ordem_servico
                     WHERE ativo = 1 AND ordem_servico_id IN ($placeholders)
                     ORDER BY ordem_servico_id ASC, id ASC";
        $stmtItens = $db->prepare($sqlItens);
        foreach ($ids as $idx => $val) {
            $stmtItens->bindValue($idx + 1, $val, \PDO::PARAM_INT);
        }
        $stmtItens->execute();
        $itens = $stmtItens->fetchAll() ?: [];

        $map = [];
        foreach ($lista as $row) {
            $row['custo_total'] = (float)($row['custo_total'] ?? 0);
            $row['itens'] = [];
            $map[(int)$row['os_id']] = $row;
        }
        foreach ($itens as $it) {
            $osId = (int)$it['ordem_servico_id'];
            if (isset($map[$osId])) {
                $map[$osId]['itens'][] = [
                    'descricao' => $it['descricao'],
                    'tipo_item' => $it['tipo_item'],
                    'quantidade' => (float)$it['quantidade'],
                    'valor_custo' => (float)$it['valor_custo'],
                ];
            }
        }
        return array_values($map);
    }

    public function itensVendidos(string $dataInicio, string $dataFim): array
    {
        $db = $this->osModel->getConnection();
        $sql = "SELECT 
                    i.tipo_item,
                    i.descricao,
                    SUM(i.quantidade) AS quantidade_total
                FROM itens_ordem_servico i
                JOIN ordens_servico o ON i.ordem_servico_id = o.id
                WHERE o.status_atual_id = 5
                  AND o.ativo = 1
                  AND i.ativo = 1
                  AND DATE(o.created_at) BETWEEN :start AND :end
                GROUP BY i.tipo_item, i.descricao
                HAVING quantidade_total > 0
                ORDER BY quantidade_total DESC, i.descricao ASC";
        $stmt = $db->prepare($sql);
        $stmt->execute(['start' => $dataInicio, 'end' => $dataFim]);
        return $stmt->fetchAll() ?: [];
    }
}
