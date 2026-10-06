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
        $hoje = date('Y-m-d');
        $dataInicioMes = date('Y-m-01');
        $dataFimMes = date('Y-m-t');
        
        $inicioSemana = date('Y-m-d', strtotime('monday this week'));
        $fimSemana = date('Y-m-d', strtotime('sunday this week'));

        // 1. Ordens de Serviço (OS)
        $stmtAbertas = $db->query("SELECT COUNT(*) as total FROM ordens_servico WHERE status_atual_id NOT IN (5, 6) AND ativo = 1");
        $totalAbertas = (int)($stmtAbertas->fetch(\PDO::FETCH_ASSOC)['total'] ?? 0);

        $stmtPagPendentesOS = $db->query("SELECT COUNT(*) as total FROM ordens_servico WHERE status_pagamento = 'pendente' AND ativo = 1 AND valor_total_os > 0 AND status_atual_id != 6");
        $totalPagPendentesOS = (int)($stmtPagPendentesOS->fetch(\PDO::FETCH_ASSOC)['total'] ?? 0);

        $stmtPagParciaisOS = $db->query("SELECT COUNT(*) as total FROM ordens_servico WHERE status_pagamento = 'parcial' AND ativo = 1 AND status_atual_id != 6");
        $totalPagParciaisOS = (int)($stmtPagParciaisOS->fetch(\PDO::FETCH_ASSOC)['total'] ?? 0);

        $stmtInconsistencias = $db->query("SELECT COUNT(*) as total FROM ordens_servico WHERE status_atual_id IN (5, 6) AND (laudo_tecnico IS NULL OR TRIM(laudo_tecnico) = '') AND ativo = 1");
        $totalInconsistencias = (int)($stmtInconsistencias->fetch(\PDO::FETCH_ASSOC)['total'] ?? 0);

        // 2. Atendimentos Externos
        $stmtExtPendentes = $db->query("SELECT COUNT(*) as total FROM atendimentos_externos ae 
                                        WHERE (SELECT COALESCE(SUM(valor_bruto), 0) FROM pagamentos_transacoes WHERE tipo_origem = 'atendimento' AND origem_id = ae.id AND ativo = 1) = 0
                                        AND (COALESCE(ae.valor_total, 0) + COALESCE(ae.valor_deslocamento, 0)) > 0");
        $totalExtPendentes = (int)($stmtExtPendentes->fetch(\PDO::FETCH_ASSOC)['total'] ?? 0);

        $stmtExtParciais = $db->query("SELECT COUNT(*) as total FROM atendimentos_externos ae 
                                       WHERE (SELECT COALESCE(SUM(valor_bruto), 0) FROM pagamentos_transacoes WHERE tipo_origem = 'atendimento' AND origem_id = ae.id AND ativo = 1) > 0
                                       AND (SELECT COALESCE(SUM(valor_bruto), 0) FROM pagamentos_transacoes WHERE tipo_origem = 'atendimento' AND origem_id = ae.id AND ativo = 1) < (COALESCE(ae.valor_total, 0) + COALESCE(ae.valor_deslocamento, 0))");
        $totalExtParciais = (int)($stmtExtParciais->fetch(\PDO::FETCH_ASSOC)['total'] ?? 0);

        // 3. CRM e Pós-Venda (Na Semana)
        $stmtCRMContatados = $db->prepare("SELECT COUNT(DISTINCT cliente_id) as total FROM cliente_interacoes WHERE tipo = 'crm' AND DATE(created_at) BETWEEN ? AND ?");
        $stmtCRMContatados->execute([$inicioSemana, $fimSemana]);
        $totalCRMContatados = (int)($stmtCRMContatados->fetch(\PDO::FETCH_ASSOC)['total'] ?? 0);

        // Pós-Venda: Usando o campo pos_venda_status na tabela ordens_servico
        // 0 = pendente, 1 = realizado
        // Para simplificar, vamos considerar OS finalizadas e entregues que entraram em período de pós-venda na semana
        $stmtPosVendaRealizados = $db->prepare("SELECT COUNT(*) as total FROM ordens_servico WHERE pos_venda_status = 1 AND DATE(updated_at) BETWEEN ? AND ? AND ativo = 1");
        $stmtPosVendaRealizados->execute([$inicioSemana, $fimSemana]);
        $totalPosVendaRealizados = (int)($stmtPosVendaRealizados->fetch(\PDO::FETCH_ASSOC)['total'] ?? 0);

        // Pós-Venda Pendentes: OS que estão no Dashboard como alerta de Pós-Venda
        $alertas = $this->getAlertas();
        $totalPosVendaPendentes = 0;
        foreach ($alertas as $alerta) {
            if (($alerta['tipo'] ?? '') === 'pos_venda') {
                $totalPosVendaPendentes++;
            }
        }

        // Métricas Legadas/Extras
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
        $stmtFinalizadas->execute([$dataInicioMes, $dataFimMes]);
        $dadosFinalizadas = $stmtFinalizadas->fetch(\PDO::FETCH_ASSOC) ?: [];
        $totalFinalizadas = (int)($dadosFinalizadas['total'] ?? 0);
        $valorFinalizadas = (float)($dadosFinalizadas['valor_total'] ?? 0);

        $stmtAtrasadas = $db->query("SELECT COUNT(*) as total FROM ordens_servico WHERE status_atual_id NOT IN (5, 6) AND ativo = 1 AND created_at < DATE_SUB(NOW(), INTERVAL 3 DAY)");
        $totalAtrasadas = (int)($stmtAtrasadas->fetch(\PDO::FETCH_ASSOC)['total'] ?? 0);

        $relatorioProducao = $this->financeService->calcularProduzido($dataInicioMes, $dataFimMes);
        $lucroMes = $relatorioProducao['totais']['lucro_previsto'];
        $faturamentoProducao = $relatorioProducao['totais']['valor_total'];

        $relatorioCaixa = $this->financeService->getVisaoCaixa($dataInicioMes, $dataFimMes);
        $faturamentoCaixa = $relatorioCaixa['entrada_bruta'];
        $lucroCaixa = $relatorioCaixa['resultado_final'];

        // Gráfico de Tendência (Últimos 7 dias)
        $trend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $stmtTrend = $db->prepare("SELECT COUNT(*) as total FROM ordens_servico WHERE DATE(created_at) = ? AND ativo = 1");
            $stmtTrend->execute([$date]);
            $count = (int)$stmtTrend->fetch(\PDO::FETCH_ASSOC)['total'];
            $trend[] = [
                'date' => date('d/m', strtotime($date)),
                'total' => $count
            ];
        }

        return [
            'total_abertas' => $totalAbertas,
            'total_pag_pendentes_os' => $totalPagPendentesOS,
            'total_pag_parciais_os' => $totalPagParciaisOS,
            'total_inconsistencias' => $totalInconsistencias,
            'total_ext_pendentes' => $totalExtPendentes,
            'total_ext_parciais' => $totalExtParciais,
            'total_crm_contatados' => $totalCRMContatados,
            'total_pos_venda_realizados' => $totalPosVendaRealizados,
            'total_pos_venda_pendentes' => $totalPosVendaPendentes,
            'total_finalizadas' => $totalFinalizadas,
            'valor_finalizadas' => $valorFinalizadas,
            'total_atrasadas' => $totalAtrasadas,
            'lucro_mes' => $lucroMes,
            'faturamento_producao' => $faturamentoProducao,
            'faturamento_caixa' => $faturamentoCaixa,
            'lucro_caixa' => $lucroCaixa,
            'trend' => $trend
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

    public function getTecnicoStats(): array
    {
        $db = $this->osModel->getConnection();

        // 1. OS sem laudo preenchido (não finalizadas e não canceladas)
        $stmtSemLaudo = $db->query("
            SELECT COUNT(*) as total 
            FROM ordens_servico 
            WHERE status_atual_id NOT IN (5, 6) 
              AND (laudo_tecnico IS NULL OR TRIM(laudo_tecnico) = '') 
              AND ativo = 1
        ");
        $totalSemLaudo = (int)($stmtSemLaudo->fetch(\PDO::FETCH_ASSOC)['total'] ?? 0);

        // 2. OS aberta a mais de 2 dias e sem atualização de status
        $stmtSemAtualizacao = $db->query("
            SELECT COUNT(*) as total 
            FROM ordens_servico os
            WHERE os.status_atual_id NOT IN (5, 6) 
              AND os.ativo = 1 
              AND os.created_at < DATE_SUB(NOW(), INTERVAL 2 DAY)
              AND COALESCE(
                  (SELECT MAX(h.created_at) FROM ordens_servico_status_historico h WHERE h.ordem_servico_id = os.id),
                  os.created_at
              ) < DATE_SUB(NOW(), INTERVAL 2 DAY)
        ");
        $totalSemAtualizacao = (int)($stmtSemAtualizacao->fetch(\PDO::FETCH_ASSOC)['total'] ?? 0);

        // 3. OS sem produto ou serviço adicionado (não finalizadas e não canceladas)
        $stmtSemItens = $db->query("
            SELECT COUNT(*) as total 
            FROM ordens_servico os 
            WHERE os.status_atual_id NOT IN (5, 6) 
              AND os.ativo = 1 
              AND NOT EXISTS (
                  SELECT 1 FROM itens_ordem_servico ios 
                  WHERE ios.ordem_servico_id = os.id 
                    AND ios.ativo = 1
              )
        ");
        $totalSemItens = (int)($stmtSemItens->fetch(\PDO::FETCH_ASSOC)['total'] ?? 0);

        // 4. OS paradas ou 'esquecidas' (não finalizadas, sem alteração há mais de 5 dias)
        $stmtEsquecidas = $db->query("
            SELECT 
                os.id, 
                os.created_at, 
                s.nome as status_nome, 
                s.cor as status_cor,
                c.nome_completo as cliente_nome,
                COALESCE(
                    (SELECT MAX(h.created_at) FROM ordens_servico_status_historico h WHERE h.ordem_servico_id = os.id),
                    os.created_at
                ) as ultima_atualizacao
            FROM ordens_servico os
            JOIN status_os s ON os.status_atual_id = s.id
            JOIN clientes c ON os.cliente_id = c.id
            WHERE os.status_atual_id NOT IN (5, 6) 
              AND os.ativo = 1 
              AND COALESCE(
                  (SELECT MAX(h.created_at) FROM ordens_servico_status_historico h WHERE h.ordem_servico_id = os.id),
                  os.created_at
              ) < DATE_SUB(NOW(), INTERVAL 5 DAY)
            ORDER BY ultima_atualizacao ASC
        ");
        $esquecidas = $stmtEsquecidas->fetchAll(\PDO::FETCH_ASSOC) ?: [];

        return [
            'total_sem_laudo' => $totalSemLaudo,
            'total_sem_atualizacao' => $totalSemAtualizacao,
            'total_sem_itens' => $totalSemItens,
            'esquecidas' => $esquecidas,
            'total_esquecidas' => count($esquecidas)
        ];
    }

    public function getCrmSemanaStats(): array
    {
        $db = $this->osModel->getConnection();
        
        $monday = date('Y-m-d', strtotime('monday this week'));
        
        $dias = [
            1 => ['nome' => 'Segunda-feira', 'data' => date('Y-m-d', strtotime($monday))],
            2 => ['nome' => 'Terça-feira', 'data' => date('Y-m-d', strtotime("$monday +1 day"))],
            3 => ['nome' => 'Quarta-feira', 'data' => date('Y-m-d', strtotime("$monday +2 days"))],
            4 => ['nome' => 'Quinta-feira', 'data' => date('Y-m-d', strtotime("$monday +3 days"))],
            5 => ['nome' => 'Sexta-feira', 'data' => date('Y-m-d', strtotime("$monday +4 days"))],
            6 => ['nome' => 'Sábado', 'data' => date('Y-m-d', strtotime("$monday +5 days"))]
        ];
        
        $res = [];
        $totalCrmGeral = 0;
        $totalPosVendaGeral = 0;
        
        foreach ($dias as $num => $info) {
            $stmt = $db->prepare("
                SELECT 
                    SUM(CASE WHEN tipo = 'crm' THEN 1 ELSE 0 END) as crm,
                    SUM(CASE WHEN tipo = 'pos_venda' THEN 1 ELSE 0 END) as pos_venda
                FROM cliente_interacoes 
                WHERE DATE(created_at) = ?
            ");
            $stmt->execute([$info['data']]);
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            $crm = (int)($row['crm'] ?? 0);
            $posVenda = (int)($row['pos_venda'] ?? 0);
            
            $res[] = [
                'dia_nome' => $info['nome'],
                'data_formatada' => date('d/m', strtotime($info['data'])),
                'crm' => $crm,
                'pos_venda' => $posVenda
            ];
            
            $totalCrmGeral += $crm;
            $totalPosVendaGeral += $posVenda;
        }
        
        return [
            'dias' => $res,
            'total_crm' => $totalCrmGeral,
            'total_pos_venda' => $totalPosVendaGeral
        ];
    }

    public function getOsParaCompra(): array
    {
        $db = $this->osModel->getConnection();
        $statusIds = [11,12,7,8,13]; // Comprar Peça, Aguardando Peça, Para POA, Para POA Autorizado, Em POA
        $placeholders = implode(',', array_fill(0, count($statusIds), '?'));
        $sql = "
            SELECT 
                os.id,
                os.status_pagamento,
                os.created_at,
                c.nome_completo as cliente_nome,
                s.nome as status_nome,
                s.id as status_id,
                COALESCE(
                    (SELECT MAX(h.created_at) 
                     FROM ordens_servico_status_historico h 
                     WHERE h.ordem_servico_id = os.id AND h.status_id = os.status_atual_id),
                    os.updated_at,
                    os.created_at
                ) as data_entrada_status,
                COALESCE((
                    SELECT GROUP_CONCAT(DISTINCT i.descricao SEPARATOR ', ')
                    FROM itens_ordem_servico i
                    WHERE i.ordem_servico_id = os.id AND i.ativo = 1 AND i.comprar_peca = 1
                ), '') as pecas,
                COALESCE((
                    SELECT GROUP_CONCAT(DISTINCT CONCAT(i.descricao, '||', COALESCE(i.link_fornecedor,'')) SEPARATOR ';;')
                    FROM itens_ordem_servico i
                    WHERE i.ordem_servico_id = os.id AND i.ativo = 1 AND i.comprar_peca = 1
                ), '') as pecas_links
            FROM ordens_servico os
            JOIN clientes c ON os.cliente_id = c.id
            JOIN status_os s ON os.status_atual_id = s.id
            WHERE os.status_atual_id IN ($placeholders) AND os.ativo = 1
            ORDER BY data_entrada_status ASC
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute($statusIds);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }
}
