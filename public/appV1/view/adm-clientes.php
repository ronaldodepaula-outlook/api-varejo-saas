<?php
session_start();
if (!isset($_SESSION['authToken'], $_SESSION['usuario'], $_SESSION['empresa'], $_SESSION['licenca'])) {
    header('Location: login.php');
    exit;
}

// Verificar e converter o tipo de dados das variáveis de sessão
$usuario = is_array($_SESSION['usuario']) ? $_SESSION['usuario'] : ['nome' => $_SESSION['usuario']];
$empresa = $_SESSION['empresa'];
$id_empresa = $_SESSION['empresa_id'];
$licenca = $_SESSION['licenca'];
$token = $_SESSION['authToken'];
$segmento = $_SESSION['segmento'] ?? '';

$id_usuario = $_SESSION['user_id'];

// Extrair nome do usuário de forma segura
$nomeUsuario = '';
if (is_array($usuario)) {
    $nomeUsuario = $usuario['nome'] ?? $usuario['name'] ?? 'Usuário';
} else {
    $nomeUsuario = (string)$usuario;
}

// Primeira letra para o avatar
$inicialUsuario = strtoupper(substr($nomeUsuario, 0, 1));

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Clientes - CRM</title>
    <!-- Bootstrap CSS -->
    <style>
        :root {
            --primary-color: #3498DB;
            --secondary-color: #2C3E50;
            --success-color: #27AE60;
            --warning-color: #F39C12;
            --danger-color: #E74C3C;
            --info-color: #17A2B8;
            --light-color: #ECF0F1;
        }
        
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .main-content {
            margin-left: 250px;
            padding: 20px;
            transition: all 0.3s;
        }
        
        .main-header {
            background: white;
            padding: 15px 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .breadcrumb-custom {
            background: transparent;
            margin-bottom: 0;
        }
        
        .header-right {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            cursor: pointer;
        }
        
        .card-custom {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            overflow: hidden;
        }
        
        .card-header-custom {
            padding: 15px 20px;
            border-bottom: 1px solid #eee;
            background: rgba(248, 249, 250, 0.8);
        }
        
        .card-body {
            padding: 20px;
        }
        
        .page-title {
            font-weight: 700;
            color: var(--secondary-color);
            margin-bottom: 5px;
        }
        
        .page-subtitle {
            color: #6c757d;
            margin-bottom: 0;
        }
        
        .table-custom th {
            border-top: none;
            font-weight: 600;
            color: #6c757d;
            font-size: 0.85rem;
            text-transform: uppercase;
        }
        
        .btn-action {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            margin: 0 2px;
        }
        
        .search-box {
            position: relative;
        }
        
        .search-box .form-control {
            padding-left: 40px;
        }
        
        .search-box .bi-search {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }
        
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }
        
        .spinner-border {
            width: 3rem;
            height: 3rem;
        }
        
        .status-badge {
            padding: 4px 8px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .status-ativo { background: rgba(39, 174, 96, 0.1); color: var(--success-color); }
        .status-inativo { background: rgba(231, 76, 60, 0.1); color: var(--danger-color); }
        .status-bloqueado { background: rgba(243, 156, 18, 0.1); color: var(--warning-color); }
        
        .classificacao-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        
        .classificacao-diamante { background: linear-gradient(135deg, #e0e0e0, #b9f2ff); color: #00b7eb; }
        .classificacao-ouro { background: linear-gradient(135deg, #ffd700, #ffed4e); color: #b8860b; }
        .classificacao-prata { background: linear-gradient(135deg, #c0c0c0, #e8e8e8); color: #808080; }
        .classificacao-bronze { background: linear-gradient(135deg, #cd7f32, #e99d61); color: #8c531b; }
        
        .customer-card {
            transition: all 0.3s ease;
            border-left: 4px solid #dee2e6;
        }
        .customer-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .contact-info {
            font-size: 0.875rem;
        }
        
        .stats-card {
            transition: all 0.3s ease;
        }
        .stats-card:hover {
            transform: translateY(-2px);
        }
        
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <main class="main-content">
        <!-- Header -->
        <header class="main-header">
            <div class="header-left">
                <button class="sidebar-toggle" type="button">
                    <i class="bi bi-list"></i>
                </button>
                
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-custom">
                        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Gestão de Clientes - CRM</li>
                    </ol>
                </nav>
            </div>
            
            <div class="header-right">
                <!-- Notificações -->
                <div class="dropdown">
                    <button class="btn btn-link text-dark position-relative" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-bell" style="font-size: 1.25rem;"></i>
                        <span class="badge bg-danger position-absolute translate-middle rounded-pill" style="font-size: 0.6rem; top: 8px; right: 8px;">3</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><h6 class="dropdown-header">Alertas de Clientes</h6></li>
                        <li><a class="dropdown-item" href="#">2 clientes inativos</a></li>
                        <li><a class="dropdown-item" href="#">1 cliente com débito vencido</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-center" href="#">Ver todos</a></li>
                    </ul>
                </div>
                
                <!-- Dropdown do Usuário -->
                <div class="dropdown user-dropdown">
                    <div class="user-avatar" data-bs-toggle="dropdown" aria-expanded="false">
                        <?php echo $inicialUsuario; ?>
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><h6 class="dropdown-header user-name"><?php echo htmlspecialchars($nomeUsuario); ?></h6></li>
                        <li><small class="dropdown-header text-muted user-email">
                            <?php 
                            if (is_array($usuario)) {
                                echo htmlspecialchars($usuario['email'] ?? $usuario['email_empresa'] ?? '');
                            } else {
                                echo htmlspecialchars($usuario);
                            }
                            ?>
                        </small></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="perfil.php"><i class="bi bi-person me-2"></i>Meu Perfil</a></li>
                        <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Configurações</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="#" id="logoutBtn"><i class="bi bi-box-arrow-right me-2"></i>Sair</a></li>
                    </ul>
                </div>
            </div>
        </header>
        
        <!-- Área de Conteúdo -->
        <div class="content-area">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="page-title">Gestão de Clientes - CRM</h1>
                    <p class="page-subtitle">Cadastre, gerencie e acompanhe seus clientes</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary" onclick="exportarRelatorioClientes()">
                        <i class="bi bi-file-earmark-text me-2"></i>Relatório
                    </button>
                    <button class="btn btn-primary" onclick="abrirModalNovoCliente()">
                        <i class="bi bi-plus-circle me-2"></i>Novo Cliente
                    </button>
                </div>
            </div>
            
            <!-- Resumo de Clientes -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card-custom stats-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="text-primary mb-0" id="totalClientesResumo">0</h5>
                                    <p class="text-muted mb-0">Total de Clientes</p>
                                </div>
                                <div class="bg-primary text-white rounded p-3">
                                    <i class="bi bi-people" style="font-size: 1.5rem;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card-custom stats-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="text-success mb-0" id="clientesAtivosResumo">0</h5>
                                    <p class="text-muted mb-0">Clientes Ativos</p>
                                </div>
                                <div class="bg-success text-white rounded p-3">
                                    <i class="bi bi-person-check" style="font-size: 1.5rem;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card-custom stats-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="text-warning mb-0" id="clientesInativosResumo">0</h5>
                                    <p class="text-muted mb-0">Clientes Inativos</p>
                                </div>
                                <div class="bg-warning text-white rounded p-3">
                                    <i class="bi bi-person-x" style="font-size: 1.5rem;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card-custom stats-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="text-info mb-0" id="clientesComDebitoResumo">0</h5>
                                    <p class="text-muted mb-0">Com Débitos</p>
                                </div>
                                <div class="bg-info text-white rounded p-3">
                                    <i class="bi bi-credit-card" style="font-size: 1.5rem;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Filtros e Busca -->
            <div class="card-custom mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="search-box">
                                <i class="bi bi-search"></i>
                                <input type="text" class="form-control" id="searchInput" placeholder="Buscar por nome, email, telefone...">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" id="filterStatus">
                                <option value="">Todos os status</option>
                                <option value="ativo">Ativo</option>
                                <option value="inativo">Inativo</option>
                                <option value="bloqueado">Bloqueado</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" id="filterClassificacao">
                                <option value="">Todas classificações</option>
                                <option value="diamante">Diamante</option>
                                <option value="ouro">Ouro</option>
                                <option value="prata">Prata</option>
                                <option value="bronze">Bronze</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" id="filterCidade">
                                <option value="">Todas cidades</option>
                                <!-- Cidades serão carregadas dinamicamente -->
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button class="btn btn-outline-secondary w-100" onclick="limparFiltros()">
                                <i class="bi bi-arrow-clockwise"></i> Limpar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Lista de Clientes -->
            <div class="row" id="listaClientes">
                <!-- Os cards de clientes serão carregados dinamicamente aqui -->
                <div class="col-12 text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Carregando...</span>
                    </div>
                    <p class="mt-2 text-muted">Carregando clientes...</p>
                </div>
            </div>
            
            <!-- Paginação -->
            <nav aria-label="Navegação de páginas" id="paginationContainer" class="mt-4">
                <ul class="pagination pagination-custom justify-content-center">
                    <!-- A paginação será gerada dinamicamente via JavaScript -->
                </ul>
            </nav>
        </div>
    </main>

    <!-- Modal para Novo Cliente -->
    <div class="modal fade" id="modalNovoCliente" tabindex="-1" aria-labelledby="modalNovoClienteLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalNovoClienteLabel">Novo Cliente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formNovoCliente">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nome Completo *</label>
                                <input type="text" class="form-control" id="nomeCliente" placeholder="Nome do cliente" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" id="emailCliente" placeholder="email@exemplo.com">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Telefone *</label>
                                <input type="text" class="form-control" id="telefoneCliente" placeholder="(11) 99999-9999" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">WhatsApp</label>
                                <input type="text" class="form-control" id="whatsappCliente" placeholder="(11) 99999-9999">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tipo de Documento</label>
                                <select class="form-select" id="tipoDocumentoCliente">
                                    <option value="cpf">CPF</option>
                                    <option value="cnpj">CNPJ</option>
                                    <option value="rg">RG</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Número do Documento</label>
                                <input type="text" class="form-control" id="documentoCliente" placeholder="000.000.000-00">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Endereço</label>
                                <input type="text" class="form-control" id="enderecoCliente" placeholder="Rua, número, bairro">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Cidade</label>
                                <input type="text" class="form-control" id="cidadeCliente" placeholder="Cidade">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Estado</label>
                                <select class="form-select" id="estadoCliente">
                                    <option value="">Selecione...</option>
                                    <option value="AC">AC</option>
                                    <option value="AL">AL</option>
                                    <option value="AP">AP</option>
                                    <option value="AM">AM</option>
                                    <option value="BA">BA</option>
                                    <option value="CE">CE</option>
                                    <option value="DF">DF</option>
                                    <option value="ES">ES</option>
                                    <option value="GO">GO</option>
                                    <option value="MA">MA</option>
                                    <option value="MT">MT</option>
                                    <option value="MS">MS</option>
                                    <option value="MG">MG</option>
                                    <option value="PA">PA</option>
                                    <option value="PB">PB</option>
                                    <option value="PR">PR</option>
                                    <option value="PE">PE</option>
                                    <option value="PI">PI</option>
                                    <option value="RJ">RJ</option>
                                    <option value="RN">RN</option>
                                    <option value="RS">RS</option>
                                    <option value="RO">RO</option>
                                    <option value="RR">RR</option>
                                    <option value="SC">SC</option>
                                    <option value="SP">SP</option>
                                    <option value="SE">SE</option>
                                    <option value="TO">TO</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">CEP</label>
                                <input type="text" class="form-control" id="cepCliente" placeholder="00000-000">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Classificação</label>
                                <select class="form-select" id="classificacaoCliente">
                                    <option value="bronze">Bronze</option>
                                    <option value="prata">Prata</option>
                                    <option value="ouro">Ouro</option>
                                    <option value="diamante">Diamante</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <select class="form-select" id="statusCliente">
                                    <option value="ativo">Ativo</option>
                                    <option value="inativo">Inativo</option>
                                    <option value="bloqueado">Bloqueado</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="consentimentoMarketing">
                                    <label class="form-check-label" for="consentimentoMarketing">
                                        Cliente consentiu com marketing
                                    </label>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Observações</label>
                                <textarea class="form-control" id="observacoesCliente" rows="3" placeholder="Observações sobre o cliente..."></textarea>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="salvarCliente()">Salvar Cliente</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Detalhes do Cliente -->
    <div class="modal fade" id="modalDetalhesCliente" tabindex="-1" aria-labelledby="modalDetalhesClienteLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalDetalhesClienteLabel">Detalhes do Cliente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="detalhesClienteCarregando" class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Carregando...</span>
                        </div>
                        <p class="mt-2">Carregando detalhes do cliente...</p>
                    </div>
                    
                    <div id="detalhesClienteConteudo" style="display: none;">
                        <!-- Abas de Navegação -->
                        <ul class="nav nav-tabs" id="clienteTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" type="button" role="tab">Informações</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="vendas-tab" data-bs-toggle="tab" data-bs-target="#vendas" type="button" role="tab">Vendas</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="debitos-tab" data-bs-toggle="tab" data-bs-target="#debitos" type="button" role="tab">Débitos</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="historico-tab" data-bs-toggle="tab" data-bs-target="#historico" type="button" role="tab">Histórico</button>
                            </li>
                        </ul>
                        
                        <div class="tab-content p-3" id="clienteTabContent">
                            <!-- Aba Informações -->
                            <div class="tab-pane fade show active" id="info" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-8">
                                        <h4 id="clienteNome"></h4>
                                        <div class="row g-3 mt-3">
                                            <div class="col-md-6">
                                                <small class="text-muted">Email:</small>
                                                <div><strong id="clienteEmail"></strong></div>
                                            </div>
                                            <div class="col-md-6">
                                                <small class="text-muted">Telefone:</small>
                                                <div><strong id="clienteTelefone"></strong></div>
                                            </div>
                                            <div class="col-md-6">
                                                <small class="text-muted">WhatsApp:</small>
                                                <div><strong id="clienteWhatsapp"></strong></div>
                                            </div>
                                            <div class="col-md-6">
                                                <small class="text-muted">Documento:</small>
                                                <div><strong id="clienteDocumento"></strong></div>
                                            </div>
                                            <div class="col-12">
                                                <small class="text-muted">Endereço:</small>
                                                <div><strong id="clienteEndereco"></strong></div>
                                            </div>
                                            <div class="col-md-4">
                                                <small class="text-muted">Cidade:</small>
                                                <div><strong id="clienteCidade"></strong></div>
                                            </div>
                                            <div class="col-md-4">
                                                <small class="text-muted">Estado:</small>
                                                <div><strong id="clienteEstado"></strong></div>
                                            </div>
                                            <div class="col-md-4">
                                                <small class="text-muted">CEP:</small>
                                                <div><strong id="clienteCep"></strong></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card">
                                            <div class="card-body text-center">
                                                <div class="mb-3">
                                                    <span class="classificacao-badge" id="clienteClassificacaoBadge">Bronze</span>
                                                </div>
                                                <div class="mb-3">
                                                    <span class="status-badge" id="clienteStatusBadge">Ativo</span>
                                                </div>
                                                <div class="mb-3">
                                                    <small class="text-muted">Data de Cadastro</small>
                                                    <div><strong id="clienteDataCadastro"></strong></div>
                                                </div>
                                                <div class="mb-3">
                                                    <small class="text-muted">Total em Vendas</small>
                                                    <div><strong id="clienteTotalVendas"></strong></div>
                                                </div>
                                                <div class="mb-3">
                                                    <small class="text-muted">Débitos Pendentes</small>
                                                    <div><strong id="clienteDebitosPendentes"></strong></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <small class="text-muted">Observações:</small>
                                    <div><strong id="clienteObservacoes"></strong></div>
                                </div>
                            </div>
                            
                            <!-- Aba Vendas -->
                            <div class="tab-pane fade" id="vendas" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Nº Venda</th>
                                                <th>Data</th>
                                                <th>Valor Total</th>
                                                <th>Forma Pagamento</th>
                                                <th>Status</th>
                                                <th>Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbodyVendasCliente">
                                            <!-- Vendas serão carregadas dinamicamente -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <!-- Aba Débitos -->
                            <div class="tab-pane fade" id="debitos" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Nº Débito</th>
                                                <th>Descrição</th>
                                                <th>Valor</th>
                                                <th>Vencimento</th>
                                                <th>Status</th>
                                                <th>Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbodyDebitosCliente">
                                            <!-- Débitos serão carregadas dinamicamente -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <!-- Aba Histórico -->
                            <div class="tab-pane fade" id="historico" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Data</th>
                                                <th>Ação</th>
                                                <th>Descrição</th>
                                                <th>Usuário</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbodyHistoricoCliente">
                                            <!-- Histórico será carregado dinamicamente -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    <button type="button" class="btn btn-primary" onclick="editarCliente()">Editar Cliente</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div class="loading-overlay d-none" id="loadingOverlay">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Carregando...</span>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Variáveis globais
        const idEmpresa = <?php echo $id_empresa; ?>;
        const token = '<?php echo $token; ?>';
        const idUsuario = <?php echo $id_usuario; ?>;
        const BASE_URL = 'https://rdpsolutions.online/saas-multiempresas/public';
        
        let clientes = [];
        let clienteSelecionado = null;
        let modalNovoCliente = null;
        let modalDetalhesCliente = null;

        // Configuração da API
        const API_CONFIG = {
            // Endpoints para Clientes
            CLIENTES_LISTAR: () => 
                `${BASE_URL}/api/vendasAssistidas/clientes/empresa/${idEmpresa}`,
            
            CLIENTE_CADASTRAR: () => 
                `${BASE_URL}/api/vendasAssistidas/clientes`,
            
            CLIENTE_DETALHES: (idCliente) => 
                `${BASE_URL}/api/vendasAssistidas/clientes/${idCliente}`,
            
            CLIENTE_ATUALIZAR: (idCliente) => 
                `${BASE_URL}/api/vendasAssistidas/clientes/${idCliente}`,
            
            CLIENTE_EXCLUIR: (idCliente) => 
                `${BASE_URL}/api/vendasAssistidas/clientes/${idCliente}`,
            
            // Endpoints para Vendas do Cliente
            VENDAS_CLIENTE: (idCliente) => 
                `${BASE_URL}/api/vendasAssistidas/assistidas/cliente/${idCliente}`,
            
            // Endpoints para Débitos do Cliente
            DEBITOS_CLIENTE: (idCliente) => 
                `${BASE_URL}/api/debitos-clientes/cliente/${idCliente}`,

            getHeaders: function() {
                return {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-ID-EMPRESA': idEmpresa.toString()
                };
            }
        };

        // Normaliza o formato do cliente vindo da API para o formato esperado pelo front-end
        function normalizeCliente(item) {
            if (!item) return {};

            return {
                id: item.id_cliente ?? item.id ?? null,
                nome_cliente: item.nome_cliente ?? item.nome ?? '',
                email: item.email ?? '',
                telefone: item.telefone ?? '' ,
                whatsapp: item.whatsapp ?? '',
                endereco: item.endereco ?? '',
                cidade: item.cidade ?? '',
                estado: item.estado ?? '',
                cep: item.cep ?? '',
                documento: item.document_number_encrypted ?? item.documento ?? '',
                // normaliza data de criação
                created_at: item.data_cadastro ?? item.created_at ?? '',
                // valores numéricos/financeiros, garantir 0 quando ausentes
                total_debitos: item.total_debitos ?? item.total_debitos_cliente ?? 0,
                total_vendas: item.total_vendas ?? 0,
                status: item.status ?? 'inativo',
                classificacao: item.classificacao ?? 'bronze'
            };
        }

        // Inicialização
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar modais
            modalNovoCliente = new bootstrap.Modal(document.getElementById('modalNovoCliente'));
            modalDetalhesCliente = new bootstrap.Modal(document.getElementById('modalDetalhesCliente'));
            
            // Carregar dados iniciais
            carregarClientes();
            
            // Configurar eventos
            document.getElementById('searchInput').addEventListener('input', filtrarClientes);
            document.getElementById('filterStatus').addEventListener('change', filtrarClientes);
            document.getElementById('filterClassificacao').addEventListener('change', filtrarClientes);
            document.getElementById('filterCidade').addEventListener('change', filtrarClientes);
            
            // Logoff
            var logoutBtn = document.getElementById('logoutBtn');
            if (logoutBtn) {
                logoutBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    fazerLogoff();
                });
            }
        });

        // Função para fazer logout
        async function fazerLogoff() {
            try {
                const response = await fetch(BASE_URL + '/api/v1/logout', {
                    method: 'POST',
                    headers: API_CONFIG.getHeaders()
                });
                
                window.location.href = 'login.php';
                
            } catch (error) {
                console.error('Erro no logout:', error);
                window.location.href = 'login.php';
            }
        }

        // ========== CLIENTES ==========
        async function carregarClientes() {
            mostrarLoading(true);
            
            try {
                const response = await fetch(
                    API_CONFIG.CLIENTES_LISTAR(),
                    {
                        method: 'GET',
                        headers: API_CONFIG.getHeaders()
                    }
                );
                
                if (!response.ok) {
                    throw new Error(`Erro ${response.status}: ${response.statusText}`);
                }
                
                const data = await response.json();
                // Ensure we're working with an array
                const rawList = Array.isArray(data) ? data : (data.clientes || data.data || []);
                // Normalizar cada item para o formato esperado pelo front-end
                clientes = rawList.map(normalizeCliente);

                exibirClientes(clientes);
                atualizarResumoClientes(clientes);
                carregarFiltrosCidades(clientes);
                
            } catch (error) {
                console.error('Erro ao carregar clientes:', error);
                mostrarNotificacao('Erro ao carregar clientes: ' + error.message, 'error');
                document.getElementById('listaClientes').innerHTML = '<div class="col-12 text-center py-5 text-muted">Erro ao carregar dados</div>';
            } finally {
                mostrarLoading(false);
            }
        }

        function exibirClientes(listaClientes) {
            const container = document.getElementById('listaClientes');
            
            if (listaClientes.length === 0) {
                container.innerHTML = '<div class="col-12 text-center py-5 text-muted">Nenhum cliente encontrado</div>';
                return;
            }
            
            container.innerHTML = listaClientes.map(cliente => {
                const statusClass = `status-${cliente.status}`;
                const classificacaoClass = `classificacao-${cliente.classificacao}`;
                
                return `
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="card customer-card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">#${cliente.id}</h6>
                                <div>
                                    <span class="classificacao-badge ${classificacaoClass}">${cliente.classificacao}</span>
                                    <span class="status-badge ${statusClass}">${cliente.status}</span>
                                </div>
                            </div>
                            <div class="card-body">
                                <h6 class="card-title">${cliente.nome_cliente}</h6>
                                
                                <div class="contact-info mb-3">
                                    <div class="mb-1">
                                        <i class="bi bi-envelope me-1"></i>
                                        ${cliente.email || 'Não informado'}
                                    </div>
                                    <div class="mb-1">
                                        <i class="bi bi-telephone me-1"></i>
                                        ${cliente.telefone}
                                    </div>
                                    ${cliente.whatsapp ? `
                                    <div class="mb-1">
                                        <i class="bi bi-whatsapp me-1"></i>
                                        ${cliente.whatsapp}
                                    </div>
                                    ` : ''}
                                    ${cliente.cidade ? `
                                    <div class="mb-1">
                                        <i class="bi bi-geo-alt me-1"></i>
                                        ${cliente.cidade}${cliente.estado ? '/' + cliente.estado : ''}
                                    </div>
                                    ` : ''}
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        Cadastro: ${formatarData(cliente.created_at)}
                                    </small>
                                    <div class="d-flex gap-1">
                                        <button class="btn btn-primary btn-sm" onclick="abrirDetalhesCliente(${cliente.id})">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-warning btn-sm" onclick="editarCliente(${cliente.id})">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        ${cliente.status !== 'inativo' ? `
                                        <button class="btn btn-outline-danger btn-sm" onclick="inativarCliente(${cliente.id})">
                                            <i class="bi bi-person-x"></i>
                                        </button>
                                        ` : `
                                        <button class="btn btn-outline-success btn-sm" onclick="ativarCliente(${cliente.id})">
                                            <i class="bi bi-person-check"></i>
                                        </button>
                                        `}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        }

        function atualizarResumoClientes(clientes) {
            const total = clientes.length;
            const ativos = clientes.filter(c => c.status === 'ativo').length;
            const inativos = clientes.filter(c => c.status === 'inativo').length;
            const comDebito = clientes.filter(c => c.total_debitos > 0).length;
            
            document.getElementById('totalClientesResumo').textContent = total;
            document.getElementById('clientesAtivosResumo').textContent = ativos;
            document.getElementById('clientesInativosResumo').textContent = inativos;
            document.getElementById('clientesComDebitoResumo').textContent = comDebito;
        }

        function carregarFiltrosCidades(clientes) {
            const cidades = [...new Set(clientes.map(c => c.cidade).filter(c => c))];
            const select = document.getElementById('filterCidade');
            
            cidades.forEach(cidade => {
                const option = document.createElement('option');
                option.value = cidade;
                option.textContent = cidade;
                select.appendChild(option);
            });
        }

        // ========== MODAIS ==========
        function abrirModalNovoCliente() {
            document.getElementById('formNovoCliente').reset();
            modalNovoCliente.show();
        }

        async function abrirDetalhesCliente(idCliente) {
            mostrarLoading(true);
            clienteSelecionado = idCliente;
            
            try {
                // Mostrar loading no modal
                document.getElementById('detalhesClienteCarregando').style.display = 'block';
                document.getElementById('detalhesClienteConteudo').style.display = 'none';
                
                // Buscar dados do cliente
                const responseCliente = await fetch(
                    API_CONFIG.CLIENTE_DETALHES(idCliente),
                    {
                        method: 'GET',
                        headers: API_CONFIG.getHeaders()
                    }
                );
                
                if (!responseCliente.ok) {
                    throw new Error(`Erro ${responseCliente.status}: ${responseCliente.statusText}`);
                }
                
                const clienteJson = await responseCliente.json();
                const cliente = normalizeCliente(clienteJson);

                // Preencher dados do cliente
                preencherDadosCliente(cliente);
                
                // Buscar vendas do cliente
                await carregarVendasCliente(idCliente);
                
                // Buscar débitos do cliente
                await carregarDebitosCliente(idCliente);
                
                // Mostrar conteúdo
                document.getElementById('detalhesClienteCarregando').style.display = 'none';
                document.getElementById('detalhesClienteConteudo').style.display = 'block';
                
                modalDetalhesCliente.show();
                
            } catch (error) {
                console.error('Erro ao carregar detalhes do cliente:', error);
                mostrarNotificacao('Erro ao carregar detalhes: ' + error.message, 'error');
            } finally {
                mostrarLoading(false);
            }
        }

        function preencherDadosCliente(cliente) {
            document.getElementById('clienteNome').textContent = cliente.nome_cliente;
            document.getElementById('clienteEmail').textContent = cliente.email || 'Não informado';
            document.getElementById('clienteTelefone').textContent = cliente.telefone;
            document.getElementById('clienteWhatsapp').textContent = cliente.whatsapp || 'Não informado';
            document.getElementById('clienteDocumento').textContent = cliente.documento || 'Não informado';
            document.getElementById('clienteEndereco').textContent = cliente.endereco || 'Não informado';
            document.getElementById('clienteCidade').textContent = cliente.cidade || 'Não informado';
            document.getElementById('clienteEstado').textContent = cliente.estado || 'Não informado';
            document.getElementById('clienteCep').textContent = cliente.cep || 'Não informado';
            document.getElementById('clienteObservacoes').textContent = cliente.observacoes || 'Nenhuma observação';
            document.getElementById('clienteDataCadastro').textContent = formatarData(cliente.created_at);
            document.getElementById('clienteTotalVendas').textContent = formatarMoeda(cliente.total_vendas || 0);
            document.getElementById('clienteDebitosPendentes').textContent = formatarMoeda(cliente.total_debitos || 0);
            
            // Classificação e Status
            document.getElementById('clienteClassificacaoBadge').textContent = cliente.classificacao;
            document.getElementById('clienteClassificacaoBadge').className = `classificacao-badge classificacao-${cliente.classificacao}`;
            document.getElementById('clienteStatusBadge').textContent = cliente.status;
            document.getElementById('clienteStatusBadge').className = `status-badge status-${cliente.status}`;
        }

        async function carregarVendasCliente(idCliente) {
            try {
                const response = await fetch(
                    API_CONFIG.VENDAS_CLIENTE(idCliente),
                    {
                        method: 'GET',
                        headers: API_CONFIG.getHeaders()
                    }
                );
                
                if (response.ok) {
                    const vendas = await response.json();
                    preencherVendasCliente(vendas);
                }
            } catch (error) {
                console.error('Erro ao carregar vendas do cliente:', error);
            }
        }

        function preencherVendasCliente(vendas) {
            const tbody = document.getElementById('tbodyVendasCliente');
            
            if (vendas.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">Nenhuma venda encontrada</td></tr>';
                return;
            }
            
            tbody.innerHTML = vendas.map(venda => {
                return `
                    <tr>
                        <td>#${venda.id}</td>
                        <td>${formatarData(venda.created_at)}</td>
                        <td>${formatarMoeda(venda.valor_total)}</td>
                        <td>${venda.forma_pagamento}</td>
                        <td>
                            <span class="badge ${venda.status === 'concluida' ? 'bg-success' : 'bg-warning'}">
                                ${venda.status}
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-primary btn-sm" onclick="verDetalhesVenda(${venda.id})">
                                <i class="bi bi-eye"></i>
                            </button>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        async function carregarDebitosCliente(idCliente) {
            try {
                const response = await fetch(
                    API_CONFIG.DEBITOS_CLIENTE(idCliente),
                    {
                        method: 'GET',
                        headers: API_CONFIG.getHeaders()
                    }
                );
                
                if (response.ok) {
                    const debitos = await response.json();
                    preencherDebitosCliente(debitos);
                }
            } catch (error) {
                console.error('Erro ao carregar débitos do cliente:', error);
            }
        }

        function preencherDebitosCliente(debitos) {
            const tbody = document.getElementById('tbodyDebitosCliente');
            
            if (debitos.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">Nenhum débito encontrado</td></tr>';
                return;
            }
            
            tbody.innerHTML = debitos.map(debito => {
                const statusClass = debito.status === 'pago' ? 'bg-success' : 
                                  debito.status === 'vencido' ? 'bg-danger' : 'bg-warning';
                
                return `
                    <tr>
                        <td>#${debito.id}</td>
                        <td>${debito.descricao}</td>
                        <td>${formatarMoeda(debito.valor)}</td>
                        <td>${formatarData(debito.data_vencimento)}</td>
                        <td>
                            <span class="badge ${statusClass}">${debito.status}</span>
                        </td>
                        <td>
                            ${debito.status === 'pendente' ? `
                            <button class="btn btn-success btn-sm" onclick="registrarPagamento(${debito.id})">
                                <i class="bi bi-check-lg"></i>
                            </button>
                            ` : ''}
                        </td>
                    </tr>
                `;
            }).join('');
        }

        // ========== AÇÕES ==========
        async function salvarCliente() {
            const form = document.getElementById('formNovoCliente');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }
            
            mostrarLoading(true);
            
            try {
                const dadosCliente = {
                    id_empresa: idEmpresa,
                    id_usuario: idUsuario,
                    nome_cliente: document.getElementById('nomeCliente').value,
                    email: document.getElementById('emailCliente').value,
                    telefone: document.getElementById('telefoneCliente').value,
                    whatsapp: document.getElementById('whatsappCliente').value,
                    documento_type: document.getElementById('tipoDocumentoCliente').value,
                    documento: document.getElementById('documentoCliente').value,
                    endereco: document.getElementById('enderecoCliente').value,
                    cidade: document.getElementById('cidadeCliente').value,
                    estado: document.getElementById('estadoCliente').value,
                    cep: document.getElementById('cepCliente').value,
                    classificacao: document.getElementById('classificacaoCliente').value,
                    status: document.getElementById('statusCliente').value,
                    consent_marketing: document.getElementById('consentimentoMarketing').checked ? 'sim' : 'nao',
                    observacao: document.getElementById('observacoesCliente').value
                };
                
                const response = await fetch(API_CONFIG.CLIENTE_CADASTRAR(), {
                    method: 'POST',
                    headers: API_CONFIG.getHeaders(),
                    body: JSON.stringify(dadosCliente)
                });
                
                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(`Erro ${response.status}: ${JSON.stringify(errorData)}`);
                }
                
                mostrarNotificacao('Cliente cadastrado com sucesso!', 'success');
                modalNovoCliente.hide();
                
                // Recarregar lista de clientes
                carregarClientes();
                
            } catch (error) {
                console.error('Erro ao cadastrar cliente:', error);
                mostrarNotificacao('Erro ao cadastrar cliente: ' + error.message, 'error');
            } finally {
                mostrarLoading(false);
            }
        }

        async function editarCliente(idCliente) {
            // Implementar edição do cliente
            mostrarNotificacao('Funcionalidade de edição em desenvolvimento', 'info');
        }

        async function inativarCliente(idCliente) {
            if (!confirm('Tem certeza que deseja inativar este cliente?')) {
                return;
            }
            
            try {
                const response = await fetch(API_CONFIG.CLIENTE_ATUALIZAR(idCliente), {
                    method: 'PUT',
                    headers: API_CONFIG.getHeaders(),
                    body: JSON.stringify({
                        status: 'inativo'
                    })
                });
                
                if (response.ok) {
                    mostrarNotificacao('Cliente inativado com sucesso!', 'success');
                    carregarClientes();
                }
            } catch (error) {
                console.error('Erro ao inativar cliente:', error);
                mostrarNotificacao('Erro ao inativar cliente: ' + error.message, 'error');
            }
        }

        async function ativarCliente(idCliente) {
            try {
                const response = await fetch(API_CONFIG.CLIENTE_ATUALIZAR(idCliente), {
                    method: 'PUT',
                    headers: API_CONFIG.getHeaders(),
                    body: JSON.stringify({
                        status: 'ativo'
                    })
                });
                
                if (response.ok) {
                    mostrarNotificacao('Cliente ativado com sucesso!', 'success');
                    carregarClientes();
                }
            } catch (error) {
                console.error('Erro ao ativar cliente:', error);
                mostrarNotificacao('Erro ao ativar cliente: ' + error.message, 'error');
            }
        }

        // ========== FILTROS ==========
        function filtrarClientes() {
            const termoBusca = document.getElementById('searchInput').value.toLowerCase();
            const statusFiltro = document.getElementById('filterStatus').value;
            const classificacaoFiltro = document.getElementById('filterClassificacao').value;
            const cidadeFiltro = document.getElementById('filterCidade').value;
            
            let clientesFiltrados = clientes;
            
            // Aplicar filtros
            if (termoBusca) {
                clientesFiltrados = clientesFiltrados.filter(cliente => 
                    cliente.nome_cliente.toLowerCase().includes(termoBusca) ||
                    (cliente.email && cliente.email.toLowerCase().includes(termoBusca)) ||
                    cliente.telefone.includes(termoBusca)
                );
            }
            
            if (statusFiltro) {
                clientesFiltrados = clientesFiltrados.filter(cliente => cliente.status === statusFiltro);
            }
            
            if (classificacaoFiltro) {
                clientesFiltrados = clientesFiltrados.filter(cliente => cliente.classificacao === classificacaoFiltro);
            }
            
            if (cidadeFiltro) {
                clientesFiltrados = clientesFiltrados.filter(cliente => cliente.cidade === cidadeFiltro);
            }
            
            exibirClientes(clientesFiltrados);
        }

        function limparFiltros() {
            document.getElementById('searchInput').value = '';
            document.getElementById('filterStatus').value = '';
            document.getElementById('filterClassificacao').value = '';
            document.getElementById('filterCidade').value = '';
            
            exibirClientes(clientes);
        }

        // ========== FUNÇÕES AUXILIARES ==========
        function formatarData(data) {
            if (!data) return '';
            try {
                const date = new Date(data);
                return date.toLocaleDateString('pt-BR');
            } catch (e) {
                return data;
            }
        }

        function formatarMoeda(valor) {
            return new Intl.NumberFormat('pt-BR', {
                style: 'currency',
                currency: 'BRL'
            }).format(valor);
        }

        function mostrarLoading(mostrar) {
            document.getElementById('loadingOverlay').classList.toggle('d-none', !mostrar);
        }

        function exportarRelatorioClientes() {
            mostrarNotificacao('Relatório de clientes exportado com sucesso!', 'success');
        }

        function verDetalhesVenda(idVenda) {
            mostrarNotificacao('Redirecionando para detalhes da venda...', 'info');
        }

        function registrarPagamento(idDebito) {
            mostrarNotificacao('Registrando pagamento...', 'info');
        }

        // Função para mostrar notificações
        function mostrarNotificacao(message, type) {
            const notification = document.createElement('div');
            notification.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
            notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            notification.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 5000);
        }
    </script>
</body>
</html>