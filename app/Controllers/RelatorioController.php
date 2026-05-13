<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Models\OrdemServico;
use App\Models\AtendimentoExterno;
use App\Services\RelatorioService;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class RelatorioController extends BaseController
{
    private RelatorioService $service;

    public function __construct()
    {
        parent::__construct();
        $this->service = new RelatorioService();
    }

    public function index()
    {
        Auth::check();
        
        // Filtros de data (padrão: mês atual)
        $dataInicio = $_GET['data_inicio'] ?? date('Y-m-01');
        $dataFim = $_GET['data_fim'] ?? date('Y-m-t');

        $financeiro = $this->service->resumoFinanceiro($dataInicio, $dataFim);
        $statusResumo = $this->service->osPorStatus($dataInicio, $dataFim);
        $atendimentos = $this->service->atendimentosResumo($dataInicio, $dataFim);
        $custosPorOS = $this->service->custosPorOS($dataInicio, $dataFim);
        $custosPorAtendimento = $this->service->custosPorAtendimento($dataInicio, $dataFim);
        $custosOSCaixa = $this->service->custosPorOSCaixa($dataInicio, $dataFim);
        $custosAtendimentoCaixa = $this->service->custosPorAtendimentoCaixa($dataInicio, $dataFim);
        $nfsOSCaixa = $this->service->nfsPorOSCaixa($dataInicio, $dataFim);
        $nfsAtendimentoCaixa = $this->service->nfsPorAtendimentoCaixa($dataInicio, $dataFim);
        $lucroReal = $this->service->lucroReal($dataInicio, $dataFim);
        $clientesResumo = $this->service->clientesNovos($dataInicio, $dataFim);
        $novosClientes = $this->service->getNovosClientes($dataInicio, $dataFim);
        $clientesQueVoltaram = $this->service->getClientesQueVoltaram($dataInicio, $dataFim);
        $itensVendidos = $this->service->itensVendidos($dataInicio, $dataFim);

        $this->render('relatorios/index', [
            'title' => 'Relatórios e Resumos',
            'current_page' => 'relatorios',
            'filtros' => [
                'data_inicio' => $dataInicio,
                'data_fim' => $dataFim
            ],
            'financeiro' => $financeiro,
            'statusResumo' => $statusResumo,
            'atendimentos' => $atendimentos,
            'custosPorOS' => $custosPorOS,
            'custosPorAtendimento' => $custosPorAtendimento,
            'custosOSCaixa' => $custosOSCaixa,
            'custosAtendimentoCaixa' => $custosAtendimentoCaixa,
            'nfsOSCaixa' => $nfsOSCaixa,
            'nfsAtendimentoCaixa' => $nfsAtendimentoCaixa,
            'lucroReal' => $lucroReal,
            'clientesResumo' => $clientesResumo,
            'novosClientes' => $novosClientes,
            'clientesQueVoltaram' => $clientesQueVoltaram,
            'itensVendidos' => $itensVendidos
        ]);
    }

    public function clientes()
    {
        Auth::check();
        
        $dataInicio = $_GET['data_inicio'] ?? date('Y-m-01');
        $dataFim = $_GET['data_fim'] ?? date('Y-m-t');

        $novosClientes = $this->service->getNovosClientes($dataInicio, $dataFim);
        $clientesQueVoltaram = $this->service->getClientesQueVoltaram($dataInicio, $dataFim);

        $this->render('relatorios/clientes', [
            'title' => 'Relatório de Clientes',
            'current_page' => 'relatorios',
            'filtros' => [
                'data_inicio' => $dataInicio,
                'data_fim' => $dataFim
            ],
            'novosClientes' => $novosClientes,
            'clientesQueVoltaram' => $clientesQueVoltaram
        ]);
    }

    public function crm()
    {
        Auth::check();
        $this->requireAdmin();

        $dataInicio = $_GET['data_inicio'] ?? date('Y-m-01');
        $dataFim = $_GET['data_fim'] ?? date('Y-m-t');

        $dadosCRM = $this->service->resumoCRM($dataInicio, $dataFim);

        $this->render('relatorios/crm', [
            'title' => 'Relatório de CRM',
            'current_page' => 'relatorios',
            'filtros' => [
                'data_inicio' => $dataInicio,
                'data_fim' => $dataFim
            ],
            'dados' => $dadosCRM
        ]);
    }

    public function exportarProducao()
    {
        Auth::check();
        
        $dataInicio = $_GET['data_inicio'] ?? date('Y-m-01');
        $dataFim = $_GET['data_fim'] ?? date('Y-m-t');

        $dados = $this->service->relatorioFinanceiroDetalhado($dataInicio, $dataFim);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $linha = 1;
        $sheet->setCellValue('A' . $linha, 'LISTA DE PRODUÇÃO');
        $sheet->mergeCells('A' . $linha . ':I' . $linha);
        $sheet->getStyle('A' . $linha)->getFont()->setBold(true)->setSize(16)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A' . $linha)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('2E7D32');
        $linha++;
        $linha++;

        $sheet->setCellValue('A' . $linha, 'Tipo');
        $sheet->setCellValue('B' . $linha, 'Número');
        $sheet->setCellValue('C' . $linha, 'Data');
        $sheet->setCellValue('D' . $linha, 'Cliente');
        $sheet->setCellValue('E' . $linha, 'Descrição');
        $sheet->setCellValue('F' . $linha, 'Custos');
        $sheet->setCellValue('G' . $linha, 'Taxas');
        $sheet->setCellValue('H' . $linha, 'Valor Total');
        $sheet->setCellValue('I' . $linha, 'Lucro Previsto');
        $sheet->getStyle('A' . $linha . ':I' . $linha)->getFont()->setBold(true);
        $linha++;

        foreach ($dados['producao']['itens'] as $item) {
            $itens = array_map(function($i) { return $i['descricao']; }, $item['itens']);
            $descricaoCompleta = $item['descricao'] . (count($itens) > 0 ? ' | ' . implode(', ', $itens) : '');
            
            $sheet->setCellValue('A' . $linha, $item['tipo']);
            $sheet->setCellValue('B' . $linha, $item['numero']);
            $sheet->setCellValue('C' . $linha, $item['data']);
            $sheet->setCellValue('D' . $linha, $item['cliente']);
            $sheet->setCellValue('E' . $linha, $descricaoCompleta);
            $sheet->setCellValue('F' . $linha, $item['custos']);
            $sheet->setCellValue('G' . $linha, $item['taxa_nf'] ?? 0);
            $sheet->setCellValue('H' . $linha, $item['valor_total']);
            $sheet->setCellValue('I' . $linha, $item['lucro_previsto']);
            $linha++;
        }

        $sheet->setCellValue('E' . $linha, 'TOTAL:');
        $sheet->setCellValue('F' . $linha, $dados['producao']['totais']['custos']);
        $sheet->setCellValue('G' . $linha, $dados['producao']['totais']['taxas']);
        $sheet->setCellValue('H' . $linha, $dados['producao']['totais']['valor_total']);
        $sheet->setCellValue('I' . $linha, $dados['producao']['totais']['lucro_previsto']);
        $sheet->getStyle('E' . $linha . ':I' . $linha)->getFont()->setBold(true);

        foreach(range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="relatorio_producao_' . date('Ymd') . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function exportarCaixa()
    {
        Auth::check();
        
        $dataInicio = $_GET['data_inicio'] ?? date('Y-m-01');
        $dataFim = $_GET['data_fim'] ?? date('Y-m-t');

        $dados = $this->service->relatorioFinanceiroDetalhado($dataInicio, $dataFim);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $linha = 1;
        $sheet->setCellValue('A' . $linha, 'LISTA DE CAIXA');
        $sheet->mergeCells('A' . $linha . ':J' . $linha);
        $sheet->getStyle('A' . $linha)->getFont()->setBold(true)->setSize(16)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A' . $linha)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('1565C0');
        $linha++;
        $linha++;

        $sheet->setCellValue('A' . $linha, 'Data Pagamento');
        $sheet->setCellValue('B' . $linha, 'Cliente');
        $sheet->setCellValue('C' . $linha, 'Origem');
        $sheet->setCellValue('D' . $linha, 'Descrição');
        $sheet->setCellValue('E' . $linha, 'Custos');
        $sheet->setCellValue('F' . $linha, 'Taxa NF');
        $sheet->setCellValue('G' . $linha, 'Taxa Cartão');
        $sheet->setCellValue('H' . $linha, 'Valor Bruto');
        $sheet->setCellValue('I' . $linha, 'Valor Líquido');
        $sheet->setCellValue('J' . $linha, 'Lucro');
        $sheet->getStyle('A' . $linha . ':J' . $linha)->getFont()->setBold(true);
        $linha++;

        foreach ($dados['caixa']['itens'] as $item) {
            $sheet->setCellValue('A' . $linha, date('Y-m-d', strtotime($item['created_at'])));
            $sheet->setCellValue('B' . $linha, $item['cliente'] ?? '');
            $sheet->setCellValue('C' . $linha, strtoupper($item['tipo_origem']) . ' #' . $item['origem_id']);
            $sheet->setCellValue('D' . $linha, $item['descricao']);
            $sheet->setCellValue('E' . $linha, $item['custos']);
            $sheet->setCellValue('F' . $linha, $item['taxa_nf'] ?? 0);
            $sheet->setCellValue('G' . $linha, $item['taxa_cartao'] ?? 0);
            $sheet->setCellValue('H' . $linha, $item['valor_bruto']);
            $sheet->setCellValue('I' . $linha, $item['valor_liquido']);
            $sheet->setCellValue('J' . $linha, $item['lucro']);
            $linha++;
        }

        $sheet->setCellValue('D' . $linha, 'TOTAL:');
        $sheet->setCellValue('E' . $linha, $dados['caixa']['totais']['custos']);
        $sheet->setCellValue('F' . $linha, $dados['caixa']['totais']['taxas_nf']);
        $sheet->setCellValue('G' . $linha, $dados['caixa']['totais']['taxas_cartao']);
        $sheet->setCellValue('H' . $linha, $dados['caixa']['totais']['valor_bruto']);
        $sheet->setCellValue('J' . $linha, $dados['caixa']['totais']['lucro']);
        $sheet->getStyle('D' . $linha . ':J' . $linha)->getFont()->setBold(true);

        foreach(range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="relatorio_caixa_' . date('Ymd') . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function exportarPendencias()
    {
        Auth::check();
        
        $dataInicio = $_GET['data_inicio'] ?? date('Y-m-01');
        $dataFim = $_GET['data_fim'] ?? date('Y-m-t');

        $dados = $this->service->relatorioFinanceiroDetalhado($dataInicio, $dataFim);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $linha = 1;
        $sheet->setCellValue('A' . $linha, 'LISTA DE PENDÊNCIAS');
        $sheet->mergeCells('A' . $linha . ':I' . $linha);
        $sheet->getStyle('A' . $linha)->getFont()->setBold(true)->setSize(16)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A' . $linha)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('EF5350');
        $linha++;
        $linha++;

        $sheet->setCellValue('A' . $linha, 'Tipo');
        $sheet->setCellValue('B' . $linha, 'Número');
        $sheet->setCellValue('C' . $linha, 'Data');
        $sheet->setCellValue('D' . $linha, 'Cliente');
        $sheet->setCellValue('E' . $linha, 'Descrição');
        $sheet->setCellValue('F' . $linha, 'Custos');
        $sheet->setCellValue('G' . $linha, 'Valor Pago');
        $sheet->setCellValue('H' . $linha, 'Valor Total');
        $sheet->setCellValue('I' . $linha, 'Valor Pendente');
        $sheet->getStyle('A' . $linha . ':I' . $linha)->getFont()->setBold(true);
        $linha++;

        foreach ($dados['pendencias']['itens'] as $item) {
            $itens = array_map(function($i) { return $i['descricao']; }, $item['itens']);
            $descricaoCompleta = $item['descricao'] . (count($itens) > 0 ? ' | ' . implode(', ', $itens) : '');
            
            $sheet->setCellValue('A' . $linha, $item['tipo']);
            $sheet->setCellValue('B' . $linha, $item['numero']);
            $sheet->setCellValue('C' . $linha, $item['data']);
            $sheet->setCellValue('D' . $linha, $item['cliente']);
            $sheet->setCellValue('E' . $linha, $descricaoCompleta);
            $sheet->setCellValue('F' . $linha, $item['custos']);
            $sheet->setCellValue('G' . $linha, $item['valor_pago']);
            $sheet->setCellValue('H' . $linha, $item['valor_total']);
            $sheet->setCellValue('I' . $linha, $item['valor_pendente']);
            $linha++;
        }

        $sheet->setCellValue('E' . $linha, 'TOTAL:');
        $sheet->setCellValue('F' . $linha, $dados['pendencias']['totais']['custos']);
        $sheet->setCellValue('I' . $linha, $dados['pendencias']['totais']['valor_total']);
        $sheet->getStyle('E' . $linha . ':I' . $linha)->getFont()->setBold(true);

        foreach(range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="relatorio_pendencias_' . date('Ymd') . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
