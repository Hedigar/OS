<?php
/**
 * SCRIPT OFICIAL DE MIGRAÇÃO - FLUXO DE CAIXA (EXECUTA 1 ÚNICA VEZ)
 * 
 * Este script cria/popula a tabela fluxo_caixa com dados válidos:
 * - Pré-processa (passo 0): recalcula valor_taxa_nf para OS aprovadas e atendimentos concluídos
 *   com emitir_nf=1 (garante que o campo está preenchido mesmo se o usuário nunca abriu a tela)
 * - Custos de itens: apenas OS em status aprovados e Atendimentos Externos Concluídos
 *   Data do custo = data da PRIMEIRA aprovação da OS / conclusão do atendimento (competência)
 * - Custos de taxa NF: apenas OS/Atendimentos aprovados/concluídos com taxa_nf > 0
 * - Pagamentos: apenas transações ativas
 * 
 * Controle de idempotência: tabela `schema_migrations_fluxo`
 * — se o version_tag já existir, o script aborta automaticamente.
 * 
 * Versão: 2.2 — Blindagem de Competência + 1-shot + Pré-cálculo Taxa NF
 * Data: 2026-08-25
 */

// Força as credenciais para o banco de dados (funciona dentro e fora do container)
$_ENV['DB_HOST'] = '127.0.0.1';
$_ENV['DB_USERNAME'] = 'root';
$_ENV['DB_PASSWORD'] = 'root';
$_ENV['DB_DATABASE'] = 'os';

// 1. Carrega o Autoloader
require_once __DIR__ . '/../app/Core/Autoload.php';

use App\Models\FluxoCaixa;
use App\Models\OrdemServico;
use App\Models\AtendimentoExterno;
use App\Models\ItemOS;
use App\Models\ConfiguracaoGeral;

const MIGRATION_VERSION_TAG = 'fluxo_caixa_competencia_v2.2_20260825';

echo "=============================================\n";
echo " SCRIPT DE MIGRAÇÃO FLUXO DE CAIXA (1-shot)\n";
echo " Versão: " . MIGRATION_VERSION_TAG . "\n";
echo "=============================================\n\n";

// Inicializa os modelos
$fluxoCaixa   = new FluxoCaixa();
$osModel      = new OrdemServico();
$atendModel   = new AtendimentoExterno();
$itemModel    = new ItemOS();
$configModel  = new ConfiguracaoGeral();
$db           = $fluxoCaixa->getConnection();

// Status de OS considerados "aprovados" (mesmo array de FinanceReportService / Controller)
$statusAprovados = [4, 5, 8, 11, 12, 14, 15];

try {
    // --- Controle de execução ÚNICA ---
    $db->exec("CREATE TABLE IF NOT EXISTS schema_migrations_fluxo (
        version_tag VARCHAR(120) PRIMARY KEY,
        executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        obs VARCHAR(255) NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    $stmtCheck = $db->prepare("SELECT executed_at FROM schema_migrations_fluxo WHERE version_tag = ?");
    $stmtCheck->execute([MIGRATION_VERSION_TAG]);
    $executadoEm = $stmtCheck->fetchColumn();

    if ($executadoEm) {
        echo "⚠️  MIGRAÇÃO JÁ EXECUTADA ANTERIORMENTE\n";
        echo "   Versão.......: " . MIGRATION_VERSION_TAG . "\n";
        echo "   Executada em.: " . $executadoEm . "\n";
        echo "   Nenhuma ação foi tomada (proteção 1-shot).\n";
        echo "=============================================\n";
        exit(0);
    }

    // 0. PRÉ-PROCESSAMENTO: Recalcular taxa_nf de TODOS OS/Atendimentos com emitir_nf=1
    echo "[0/7] Pré-processamento: Recalculando taxa_nf de OS aprovadas e Atendimentos concluídos...\n";

    $percProdutos = (float)$configModel->getValor('nf_porcentagem_produtos') ?: 3;
    $percServicos = (float)$configModel->getValor('nf_porcentagem_servicos') ?: 6;
    echo "   Percentuais NF (cfg. geral): produtos={$percProdutos}%, serviços={$percServicos}%\n";

    // 0a) OS aprovadas com emitir_nf = 1
    $placeholders = implode(',', array_fill(0, count($statusAprovados), '?'));
    $sqlOsComNf = "SELECT os.id FROM ordens_servico os
                   WHERE os.ativo = 1 AND os.emitir_nf = 1 AND os.status_atual_id IN ($placeholders)";
    $stmtOsComNf = $db->prepare($sqlOsComNf);
    $stmtOsComNf->execute($statusAprovados);
    $osComNf = $stmtOsComNf->fetchAll(PDO::FETCH_COLUMN, 0);
    $countOsTaxa = 0;
    foreach ($osComNf as $osId) {
        $itens = $itemModel->findByOsId($osId);
        if ($osModel->updateTotals((int)$osId, $itens)) {
            $countOsTaxa++;
        }
    }
    echo "   ✅ OS com taxa NF recalculada: {$countOsTaxa}/" . count($osComNf) . "\n";

    // 0b) Atendimentos concluídos com emitir_nf = 1
    $sqlAtComNf = "SELECT ae.id FROM atendimentos_externos ae
                   WHERE ae.ativo = 1 AND ae.emitir_nf = 1 AND ae.status = 'concluido'";
    $stmtAtComNf = $db->query($sqlAtComNf);
    $atComNf = $stmtAtComNf->fetchAll(PDO::FETCH_COLUMN, 0);
    $countAtTaxa = 0;
    foreach ($atComNf as $atId) {
        $itens = $atendModel->listarItens((int)$atId);
        if ($atendModel->updateTotals((int)$atId, $itens)) {
            $countAtTaxa++;
        }
    }
    echo "   ✅ Atend. com taxa NF recalculada: {$countAtTaxa}/" . count($atComNf) . "\n\n";

    // 1. Limpa a tabela (ainda não foi aplicada esta versão)
    echo "[1/7] Limpando fluxo_caixa...\n";
    $stmt = $db->exec("TRUNCATE TABLE fluxo_caixa");
    echo "   ✅ Tabela fluxo_caixa limpa com sucesso!\n\n";

    // 2. Backfill de custos de itens (peças + serviços)
    echo "[2/7] Backfill de custos de itens (data = data da 1ª aprovação / conclusão)...\n";
    $sqlItens = "
        SELECT 
            ios.id, 
            ios.ordem_servico_id, 
            ios.atendimento_externo_id, 
            ios.quantidade, 
            COALESCE(NULLIF(ios.valor_custo, 0), NULLIF(ios.custo, 0)) as valor_custo,
            COALESCE(
                (SELECT DATE(MIN(h.created_at)) 
                 FROM ordens_servico_status_historico h 
                 WHERE h.ordem_servico_id = ios.ordem_servico_id 
                   AND h.status_id IN ($placeholders)),
                (SELECT DATE(COALESCE(ae_sub.updated_at, ae_sub.created_at)) 
                 FROM atendimentos_externos ae_sub WHERE ae_sub.id = ios.atendimento_externo_id AND ae_sub.status = 'concluido'),
                DATE(ios.created_at)
            ) as data_transacao
        FROM itens_ordem_servico ios
        LEFT JOIN ordens_servico os ON ios.ordem_servico_id = os.id
        LEFT JOIN atendimentos_externos ae ON ios.atendimento_externo_id = ae.id
        WHERE ios.ativo = 1
        AND (
            (ios.ordem_servico_id IS NOT NULL AND os.status_atual_id IN ($placeholders))
            OR 
            (ios.atendimento_externo_id IS NOT NULL AND ae.status = 'concluido')
        )
    ";
    $stmt = $db->prepare($sqlItens);
    $params = array_merge($statusAprovados, $statusAprovados);
    $stmt->execute($params);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $countCosts = 0;
    foreach ($items as $item) {
        $valorTotal = $item['quantidade'] * $item['valor_custo'];
        if ($valorTotal <= 0) continue;

        if ($item['ordem_servico_id']) {
            $fluxoCaixa->registrarCustoItemOs($item['id'], $item['ordem_servico_id'], $valorTotal, $item['data_transacao']);
        } elseif ($item['atendimento_externo_id']) {
            $fluxoCaixa->registrarCustoItemAtendimento($item['id'], $item['atendimento_externo_id'], $valorTotal, $item['data_transacao']);
        }
        $countCosts++;
    }
    echo "   ✅ Backfilled $countCosts custos de itens!\n\n";

    // 3. Backfill de custos de taxa NF (OS e Atendimentos)
    echo "[3/7] Backfill de custos de taxa NF (data = data da 1ª aprovação / conclusão)...\n";
    $countNf = 0;

    // 3a) NF de OS aprovadas
    $sqlOsNf = "
        SELECT 
            os.id,
            os.valor_taxa_nf,
            COALESCE(
                (SELECT DATE(MIN(h.created_at)) 
                 FROM ordens_servico_status_historico h 
                 WHERE h.ordem_servico_id = os.id 
                   AND h.status_id IN ($placeholders)),
                DATE(os.created_at)
            ) as data_competencia
        FROM ordens_servico os
        WHERE os.ativo = 1
          AND os.emitir_nf = 1
          AND os.status_atual_id IN ($placeholders)
          AND COALESCE(os.valor_taxa_nf, 0) > 0
    ";
    $stmtOsNf = $db->prepare($sqlOsNf);
    $paramsNf = array_merge($statusAprovados, $statusAprovados);
    $stmtOsNf->execute($paramsNf);
    foreach ($stmtOsNf->fetchAll(PDO::FETCH_ASSOC) as $nf) {
        if ($fluxoCaixa->registrarCustoTaxaNf('os', $nf['id'], (float)$nf['valor_taxa_nf'], $nf['data_competencia'])) {
            $countNf++;
        }
    }

    // 3b) NF de Atendimentos concluídos
    $sqlAtNf = "
        SELECT 
            ae.id,
            ae.valor_taxa_nf,
            DATE(COALESCE(ae.updated_at, ae.created_at)) as data_competencia
        FROM atendimentos_externos ae
        WHERE ae.ativo = 1
          AND ae.emitir_nf = 1
          AND ae.status = 'concluido'
          AND COALESCE(ae.valor_taxa_nf, 0) > 0
    ";
    $stmtAtNf = $db->query($sqlAtNf);
    foreach ($stmtAtNf->fetchAll(PDO::FETCH_ASSOC) as $nf) {
        if ($fluxoCaixa->registrarCustoTaxaNf('atendimento', $nf['id'], (float)$nf['valor_taxa_nf'], $nf['data_competencia'])) {
            $countNf++;
        }
    }
    echo "   ✅ Backfilled $countNf custos de taxa NF!\n\n";

    // 4. Backfill de pagamentos
    echo "[4/7] Backfill de pagamentos...\n";
    $stmt = $db->query("
        SELECT 
            id, 
            tipo_origem, 
            origem_id, 
            valor_bruto,
            DATE(created_at) as data_transacao
        FROM pagamentos_transacoes 
        WHERE ativo = 1
    ");
    $pagamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $countPayments = 0;
    foreach ($pagamentos as $pagamento) {
        $fluxoCaixa->registrarEntradaPagamento(
            $pagamento['id'],
            $pagamento['tipo_origem'],
            $pagamento['origem_id'],
            $pagamento['valor_bruto'],
            $pagamento['data_transacao']
        );
        $countPayments++;
    }
    echo "   ✅ Backfilled $countPayments pagamentos!\n\n";

    // 5. Resumo quantitativo por tipo de referência
    echo "[5/7] Resumo quantitativo por tipo de referência...\n";
    $stmtResumo = $db->query("SELECT referencia_tipo, tipo, COUNT(*) as qtd, SUM(valor) as total
                              FROM fluxo_caixa GROUP BY referencia_tipo, tipo ORDER BY tipo, referencia_tipo");
    foreach ($stmtResumo->fetchAll(PDO::FETCH_ASSOC) as $row) {
        echo "   {$row['tipo']} | {$row['referencia_tipo']}: {$row['qtd']} x R$ " . number_format((float)$row['total'], 2, ',', '.') . "\n";
    }
    echo "\n";

    // 6. Verificação final
    echo "[6/7] Verificação final dos totais...\n";
    $stmtCustos = $db->query("SELECT SUM(valor) as total, COUNT(*) as qtd FROM fluxo_caixa WHERE tipo = 'custo'");
    $resCustos = $stmtCustos->fetch(PDO::FETCH_ASSOC);
    $totalCustos = (float)$resCustos['total'];
    $qtdCustos = (int)$resCustos['qtd'];

    $stmtEntradas = $db->query("SELECT SUM(valor) as total, COUNT(*) as qtd FROM fluxo_caixa WHERE tipo = 'entrada'");
    $resEntradas = $stmtEntradas->fetch(PDO::FETCH_ASSOC);
    $totalEntradas = (float)$resEntradas['total'];
    $qtdEntradas = (int)$resEntradas['qtd'];

    echo "   Custos:    R$ " . number_format($totalCustos, 2, ',', '.') . "  ({$qtdCustos} lançamentos)\n";
    echo "   Entradas:  R$ " . number_format($totalEntradas, 2, ',', '.') . "  ({$qtdEntradas} lançamentos)\n";
    echo "   Saldo:     R$ " . number_format($totalEntradas - $totalCustos, 2, ',', '.') . "\n\n";

    // 7. Marca a migration como executada
    echo "[7/7] Marcando migration como executada (garante 1-shot)...\n";
    $stmtMarca = $db->prepare("INSERT INTO schema_migrations_fluxo (version_tag, obs) VALUES (?, ?)");
    $stmtMarca->execute([
        MIGRATION_VERSION_TAG,
        "Lançamentos: {$qtdCustos} custos + {$qtdEntradas} entradas. Saldo R$ " . number_format($totalEntradas - $totalCustos, 2, ',', '.')
    ]);
    echo "   ✅ Tag " . MIGRATION_VERSION_TAG . " gravada com sucesso!\n\n";

    echo "=============================================\n";
    echo "✅ MIGRAÇÃO CONCLUÍDA COM SUCESSO!\n";
    echo "=============================================\n";
    echo "📌 O script NÃO SERÁ EXECUTADO NOVAMENTE nesta base.\n";
    echo "   Para rodar de novo, execute: DELETE FROM schema_migrations_fluxo WHERE version_tag = '" . MIGRATION_VERSION_TAG . "';\n";

} catch (PDOException $e) {
    echo "\n=============================================\n";
    echo "❌ ERRO NA MIGRAÇÃO\n";
    echo "=============================================\n";
    echo "Mensagem: " . $e->getMessage() . "\n";
    if (method_exists($e, 'getTraceAsString')) {
        echo "Trace...:\n" . $e->getTraceAsString() . "\n";
    }
    echo "=============================================\n";
    exit(1);
} catch (Throwable $e) {
    echo "\n=============================================\n";
    echo "❌ ERRO GENÉRICO NA MIGRAÇÃO\n";
    echo "=============================================\n";
    echo "Mensagem: " . $e->getMessage() . "\n";
    echo "Arquivo.: " . $e->getFile() . " L" . $e->getLine() . "\n";
    echo "Trace...:\n" . $e->getTraceAsString() . "\n";
    echo "=============================================\n";
    exit(1);
}
