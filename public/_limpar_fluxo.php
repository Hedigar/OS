<?php
/**
 * SCRIPT TEMPORÁRIO - LIMPEZA DO FLUXO DE CAIXA
 * 
 * INSTRUÇÕES:
 * 1. Altere a senha abaixo!
 * 2. Envie este arquivo para a pasta public_html/public (ou onde fica o index.php)
 * 3. Acesse no navegador: https://seusite.com/_limpar_fluxo.php?senha=SUA_SENHA
 * 4. DELETE ESTE ARQUIVO IMEDIATAMENTE APÓS USAR! (se deixar, qualquer pessoa pode acessar)
 */

// ================= CONFIGURAÇÃO =================
define('SENHA_ACESSO', 'altere_essa_senha_123'); // <<< ALTERE ESSA SENHA!
// ================================================

if (($_GET['senha'] ?? '') !== SENHA_ACESSO) {
    http_response_code(403);
    die('❌ Acesso negado. Senha incorreta.');
}

require_once __DIR__ . '/../app/Core/Autoload.php';

use App\Models\FluxoCaixa;

echo "<pre style='font-family:monospace;font-size:14px;background:#111;color:#0f0;padding:20px;border-radius:8px;'>";
echo "=============================================\n";
echo " SCRIPT DE LIMPEZA - FLUXO DE CAIXA\n";
echo "=============================================\n\n";

try {
    $fluxoCaixa = new FluxoCaixa();
    $db = $fluxoCaixa->getConnection();

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
        echo "\n⚠️  NÃO ESQUEÇA DE DELETAR ESTE ARQUIVO: _limpar_fluxo.php\n";
        exit(0);
    }

    echo "\n   Encontradas " . count($entriesToRemove) . " entradas para remover!\n\n";

    // 2. Remove as entradas
    echo "[2/3] Removendo entradas inválidas...\n";
    $idsToRemove = array_column($entriesToRemove, 'id');
    $placeholders = str_repeat('?,', count($idsToRemove) - 1) . '?';
    $deleteSql = "DELETE FROM fluxo_caixa WHERE id IN ($placeholders)";
    $deleteStmt = $db->prepare($deleteSql);
    $deleteStmt->execute($idsToRemove);
    $removedCount = $deleteStmt->rowCount();

    echo "   ✅ Removidas $removedCount entradas do fluxo_caixa!\n\n";
    
    // 3. Exibe relatório
    echo "[3/3] Relatório de limpeza:\n";
    echo "   - Pagamentos removidos: " . count($pagamentosInvalidos) . "\n";
    echo "   - Itens de OS removidos: " . count($itensOsInvalidos) . "\n";
    echo "   - Itens de atendimento removidos: " . count($itensAtendInvalidos) . "\n\n";
    
    echo "=============================================\n";
    echo "✅ LIMPEZA CONCLUÍDA COM SUCESSO!\n";
    echo "=============================================\n";
    echo "\n⚠️  ⚠️  ⚠️  ATENÇÃO! DELETE ESTE ARQUIVO AGORA! ⚠️ ⚠️ ⚠️\n";
    echo "   Arquivo: public/_limpar_fluxo.php\n";
    echo "   Deixe o arquivo existindo = risco de segurança!\n";
    echo "</pre>";
} catch (PDOException $e) {
    echo "\n=============================================\n";
    echo "❌ ERRO NA LIMPEZA\n";
    echo "=============================================\n";
    echo "Mensagem: " . $e->getMessage() . "\n";
    echo "=============================================\n";
    echo "</pre>";
    exit(1);
}
