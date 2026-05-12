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
        
        // 1. Total Bruto, Produtos, Serviços, Taxa NF das OS
        $sqlOS = "SELECT 
                    COALESCE(SUM(valor_total_os), 0) as total_bruto_os,
                    COALESCE(SUM(valor_total_produtos), 0) as total_produtos_os,
                    COALESCE(SUM(valor_total_servicos), 0) as total_servicos_os,
                    COALESCE(SUM(valor_taxa_nf), 0) as total_taxa_nf_os
                FROM ordens_servico 
                WHERE status_atual_id IN (4, 5, 8, 10, 11, 12) AND ativo = 1 
                AND DATE(created_at) BETWEEN ? AND ?";
        $stmtOS = $db->prepare($sqlOS);
        $stmtOS->execute([$dataInicio, $dataFim]);
        $dadosOS = $stmtOS->fetch() ?: [];
        
        // 2. Custo das Peças das OS
        $sqlCustoOS = "SELECT COALESCE(SUM(i.quantidade * COALESCE(NULLIF(i.valor_custo, 0), NULLIF(i.custo, 0), 0)), 0) as total_custo_os
                     FROM itens_ordem_servico i 
                     JOIN ordens_servico o ON i.ordem_servico_id = o.id 
                     WHERE o.status_atual_id IN (4, 5, 8, 10, 11, 12) AND o.ativo = 1 AND i.ativo = 1
                     AND DATE(i.created_at) BETWEEN ? AND ?";
        $stmtCustoOS = $db->prepare($sqlCustoOS);
        $stmtCustoOS->execute([$dataInicio, $dataFim]);
        $custoOS = $stmtCustoOS->fetchColumn() ?: 0;
        
        // 3. Total Bruto e Taxa NF dos Atendimentos
        $sqlAtend = "SELECT 
                    COALESCE(SUM(COALESCE(a.valor_total, 0) + COALESCE(a.valor_deslocamento, 0)), 0) as total_bruto_atend,
                    COALESCE(SUM(a.valor_taxa_nf), 0) as total_taxa_nf_atend
                FROM atendimentos_externos a
                WHERE a.status = 'concluido' AND a.ativo = 1 
                AND DATE(a.created_at) BETWEEN ? AND ?";
        $stmtAtend = $db->prepare($sqlAtend);
        $stmtAtend->execute([$dataInicio, $dataFim]);
        $dadosAtend = $stmtAtend->fetch() ?: [];
        
        // 4. Total Produtos dos Atendimentos
        $sqlProdAtend = "SELECT COALESCE(SUM(i.quantidade * COALESCE(i.valor_unitario, 0)), 0) as total_produtos_atend
                     FROM itens_ordem_servico i 
                     JOIN atendimentos_externos ae ON i.atendimento_externo_id = ae.id
                     WHERE ae.status = 'concluido' AND ae.ativo = 1 AND i.ativo = 1
                     AND DATE(ae.created_at) BETWEEN ? AND ?";
        $stmtProdAtend = $db->prepare($sqlProdAtend);
        $stmtProdAtend->execute([$dataInicio, $dataFim]);
        $prodAtend = $stmtProdAtend->fetchColumn() ?: 0;
        
        // 5. Total Serviços dos Atendimentos
        $sqlServAtend = "SELECT COALESCE(SUM(i.quantidade * COALESCE(i.valor_mao_de_obra, 0)), 0) as total_servicos_atend
                     FROM itens_ordem_servico i 
                     JOIN atendimentos_externos ae ON i.atendimento_externo_id = ae.id
                     WHERE ae.status = 'concluido' AND ae.ativo = 1 AND i.ativo = 1
                     AND DATE(ae.created_at) BETWEEN ? AND ?";
        $stmtServAtend = $db->prepare($sqlServAtend);
        $stmtServAtend->execute([$dataInicio, $dataFim]);
        $servAtend = $stmtServAtend->fetchColumn() ?: 0;
        
        // 6. Custo das Peças dos Atendimentos
        $sqlCustoAtend = "SELECT COALESCE(SUM(i.quantidade * COALESCE(NULLIF(i.valor_custo, 0), NULLIF(i.custo, 0), 0)), 0) as total_custo_atend
                     FROM itens_ordem_servico i 
                     JOIN atendimentos_externos ae ON i.atendimento_externo_id = ae.id 
                     WHERE ae.status = 'concluido' AND ae.ativo = 1 AND i.ativo = 1
                     AND DATE(i.created_at) BETWEEN ? AND ?";
        $stmtCustoAtend = $db->prepare($sqlCustoAtend);
        $stmtCustoAtend->execute([$dataInicio, $dataFim]);
        $custoAtend = $stmtCustoAtend->fetchColumn() ?: 0;
        
        // Somar os valores
        return [
            'total_bruto' => ($dadosOS['total_bruto_os'] ?? 0) + ($dadosAtend['total_bruto_atend'] ?? 0),
            'total_produtos' => ($dadosOS['total_produtos_os'] ?? 0) + $prodAtend,
            'total_servicos' => ($dadosOS['total_servicos_os'] ?? 0) + $servAtend,
            'total_taxa_nf' => ($dadosOS['total_taxa_nf_os'] ?? 0) + ($dadosAtend['total_taxa_nf_atend'] ?? 0),
            'total_custo' => $custoOS + $custoAtend,
            'total_bruto_os' => $dadosOS['total_bruto_os'] ?? 0,
            'total_bruto_atend' => $dadosAtend['total_bruto_atend'] ?? 0
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
        return $this->obterCustosOS($dataInicio, $dataFim, 'updated_at');
    }

    public function custosPorOSCaixa(string $dataInicio, string $dataFim): array
    {
        return $this->obterCustosOS($dataInicio, $dataFim, 'updated_at');
    }

    private function obterCustosOS(string $dataInicio, string $dataFim, string $tipoData): array
    {
        $db = $this->osModel->getConnection();
        
        $whereData = "DATE(i.created_at) BETWEEN :start AND :end";

        $sql = "SELECT 
                    os.id as os_id,
                    c.nome_completo as cliente_nome,
                    SUM(i.quantidade * COALESCE(NULLIF(i.valor_custo, 0), NULLIF(i.custo, 0), 0)) as custo_total
                FROM ordens_servico os
                JOIN clientes c ON c.id = os.cliente_id
                JOIN itens_ordem_servico i ON i.ordem_servico_id = os.id AND i.ativo = 1
                WHERE os.status_atual_id = 5 
                  AND os.ativo = 1 
                  AND $whereData
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

    public function custosPorAtendimento(string $dataInicio, string $dataFim): array
    {
        return $this->obterCustosAtendimento($dataInicio, $dataFim, 'updated_at');
    }

    public function custosPorAtendimentoCaixa(string $dataInicio, string $dataFim): array
    {
        return $this->obterCustosAtendimento($dataInicio, $dataFim, 'updated_at');
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

    private function obterCustosAtendimento(string $dataInicio, string $dataFim, string $tipoData): array
    {
        $db = $this->osModel->getConnection();

        $whereData = "DATE(i.created_at) BETWEEN :start AND :end";

        $sql = "SELECT 
                    a.id as atendimento_id,
                    c.nome_completo as cliente_nome,
                    SUM(i.quantidade * COALESCE(NULLIF(i.valor_custo, 0), NULLIF(i.custo, 0), 0)) as custo_total
                FROM atendimentos_externos a
                JOIN clientes c ON c.id = a.cliente_id
                JOIN itens_ordem_servico i ON i.atendimento_externo_id = a.id AND i.ativo = 1
                WHERE a.ativo = 1 AND a.status = 'concluido'
                  AND $whereData
                GROUP BY a.id, c.nome_completo
                HAVING custo_total > 0
                ORDER BY a.id DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute(['start' => $dataInicio, 'end' => $dataFim]);
        $lista = $stmt->fetchAll() ?: [];

        if (empty($lista)) return [];

        $ids = array_map(static fn($r) => (int)$r['atendimento_id'], $lista);
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sqlItens = "SELECT atendimento_externo_id, descricao, tipo_item, quantidade, COALESCE(NULLIF(valor_custo, 0), NULLIF(custo, 0), 0) as valor_custo
                     FROM itens_ordem_servico
                     WHERE ativo = 1 AND atendimento_externo_id IN ($placeholders)
                     ORDER BY atendimento_externo_id ASC, id ASC";
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
            $map[(int)$row['atendimento_id']] = $row;
        }
        foreach ($itens as $it) {
            $atId = (int)$it['atendimento_externo_id'];
            if (isset($map[$atId])) {
                $map[$atId]['itens'][] = [
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
        $db = $this->osModel->getConnection();
        
        // 1. Receita Liquida (Pagamentos Reais - Taxas de Maquina)
        $sqlReceita = "SELECT SUM(valor_liquido) as total_liquido 
                       FROM pagamentos_transacoes 
                       WHERE ativo = 1 
                       AND DATE(created_at) BETWEEN :start AND :end";
        $stmtReceita = $db->prepare($sqlReceita);
        $stmtReceita->execute(['start' => $dataInicio, 'end' => $dataFim]);
        $receitaLiquida = (float)($stmtReceita->fetchColumn() ?: 0);

        // 2. Custos de OS (Pecas) - Baseado na data de criacao do ITEM para OSs finalizadas
        $sqlCustosOS = "SELECT SUM(i.quantidade * COALESCE(NULLIF(i.valor_custo, 0), NULLIF(i.custo, 0), 0))
                        FROM itens_ordem_servico i
                        JOIN ordens_servico o ON i.ordem_servico_id = o.id
                        WHERE i.ativo = 1
                          AND o.status_atual_id = 5 
                          AND o.ativo = 1
                          AND DATE(i.created_at) BETWEEN :start1 AND :end1";
        $stmtOS = $db->prepare($sqlCustosOS);
        $stmtOS->execute(['start1' => $dataInicio, 'end1' => $dataFim]);
        $custoOS = (float)($stmtOS->fetchColumn() ?: 0);

        // 3. Custos de Atendimentos (Pecas)
        $sqlCustosAt = "SELECT SUM(i.quantidade * COALESCE(NULLIF(i.valor_custo, 0), NULLIF(i.custo, 0), 0))
                        FROM itens_ordem_servico i
                        JOIN atendimentos_externos a ON i.atendimento_externo_id = a.id
                        WHERE i.ativo = 1
                          AND a.status = 'concluido'
                          AND a.ativo = 1
                          AND DATE(i.created_at) BETWEEN :start2 AND :end2";
        $stmtAt = $db->prepare($sqlCustosAt);
        $stmtAt->execute(['start2' => $dataInicio, 'end2' => $dataFim]);
        $custoAt = (float)($stmtAt->fetchColumn() ?: 0);

        // 4. Custos de Impostos (Nota Fiscal)
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

        $custoPecas = $custoOS + $custoAt;
        $totalDescontos = $custoPecas + $custoNF;

        return [
            'receita_liquida' => $receitaLiquida,
            'custo_os' => $custoOS,
            'custo_atendimentos' => $custoAt,
            'custo_pecas' => $custoPecas,
            'custo_nf' => $custoNF,
            'total_descontos' => $totalDescontos,
            'lucro_real' => $receitaLiquida - $totalDescontos
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
}
