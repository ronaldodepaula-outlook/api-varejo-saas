-- ==========================================
-- TABELA: Fornecedores
-- ==========================================
CREATE TABLE tb_fornecedores (
    id_fornecedor INT AUTO_INCREMENT PRIMARY KEY,
    id_empresa INT NOT NULL,
    razao_social VARCHAR(150) NOT NULL,
    nome_fantasia VARCHAR(150),
    cnpj VARCHAR(20) UNIQUE,
    inscricao_estadual VARCHAR(30),
    contato VARCHAR(100) NULL,
    telefone VARCHAR(20) NULL,
    email VARCHAR(100) NULL,
    endereco VARCHAR(255) NULL,
    cidade VARCHAR(100) NULL,
    estado VARCHAR(2) NULL,
    cep VARCHAR(10) NULL,
    status ENUM('ativo','inativo') DEFAULT 'ativo',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_empresa) REFERENCES tb_empresas(id_empresa)
);

-- ==========================================
-- TABELA: Notas Fiscais
-- ==========================================
CREATE TABLE tb_notas_fiscais (
    id_nota INT AUTO_INCREMENT PRIMARY KEY,
    id_empresa INT NOT NULL,
    id_filial INT NOT NULL,
    id_fornecedor INT NOT NULL,
    numero_nota VARCHAR(50) NOT NULL,
    serie VARCHAR(10),
    chave_acesso VARCHAR(60) UNIQUE,
    data_emissao DATE,
    data_entrada DATE,
    valor_total DECIMAL(12,2) DEFAULT 0,
    status ENUM('aberta','fechada','cancelada') DEFAULT 'aberta',
    observacao TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_empresa) REFERENCES tb_empresas(id_empresa),
    FOREIGN KEY (id_filial) REFERENCES tb_filiais(id_filial),
    FOREIGN KEY (id_fornecedor) REFERENCES tb_fornecedores(id_fornecedor)
);

-- ==========================================
-- TABELA: Itens da Nota Fiscal
-- ==========================================
CREATE TABLE tb_itens_nota (
    id_item INT AUTO_INCREMENT PRIMARY KEY,
    id_nota INT NOT NULL,
    id_empresa INT NOT NULL,
    id_filial INT NOT NULL,
    id_produto INT NOT NULL,
    quantidade DECIMAL(10,2) NOT NULL,
    valor_unitario DECIMAL(10,2) DEFAULT 0,
    valor_total DECIMAL(12,2) GENERATED ALWAYS AS (quantidade * valor_unitario) STORED,
    cst VARCHAR(10),
    cfop VARCHAR(10),
    ncm VARCHAR(10),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_nota) REFERENCES tb_notas_fiscais(id_nota),
    FOREIGN KEY (id_empresa) REFERENCES tb_empresas(id_empresa),
    FOREIGN KEY (id_filial) REFERENCES tb_filiais(id_filial),
    FOREIGN KEY (id_produto) REFERENCES tb_produtos(id_produto)
);

-- ==========================================
-- TABELA: Movimentações de Estoque
-- ==========================================
CREATE TABLE tb_movimentacoes (
    id_movimentacao INT AUTO_INCREMENT PRIMARY KEY,
    id_empresa INT NOT NULL,
    id_filial INT NOT NULL,
    id_produto INT NOT NULL,
    tipo_movimentacao ENUM('entrada','saida','transferencia','ajuste') NOT NULL,
    origem ENUM('nota_fiscal','manual','transferencia','inventario') DEFAULT 'manual',
    id_referencia INT NULL, -- pode guardar o id_nota, id_transferencia ou id_inventario
    quantidade DECIMAL(10,2) NOT NULL,
    custo_unitario DECIMAL(10,2) DEFAULT 0,
    observacao VARCHAR(255),
    id_usuario INT NULL,
    data_movimentacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_empresa) REFERENCES tb_empresas(id_empresa),
    FOREIGN KEY (id_filial) REFERENCES tb_filiais(id_filial),
    FOREIGN KEY (id_produto) REFERENCES tb_produtos(id_produto),
    FOREIGN KEY (id_usuario) REFERENCES tb_usuarios(id_usuario)
);

-- ==========================================
-- TABELA: Estoque por Filial
-- ==========================================
CREATE TABLE tb_estoque (
    id_estoque INT AUTO_INCREMENT PRIMARY KEY,
    id_empresa INT NOT NULL,
    id_filial INT NOT NULL,
    id_produto INT NOT NULL,
    quantidade DECIMAL(10,2) DEFAULT 0,
    estoque_minimo DECIMAL(10,2) DEFAULT 0,
    estoque_maximo DECIMAL(10,2) DEFAULT 0,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_empresa) REFERENCES tb_empresas(id_empresa),
    FOREIGN KEY (id_filial) REFERENCES tb_filiais(id_filial),
    FOREIGN KEY (id_produto) REFERENCES tb_produtos(id_produto)
);

-- ==========================================
-- TABELA: Capa de Transferência
-- ==========================================
CREATE TABLE tb_capa_transferencia (
    id_capa_transferencia INT AUTO_INCREMENT PRIMARY KEY,
    id_empresa INT NOT NULL,
    id_filial_origem INT NOT NULL,
    id_filial_destino INT NOT NULL,
    data_transferencia TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pendente','enviada','recebida','cancelada') DEFAULT 'pendente',
    observacao TEXT,
    id_usuario INT NULL,
    FOREIGN KEY (id_empresa) REFERENCES tb_empresas(id_empresa),
    FOREIGN KEY (id_filial_origem) REFERENCES tb_filiais(id_filial),
    FOREIGN KEY (id_filial_destino) REFERENCES tb_filiais(id_filial),
    FOREIGN KEY (id_usuario) REFERENCES tb_usuarios(id_usuario)
);

-- ==========================================
-- TABELA: Itens da Transferência
-- ==========================================
CREATE TABLE tb_transferencias (
    id_transferencia INT AUTO_INCREMENT PRIMARY KEY,
    id_capa_transferencia INT NOT NULL,
    id_empresa INT NOT NULL,
    id_filial_origem INT NOT NULL,
    id_filial_destino INT NOT NULL,
    id_produto INT NOT NULL,
    quantidade DECIMAL(10,2) NOT NULL,
    status ENUM('pendente','enviada','recebida','cancelada') DEFAULT 'pendente',
    data_transferencia TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    observacao TEXT,
    FOREIGN KEY (id_capa_transferencia) REFERENCES tb_capa_transferencia(id_capa_transferencia),
    FOREIGN KEY (id_empresa) REFERENCES tb_empresas(id_empresa),
    FOREIGN KEY (id_filial_origem) REFERENCES tb_filiais(id_filial),
    FOREIGN KEY (id_filial_destino) REFERENCES tb_filiais(id_filial),
    FOREIGN KEY (id_produto) REFERENCES tb_produtos(id_produto)
);

-- ==========================================
-- TABELA: Capa de Inventário
-- ==========================================
CREATE TABLE tb_capa_inventario (
    id_capa_inventario INT AUTO_INCREMENT PRIMARY KEY,
    id_empresa INT NOT NULL,
    id_filial INT NOT NULL,
    descricao VARCHAR(150) NULL,
    data_inicio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_fechamento TIMESTAMP NULL,
    status ENUM('em_andamento','concluido','cancelado') DEFAULT 'em_andamento',
    observacao TEXT,
    id_usuario INT NULL,
    FOREIGN KEY (id_empresa) REFERENCES tb_empresas(id_empresa),
    FOREIGN KEY (id_filial) REFERENCES tb_filiais(id_filial),
    FOREIGN KEY (id_usuario) REFERENCES tb_usuarios(id_usuario)
);

-- ==========================================
-- TABELA: Itens do Inventário
-- ==========================================
CREATE TABLE tb_inventario (
    id_inventario INT AUTO_INCREMENT PRIMARY KEY,
    id_capa_inventario INT NOT NULL,
    id_empresa INT NOT NULL,
    id_filial INT NOT NULL,
    id_produto INT NOT NULL,
    quantidade_fisica DECIMAL(10,2) NOT NULL,
    quantidade_sistema DECIMAL(10,2) NOT NULL,
    diferenca DECIMAL(10,2) GENERATED ALWAYS AS (quantidade_fisica - quantidade_sistema) STORED,
    motivo VARCHAR(255),
    data_inventario TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    id_usuario INT NULL,
    FOREIGN KEY (id_capa_inventario) REFERENCES tb_capa_inventario(id_capa_inventario),
    FOREIGN KEY (id_empresa) REFERENCES tb_empresas(id_empresa),
    FOREIGN KEY (id_filial) REFERENCES tb_filiais(id_filial),
    FOREIGN KEY (id_produto) REFERENCES tb_produtos(id_produto),
    FOREIGN KEY (id_usuario) REFERENCES tb_usuarios(id_usuario)
);


DELIMITER $$

CREATE TRIGGER trg_itens_nota_entrada
AFTER INSERT ON tb_itens_nota
FOR EACH ROW
BEGIN
    -- Verifica se já existe registro do produto na filial
    IF EXISTS (
        SELECT 1 FROM tb_estoque 
        WHERE id_empresa = NEW.id_empresa 
          AND id_filial = NEW.id_filial 
          AND id_produto = NEW.id_produto
    ) THEN
        UPDATE tb_estoque
        SET quantidade = quantidade + NEW.quantidade,
            updated_at = CURRENT_TIMESTAMP
        WHERE id_empresa = NEW.id_empresa 
          AND id_filial = NEW.id_filial 
          AND id_produto = NEW.id_produto;
    ELSE
        INSERT INTO tb_estoque (id_empresa, id_filial, id_produto, quantidade)
        VALUES (NEW.id_empresa, NEW.id_filial, NEW.id_produto, NEW.quantidade);
    END IF;

    -- Registra a movimentação
    INSERT INTO tb_movimentacoes (
        id_empresa, id_filial, id_produto, tipo_movimentacao,
        origem, id_referencia, quantidade, custo_unitario, observacao
    )
    VALUES (
        NEW.id_empresa, NEW.id_filial, NEW.id_produto, 'entrada',
        'nota_fiscal', NEW.id_nota, NEW.quantidade, NEW.valor_unitario, 'Entrada de NF automática'
    );
END$$

DELIMITER ;


DELIMITER $$

CREATE TRIGGER trg_transferencia_movimento
AFTER INSERT ON tb_transferencias
FOR EACH ROW
BEGIN
    -- 🔸 Diminui da filial de origem
    UPDATE tb_estoque
    SET quantidade = quantidade - NEW.quantidade,
        updated_at = CURRENT_TIMESTAMP
    WHERE id_empresa = NEW.id_empresa
      AND id_filial = NEW.id_filial_origem
      AND id_produto = NEW.id_produto;

    -- 🔸 Aumenta na filial de destino
    IF EXISTS (
        SELECT 1 FROM tb_estoque 
        WHERE id_empresa = NEW.id_empresa 
          AND id_filial = NEW.id_filial_destino 
          AND id_produto = NEW.id_produto
    ) THEN
        UPDATE tb_estoque
        SET quantidade = quantidade + NEW.quantidade,
            updated_at = CURRENT_TIMESTAMP
        WHERE id_empresa = NEW.id_empresa
          AND id_filial = NEW.id_filial_destino
          AND id_produto = NEW.id_produto;
    ELSE
        INSERT INTO tb_estoque (id_empresa, id_filial, id_produto, quantidade)
        VALUES (NEW.id_empresa, NEW.id_filial_destino, NEW.id_produto, NEW.quantidade);
    END IF;

    -- 🔸 Registra movimentações nas duas filiais
    INSERT INTO tb_movimentacoes (
        id_empresa, id_filial, id_produto, tipo_movimentacao,
        origem, id_referencia, quantidade, observacao
    ) VALUES (
        NEW.id_empresa, NEW.id_filial_origem, NEW.id_produto,
        'saida', 'transferencia', NEW.id_transferencia,
        NEW.quantidade, 'Saída por transferência'
    );

    INSERT INTO tb_movimentacoes (
        id_empresa, id_filial, id_produto, tipo_movimentacao,
        origem, id_referencia, quantidade, observacao
    ) VALUES (
        NEW.id_empresa, NEW.id_filial_destino, NEW.id_produto,
        'entrada', 'transferencia', NEW.id_transferencia,
        NEW.quantidade, 'Entrada por transferência'
    );
END$$

DELIMITER ;


DELIMITER $$

CREATE TRIGGER trg_inventario_ajuste
AFTER INSERT ON tb_inventario
FOR EACH ROW
BEGIN
    -- Atualiza o estoque de acordo com a contagem física
    UPDATE tb_estoque
    SET quantidade = NEW.quantidade_fisica,
        updated_at = CURRENT_TIMESTAMP
    WHERE id_empresa = NEW.id_empresa
      AND id_filial = NEW.id_filial
      AND id_produto = NEW.id_produto;

    -- Registra movimentação de ajuste
    INSERT INTO tb_movimentacoes (
        id_empresa, id_filial, id_produto, tipo_movimentacao,
        origem, id_referencia, quantidade, observacao
    )
    VALUES (
        NEW.id_empresa, NEW.id_filial, NEW.id_produto, 'ajuste',
        'inventario', NEW.id_inventario, NEW.diferenca, 'Ajuste de inventário'
    );
END$$

DELIMITER ;


DELIMITER $$

CREATE TRIGGER trg_movimentacao_manual
AFTER INSERT ON tb_movimentacoes
FOR EACH ROW
BEGIN
    IF NEW.origem = 'manual' THEN
        IF NEW.tipo_movimentacao = 'saida' THEN
            UPDATE tb_estoque
            SET quantidade = quantidade - NEW.quantidade,
                updated_at = CURRENT_TIMESTAMP
            WHERE id_empresa = NEW.id_empresa
              AND id_filial = NEW.id_filial
              AND id_produto = NEW.id_produto;
        ELSEIF NEW.tipo_movimentacao = 'entrada' THEN
            UPDATE tb_estoque
            SET quantidade = quantidade + NEW.quantidade,
                updated_at = CURRENT_TIMESTAMP
            WHERE id_empresa = NEW.id_empresa
              AND id_filial = NEW.id_filial
              AND id_produto = NEW.id_produto;
        END IF;
    END IF;
END$$

DELIMITER ;

DELIMITER $$

CREATE TRIGGER trg_produto_cria_estoque
AFTER INSERT ON tb_produtos
FOR EACH ROW
BEGIN
    DECLARE v_id_filial_matriz INT;

    -- 🏢 Busca a filial marcada como MATRIZ da empresa
    SELECT id_filial
    INTO v_id_filial_matriz
    FROM tb_filiais
    WHERE id_empresa = NEW.id_empresa
      AND tipo_filial = 'matriz'
    LIMIT 1;

    -- 🧱 Se encontrar a matriz, cria o registro de estoque
    IF v_id_filial_matriz IS NOT NULL THEN
        INSERT INTO tb_estoque (
            id_empresa,
            id_filial,
            id_produto,
            quantidade,
            estoque_minimo,
            estoque_maximo,
            updated_at
        ) VALUES (
            NEW.id_empresa,
            v_id_filial_matriz,
            NEW.id_produto,
            0,      -- quantidade inicial
            5,      -- estoque mínimo padrão
            20,     -- estoque máximo padrão
            CURRENT_TIMESTAMP
        );
    END IF;
END$$

DELIMITER ;



-- =========================================================
-- 🔄 REMOVE A TRIGGER EXISTENTE SE ELA JÁ EXISTIR
-- =========================================================
DROP TRIGGER IF EXISTS trg_produto_cria_estoque;
DELIMITER $$

CREATE TRIGGER trg_produto_cria_estoque
AFTER INSERT ON tb_produtos
FOR EACH ROW
BEGIN
    DECLARE v_id_filial_matriz INT;

    -- 🏢 1️⃣ Busca a filial matriz da empresa
    SELECT id_filial
    INTO v_id_filial_matriz
    FROM tb_filiais
    WHERE id_empresa = NEW.id_empresa
      AND tipo_filial = 'matriz'
    LIMIT 1;

    -- 🧱 2️⃣ Se não existir matriz, define a primeira filial como matriz
    IF v_id_filial_matriz IS NULL THEN
        SELECT id_filial
        INTO v_id_filial_matriz
        FROM tb_filiais
        WHERE id_empresa = NEW.id_empresa
        ORDER BY id_filial ASC
        LIMIT 1;

        -- Se encontrou uma filial, define como matriz
        IF v_id_filial_matriz IS NOT NULL THEN
            UPDATE tb_filiais
            SET tipo_filial = 'matriz'
            WHERE id_filial = v_id_filial_matriz;
        END IF;
    END IF;

    -- 🧾 3️⃣ Se houver filial matriz (nova ou existente), cria o registro no estoque
    IF v_id_filial_matriz IS NOT NULL THEN
        INSERT INTO tb_estoque (
            id_empresa,
            id_filial,
            id_produto,
            quantidade,
            estoque_minimo,
            estoque_maximo,
            updated_at
        ) VALUES (
            NEW.id_empresa,
            v_id_filial_matriz,
            NEW.id_produto,
            0,      -- quantidade inicial
            5,      -- estoque mínimo padrão
            20,     -- estoque máximo padrão
            CURRENT_TIMESTAMP
        );
    END IF;
END$$

DELIMITER ;


