-- Adiciona a coluna lista_negra na tabela clientes
ALTER TABLE clientes ADD COLUMN IF NOT EXISTS lista_negra TINYINT(1) DEFAULT 0 COMMENT '1 se o cliente está na lista negra, 0 caso contrário';

-- Adiciona um índice para melhorar o desempenho das consultas
CREATE INDEX IF NOT EXISTS idx_lista_negra ON clientes(lista_negra);
