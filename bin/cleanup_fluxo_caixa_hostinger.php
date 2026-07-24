<?php
/**
 * SCRIPT DE LIMPEZA - FLUXO DE CAIXA (VERSÃO PARA HOSPEDAGEM COMPARTILHADA)
 * 
 * INSTRUÇÕES PARA HOSTINGER / HOSPEDAGEM COMPARTILHADA:
 * 
 * 1. Edite as 4 linhas abaixo com suas credenciais do banco de dados.
 *    Você encontra essas informações no painel da Hostinger:
 *    Hospedagem -> Bancos de Dados -> Gerenciar (no banco correto)
 * 
 * 2. Envie este arquivo para a pasta do seu projeto (a mesma pasta que tem app/, bin/, etc)
 * 
 * 3. Rode via SSH:  php bin/cleanup_fluxo_caixa_hostinger.php
 * 
 * 4. OU coloque na pasta public/ e acesse via navegador (menos recomendado, delete após usar!)
 * 
 * VERSÃO: 1.1
 * DATA:   2026-07-24
 */

// ============================================================
// 🔧 EDITE SUAS CREDENCIAIS AQUI ABAIXO:
// ============================================================
$DB_HOST     = 'localhost';          // Geralmente 'localhost' na Hostinger (às vezes 127.0.0.1)
$DB_USERNAME = 'u233127180_os_user';  // Seu usuário do MySQL (encontre no painel)
$DB_PASSWORD = 'sua_senha_aqui';      // Sua senha do MySQL
$DB_DATABASE = 'u233127180_os';       // Nome do banco de dados
// ============================================================
// NÃO EDITE NADA DAQUI PARA BAIXO
// ============================================================

$_ENV['DB_HOST']     = $DB_HOST;
$_ENV['DB_USERNAME'] = $DB_USERNAME;
$_ENV['DB_PASSWORD'] = $DB_PASSWORD;
$_ENV['DB_DATABASE'] = $DB_DATABASE;

putenv("DB_HOST={$DB_HOST}");
putenv("DB_USERNAME={$DB_USERNAME}");
putenv("DB_PASSWORD={$DB_PASSWORD}");
putenv("DB_DATABASE={$DB_DATABASE}");

require_once __DIR__ . '/../app/Core/Autoload.php';

use App\Models\FluxoCaixa;

echo "=============================================\n";
echo " SCRIPT DE LIMPEZA - FLUXO DE CAIXA\n";
echo "  (Versão para Hospedagem Compartilhada)\n";
echo "=============================================\n\n";

try {
    $fluxoCaixa = new FluxoCaixa();
    $db = $fluxoCaixa->getConnection();
    echo "✅ Conectado no banco: {$DB_DATABASE} (host: {$DB_HOST})\n\n";

    echo "[1/3] Buscando entradas inválidas...\n";
    
    // 1a. Pagamentos inativos ou inexistentes
    $sqlPagamentos = "
        SELECT fc.id, fc.referencia_id 
        FROM fluxo_caixa fc
        LEFT JOIN pagamentos_transacoes pt ON fc.referencia_tipo = 'pagamento' AND fc.referencia_id = pt.id
        WHERE fc.referencia_tipo = 'pagamento'
        AND (pt.id IS NULL OR pt.ativo = 0)
    ";
    $stmtPag = $db->prepare($sqlPagamentos);
    $stmtPag->execute();
    $pagamentosInvalidos = $stmtPag->fetchAll(PDO::FETCH_ASSOC);
    echo "   Pagamentos inválidos: " . count($pagamentosInvalidos) . "\n";
    
    // 1b. Itens de OS inativos ou inexistentes
    $sqlItensOs = "
        SELECT fc.id, fc.referencia_id 
        FROM fluxo_caixa fc
        LEFT JOIN itens_ordem_servico ios ON fc.referencia_tipo = 'item_os' AND fc.referencia_id = ios.id
        WHERE fc.referencia_tipo = 'item_os'
        AND (ios.id IS NULL OR ios.ativo = 0)
    ";
    $stmtItensOs = $db->prepare($sqlItensOs);
    $stmtItensOs->execute();
    $itensOsInvalidos = $stmtItensOs->fetchAll(PDO::FETCH_ASSOC);
    echo "   Itens de OS inválidos: " . count($itensOsInvalidos) . "\n";
    
    // 1c. Itens de atendimento inativos ou inexistentes
    $sqlItensAtend = "
        SELECT fc.id, fc.referencia_id 
        FROM fluxo_caixa fc
        LEFT JOIN itens_ordem_servico ios ON fc.referencia_tipo = 'item_atendimento' AND fc.referencia_id = ios.id
        WHERE fc.referencia_tipo = 'item_atendimento'
        AND (ios.id IS NULL OR ios.ativo = 0)
    ";
    $stmtItensAtend = $db->prepare($sqlItensAtend);
    $stmtItensAtend->execute();
    $itensAtendInvalidos = $stmtItensAtend->fetchAll(PDO::FETCH_ASSOC);
    echo "   Itens de atendimento inválidos: " . count($itensAtendInvalidos) . "\n";
    
    $entriesToRemove = array_merge($pagamentosInvalidos, $itensOsInvalidos, $itensAtendInvalidos);

    if (empty($entriesToRemove)) {
        echo "\n   ✅ Nenhuma entrada para remover do fluxo_caixa!\n\n";
        echo "=============================================\n";
        echo "✅ LIMPEZA CONCLUÍDA COM SUCESSO!\n";
        echo "=============================================\n";
        exit(0);
    }

    echo "\n   Encontradas " . count($entriesToRemove) . " entradas para remover!\n\n";

    echo "[2/3] Removendo entradas inválidas...\n";
    $idsToRemove = array_column($entriesToRemove, 'id');
    $placeholders = str_repeat('?,', count($idsToRemove) - 1) . '?';
    $deleteSql = "DELETE FROM fluxo_caixa WHERE id IN ($placeholders)";
    $deleteStmt = $db->prepare($deleteSql);
    $deleteStmt->execute($idsToRemove);
    $removedCount = $deleteStmt->rowCount();

    echo "   ✅ Removidas $removedCount entradas do fluxo_caixa!\n\n";
    
    echo "[3/3] Relatório de limpeza:\n";
    echo "   - Pagamentos removidos: " . count($pagamentosInvalidos) . "\n";
    echo "   - Itens de OS removidos: " . count($itensOsInvalidos) . "\n";
    echo "   - Itens de atendimento removidos: " . count($itensAtendInvalidos) . "\n\n";
    
    echo "=============================================\n";
    echo "✅ LIMPEZA CONCLUÍDA COM SUCESSO!\n";
    echo "=============================================\n";
} catch (PDOException $e) {
    echo "\n=============================================\n";
    echo "❌ ERRO NA LIMPEZA\n";
    echo "=============================================\n";
    echo "Mensagem: " . $e->getMessage() . "\n\n";
    echo "💡 Dica: Verifique se as credenciais no topo do arquivo estão corretas.\n";
    echo "   Hostinger costuma usar: localhost como host.\n";
    echo "   Confira o usuário e senha no painel: Bancos de Dados.\n";
    echo "=============================================\n";
    exit(1);
}
