<?php
/**
 * SCRIPT DE LIMPEZA - FLUXO DE CAIXA
 * 
 * Este script remove entradas no fluxo_caixa que estão relacionadas a pagamentos inativos ou inexistentes
 * 
 * Versão: 1.0
 * Data: 2026-07-11
 */

// Força as credenciais do Docker para o banco de dados
$_ENV['DB_HOST'] = 'db';
$_ENV['DB_USERNAME'] = 'os_user';
$_ENV['DB_PASSWORD'] = 'os_pass';
$_ENV['DB_DATABASE'] = 'os';

// 1. Carrega o Autoloader (que carrega Composer e config/config.php)
require_once __DIR__ . '/../app/Core/Autoload.php';

use App\Models\FluxoCaixa;

echo "=============================================\n";
echo " SCRIPT DE LIMPEZA - FLUXO DE CAIXA\n";
echo "=============================================\n\n";

try {
    // Inicializa o modelo e obtém a conexão
    $fluxoCaixa = new FluxoCaixa();
    $db = $fluxoCaixa->getConnection();

    // 1. Encontra todas as referências 'pagamento' em fluxo_caixa que referem-se a pagamentos inativos (ativo = 0) ou inexistentes
    echo "[1/2] Buscando entradas inválidas...\n";
    $sql = "
        SELECT fc.id, fc.referencia_id 
        FROM fluxo_caixa fc
        LEFT JOIN pagamentos_transacoes pt ON fc.referencia_tipo = 'pagamento' AND fc.referencia_id = pt.id
        WHERE fc.referencia_tipo = 'pagamento'
        AND (pt.id IS NULL OR pt.ativo = 0)
    ";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $entriesToRemove = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($entriesToRemove)) {
        echo "   ✅ Nenhuma entrada para remover do fluxo_caixa!\n\n";
        echo "=============================================\n";
        echo "✅ LIMPEZA CONCLUÍDA COM SUCESSO!\n";
        echo "=============================================\n";
        exit(0);
    }

    echo "   Encontradas " . count($entriesToRemove) . " entradas para remover!\n\n";

    // 2. Remove as entradas
    echo "[2/2] Removendo entradas inválidas...\n";
    $idsToRemove = array_column($entriesToRemove, 'id');
    $placeholders = str_repeat('?,', count($idsToRemove) - 1) . '?';
    $deleteSql = "DELETE FROM fluxo_caixa WHERE id IN ($placeholders)";
    $deleteStmt = $db->prepare($deleteSql);
    $deleteStmt->execute($idsToRemove);
    $removedCount = $deleteStmt->rowCount();

    echo "   ✅ Removidas $removedCount entradas do fluxo_caixa!\n\n";
    echo "=============================================\n";
    echo "✅ LIMPEZA CONCLUÍDA COM SUCESSO!\n";
    echo "=============================================\n";
} catch (PDOException $e) {
    echo "\n=============================================\n";
    echo "❌ ERRO NA LIMPEZA\n";
    echo "=============================================\n";
    echo "Mensagem: " . $e->getMessage() . "\n";
    echo "=============================================\n";
    exit(1);
}
