<?php

namespace App\Services;

use App\Models\OrdemServico;
use App\Models\Log;

class DashboardService
{
    private OrdemServico $osModel;
    private Log $logModel;
    private FinanceReportService $financeService;

    public function __construct()
    {
        $this->osModel = new OrdemServico();
        $this->logModel = new Log();
        $this->financeService = new FinanceReportService();
    }

    public function getStats(): array
    {
        $db = $this->osModel->getConnection();
        $dataInicio = date('Y-m-01');
        $dataFim = date('Y-m-t');

        $stmtAbertas = $db->query("SELECT COUNT(*) as total FROM ordens_servico WHERE status_atual_id NOT IN (5, 6) AND ativo = 1");
        $totalAbertas = (int)($stmtAbertas->fetch(\PDO::FETCH_ASSOC)['total'] ?? 0);

        $stmtFinalizadas = $db->prepare("SELECT COUNT(*) as total, SUM(os.valor_total_os) as valor_total 
                                        FROM ordens_servico os
                                        LEFT JOIN (
                                            SELECT ordem_servico_id, MAX(created_at) as created_at
                                            FROM ordens_servico_status_historico
                                            WHERE status_id = 5
                                            GROUP BY ordem_servico_id
                                        ) h ON os.id = h.ordem_servico_id
                                        WHERE os.status_atual_id = 5 AND os.ativo = 1
                                        AND DATE(COALESCE(h.created_at, os.updated_at)) BETWEEN ? AND ?");
        $stmtFinalizadas->execute([$dataInicio, $dataFim]);
        $dadosFinalizadas = $stmtFinalizadas->fetch(\PDO::FETCH_ASSOC) ?: [];
        $totalFinalizadas = (int)($dadosFinalizadas['total'] ?? 0);
        $valorFinalizadas = (float)($dadosFinalizadas['valor_total'] ?? 0);

        $stmtAtrasadas = $db->query("SELECT COUNT(*) as total FROM ordens_servico WHERE status_atual_id NOT IN (5, 6) AND ativo = 1 AND created_at < DATE_SUB(NOW(), INTERVAL 3 DAY)");
        $totalAtrasadas = (int)($stmtAtrasadas->fetch(\PDO::FETCH_ASSOC)['total'] ?? 0);

        $relatorio = $this->financeService->calcularProduzido($dataInicio, $dataFim);
        $lucroMes = $relatorio['totais']['lucro_previsto'];

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
