<?php
// Carrega o autoload e .env só para pegar configs básicas (não precisamos da conexão padrão)
require_once __DIR__ . '/app/Core/Autoload.php';

// Conecta diretamente no banco via 127.0.0.1
try {
    $db = new PDO(
        'mysql:host=127.0.0.1;dbname=os;charset=utf8mb4',
        'root',
        'root',
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
    echo "✅ Conexão com o banco de dados estabelecida com sucesso!\n\n";
} catch (PDOException $e) {
    die("❌ Erro ao conectar no banco: " . $e->getMessage() . "\n");
}

echo "=== Verificação de Atendimentos Externos ===\n";

// Buscar todos os atendimentos
$stmt = $db->query("SELECT id FROM atendimentos_externos ORDER BY id DESC");
$atendimentos = $stmt->fetchAll();

if (empty($atendimentos)) {
    echo "ℹ️ Nenhum atendimento encontrado!\n";
    exit;
}

echo "📋 Encontrados " . count($atendimentos) . " atendimento(s)!\n";
echo str_repeat("-", 50) . "\n";

foreach ($atendimentos as $atendimento) {
    $id = $atendimento['id'];
    echo "\n🔍 Processando Atendimento #{$id}:\n";
    
    // 1. Pegar itens do atendimento
    $stmtItens = $db->prepare("SELECT * FROM itens_ordem_servico WHERE atendimento_externo_id = :id AND ativo = 1");
    $stmtItens->execute(['id' => $id]);
    $itens = $stmtItens->fetchAll();
    echo "- 📦 Itens: " . count($itens) . "\n";
    
    // 2. Calcular totals manualmente (mesma logica do updateTotals)
    $totalProdutos = 0.00;
    $totalServicos = 0.00;
    
    if (!empty($itens)) {
        foreach ($itens as $item) {
            $tipo = $item['tipo_item'] ?? $item['tipo'] ?? 'servico';
            $valorItem = ($item['quantidade'] * ($item['valor_unitario'] + ($item['valor_mao_de_obra'] ?? 0)));
            $descontoItem = (float)($item['desconto'] ?? 0);
            
            if ($tipo === 'produto') {
                $totalProdutos += ($valorItem - $descontoItem);
            } else {
                $totalServicos += ($valorItem - $descontoItem);
            }
        }
    }
    
    // 3. Pegar valor_deslocamento e emitir_nf do atendimento
    $stmtAtend = $db->prepare("SELECT valor_deslocamento, emitir_nf FROM atendimentos_externos WHERE id = :id");
    $stmtAtend->execute(['id' => $id]);
    $dadosAtend = $stmtAtend->fetch();
    
    $valorDeslocamento = (float)($dadosAtend['valor_deslocamento'] ?? 0);
    $emitirNF = (int)($dadosAtend['emitir_nf'] ?? 0);
    $valorTotal = $totalProdutos + $totalServicos;
    
    // 4. Calcular taxa NF se necessário
    $valorTaxaNF = 0.00;
    if ($emitirNF) {
        // Pegar configurações de taxa NF
        $stmtConfigProd = $db->prepare("SELECT valor FROM configuracoes_gerais WHERE chave = 'nf_porcentagem_produtos'");
        $stmtConfigProd->execute();
        $percProdutos = (float)($stmtConfigProd->fetchColumn() ?? 3);
        
        $stmtConfigServ = $db->prepare("SELECT valor FROM configuracoes_gerais WHERE chave = 'nf_porcentagem_servicos'");
        $stmtConfigServ->execute();
        $percServicos = (float)($stmtConfigServ->fetchColumn() ?? 6);
        
        $valorTaxaNF = ($totalProdutos * ($percProdutos / 100)) + (($totalServicos + $valorDeslocamento) * ($percServicos / 100));
    }
    
    // 5. Atualizar o banco de dados
    $stmtUpdate = $db->prepare("
        UPDATE atendimentos_externos 
        SET valor_total = :valor_total, valor_taxa_nf = :valor_taxa_nf 
        WHERE id = :id
    ");
    $result = $stmtUpdate->execute([
        'valor_total' => $valorTotal,
        'valor_taxa_nf' => $valorTaxaNF,
        'id' => $id
    ]);
    
    echo "- ✅ UpdateTotals: " . ($result ? "SUCESSO" : "FALHA") . "\n";
    
    // 6. Verificar o valor gravado
    $stmtCheck = $db->prepare("SELECT valor_total, valor_deslocamento, valor_taxa_nf FROM atendimentos_externos WHERE id = :id");
    $stmtCheck->execute(['id' => $id]);
    $dadosFinal = $stmtCheck->fetch();
    
    echo "- 💰 Valor Total (itens): R$ " . number_format($dadosFinal['valor_total'], 2, ',', '.') . "\n";
    echo "- 🚗 Valor Deslocamento: R$ " . number_format($dadosFinal['valor_deslocamento'], 2, ',', '.') . "\n";
    echo "- 🧾 Valor Total Geral: R$ " . number_format(($dadosFinal['valor_total'] + $dadosFinal['valor_deslocamento']), 2, ',', '.') . "\n";
    echo "- 📄 Valor Taxa NF: R$ " . number_format($dadosFinal['valor_taxa_nf'], 2, ',', '.') . "\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "✅ Verificação concluída com sucesso!\n";
