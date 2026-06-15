-- Migration to add blacklist support for CRM
-- Run this in your database

-- Add lista_negra column to clientes table
ALTER TABLE clientes 
ADD COLUMN lista_negra TINYINT(1) NOT NULL DEFAULT 0 
COMMENT '1 = Cliente na lista negra, não receberá campanhas CRM; 0 = Normal';

-- Optional: Add index for faster filtering
CREATE INDEX idx_lista_negra ON clientes(lista_negra);