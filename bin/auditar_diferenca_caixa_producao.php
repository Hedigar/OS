<?php
/**
 * SCRIPT DE AUDITORIA - DIFERENÇA CAIXA VS PRODUÇÃO
 * 
 * Este script:
 * 1. Lista todas as entradas no fluxo_caixa que não possuem OS com status 5 (Finalizada)
 *    ou atendimento_externo com status 'concluído'
 * 2. Calcula o valor total dessas 'entradas órfãs'
 * 
 * Versão: 1.0
 * Data: 2026-07-11
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
echo " SCRIPT DE AUDITORIA - DIFERENÇA CAIXA VS PRODUÇÃO\n";
echo "=============================================\n\n";

try {
    // Inicializa o modelo e obtém a conexão
    $fluxoCaixa = new FluxoCaixa();
    $db = $fluxoCaixa->getConnection();

    // 1. Busca todas as entradas órfãs
    echo "[1/2] Buscando entradas órfãs no fluxo_caixa...\n";
    $sql = "
        SELECT 
            fc.id,
            fc.data,
            fc.tipo,
            fc.valor,
            fc.referencia_tipo,
            fc.referencia_id,
            fc.os_id,
            fc.atendimento_externo_id,
            os.status_atual_id,
            os.status_pagamento,
            ae.status as atendimento_status
        FROM fluxo_caixa fc
        LEFT JOIN ordens_servico os ON fc.os_id = os.id
        LEFT JOIN atendimentos_externos ae ON fc.atendimento_externo_id = ae.id
        WHERE fc.tipo = 'entrada'
        AND fc.referencia_tipo = 'pagamento'
        AND (
            (fc.os_id IS NOT NULL AND (os.status_atual_id != 5 OR os.ativo = 0))
            OR 
            (fc.atendimento_externo_id IS NOT NULL AND (ae.status != 'concluido' OR ae.ativo = 0))
            OR 
            (fc.os_id IS NULL AND fc.atendimento_externo_id IS NULL)
        )
        ORDER BY fc.data DESC
    ";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $entradasOrfas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 2. Calcula o valor total
    echo "[2/2] Calculando valor total das entradas órfãs...\n\n";
    $totalOrfaos = 0;
    foreach ($entradasOrfas as $entrada) {
        $totalOrfaos += $entrada['valor'];
    }

    // Exibe o resultado
    if (empty($entradasOrfas)) {
        echo "   ✅ Nenhuma entrada órfã encontrada!\n\n";
    } else {
        echo "   --- Lista de entradas órfãs ---\n";
        foreach ($entradasOrfas as $entrada) {
            $origem = $entrada['os_id'] ? "OS {$entrada['os_id']}" : ($entrada['atendimento_externo_id'] ? "Atendimento {$entrada['atendimento_externo_id']}" : "Sem origem");
            $status = $entrada['os_id'] ? "Status OS: " . ($entrada['status_atual_id'] ?: 'N/A') : ($entrada['atendimento_externo_id'] ? "Status Atendimento: " . ($entrada['atendimento_status'] ?: 'N/A') : "N/A");
            echo "   - ID: {$entrada['id']} | Data: {$entrada['data']} | Valor: R$ " . number_format($entrada['valor'], 2, ',', '.') . " | Origem: {$origem} | {$status}\n";
        }
        echo "\n";
    }

    echo "=============================================\n";
    echo " Total de entradas órfãs: " . count($entradasOrfas) . "\n";
    echo " Valor total das entradas órfãs: R$ " . number_format($totalOrfaos, 2, ',', '.') . "\n";
    echo "=============================================\n";
    echo "✅ AUDITORIA CONCLUÍDA COM SUCESSO!\n";
    echo "=============================================\n";
} catch (PDOException $e) {
    echo "\n=============================================\n";
    echo "❌ ERRO NA AUDITORIA\n";
    echo "=============================================\n";
    echo "Mensagem: " . $e->getMessage() . "\n";
    echo "=============================================\n";
    exit(1);
}
