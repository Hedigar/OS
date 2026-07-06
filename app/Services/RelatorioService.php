<?php

namespace App\Services;

use App\Models\OrdemServico;
use App\Services\FinanceReportService;

class RelatorioService
{
    private OrdemServico $osModel;

    public function __construct()
    {
        $this->osModel = new OrdemServico();
    }

    public function resumoFinanceiro(string $dataInicio, string $dataFim): array
    {
        $financeService = new FinanceReportService();
        $relatorio = $financeService->relatorioFinanceiroCompleto($dataInicio, $dataFim);
        
        return [
            'total_bruto' => $relatorio['produzido']['totais']['valor_total'],
            'total_produtos' => 0, // Manter para compatibilidade, se precisar
            'total_servicos' => 0, // Manter para compatibilidade, se precisar
            'total_taxa_nf' => $relatorio['produzido']['totais']['taxas'],
            'total_custos' => $relatorio['produzido']['totais']['custos'],
            'total_bruto_os' => 0, // Manter para compatibilidade
            'total_bruto_atend' => 0, // Manter para compatibilidade
        ];
    }

    public function clientesNovos(string $dataInicio, string $dataFim): array
    {
        $db = $this->osModel->getConnection();

        $sqlTotal = "SELECT COUNT(*) AS total_novos
                     FROM clientes
                     WHERE DATE(created_at) BETWEEN :start AND :end";
        $stmtTotal = $db->prepare($sqlTotal);
        $stmtTotal->execute(['start' => $dataInicio, 'end' => $dataFim]);
        $totalNovos = (int)($stmtTotal->fetchColumn() ?: 0);

        $sqlQueVoltaram = "SELECT COUNT(DISTINCT os_nova.cliente_id) AS total_clientes_que_voltaram
                           FROM ordens_servico os_nova
                           WHERE DATE(os_nova.created_at) BETWEEN ? AND ?
                             AND os_nova.ativo = 1
                             AND EXISTS (
                                SELECT 1
                                FROM ordens_servico os_antiga
                                WHERE os_antiga.cliente_id = os_nova.cliente_id
                                  AND os_antiga.ativo = 1
                                  AND DATE(os_antiga.created_at) < ?
                           )";

        $stmtVoltaram = $db->prepare($sqlQueVoltaram);
        $stmtVoltaram->execute([
            $dataInicio,
            $dataFim,
            $dataInicio
        ]);
        $totalQueVoltaram = (int)($stmtVoltaram->fetchColumn() ?: 0);

        return [
            'novos' => $totalNovos,
            'clientes_que_voltaram' => $totalQueVoltaram
        ];
    }

    public function getNovosClientes(string $dataInicio, string $dataFim): array
    {
        $db = $this->osModel->getConnection();
        // Busca clientes novos e suas OSs no mesmo período, se houver
        $sql = "SELECT 
                    c.id, 
                    c.nome_completo, 
                    c.created_at,
                    os.id as os_id,
                    os.created_at as os_data,
                    os.defeito_relatado
                FROM clientes c
                LEFT JOIN ordens_servico os ON os.cliente_id = c.id AND os.ativo = 1 AND DATE(os.created_at) BETWEEN :start2 AND :end2
                WHERE DATE(c.created_at) BETWEEN :start AND :end
                ORDER BY c.created_at DESC, os.created_at DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            'start' => $dataInicio, 
            'end' => $dataFim,
            'start2' => $dataInicio,
            'end2' => $dataFim
        ]);
        $results = $stmt->fetchAll() ?: [];

        $clientes = [];
        foreach ($results as $row) {
            $id = $row['id'];
            if (!isset($clientes[$id])) {
                $clientes[$id] = [
                    'id' => $id,
                    'nome_completo' => $row['nome_completo'],
                    'created_at' => $row['created_at'],
                    'ordens' => []
                ];
            }
            if ($row['os_id']) {
                $clientes[$id]['ordens'][] = [
                    'id' => $row['os_id'],
                    'data' => $row['os_data'],
                    'defeito' => $row['defeito_relatado']
                ];
            }
        }
        return array_values($clientes);
    }

    public function getClientesQueVoltaram(string $dataInicio, string $dataFim): array
    {
        $db = $this->osModel->getConnection();
        $sql = "SELECT
                    c.id as cliente_id,
                    c.nome_completo,
                    os_nova.id as os_id,
                    os_nova.created_at as os_data,
                    os_nova.defeito_relatado
                FROM ordens_servico os_nova
                JOIN clientes c ON c.id = os_nova.cliente_id
                WHERE
                    DATE(os_nova.created_at) BETWEEN :start_date AND :end_date
                    AND os_nova.ativo = 1
                    AND EXISTS (
                        SELECT 1
                        FROM ordens_servico os_antiga
                        WHERE os_antiga.cliente_id = os_nova.cliente_id
                          AND os_antiga.ativo = 1
                          AND DATE(os_antiga.created_at) < :start_date_exists
                    )
                ORDER BY c.nome_completo, os_nova.created_at DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute([
            'start_date' => $dataInicio,
            'end_date' => $dataFim,
            'start_date_exists' => $dataInicio
        ]);
        $results = $stmt->fetchAll() ?: [];

        // Group by client
        $clientes = [];
        foreach ($results as $row) {
            $clienteId = $row['cliente_id'];
            if (!isset($clientes[$clienteId])) {
                $clientes[$clienteId] = [
                    'id' => $clienteId,
                    'nome_completo' => $row['nome_completo'],
                    'ordens' => []
                ];
            }
            $clientes[$clienteId]['ordens'][] = [
                'id' => $row['os_id'],
                'data' => $row['os_data'],
                'defeito' => $row['defeito_relatado']
            ];
        }

        return array_values($clientes);
    }

    public function osPorStatus(string $dataInicio, string $dataFim): array
    {
        $db = $this->osModel->getConnection();
        $sql = "SELECT s.nome, COUNT(os.id) as total, s.cor 
                FROM status_os s
                LEFT JOIN ordens_servico os ON os.status_atual_id = s.id AND os.ativo = 1 AND DATE(os.updated_at) BETWEEN :start AND :end
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
                    COUNT(*) AS total,
                    COALESCE(SUM(COALESCE(a.valor_deslocamento, 0) + COALESCE((
                        SELECT SUM((i.quantidade * (COALESCE(i.valor_unitario, 0) + COALESCE(i.valor_mao_de_obra, 0))) - COALESCE(i.desconto, 0))
                        FROM itens_ordem_servico i
                        WHERE i.atendimento_externo_id = a.id AND i.ativo = 1
                    ), 0)), 0) AS valor_total,
                    COALESCE(SUM(a.valor_deslocamento), 0) AS valor_deslocamento,
                    COALESCE(SUM(a.valor_taxa_nf), 0) AS total_taxa_nf,
                    COALESCE(SUM((
                        SELECT SUM(i2.quantidade * COALESCE(NULLIF(i2.valor_custo, 0), NULLIF(i2.custo, 0), 0))
                        FROM itens_ordem_servico i2
                        WHERE i2.atendimento_externo_id = a.id AND i2.ativo = 1
                    )), 0) AS custo_total,
                    COALESCE(SUM(
                        COALESCE((
                            SELECT SUM((i.quantidade * (COALESCE(i.valor_unitario, 0) + COALESCE(i.valor_mao_de_obra, 0))) - COALESCE(i.desconto, 0))
                            FROM itens_ordem_servico i
                            WHERE i.atendimento_externo_id = a.id AND i.ativo = 1
                        ), 0)
                        - COALESCE((
                            SELECT SUM(i2.quantidade * COALESCE(NULLIF(i2.valor_custo, 0), NULLIF(i2.custo, 0), 0))
                            FROM itens_ordem_servico i2
                            WHERE i2.atendimento_externo_id = a.id AND i2.ativo = 1
                        ), 0)
                        + COALESCE(a.valor_deslocamento, 0)
                        - COALESCE(a.valor_taxa_nf, 0)
                    ), 0) AS lucro_total
                FROM atendimentos_externos a
                WHERE a.status = 'concluido' AND DATE(a.updated_at) BETWEEN :start AND :end";
        $stmt = $db->prepare($sql);
        $stmt->execute(['start' => $dataInicio, 'end' => $dataFim]);
        return $stmt->fetch() ?: [];
    }

    public function custosPorOS(string $dataInicio, string $dataFim): array
    {
        $fluxoCaixaModel = new \App\Models\FluxoCaixa();
        $fluxoItens = $fluxoCaixaModel->getRelatorioPorPeriodo($dataInicio, $dataFim);
        
        $result = [];
        foreach ($fluxoItens as $item) {
            if ($item['tipo'] === 'custo' && $item['os_id']) {
                if (!isset($result[$item['os_id']])) {
                    $result[$item['os_id']] = [
                        'os_id' => $item['os_id'],
                        'cliente_nome' => $item['cliente_nome'] ?? '',
                        'custo_total' => 0,
                        'itens' => []
                    ];
                }
                $result[$item['os_id']]['custo_total'] += $item['valor'];
                $result[$item['os_id']]['itens'][] = [
                    'descricao' => $item['descricao_origem'] ?? 'Custo',
                    'tipo_item' => 'produto',
                    'quantidade' => 1,
                    'valor_custo' => $item['valor']
                ];
            }
        }
        
        return array_values($result);
    }

    public function custosPorOSCaixa(string $dataInicio, string $dataFim): array
    {
        return $this->custosPorOS($dataInicio, $dataFim);
    }

    public function custosPorAtendimento(string $dataInicio, string $dataFim): array
    {
        $fluxoCaixaModel = new \App\Models\FluxoCaixa();
        $fluxoItens = $fluxoCaixaModel->getRelatorioPorPeriodo($dataInicio, $dataFim);
        
        $result = [];
        foreach ($fluxoItens as $item) {
            if ($item['tipo'] === 'custo' && $item['atendimento_externo_id']) {
                if (!isset($result[$item['atendimento_externo_id']])) {
                    $result[$item['atendimento_externo_id']] = [
                        'atendimento_id' => $item['atendimento_externo_id'],
                        'cliente_nome' => $item['cliente_nome'] ?? '',
                        'custo_total' => 0,
                        'itens' => []
                    ];
                }
                $result[$item['atendimento_externo_id']]['custo_total'] += $item['valor'];
                $result[$item['atendimento_externo_id']]['itens'][] = [
                    'descricao' => $item['descricao_origem'] ?? 'Custo',
                    'tipo_item' => 'produto',
                    'quantidade' => 1,
                    'valor_custo' => $item['valor']
                ];
            }
        }
        
        return array_values($result);
    }

    public function custosPorAtendimentoCaixa(string $dataInicio, string $dataFim): array
    {
        return $this->custosPorAtendimento($dataInicio, $dataFim);
    }

    public function nfsPorOSCaixa(string $dataInicio, string $dataFim): array
    {
        $db = $this->osModel->getConnection();
        $sql = "SELECT 
                    os.id as os_id,
                    c.nome_completo as cliente_nome,
                    os.valor_total_os,
                    os.valor_taxa_nf
                FROM ordens_servico os
                JOIN clientes c ON c.id = os.cliente_id
                WHERE os.ativo = 1 AND os.status_atual_id = 5 AND os.emitir_nf = 1
                  AND DATE(COALESCE(os.updated_at, os.created_at)) BETWEEN :start AND :end
                  AND os.valor_taxa_nf > 0
                ORDER BY os.id DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute(['start' => $dataInicio, 'end' => $dataFim]);
        return $stmt->fetchAll() ?: [];
    }

    public function nfsPorAtendimentoCaixa(string $dataInicio, string $dataFim): array
    {
        $db = $this->osModel->getConnection();
        $sql = "SELECT 
                    a.id as atendimento_id,
                    c.nome_completo as cliente_nome,
                    (COALESCE(a.valor_deslocamento, 0) + COALESCE((
                        SELECT SUM(i.valor_total)
                        FROM itens_ordem_servico i
                        WHERE i.atendimento_externo_id = a.id AND i.ativo = 1
                    ), 0)) as valor_total,
                    a.valor_taxa_nf
                FROM atendimentos_externos a
                JOIN clientes c ON c.id = a.cliente_id
                WHERE a.ativo = 1 AND a.status = 'concluido' AND a.emitir_nf = 1
                  AND DATE(COALESCE(a.updated_at, a.created_at)) BETWEEN :start AND :end
                  AND a.valor_taxa_nf > 0
                ORDER BY a.id DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute(['start' => $dataInicio, 'end' => $dataFim]);
        return $stmt->fetchAll() ?: [];
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
                  AND DATE(i.created_at) BETWEEN :start AND :end
                GROUP BY i.tipo_item, i.descricao
                HAVING quantidade_total > 0
                ORDER BY quantidade_total DESC, i.descricao ASC";
        $stmt = $db->prepare($sql);
        $stmt->execute(['start' => $dataInicio, 'end' => $dataFim]);
        return $stmt->fetchAll() ?: [];
    }

    public function lucroReal(string $dataInicio, string $dataFim): array
    {
        $fluxoCaixaModel = new \App\Models\FluxoCaixa();
        $totais = $fluxoCaixaModel->getTotaisPorPeriodo($dataInicio, $dataFim);

        // 4. Custos de Impostos (Nota Fiscal)
        $db = $this->osModel->getConnection();
        $sqlTaxaNF = "SELECT 
                        (SELECT COALESCE(SUM(valor_taxa_nf), 0) FROM ordens_servico WHERE ativo = 1 AND status_atual_id = 5 AND DATE(COALESCE(updated_at, created_at)) BETWEEN :s1 AND :e1) +
                        (SELECT COALESCE(SUM(valor_taxa_nf), 0) FROM atendimentos_externos WHERE ativo = 1 AND status = 'concluido' AND DATE(COALESCE(updated_at, created_at)) BETWEEN :s2 AND :e2)
                      as total_nf";
        $stmtNF = $db->prepare($sqlTaxaNF);
        $stmtNF->execute([
            's1' => $dataInicio, 'e1' => $dataFim,
            's2' => $dataInicio, 'e2' => $dataFim
        ]);
        $custoNF = (float)($stmtNF->fetchColumn() ?: 0);

        $totalDescontos = $totais['custo'] + $custoNF;

        return [
            'receita_liquida' => $totais['entrada'],
            'custo_os' => $totais['custo'],
            'custo_atendimentos' => 0,
            'custo_pecas' => $totais['custo'],
            'custo_nf' => $custoNF,
            'total_descontos' => $totalDescontos,
            'lucro_real' => $totais['entrada'] - $totalDescontos
        ];
    }

    public function resumoCRM(string $dataInicio, string $dataFim): array
    {
        $db = $this->osModel->getConnection();

        $sqlResumo = "SELECT 
                        COUNT(*) as total_interacoes,
                        COUNT(DISTINCT cliente_id) as total_clientes_contactados,
                        SUM(CASE WHEN tipo = 'pos_venda' THEN 1 ELSE 0 END) as total_pos_venda,
                        SUM(CASE WHEN tipo = 'campanha' THEN 1 ELSE 0 END) as total_campanhas,
                        SUM(CASE WHEN tipo = 'ligacao' THEN 1 ELSE 0 END) as total_ligacoes,
                        SUM(CASE WHEN resposta_cliente IS NOT NULL AND resposta_cliente != '' THEN 1 ELSE 0 END) as total_com_resposta,
                        AVG(nota_satisfacao) as media_nota,
                        COUNT(DISTINCT campanha_id) as total_campanhas_ativas
                    FROM cliente_interacoes
                    WHERE DATE(created_at) BETWEEN :start AND :end";
        $stmtResumo = $db->prepare($sqlResumo);
        $stmtResumo->execute(['start' => $dataInicio, 'end' => $dataFim]);
        $resumo = $stmtResumo->fetch() ?: [];

        $sqlPorDia = "SELECT 
                        DATE(created_at) as data,
                        COUNT(*) as total,
                        SUM(CASE WHEN tipo = 'pos_venda' THEN 1 ELSE 0 END) as pos_venda,
                        SUM(CASE WHEN tipo = 'campanha' THEN 1 ELSE 0 END) as campanha,
                        SUM(CASE WHEN resposta_cliente IS NOT NULL AND resposta_cliente != '' THEN 1 ELSE 0 END) as com_resposta
                    FROM cliente_interacoes
                    WHERE DATE(created_at) BETWEEN :start AND :end
                    GROUP BY DATE(created_at)
                    ORDER BY data DESC";
        $stmtPorDia = $db->prepare($sqlPorDia);
        $stmtPorDia->execute(['start' => $dataInicio, 'end' => $dataFim]);
        $porDia = $stmtPorDia->fetchAll() ?: [];

        $sqlPorUsuario = "SELECT 
                            u.nome as usuario_nome,
                            COUNT(*) as total_interacoes,
                            SUM(CASE WHEN tipo = 'pos_venda' THEN 1 ELSE 0 END) as total_pos_venda,
                            SUM(CASE WHEN tipo = 'campanha' THEN 1 ELSE 0 END) as total_campanhas,
                            SUM(CASE WHEN resposta_cliente IS NOT NULL AND resposta_cliente != '' THEN 1 ELSE 0 END) as total_com_resposta,
                            AVG(nota_satisfacao) as media_nota
                        FROM cliente_interacoes ci
                        LEFT JOIN usuarios u ON ci.usuario_id = u.id
                        WHERE DATE(ci.created_at) BETWEEN :start AND :end
                        GROUP BY u.id, u.nome
                        ORDER BY total_interacoes DESC";
        $stmtPorUsuario = $db->prepare($sqlPorUsuario);
        $stmtPorUsuario->execute(['start' => $dataInicio, 'end' => $dataFim]);
        $porUsuario = $stmtPorUsuario->fetchAll() ?: [];

        $sqlDetalhes = "SELECT 
                            ci.id,
                            ci.tipo,
                            ci.canal,
                            ci.assunto,
                            ci.descricao,
                            ci.resposta_cliente,
                            ci.nota_satisfacao,
                            ci.created_at,
                            ci.ordem_servico_id,
                            c.nome_completo as cliente_nome,
                            c.telefone_principal,
                            u.nome as usuario_nome,
                            cc.nome as campanha_nome
                        FROM cliente_interacoes ci
                        LEFT JOIN clientes c ON ci.cliente_id = c.id
                        LEFT JOIN usuarios u ON ci.usuario_id = u.id
                        LEFT JOIN crm_campanhas cc ON ci.campanha_id = cc.id
                        WHERE DATE(ci.created_at) BETWEEN :start AND :end
                        ORDER BY ci.created_at DESC";
        $stmtDetalhes = $db->prepare($sqlDetalhes);
        $stmtDetalhes->execute(['start' => $dataInicio, 'end' => $dataFim]);
        $detalhes = $stmtDetalhes->fetchAll() ?: [];

        $sqlPosVendaOS = "SELECT 
                            os.id as os_id,
                            os.pos_venda_status,
                            os.pos_venda_nota,
                            os.pos_venda_data,
                            c.nome_completo as cliente_nome,
                            os.created_at as os_data
                        FROM ordens_servico os
                        LEFT JOIN clientes c ON os.cliente_id = c.id
                        WHERE os.ativo = 1 
                        AND os.pos_venda_status = 1
                        AND DATE(os.pos_venda_data) BETWEEN :start AND :end
                        ORDER BY os.pos_venda_data DESC";
        $stmtPosVendaOS = $db->prepare($sqlPosVendaOS);
        $stmtPosVendaOS->execute(['start' => $dataInicio, 'end' => $dataFim]);
        $posVendaOS = $stmtPosVendaOS->fetchAll() ?: [];

        $sqlCampanhas = "SELECT 
                            cc.id,
                            cc.nome,
                            cc.mensagem_padrao,
                            cc.status,
                            cc.created_at,
                            u.nome as usuario_nome,
                            COUNT(ci.id) as total_enviados
                        FROM crm_campanhas cc
                        LEFT JOIN usuarios u ON cc.usuario_id = u.id
                        LEFT JOIN cliente_interacoes ci ON cc.id = ci.campanha_id
                        WHERE DATE(cc.created_at) BETWEEN :start AND :end OR cc.status = 'ativa'
                        GROUP BY cc.id
                        ORDER BY cc.created_at DESC";
        $stmtCampanhas = $db->prepare($sqlCampanhas);
        $stmtCampanhas->execute(['start' => $dataInicio, 'end' => $dataFim]);
        $campanhas = $stmtCampanhas->fetchAll() ?: [];

        return [
            'resumo' => $resumo,
            'por_dia' => $porDia,
            'por_usuario' => $porUsuario,
            'detalhes' => $detalhes,
            'pos_venda_os' => $posVendaOS,
            'campanhas' => $campanhas
        ];
    }

    public function relatorioFinanceiroDetalhado(string $dataInicio, string $dataFim): array
    {
        $financeService = new FinanceReportService();
        return $financeService->relatorioFinanceiroCompleto($dataInicio, $dataFim);
    }


}
