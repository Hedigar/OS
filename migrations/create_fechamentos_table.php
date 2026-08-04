<?php

require_once __DIR__ . '/../app/Core/Autoload.php';

use App\Core\Database;

try {
    $db = Database::getInstance()->getConnection();
    
    echo "Iniciando migração de banco de dados...\n";

    // 1. Criar a tabela fechamentos_mensais
    $sqlTable = "CREATE TABLE IF NOT EXISTS fechamentos_mensais (
        id INT AUTO_INCREMENT PRIMARY KEY,
        ano INT NOT NULL,
        mes INT NOT NULL,
        fechado_em DATETIME NOT NULL,
        usuario_id INT NOT NULL,
        observacoes TEXT NULL,
        UNIQUE KEY uq_periodo (ano, mes)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    
    $db->exec($sqlTable);
    echo "Tabela 'fechamentos_mensais' criada ou já existente.\n";

    // 2. Garantir índice único na tabela fluxo_caixa para evitar duplicidade física
    // Vamos verificar se a chave 'uk_referencia' ou similar já existe na tabela fluxo_caixa
    $stmt = $db->query("SHOW KEYS FROM fluxo_caixa WHERE Key_name = 'uk_referencia'");
    $hasKey = $stmt->fetch();

    if (!$hasKey) {
        // Remove quaisquer duplicatas existentes antes de adicionar o índice único para evitar erros na migração
        echo "Limpando possíveis duplicatas em fluxo_caixa para garantir integridade...\n";
        $sqlClean = "DELETE f1 FROM fluxo_caixa f1
                     INNER JOIN fluxo_caixa f2 
                     WHERE f1.id > f2.id 
                       AND f1.referencia_tipo = f2.referencia_tipo 
                       AND f1.referencia_id = f2.referencia_id";
        $db->exec($sqlClean);

        echo "Adicionando chave única 'uk_referencia' em fluxo_caixa...\n";
        $sqlAlter = "ALTER TABLE fluxo_caixa ADD UNIQUE KEY uk_referencia (referencia_tipo, referencia_id);";
        $db->exec($sqlAlter);
        echo "Chave única adicionada com sucesso!\n";
    } else {
        echo "Chave única 'uk_referencia' já existe na tabela fluxo_caixa.\n";
    }

    echo "Migração concluída com sucesso!\n";

} catch (\Throwable $e) {
    echo "Erro durante a migração: " . $e->getMessage() . "\n";
    exit(1);
}
