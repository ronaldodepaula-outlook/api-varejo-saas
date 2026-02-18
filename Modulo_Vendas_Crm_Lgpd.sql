-- Módulo: Vendas Assistidas + CRM (LGPD-ready)
-- Arquivo: modulo_vendas_crm_lgpd.sql
-- Conteúdo: DDL (tabelas), triggers, procedures e pseudocódigo para hashing/criptografia
-- IMPORTANTE: operações de criptografia devem ser feitas na camada de aplicação usando KMS.

/* ====================================================================
   TABELAS (DDL)
   ==================================================================== */

-- 1) tb_clientes (CRM — LGPD-aware)
CREATE TABLE IF NOT EXISTS `tb_clientes` (
  `id_cliente` INT NOT NULL AUTO_INCREMENT,
  `id_empresa` INT NOT NULL,
  `nome_cliente` VARCHAR(150) DEFAULT NULL,
  `email` VARCHAR(150) DEFAULT NULL,
  `telefone` VARCHAR(20) DEFAULT NULL,
  `whatsapp` VARCHAR(20) DEFAULT NULL,
  `endereco` VARCHAR(255) DEFAULT NULL,
  `cidade` VARCHAR(100) DEFAULT NULL,
  `estado` VARCHAR(2) DEFAULT NULL,
  `cep` VARCHAR(10) DEFAULT NULL,
  `cpf_hash` CHAR(64) DEFAULT NULL,
  `cpf_encrypted` VARBINARY(512) DEFAULT NULL,
  `document_type` ENUM('cpf','cnpj','rg','outro') DEFAULT NULL,
  `document_number_encrypted` VARBINARY(512) DEFAULT NULL,
  `consent_marketing` ENUM('sim','nao') DEFAULT 'nao',
  `id_consentimento` INT DEFAULT NULL,
  `classificacao` ENUM('bronze','prata','ouro','diamante') DEFAULT 'bronze',
  `status` ENUM('ativo','inativo') DEFAULT 'ativo',
  `data_cadastro` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `data_ultima_atualizacao` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `data_reter_ate` DATE DEFAULT NULL,
  `flag_excluido_logico` TINYINT(1) DEFAULT 0,
  `data_exclusao_solicitada` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id_cliente`),
  INDEX `idx_empresa_status` (`id_empresa`, `status`),
  INDEX `idx_cpf_hash` (`cpf_hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 2) tb_consentimentos (registro imutável de consentimentos)
CREATE TABLE IF NOT EXISTS `tb_consentimentos` (
  `id_consentimento` INT NOT NULL AUTO_INCREMENT,
  `id_empresa` INT NOT NULL,
  `id_cliente` INT DEFAULT NULL,
  `id_usuario` INT DEFAULT NULL,
  `tipo_consentimento` ENUM('marketing','email_news','sms','compartilhamento_terceiro','outro') NOT NULL,
  `texto_consentimento` TEXT NOT NULL,
  `versao` VARCHAR(50) DEFAULT NULL,
  `concedido` ENUM('sim','nao') NOT NULL,
  `data_consentimento` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `ip_origem` VARCHAR(45) DEFAULT NULL,
  `user_agent` TEXT DEFAULT NULL,
  PRIMARY KEY (`id_consentimento`),
  INDEX (`id_cliente`),
  INDEX (`id_empresa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 3) tb_interacoes_clientes (histórico de contatos)
CREATE TABLE IF NOT EXISTS `tb_interacoes_clientes` (
  `id_interacao` INT NOT NULL AUTO_INCREMENT,
  `id_empresa` INT NOT NULL,
  `id_cliente` INT NOT NULL,
  `id_usuario` INT NOT NULL,
  `tipo_interacao` ENUM('ligacao','visita','email','whatsapp','outros') DEFAULT 'outros',
  `descricao` TEXT NOT NULL,
  `data_interacao` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `privado` ENUM('sim','nao') DEFAULT 'nao',
  PRIMARY KEY (`id_interacao`),
  INDEX (`id_cliente`),
  INDEX (`id_empresa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 4) tb_clientes_audit (log de acessos/alterações - metadados apenas)
CREATE TABLE IF NOT EXISTS `tb_clientes_audit` (
  `id_audit` BIGINT NOT NULL AUTO_INCREMENT,
  `id_empresa` INT NOT NULL,
  `id_cliente` INT DEFAULT NULL,
  `id_usuario` INT NOT NULL,
  `acao` ENUM('CREATE','READ','UPDATE','DELETE','EXPORT') NOT NULL,
  `campos_afetados` TEXT DEFAULT NULL,
  `justificativa` TEXT DEFAULT NULL,
  `ip_origem` VARCHAR(45) DEFAULT NULL,
  `timestamp` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_audit`),
  INDEX (`id_cliente`),
  INDEX (`id_empresa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 5) tb_debitos_clientes (ajustado para LGPD)
CREATE TABLE IF NOT EXISTS `tb_debitos_clientes` (
  `id_debito` INT NOT NULL AUTO_INCREMENT,
  `id_empresa` INT NOT NULL,
  `id_filial` INT NOT NULL,
  `id_cliente` INT DEFAULT NULL,
  `pseudonymous_customer_id` CHAR(64) DEFAULT NULL,
  `id_venda` INT NOT NULL,
  `valor` DECIMAL(12,2) NOT NULL,
  `status` ENUM('pendente','liquidado','cancelado') DEFAULT 'pendente',
  `data_geracao` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `data_pagamento` TIMESTAMP NULL DEFAULT NULL,
  `observacao` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id_debito`),
  INDEX (`id_empresa`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 6) tb_vendas_assistidas (adaptação LGPD)
CREATE TABLE IF NOT EXISTS `tb_vendas_assistidas` (
  `id_venda` INT NOT NULL AUTO_INCREMENT,
  `id_empresa` INT NOT NULL,
  `id_filial` INT NOT NULL,
  `id_cliente` INT DEFAULT NULL,
  `pseudonymous_customer_id` CHAR(64) DEFAULT NULL,
  `id_usuario` INT NOT NULL,
  `tipo_venda` ENUM('prevenda','balcao','consignado','delivery') DEFAULT 'balcao',
  `forma_pagamento` ENUM('dinheiro','pix','cartao_credito','cartao_debito','boleto','fiado') DEFAULT 'dinheiro',
  `status` ENUM('aberta','finalizada','cancelada') DEFAULT 'aberta',
  `valor_total` DECIMAL(12,2) DEFAULT 0.00,
  `observacao` TEXT,
  `data_venda` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `data_fechamento` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id_venda`),
  INDEX (`id_empresa`,`id_filial`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 7) tb_itens_venda_assistida
CREATE TABLE IF NOT EXISTS `tb_itens_venda_assistida` (
  `id_item_venda` INT NOT NULL AUTO_INCREMENT,
  `id_venda` INT NOT NULL,
  `id_empresa` INT NOT NULL,
  `id_filial` INT NOT NULL,
  `id_produto` INT NOT NULL,
  `quantidade` DECIMAL(10,2) NOT NULL,
  `valor_unitario` DECIMAL(10,2) NOT NULL,
  `valor_total` DECIMAL(12,2) GENERATED ALWAYS AS ((`quantidade` * `valor_unitario`)) STORED,
  PRIMARY KEY (`id_item_venda`),
  INDEX (`id_venda`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Observações: chaves estrangeiras não são impostas rigidamente para facilitar migração em bases existentes.
-- Caso deseje FK fortes, podemos incluir CONSTRAINTs com ON DELETE SET NULL/RESTRICT conforme necessidade.

/* ====================================================================
   TRIGGERS E PROCEDURES SUGERIDAS
   ==================================================================== */

DELIMITER $$

-- Trigger: Auditar criação/atualização/exclusão em tb_clientes (metadados apenas)
CREATE TRIGGER trg_clientes_audit_after_insert
AFTER INSERT ON tb_clientes
FOR EACH ROW
BEGIN
  INSERT INTO tb_clientes_audit (id_empresa, id_cliente, id_usuario, acao, campos_afetados, ip_origem)
  VALUES (NEW.id_empresa, NEW.id_cliente, NULL, 'CREATE', JSON_OBJECT('fields','*created*'), NULL);
END$$

CREATE TRIGGER trg_clientes_audit_after_update
AFTER UPDATE ON tb_clientes
FOR EACH ROW
BEGIN
  -- registrar apenas quais campos foram alterados (nomes) - sem valores sensíveis
  DECLARE v_fields TEXT DEFAULT '';
  IF OLD.nome_cliente <> NEW.nome_cliente THEN SET v_fields = CONCAT(v_fields, 'nome_cliente,'); END IF;
  IF OLD.email <> NEW.email THEN SET v_fields = CONCAT(v_fields, 'email,'); END IF;
  IF OLD.telefone <> NEW.telefone THEN SET v_fields = CONCAT(v_fields, 'telefone,'); END IF;
  IF OLD.whatsapp <> NEW.whatsapp THEN SET v_fields = CONCAT(v_fields, 'whatsapp,'); END IF;
  IF OLD.status <> NEW.status THEN SET v_fields = CONCAT(v_fields, 'status,'); END IF;

  IF v_fields = '' THEN SET v_fields = 'campos_nao_especificados'; END IF;

  INSERT INTO tb_clientes_audit (id_empresa, id_cliente, id_usuario, acao, campos_afetados, ip_origem)
  VALUES (NEW.id_empresa, NEW.id_cliente, NULL, 'UPDATE', v_fields, NULL);
END$$

CREATE TRIGGER trg_clientes_audit_after_delete
AFTER DELETE ON tb_clientes
FOR EACH ROW
BEGIN
  INSERT INTO tb_clientes_audit (id_empresa, id_cliente, id_usuario, acao, campos_afetados, ip_origem)
  VALUES (OLD.id_empresa, OLD.id_cliente, NULL, 'DELETE', 'registro_excluido', NULL);
END$$

-- Trigger: ao finalizar venda -> atualizar estoque e gerar débito se necessário
CREATE TRIGGER trg_venda_assistida_finalizada
AFTER UPDATE ON tb_vendas_assistidas
FOR EACH ROW
BEGIN
  DECLARE v_id INT;
  IF NEW.status = 'finalizada' AND OLD.status <> 'finalizada' THEN
    -- Inserir movimentações somente quando estoque existir (similar às triggers que já existem no esquema principal)
    INSERT INTO tb_movimentacoes (id_empresa, id_filial, id_produto, tipo_movimentacao, origem, id_referencia, quantidade, saldo_anterior, saldo_atual, observacao, id_usuario, data_movimentacao)
    SELECT
      i.id_empresa,
      i.id_filial,
      i.id_produto,
      'saida',
      'venda_assistida',
      i.id_venda,
      i.quantidade,
      COALESCE(e.quantidade,0),
      COALESCE(e.quantidade,0) - i.quantidade,
      CONCAT('Venda assistida ', i.id_venda),
      v.id_usuario,
      NOW()
    FROM tb_itens_venda_assistida i
    JOIN tb_vendas_assistidas v ON v.id_venda = i.id_venda
    LEFT JOIN tb_estoque e ON e.id_empresa = i.id_empresa AND e.id_filial = i.id_filial AND e.id_produto = i.id_produto
    WHERE v.id_venda = NEW.id_venda;

    -- Atualiza estoque - subtrai quantidade quando existe
    UPDATE tb_estoque e
    JOIN tb_itens_venda_assistida i ON e.id_empresa = i.id_empresa AND e.id_filial = i.id_filial AND e.id_produto = i.id_produto
    SET e.quantidade = e.quantidade - i.quantidade
    WHERE i.id_venda = NEW.id_venda;

    -- Gerar débito automático quando for fiado ou prevenda
    IF NEW.forma_pagamento = 'fiado' OR NEW.tipo_venda = 'prevenda' THEN
      INSERT INTO tb_debitos_clientes (id_empresa, id_filial, id_cliente, pseudonymous_customer_id, id_venda, valor, status, data_geracao)
      VALUES (NEW.id_empresa, NEW.id_filial, NEW.id_cliente, NEW.pseudonymous_customer_id, NEW.id_venda, NEW.valor_total, 'pendente', NOW());
    END IF;
  END IF;
END$$

DELIMITER ;

/* ====================================================================
   PROCEDURES SUGERIDAS (PSEUDONIMIZAÇÃO / PURGE)
   ==================================================================== */

-- 1) Procedure de pseudonimização (aplicação chama this quando confirmar solicitação do titular)
-- Nota: idealmente a descriptografia / criptografia é feita na camada app com KMS.

DELIMITER $$
CREATE PROCEDURE proc_pseudonimizar_cliente(IN p_id_cliente INT, IN p_usuario INT)
BEGIN
  -- marca exclusão lógica, gera pseudonymous_customer_id e remove dados PII visíveis
  UPDATE tb_clientes
  SET
    nome_cliente = CONCAT('EXCLUIDO_', id_cliente, '_', DATE_FORMAT(NOW(), '%Y%m%d')),
    email = NULL,
    telefone = NULL,
    whatsapp = NULL,
    endereco = NULL,
    cpf_hash = NULL,
    cpf_encrypted = NULL,
    document_number_encrypted = NULL,
    flag_excluido_logico = 1,
    data_exclusao_solicitada = NOW()
  WHERE id_cliente = p_id_cliente;

  INSERT INTO tb_clientes_audit (id_empresa, id_cliente, id_usuario, acao, campos_afetados, justificativa, ip_origem)
  VALUES ((SELECT id_empresa FROM tb_clientes WHERE id_cliente = p_id_cliente LIMIT 1), p_id_cliente, p_usuario, 'DELETE', 'pseudonimizacao', 'Solicitação do titular');
END$$
DELIMITER ;

-- 2) Procedure / job de limpeza (executar periodicamente para remoção definitiva conforme data_reter_ate)
DELIMITER $$
CREATE PROCEDURE proc_purge_clientes_expirados()
BEGIN
  -- ATENÇÃO: operação destrutiva. Deve ser executada apenas por administrador.
  DELETE FROM tb_clientes WHERE flag_excluido_logico = 1 AND data_reter_ate IS NOT NULL AND data_reter_ate < CURDATE();
  -- Logs de exclusão podem ser mantidos separadamente se necessário.
END$$
DELIMITER ;

/* ====================================================================
   PSEUDOCÓDIGO / RECOMENDAÇÕES DE IMPLEMENTAÇÃO (camada aplicação)
   ==================================================================== */

-- 1) Geração de cpf_hash (HMAC-SHA256)
-- Pseudocódigo (nodejs/python-like):
-- chave_hmac = get_kms_secret("hmac_key_empresa_<id_empresa>")
-- cpf_limpo = only_digits(cpf_input)
-- cpf_hash = HMAC_SHA256(chave_hmac, cpf_limpo).hexdigest()
-- Salvar cpf_hash no DB; salvar cpf_encrypted usando KMS (ver abaixo)

-- 2) Encriptação do CPF (AES via KMS)
-- plaintext = cpf_input
-- encrypted = KMS.encrypt(plaintext, key_id)
-- salvar encrypted.bytes em cpf_encrypted (VARBINARY)

-- 3) Mascaramento em UI (exemplo):
-- mostrarCPF = '***.***.' + last3digits(cpf)

-- 4) Fluxo de consentimento (exemplo):
-- Quando cliente aceitar marketing:
--   inserir registro em tb_consentimentos (concedido = 'sim', versao = 'v1.0')
--   atualizar tb_clientes.consent_marketing = 'sim' e id_consentimento = <id>

-- 5) Acesso / logs:
--   - toda leitura de dados sensíveis deve registrar uma linha em tb_clientes_audit com acao='READ'
--   - campos_afetados deve conter somente os nomes dos campos lidos (ex: "email,telefone")

-- 6) Gerenciamento de chaves:
--   - utilizar KMS (ex: AWS KMS, Azure Key Vault, HashiCorp Vault)
--   - não armazenar chaves no código fonte
--   - rotacionar chaves periodicamente e suportar re-encrypt via job

/* ====================================================================
   NOTAS FINAIS E CHECKLIST
   ==================================================================== */

-- 1) Revisar permissões de banco: separar usuário de leitura e usuário de escrita para processos e APIs.
-- 2) Verificar backup cifrado e acesso restrito aos backups.
-- 3) Testar procedimentos de exclusão/pseudonimização em ambiente de homologação antes de produção.
-- 4) Documentar fluxos para atendimento a solicitações de titulares (retificação, portabilidade, exclusão).
-- 5) Se desejar, posso incluir aqui scripts para migrar CPFs existentes para o modelo (gerar cpf_hash e mover CPF para campo encriptado) — será necessário ter a chave KMS disponível.

-- FIM DO ARQUIVO
