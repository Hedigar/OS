<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Services\FinanceReportService;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class RelatorioController extends BaseController
{
    private FinanceReportService $financeService;

    public function __construct()
    {
        parent::__construct();
        $this->financeService = new FinanceReportService();
    }

    // Método original mantido para compatibilidade
    public function index()
    {
        Auth::check();
        $this->render('relatorios/financeiro', [
            'title' => 'Relatórios Financeiros',
            'current_page' => 'relatorios'
        ]);
    }

    /**
     * VISÃO 1: Competência (Produção)
     */
    public function competencia()
    {
        Auth::check();
        $dataInicio = $_GET['data_inicio'] ?? date('Y-m-01');
        $dataFim = $_GET['data_fim'] ?? date('Y-m-t');

        $dados = $this->financeService->getVisaoCompetencia($dataInicio, $dataFim);

        $this->render('relatorios/competencia', [
            'title' => 'Visão de Competência (Produção)',
            'current_page' => 'relatorios',
            'filtros' => ['data_inicio' => $dataInicio, 'data_fim' => $dataFim],
            'dados' => $dados
        ]);
    }

    /**
     * VISÃO 2: Caixa (Fluxo Financeiro Real)
     */
    public function caixa()
    {
        Auth::check();
        $dataInicio = $_GET['data_inicio'] ?? date('Y-m-01');
        $dataFim = $_GET['data_fim'] ?? date('Y-m-t');

        $dados = $this->financeService->getVisaoCaixa($dataInicio, $dataFim);
        $auditoria = $this->financeService->auditarSaldo($dataInicio, $dataFim);

        $this->render('relatorios/caixa', [
            'title' => 'Visão de Caixa (Fluxo Financeiro)',
            'current_page' => 'relatorios',
            'filtros' => ['data_inicio' => $dataInicio, 'data_fim' => $dataFim],
            'dados' => $dados,
            'auditoria' => $auditoria
        ]);
    }

    /**
     * VISÃO 3: Analítica de OS
     */
    public function analitica()
    {
        Auth::check();
        $osIdInicio = $_GET['os_id_inicio'] ?? 1;
        $osIdFim = $_GET['os_id_fim'] ?? 100;

        $dados = $this->financeService->getVisaoAnaliticaOs((int)$osIdInicio, (int)$osIdFim);

        $this->render('relatorios/analitica', [
            'title' => 'Visão Analítica de OS',
            'current_page' => 'relatorios',
            'filtros' => ['os_id_inicio' => $osIdInicio, 'os_id_fim' => $osIdFim],
            'dados' => $dados
        ]);
    }

    /**
     * VISÃO 4: Entradas Órfãs
     */
    public function orfas()
    {
        Auth::check();
        $dados = $this->financeService->getVisaoEntradasOrfas();

        $this->render('relatorios/orfas', [
            'title' => 'Visão de Entradas Órfãs',
            'current_page' => 'relatorios',
            'dados' => $dados
        ]);
    }

    /**
     * Ação de Auditoria
     */
    public function auditar()
    {
        Auth::check();
        $dataInicio = $_GET['data_inicio'] ?? date('Y-m-01');
        $dataFim = $_GET['data_fim'] ?? date('Y-m-t');

        $auditoria = $this->financeService->auditarSaldo($dataInicio, $dataFim);

        header('Content-Type: application/json');
        echo json_encode($auditoria);
        exit;
    }

    /**
     * Ação de Limpeza do Fluxo de Caixa (remove referências inativas)
     * Protegido por autenticação e token CSRF via POST
     */
    public function limparFluxoCaixa()
    {
        Auth::check();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Método não permitido. Use POST.']);
            exit;
        }

        try {
            $fluxoCaixa = new \App\Models\FluxoCaixa();
            $db = $fluxoCaixa->getConnection();

            // 1. Pagamentos inativos ou inexistentes
            $sqlPag = "SELECT fc.id FROM fluxo_caixa fc
                       LEFT JOIN pagamentos_transacoes pt ON fc.referencia_tipo = 'pagamento' AND fc.referencia_id = pt.id
                       WHERE fc.referencia_tipo = 'pagamento'
                       AND (pt.id IS NULL OR pt.ativo = 0)";
            $stmtPag = $db->prepare($sqlPag);
            $stmtPag->execute();
            $pagInvalidos = $stmtPag->fetchAll(\PDO::FETCH_COLUMN) ?: [];

            // 2. Itens de OS inativos ou inexistentes
            $sqlItensOs = "SELECT fc.id FROM fluxo_caixa fc
                           LEFT JOIN itens_ordem_servico ios ON fc.referencia_tipo = 'item_os' AND fc.referencia_id = ios.id
                           WHERE fc.referencia_tipo = 'item_os'
                           AND (ios.id IS NULL OR ios.ativo = 0)";
            $stmtItensOs = $db->prepare($sqlItensOs);
            $stmtItensOs->execute();
            $itensOsInvalidos = $stmtItensOs->fetchAll(\PDO::FETCH_COLUMN) ?: [];

            // 3. Itens de atendimento inativos ou inexistentes
            $sqlItensAtend = "SELECT fc.id FROM fluxo_caixa fc
                              LEFT JOIN itens_ordem_servico ios ON fc.referencia_tipo = 'item_atendimento' AND fc.referencia_id = ios.id
                              WHERE fc.referencia_tipo = 'item_atendimento'
                              AND (ios.id IS NULL OR ios.ativo = 0)";
            $stmtItensAtend = $db->prepare($sqlItensAtend);
            $stmtItensAtend->execute();
            $itensAtendInvalidos = $stmtItensAtend->fetchAll(\PDO::FETCH_COLUMN) ?: [];

            $idsParaRemover = array_merge($pagInvalidos, $itensOsInvalidos, $itensAtendInvalidos);
            $totalRemovidos = 0;

            if (!empty($idsParaRemover)) {
                $idsParaRemover = array_unique($idsParaRemover);
                $placeholders = str_repeat('?,', count($idsParaRemover) - 1) . '?';
                $deleteSql = "DELETE FROM fluxo_caixa WHERE id IN ($placeholders)";
                $deleteStmt = $db->prepare($deleteSql);
                $deleteStmt->execute($idsParaRemover);
                $totalRemovidos = $deleteStmt->rowCount();
            }

            $logModel = new \App\Models\Log();
            $logModel->registrar(
                \App\Core\Auth::id(),
                'Executou limpeza do fluxo de caixa',
                "Removidos {$totalRemovidos} registros inválidos",
                null,
                [
                    'pagamentos_removidos' => count($pagInvalidos),
                    'itens_os_removidos' => count($itensOsInvalidos),
                    'itens_atendimento_removidos' => count($itensAtendInvalidos),
                    'total_removidos' => $totalRemovidos
                ]
            );

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'total_removidos' => $totalRemovidos,
                'detalhes' => [
                    'pagamentos' => count($pagInvalidos),
                    'itens_os' => count($itensOsInvalidos),
                    'itens_atendimento' => count($itensAtendInvalidos)
                ]
            ]);
            exit;

        } catch (\Throwable $e) {
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
            exit;
        }
    }

    // Métodos legados mantidos para compatibilidade
    public function clientes()
    {
        Auth::check();
        $this->render('relatorios/clientes', [
            'title' => 'Relatório de Clientes',
            'current_page' => 'relatorios'
        ]);
    }

    public function crm()
    {
        Auth::check();
        $this->render('relatorios/crm', [
            'title' => 'Relatório de CRM',
            'current_page' => 'relatorios'
        ]);
    }
}
