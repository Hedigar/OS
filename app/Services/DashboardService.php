<?php

namespace App\Services;

use App\Models\OrdemServico;
use App\Models\Log;

class DashboardService
{
    private OrdemServico $osModel;
    private Log $logModel;

    public function __construct()
    {
        $this->osModel = new OrdemServico();
        $this->logModel = new Log();
    }

    public function getStats(): array
    {
        $db = $this->osModel->getConnection();

        $stmtAbertas = $db->query("SELECT COUNT(*) as total FROM ordens_servico WHERE status_atual_id NOT IN (5, 6) AND ativo = 1");
        $totalAbertas = (int)($stmtAbertas->fetch(\PDO::FETCH_ASSOC)['total'] ?? 0);

        $stmtFinalizadas = $db->query("SELECT COUNT(*) as total, SUM(valor_total_os) as valor_total FROM ordens_servico WHERE status_atual_id = 5 AND ativo = 1");
        $dadosFinalizadas = $stmtFinalizadas->fetch(\PDO::FETCH_ASSOC) ?: [];
        $totalFinalizadas = (int)($dadosFinalizadas['total'] ?? 0);
        $valorFinalizadas = (float)($dadosFinalizadas['valor_total'] ?? 0);

        $stmtAtrasadas = $db->query("SELECT COUNT(*) as total FROM ordens_servico WHERE status_atual_id NOT IN (5, 6) AND ativo = 1 AND created_at < DATE_SUB(NOW(), INTERVAL 3 DAY)");
        $totalAtrasadas = (int)($stmtAtrasadas->fetch(\PDO::FETCH_ASSOC)['total'] ?? 0);

        $stmtLucro = $db->query("SELECT 
                        SUM(os.valor_total_os) as total_bruto,
                        (SELECT SUM(i.quantidade * i.custo) 
                         FROM itens_ordem_servico i 
                         JOIN ordens_servico o ON i.ordem_servico_id = o.id 
                         WHERE o.status_atual_id = 5 AND o.ativo = 1 
                         AND MONTH(o.created_at) = MONTH(CURRENT_DATE()) 
                         AND YEAR(o.created_at) = YEAR(CURRENT_DATE())) as total_custo
                      FROM ordens_servico os
                      WHERE os.status_atual_id = 5 AND os.ativo = 1 
                      AND MONTH(os.created_at) = MONTH(CURRENT_DATE()) 
                      AND YEAR(os.created_at) = YEAR(CURRENT_DATE())");
        $dadosLucro = $stmtLucro->fetch(\PDO::FETCH_ASSOC) ?: [];
        $lucroMes = (float)($dadosLucro['total_bruto'] ?? 0) - (float)($dadosLucro['total_custo'] ?? 0);

        return [
            'total_abertas' => $totalAbertas,
            'total_finalizadas' => $totalFinalizadas,
            'valor_finalizadas' => $valorFinalizadas,
            'total_atrasadas' => $totalAtrasadas,
            'lucro_mes' => $lucroMes
        ];
    }

    public function getRecentActivities(int $limit = 10): array
    {
        return $this->logModel->getRecentes($limit);
    }

    public function getAlertas(): array
    {
        return $this->osModel->getAlertasDashboard();
    }

    public function filterAlertasParaUsuario(array $alertas, bool $adminOuTecnico): array
    {
        if ($adminOuTecnico) return $alertas;
        return array_values(array_filter($alertas, fn($a) => ($a['nivel'] ?? '') === 'todos'));
    }
}
