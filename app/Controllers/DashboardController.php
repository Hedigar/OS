<?php

namespace App\Controllers;

use App\Core\Auth;

class DashboardController extends BaseController
{
    public function index()
    {
        $user = Auth::user();
        $osModel = new \App\Models\OrdemServico();
        
        $db = $osModel->getConnection();
        
        // OS Abertas (Toda OS que não está cancelada (6) ou finalizada (5))
        $sqlAbertas = "SELECT COUNT(*) as total FROM ordens_servico WHERE status_atual_id NOT IN (5, 6) AND ativo = 1";
        $stmtAbertas = $db->query($sqlAbertas);
        $totalAbertas = $stmtAbertas->fetch(\PDO::FETCH_ASSOC)['total'];

        // OS Finalizadas (Status 5) - Contabilizar o valor
        $sqlFinalizadas = "SELECT COUNT(*) as total, SUM(valor_total_os) as valor_total FROM ordens_servico WHERE status_atual_id = 5 AND ativo = 1";
        $stmtFinalizadas = $db->query($sqlFinalizadas);
        $dadosFinalizadas = $stmtFinalizadas->fetch(\PDO::FETCH_ASSOC);
        $totalFinalizadas = $dadosFinalizadas['total'];
        $valorFinalizadas = $dadosFinalizadas['valor_total'] ?? 0;

        // OS Atrasadas (Exemplo: Abertas há mais de 3 dias - ajuste conforme regra de negócio)
        $sqlAtrasadas = "SELECT COUNT(*) as total FROM ordens_servico WHERE status_atual_id NOT IN (5, 6) AND ativo = 1 AND created_at < DATE_SUB(NOW(), INTERVAL 3 DAY)";
        $stmtAtrasadas = $db->query($sqlAtrasadas);
        $totalAtrasadas = $stmtAtrasadas->fetch(\PDO::FETCH_ASSOC)['total'];

        // Buscar logs recentes para o fluxo de atividades
        $logModel = new \App\Models\Log();
        $atividades = $logModel->getRecentes(10);

        // Lucro do mês atual
        $sqlLucro = "SELECT 
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
                      AND YEAR(os.created_at) = YEAR(CURRENT_DATE())";
        $stmtLucro = $db->query($sqlLucro);
        $dadosLucro = $stmtLucro->fetch(\PDO::FETCH_ASSOC);
        $lucroMes = ($dadosLucro['total_bruto'] ?? 0) - ($dadosLucro['total_custo'] ?? 0);

        $alertas = $osModel->getAlertasDashboard();

        if (!Auth::isAdmin() && !Auth::isTecnico()) {
            $alertas = array_values(array_filter($alertas, function ($alerta) {
                return ($alerta['nivel'] ?? '') === 'todos';
            }));
        }

        $this->render('dashboard/index', [
            'title' => 'Dashboard',
            'user' => $user,
            'stats' => [
                'total_abertas' => $totalAbertas,
                'total_finalizadas' => $totalFinalizadas,
                'valor_finalizadas' => $valorFinalizadas,
                'total_atrasadas' => $totalAtrasadas,
                'lucro_mes' => $lucroMes
            ],
            'atividades' => $atividades,
            'alertas' => $alertas
        ]);
    }
}
