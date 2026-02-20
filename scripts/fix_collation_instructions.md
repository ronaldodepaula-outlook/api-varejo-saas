# Corrigir mistura de collations (passos seguros)

Banco alvo: `anto4524_db_sas_multi` (retirado de .env)
Objetivo: unificar charset/collation para `utf8mb4_0900_ai_ci` (recomendado MySQL 8).

IMPORTANTE: faça backup do banco antes de executar qualquer ALTER.

1) Verificar estado atual

No MySQL (CLI ou cliente), execute:

```sql
-- servidor / banco
SHOW VARIABLES LIKE 'character_set_server';
SHOW VARIABLES LIKE 'collation_server';
SELECT default_character_set_name, default_collation_name
  FROM information_schema.SCHEMATA
  WHERE schema_name = 'anto4524_db_sas_multi';

-- tabelas
SELECT TABLE_NAME, TABLE_COLLATION
  FROM information_schema.TABLES
  WHERE TABLE_SCHEMA = 'anto4524_db_sas_multi'
  ORDER BY TABLE_NAME;

-- colunas com collation
SELECT TABLE_NAME, COLUMN_NAME, COLLATION_NAME
  FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = 'anto4524_db_sas_multi' AND COLLATION_NAME IS NOT NULL
  ORDER BY TABLE_NAME;
```

2) Gerar ALTERs para todas as tabelas (revisar antes de executar)

```sql
SET @schema = 'anto4524_db_sas_multi';
SELECT CONCAT('ALTER TABLE `', TABLE_NAME, '` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;') AS stmt
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = @schema AND TABLE_TYPE = 'BASE TABLE';
```

Copie os `stmt` resultantes e cole/execute no seu cliente MySQL.

3) Alternativa: executar em lote (cuidado com tempo de execução em tabelas grandes)

```sql
-- Gera e executa dinamicamente (faça backup primeiro)
SET @schema = 'anto4524_db_sas_multi';
SELECT GROUP_CONCAT(CONCAT('ALTER TABLE `', TABLE_SCHEMA, '`.`', TABLE_NAME, '` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;') SEPARATOR ' ') INTO @sql
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = @schema AND TABLE_TYPE = 'BASE TABLE';

-- Verifique @sql antes de executar
SELECT @sql;

-- Para executar (somente se o conteúdo for verificado):
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
```

OBS: `GROUP_CONCAT` tem limite de tamanho (`group_concat_max_len`); se for maior que o limite, use o método de gerar os comandos e executá-los manualmente.

4) Ajustar collation do banco para futuras criações:

```sql
ALTER DATABASE `anto4524_db_sas_multi` CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
```

5) Verificar triggers/procedures que usam `LIKE` (podem comparar colunas com collations diferentes)

```sql
SELECT TRIGGER_NAME, EVENT_MANIPULATION, ACTION_STATEMENT
FROM information_schema.TRIGGERS
WHERE TRIGGER_SCHEMA = 'anto4524_db_sas_multi';

SELECT ROUTINE_NAME, ROUTINE_TYPE, ROUTINE_DEFINITION
FROM information_schema.ROUTINES
WHERE ROUTINE_SCHEMA = 'anto4524_db_sas_multi';
```

Se encontrar comparações problemáticas, adapte a expressão usando `COLLATE` explicitamente, por exemplo:

```sql
WHERE coluna1 COLLATE utf8mb4_0900_ai_ci LIKE coluna2 COLLATE utf8mb4_0900_ai_ci
```

6) Mudança temporária na aplicação (opcional)

Editar `config/database.php` para usar:

```php
'charset' => 'utf8mb4',
'collation' => 'utf8mb4_0900_ai_ci',
```

Após editar, reinicie o servidor PHP (ou o serviço do seu ambiente) para aplicar.

7) Testes

- Reinicie app
- Tente criar pedido novamente
- Caso ainda ocorra erro, capture a query/trigger que disparou e revise collations das colunas envolvidas.

---
Se quiser, eu posso:
- Gerar um arquivo SQL pronto com os `ALTER TABLE` para todas as tabelas (eu preciso listar as tabelas; posso gerar comandos de `SELECT` que você executa),
- Ou editar `config/database.php` para aplicar a collation temporária no projeto.

Diga qual ação prefere que eu realize agora.
