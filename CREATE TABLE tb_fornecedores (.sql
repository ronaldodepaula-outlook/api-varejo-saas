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

CREATE TABLE tb_movimentacoes (
    id_movimentacao INT AUTO_INCREMENT PRIMARY KEY,
    id_empresa INT NOT NULL,
    id_filial INT NOT NULL,
    id_produto INT NOT NULL,
    tipo_movimentacao ENUM('entrada','saida','transferencia','ajuste') NOT NULL,
    origem ENUM('nota_fiscal','manual','transferencia','inventario') DEFAULT 'manual',
    id_referencia INT NULL, -- pode guardar o id_nota ou id_transferencia
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


CREATE TABLE tb_transferencias (
    id_transferencia INT AUTO_INCREMENT PRIMARY KEY,
    id_empresa INT NOT NULL,
    id_filial_origem INT NOT NULL,
    id_filial_destino INT NOT NULL,
    id_produto INT NOT NULL,
    quantidade DECIMAL(10,2) NOT NULL,
    status ENUM('pendente','enviada','recebida','cancelada') DEFAULT 'pendente',
    data_transferencia TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    observacao TEXT,
    FOREIGN KEY (id_empresa) REFERENCES tb_empresas(id_empresa),
    FOREIGN KEY (id_filial_origem) REFERENCES tb_filiais(id_filial),
    FOREIGN KEY (id_filial_destino) REFERENCES tb_filiais(id_filial),
    FOREIGN KEY (id_produto) REFERENCES tb_produtos(id_produto)
);

CREATE TABLE tb_inventario (
    id_inventario INT AUTO_INCREMENT PRIMARY KEY,
    id_empresa INT NOT NULL,
    id_filial INT NOT NULL,
    id_produto INT NOT NULL,
    quantidade_fisica DECIMAL(10,2) NOT NULL,
    quantidade_sistema DECIMAL(10,2) NOT NULL,
    diferenca DECIMAL(10,2) GENERATED ALWAYS AS (quantidade_fisica - quantidade_sistema) STORED,
    motivo VARCHAR(255),
    data_inventario TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    id_usuario INT NULL,
    FOREIGN KEY (id_empresa) REFERENCES tb_empresas(id_empresa),
    FOREIGN KEY (id_filial) REFERENCES tb_filiais(id_filial),
    FOREIGN KEY (id_produto) REFERENCES tb_produtos(id_produto),
    FOREIGN KEY (id_usuario) REFERENCES tb_usuarios(id_usuario)
);