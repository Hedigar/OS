# Guia de Deploy - Migração do Fluxo de Caixa

## Versão
Script: `bin/migrate_fluxo_caixa.php`
Versão: 1.0
Data: 2026-07-06

---

## Pré-requisitos
- Docker e docker-compose rodando
- Acesso ao terminal do servidor

---

## Passo a Passo de Deploy

### 1. Backup Prévio (Obrigatório!)
Antes de executar a migração, faça um backup da tabela `fluxo_caixa`:

```bash
# Entrar no container do banco
docker exec -it os_db bash

# Dentro do container, fazer backup
mysqldump -u os_user -pos_pass os fluxo_caixa > /tmp/backup_fluxo_caixa_$(date +%Y%m%d_%H%M%S).sql

# Sair do container
exit

# Copiar o backup para o host (opcional, mas recomendado)
docker cp os_db:/tmp/backup_fluxo_caixa_*.sql ./backup/
```

### 2. Executar a Migração
No diretório do projeto, execute:

```bash
docker exec -it os_web php /var/www/html/bin/migrate_fluxo_caixa.php
```

### 3. Verificar os Totais Pós-Migração
Acesse o banco de dados e execute as queries de verificação:

```bash
# Entrar no container do banco
docker exec -it os_db mysql -u os_user -pos_pass os
```

Dentro do MySQL:
```sql
-- Verificar total de custos
SELECT SUM(valor) as total_custos FROM fluxo_caixa WHERE tipo = 'custo';

-- Verificar total de entradas
SELECT SUM(valor) as total_entradas FROM fluxo_caixa WHERE tipo = 'entrada';

-- Verificar saldo
SELECT 
  SUM(CASE WHEN tipo = 'entrada' THEN valor ELSE 0 END) -
  SUM(CASE WHEN tipo = 'custo' THEN valor ELSE 0 END) as saldo
FROM fluxo_caixa;
```

**Valores esperados (aproximados):**
- Total de custos: ~R$ 24.900,00
- Total de entradas: ~R$ 90.091,00
- Saldo: ~R$ 65.191,00

### 4. Reverter em Caso de Erro
Se precisar restaurar o backup:

```bash
# Entrar no container do banco
docker exec -it os_db bash

# Restaurar o backup
mysql -u os_user -pos_pass os < /tmp/backup_fluxo_caixa_DATA_HORA.sql
```

---

## O que a Migração Faz?
1. **Limpa a tabela `fluxo_caixa`** (TRUNCATE)
2. **Popula custos válidos**:
   - Apenas Ordens de Serviço com status **Finalizada** (status_atual_id = 5)
   - Apenas Atendimentos Externos com status **concluido**
3. **Popula pagamentos válidos**:
   - Apenas transações ativas (`ativo = 1`)
4. **Mostra um relatório final** com os totais

---

## Contato/Suporte
Em caso de dúvidas, contate a equipe de desenvolvimento.
