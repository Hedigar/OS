<?php
/**
 * SCRIPT OFICIAL DE MIGRAÇÃO - FLUXO DE CAIXA
 * 
 * Este script limpa e popula a tabela fluxo_caixa com dados válidos:
 * - Custos: apenas OS Finalizadas (status 5) e Atendimentos Externos Concluídos
 * - Pagamentos: apenas transações ativas
 * 
 * Versão: 1.0
 * Data: 2026-07-06
 */

// Força as credenciais para o banco de dados (funciona dentro e fora do container)
$_ENV['DB_HOST'] = '127.0.0.1';
$_ENV['DB_USERNAME'] = 'root';
$_ENV['DB_PASSWORD'] = 'root';
$_ENV['DB_DATABASE'] = 'os';

// 1. Carrega o Autoloader
require_once __DIR__ . '/../app/Core/Autoload.php';

use App\Models\FluxoCaixa;

echo "=============================================\n";
echo " SCRIPT OFICIAL DE MIGRAÇÃO - FLUXO DE CAIXA\n";
echo "=============================================\n\n";

// Inicializa o modelo
$fluxoCaixa = new FluxoCaixa();
$db = $fluxoCaixa->getConnection();

try {
    // 1. Limpa a tabela
    echo "[1/4] Limpando tabela fluxo_caixa...\n";
    $stmt = $db->exec("TRUNCATE TABLE fluxo_caixa");
    echo "   ✅ Tabela limpa com sucesso!\n\n";

    // 2. Backfill de custos
    echo "[2/4] Backfill de custos...\n";
    $stmt = $db->query("
        SELECT 
            ios.id, 
            ios.ordem_servico_id, 
            ios.atendimento_externo_id, 
            ios.quantidade, 
            COALESCE(NULLIF(ios.valor_custo, 0), NULLIF(ios.custo, 0)) as valor_custo,
            COALESCE(DATE(os.created_at), DATE(ae.created_at), DATE(ios.created_at)) as data_transacao
        FROM itens_ordem_servico ios
        LEFT JOIN ordens_servico os ON ios.ordem_servico_id = os.id
        LEFT JOIN atendimentos_externos ae ON ios.atendimento_externo_id = ae.id
        WHERE ios.ativo = 1
        AND (
            -- Apenas OS com status Finalizada (id 5)
            (ios.ordem_servico_id IS NOT NULL AND os.status_atual_id = 5)
            OR 
            -- Apenas Atendimentos Externos com status Concluído
            (ios.atendimento_externo_id IS NOT NULL AND ae.status = 'concluido')
        )
    ");
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
    echo "   ✅ Backfilled $countCosts custos!\n\n";

    // 3. Backfill de pagamentos
    echo "[3/4] Backfill de pagamentos...\n";
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

    // 4. Verificação final
    echo "[4/4] Verificação final dos totais...\n";
    $stmtCustos = $db->query("SELECT SUM(valor) as total FROM fluxo_caixa WHERE tipo = 'custo'");
    $totalCustos = (float)$stmtCustos->fetch(PDO::FETCH_ASSOC)['total'];

    $stmtEntradas = $db->query("SELECT SUM(valor) as total FROM fluxo_caixa WHERE tipo = 'entrada'");
    $totalEntradas = (float)$stmtEntradas->fetch(PDO::FETCH_ASSOC)['total'];

    echo "   Total de custos:   R$ " . number_format($totalCustos, 2, ',', '.') . "\n";
    echo "   Total de entradas: R$ " . number_format($totalEntradas, 2, ',', '.') . "\n";
    echo "   Saldo:             R$ " . number_format($totalEntradas - $totalCustos, 2, ',', '.') . "\n\n";

    echo "=============================================\n";
    echo "✅ MIGRAÇÃO CONCLUÍDA COM SUCESSO!\n";
    echo "=============================================\n";

} catch (PDOException $e) {
    echo "\n=============================================\n";
    echo "❌ ERRO NA MIGRAÇÃO\n";
    echo "=============================================\n";
    echo "Mensagem: " . $e->getMessage() . "\n";
    echo "=============================================\n";
    exit(1);
}
