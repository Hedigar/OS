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
                    (SELECT SUM(i.quantidade * i.custo) 
                     FROM itens_ordem_servico i 
                     JOIN ordens_servico o ON i.ordem_servico_id = o.id 
                     WHERE o.status_atual_id = 5 AND o.ativo = 1 
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
}
