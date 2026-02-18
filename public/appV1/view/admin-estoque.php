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
    <title>Gestão de Estoque - SAS Multi</title>
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
        
        .stock-level-good { color: #28a745; }
        .stock-level-warning { color: #ffc107; }
        .stock-level-danger { color: #dc3545; }
        
        .branch-card {
            transition: all 0.3s ease;
            cursor: pointer;
            border-left: 4px solid #dee2e6;
        }
        .branch-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .branch-card.selected {
            border-left-color: #007bff;
            background-color: #f8f9fa;
        }
        
        .movement-entry { border-left: 3px solid #28a745; }
        .movement-exit { border-left: 3px solid #dc3545; }
        .movement-transfer { border-left: 3px solid #007bff; }
        .movement-adjustment { border-left: 3px solid #ffc107; }
        
        .pagination-custom .page-link {
            color: var(--primary-color);
            border: 1px solid #dee2e6;
        }
        
        .pagination-custom .page-item.active .page-link {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .tab-content {
            padding: 20px 0;
        }
        
        .stock-summary-card {
            text-align: center;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        
        .stock-summary-card .value {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .stock-summary-card .label {
            font-size: 0.8rem;
            color: #6c757d;
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
                        <li class="breadcrumb-item active">Gestão de Estoque</li>
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
                        <li><h6 class="dropdown-header">Alertas de Estoque</h6></li>
                        <li><a class="dropdown-item" href="#">5 produtos com estoque baixo</a></li>
                        <li><a class="dropdown-item" href="#">2 produtos com estoque zerado</a></li>
                        <li><a class="dropdown-item" href="#">Inventário pendente para 3 produtos</a></li>
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
                    <h1 class="page-title">Gestão de Estoque</h1>
                    <p class="page-subtitle">Controle, movimente e analise seu estoque</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary" onclick="abrirModalInventario()">
                        <i class="bi bi-clipboard-check me-2"></i>Inventário
                    </button>
                    <button class="btn btn-outline-primary" onclick="abrirModalTransferencia()">
                        <i class="bi bi-arrow-left-right me-2"></i>Transferência
                    </button>
                    <button class="btn btn-primary" onclick="abrirModalMovimentacao()">
                        <i class="bi bi-plus-circle me-2"></i>Nova Movimentação
                    </button>
                </div>
            </div>
            
            <!-- Resumo do Estoque -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card-custom">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="text-primary mb-0" id="totalProdutosResumo">0</h5>
                                    <p class="text-muted mb-0">Total de Produtos</p>
                                </div>
                                <div class="bg-primary text-white rounded p-3">
                                    <i class="bi bi-box-seam" style="font-size: 1.5rem;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card-custom">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="text-success mb-0" id="valorTotalResumo">R$ 0</h5>
                                    <p class="text-muted mb-0">Valor em Estoque</p>
                                </div>
                                <div class="bg-success text-white rounded p-3">
                                    <i class="bi bi-currency-dollar" style="font-size: 1.5rem;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card-custom">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="text-warning mb-0" id="produtosAlertaResumo">0</h5>
                                    <p class="text-muted mb-0">Produtos em Alerta</p>
                                </div>
                                <div class="bg-warning text-white rounded p-3">
                                    <i class="bi bi-exclamation-triangle" style="font-size: 1.5rem;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card-custom">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="text-info mb-0">0</h5>
                                    <p class="text-muted mb-0">Inventários Ativos</p>
                                </div>
                                <div class="bg-info text-white rounded p-3">
                                    <i class="bi bi-clipboard-data" style="font-size: 1.5rem;"></i>
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
                                <input type="text" class="form-control" id="searchInput" placeholder="Buscar por código, descrição, código de barras...">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" id="filterCategoria">
                                <option value="">Todas as categorias</option>
                                <!-- Categorias serão carregadas dinamicamente -->
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" id="filterStatus">
                                <option value="">Todos os status</option>
                                <option value="ativo">Ativo</option>
                                <option value="inativo">Inativo</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" id="filterEstoque">
                                <option value="">Todos</option>
                                <option value="baixo">Estoque Baixo</option>
                                <option value="zerado">Estoque Zerado</option>
                                <option value="normal">Estoque Normal</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-outline-secondary w-100" onclick="limparFiltros()">
                                <i class="bi bi-arrow-clockwise"></i> Limpar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tabela de Produtos -->
            <div class="card-custom">
                <div class="card-header-custom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Lista de Produtos</h5>
                    <div class="d-flex align-items-center">
                        <span class="text-muted me-3" id="totalProdutos">Carregando...</span>
                        <button class="btn btn-sm btn-outline-primary" onclick="carregarProdutos()">
                            <i class="bi bi-arrow-clockwise"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="tabelaProdutos">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Descrição</th>
                                    <th>Categoria</th>
                                    <th>Unidade</th>
                                    <th>Estoque Total</th>
                                    <th>Custo Médio</th>
                                    <th>Status</th>
                                    <th width="120">Gerenciar</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyProdutos">
                                <tr>
                                    <td colspan="8" class="text-center text-muted">Carregando produtos...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Paginação -->
                    <nav aria-label="Navegação de páginas" id="paginationContainer">
                        <ul class="pagination pagination-custom justify-content-center mb-0">
                            <!-- A paginação será gerada dinamicamente via JavaScript -->
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal para Detalhes do Estoque -->
    <div class="modal fade" id="modalDetalhesEstoque" tabindex="-1" aria-labelledby="modalDetalhesEstoqueLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalDetalhesEstoqueLabel">Detalhes do Estoque</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="detalhesProdutoCarregando" class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Carregando...</span>
                        </div>
                        <p class="mt-2">Carregando detalhes do produto...</p>
                    </div>
                    
                    <div id="detalhesProdutoConteudo" style="display: none;">
                        <!-- Header do Produto -->
                        <div class="row mb-4">
                            <div class="col-md-8">
                                <h4 id="produtoDescricao"></h4>
                                <p class="text-muted" id="produtoCodigo"></p>
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <small class="text-muted">Código de Barras:</small>
                                        <div><strong id="produtoCodigoBarras"></strong></div>
                                    </div>
                                    <div class="col-md-3">
                                        <small class="text-muted">Unidade:</small>
                                        <div><strong id="produtoUnidade"></strong></div>
                                    </div>
                                    <div class="col-md-3">
                                        <small class="text-muted">NCM:</small>
                                        <div><strong id="produtoNCM"></strong></div>
                                    </div>
                                    <div class="col-md-3">
                                        <small class="text-muted">Categoria:</small>
                                        <div><strong id="produtoCategoria"></strong></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <div class="card bg-primary text-white text-center">
                                            <div class="card-body py-2">
                                                <h6 class="card-title mb-1">Estoque Total</h6>
                                                <h4 class="mb-0" id="produtoEstoqueTotal">0</h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="card bg-success text-white text-center">
                                            <div class="card-body py-2">
                                                <h6 class="card-title mb-1">Valor Total</h6>
                                                <h4 class="mb-0" id="produtoValorTotal">R$ 0</h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="card bg-info text-white text-center">
                                            <div class="card-body py-2">
                                                <h6 class="card-title mb-1">Custo Médio</h6>
                                                <h4 class="mb-0" id="produtoCustoMedio">R$ 0</h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="card bg-warning text-white text-center">
                                            <div class="card-body py-2">
                                                <h6 class="card-title mb-1">Reservado</h6>
                                                <h4 class="mb-0" id="produtoReservado">0</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <!-- Lista de Filiais -->
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0">Estoque por Filial</h5>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="list-group list-group-flush" id="listaFiliais">
                                            <!-- Lista de filiais será carregada dinamicamente -->
                                        </div>
                                    </div>
                                </div>

                                <!-- Ações Rápidas -->
                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h6 class="mb-0">Ações Rápidas</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-grid gap-2">
                                            <button class="btn btn-outline-primary btn-sm" onclick="abrirModalTransferenciaProduto()">
                                                <i class="bi bi-arrow-left-right me-1"></i>
                                                Transferir Entre Filiais
                                            </button>
                                            <button class="btn btn-outline-success btn-sm" onclick="abrirModalAjusteEstoque()">
                                                <i class="bi bi-pencil-square me-1"></i>
                                                Ajuste de Estoque
                                            </button>
                                            <button class="btn btn-outline-info btn-sm" onclick="incluirEmInventario()">
                                                <i class="bi bi-clipboard-check me-1"></i>
                                                Incluir em Inventário
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Histórico de Movimentações -->
                            <div class="col-md-8">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0" id="tituloFichaEstoque">Ficha de Estoque</h5>
                                        <div class="d-flex gap-2">
                                            <select class="form-select form-select-sm" id="filtroPeriodo" style="width: auto;">
                                                <option value="30">Últimos 30 dias</option>
                                                <option value="60">Últimos 60 dias</option>
                                                <option value="90">Últimos 90 dias</option>
                                                <option value="365">Este ano</option>
                                            </select>
                                            <button class="btn btn-outline-primary btn-sm" onclick="exportarFichaEstoque()">
                                                <i class="bi bi-download"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <!-- Informações do Estoque Atual -->
                                        <div class="row mb-4">
                                            <div class="col-md-3">
                                                <div class="text-center p-3 bg-light rounded">
                                                    <h6 class="text-muted mb-1">Estoque Atual</h6>
                                                    <h4 class="mb-0 text-primary" id="estoqueAtualFilial">0</h4>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="text-center p-3 bg-light rounded">
                                                    <h6 class="text-muted mb-1">Custo Médio</h6>
                                                    <h4 class="mb-0 text-success" id="custoMedioFilial">R$ 0</h4>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="text-center p-3 bg-light rounded">
                                                    <h6 class="text-muted mb-1">Valor Total</h6>
                                                    <h4 class="mb-0 text-info" id="valorTotalFilial">R$ 0</h4>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="text-center p-3 bg-light rounded">
                                                    <h6 class="text-muted mb-1">Última Mov.</h6>
                                                    <h4 class="mb-0 text-warning" id="ultimaMovimentacao">-</h4>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Histórico de Movimentações -->
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Data</th>
                                                        <th>Tipo</th>
                                                        <th>Documento</th>
                                                        <th>Qtd</th>
                                                        <th>Custo Unit.</th>
                                                        <th>Saldo</th>
                                                        <th>Custo Médio</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tbodyMovimentacoes">
                                                    <!-- Histórico será carregado dinamicamente -->
                                                </tbody>
                                            </table>
                                        </div>

                                        <!-- Paginação do Histórico -->
                                        <nav class="mt-3">
                                            <ul class="pagination pagination-sm justify-content-center" id="paginacaoMovimentacoes">
                                                <!-- Paginação será gerada dinamicamente -->
                                            </ul>
                                        </nav>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Nova Movimentação -->
    <div class="modal fade" id="modalMovimentacao" tabindex="-1" aria-labelledby="modalMovimentacaoLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalMovimentacaoLabel">Nova Movimentação</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formMovimentacao">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Tipo de Movimentação *</label>
                                <select class="form-select" id="tipoMovimentacao" required>
                                    <option value="">Selecione...</option>
                                    <option value="entrada">Entrada</option>
                                    <option value="saida">Saída</option>
                                    <option value="ajuste">Ajuste</option>
                                    <option value="transferencia">Transferência</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Filial *</label>
                                <select class="form-select" id="filialMovimentacao" required>
                                    <option value="">Selecione...</option>
                                    <!-- Opções serão carregadas dinamicamente -->
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Produto *</label>
                                <select class="form-select" id="produtoMovimentacao" required>
                                    <option value="">Selecione...</option>
                                    <!-- Opções serão carregadas dinamicamente -->
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Quantidade *</label>
                                <input type="number" class="form-control" id="quantidadeMovimentacao" step="0.01" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Custo Unitário *</label>
                                <input type="number" class="form-control" id="custoMovimentacao" step="0.01" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Documento/Referência</label>
                                <input type="text" class="form-control" id="documentoMovimentacao" placeholder="Ex: NF-e 123456, AJ-001, etc.">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Observações</label>
                                <textarea class="form-control" id="observacoesMovimentacao" rows="3"></textarea>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="confirmarMovimentacao()">Confirmar Movimentação</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Transferência -->
    <div class="modal fade" id="modalTransferencia" tabindex="-1" aria-labelledby="modalTransferenciaLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTransferenciaLabel">Transferência entre Filiais</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formTransferencia">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Produto *</label>
                                <select class="form-select" id="produtoTransferencia" required>
                                    <option value="">Selecione...</option>
                                    <!-- Opções serão carregadas dinamicamente -->
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Quantidade *</label>
                                <input type="number" class="form-control" id="quantidadeTransferencia" step="0.01" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Filial de Origem *</label>
                                <select class="form-select" id="origemTransferencia" required>
                                    <option value="">Selecione...</option>
                                    <!-- Opções serão carregadas dinamicamente -->
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Filial de Destino *</label>
                                <select class="form-select" id="destinoTransferencia" required>
                                    <option value="">Selecione...</option>
                                    <!-- Opções serão carregadas dinamicamente -->
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Custo Unitário *</label>
                                <input type="number" class="form-control" id="custoTransferencia" step="0.01" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Observações</label>
                                <textarea class="form-control" id="observacoesTransferencia" rows="3"></textarea>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="confirmarTransferencia()">Confirmar Transferência</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Inventário -->
    <div class="modal fade" id="modalInventario" tabindex="-1" aria-labelledby="modalInventarioLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalInventarioLabel">Gestão de Inventário</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-tabs" id="inventarioTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="novo-inventario-tab" data-bs-toggle="tab" data-bs-target="#novo-inventario" type="button" role="tab">Novo Inventário</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="inventarios-ativos-tab" data-bs-toggle="tab" data-bs-target="#inventarios-ativos" type="button" role="tab">Inventários Ativos</button>
                        </li>
                    </ul>
                    
                    <div class="tab-content p-3" id="inventarioTabContent">
                        <div class="tab-pane fade show active" id="novo-inventario" role="tabpanel">
                            <form id="formNovoInventario">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Descrição do Inventário *</label>
                                        <input type="text" class="form-control" id="descricaoInventario" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Filial *</label>
                                        <select class="form-select" id="filialInventario" required>
                                            <option value="">Selecione...</option>
                                            <!-- Opções serão carregadas dinamicamente -->
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Produtos para Inventariar</label>
                                        <div class="border rounded p-3">
                                            <div class="d-flex justify-content-between mb-3">
                                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="adicionarProdutoInventario()">
                                                    <i class="bi bi-plus-circle"></i> Adicionar Produto
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="selecionarTodosProdutos()">
                                                    <i class="bi bi-check-all"></i> Selecionar Todos
                                                </button>
                                            </div>
                                            <div id="listaProdutosInventario" style="max-height: 200px; overflow-y: auto;">
                                                <!-- Lista de produtos será carregada dinamicamente -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane fade" id="inventarios-ativos" role="tabpanel">
                            <div id="listaInventariosAtivos">
                                <!-- Lista de inventários ativos será carregada dinamicamente -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="iniciarInventario()">Iniciar Inventário</button>
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
    const BASE_URL = 'https://rdpsolutions.online/saas-multiempresas/public';
        
        let produtos = [];
        let filiais = [];
        let produtoSelecionado = null;
        let filialSelecionada = null;
        let modalDetalhesEstoque = null;
        let modalMovimentacao = null;
        let modalTransferencia = null;
        let modalInventario = null;

        // Configuração da API
        const API_CONFIG = {
            // Endpoints
            PRODUTOS_EMPRESA: (idEmpresa) => 
                `${BASE_URL}/api/v1/empresas/${idEmpresa}/produtos`,
            
            ESTOQUE_PRODUTO_FILIAIS: (idEmpresa, idProduto) => 
                `${BASE_URL}/api/estoques/empresa/${idEmpresa}/produto/${idProduto}/filiais`,
            
            MOVIMENTACOES_PRODUTO_FILIAL: (idEmpresa, idFilial, idProduto) => 
                `${BASE_URL}/api/movimentacoes/empresa/${idEmpresa}/filial/${idFilial}/produto/${idProduto}`,
            
            FILIAIS_EMPRESA: (idEmpresa) => 
                `${BASE_URL}/api/filiais/empresa/${idEmpresa}`,
            
            getHeaders: function() {
                return {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json',
                    'X-ID-EMPRESA': idEmpresa.toString()
                };
            },

            getJsonHeaders: function() {
                return {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-ID-EMPRESA': idEmpresa.toString()
                };
            }
        };

        // Inicialização
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar modais
            modalDetalhesEstoque = new bootstrap.Modal(document.getElementById('modalDetalhesEstoque'));
            modalMovimentacao = new bootstrap.Modal(document.getElementById('modalMovimentacao'));
            modalTransferencia = new bootstrap.Modal(document.getElementById('modalTransferencia'));
            modalInventario = new bootstrap.Modal(document.getElementById('modalInventario'));
            
            // Carregar dados iniciais
            carregarProdutos();
            carregarFiliais();
            
            // Configurar eventos
            document.getElementById('searchInput').addEventListener('input', filtrarProdutos);
            document.getElementById('filterStatus').addEventListener('change', filtrarProdutos);
            document.getElementById('filterEstoque').addEventListener('change', filtrarProdutos);
            
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

        // ========== PRODUTOS ==========
        async function carregarProdutos() {
            mostrarLoading(true);
            
            try {
                // Buscar produtos da API
                const response = await fetch(
                    API_CONFIG.PRODUTOS_EMPRESA(idEmpresa),
                    {
                        method: 'GET',
                        headers: API_CONFIG.getHeaders()
                    }
                );
                
                if (!response.ok) {
                    throw new Error(`Erro ${response.status}: ${response.statusText}`);
                }
                
                const data = await response.json();
                
                // Extrair lista de produtos da resposta
                produtos = data.data || [];
                
                // Para cada produto, vamos buscar o estoque total
                for (let produto of produtos) {
                    await carregarEstoqueProduto(produto);
                }
                
                exibirProdutos(produtos);
                atualizarResumoEstoque(produtos);
                
            } catch (error) {
                console.error('Erro ao carregar produtos:', error);
                mostrarNotificacao('Erro ao carregar produtos: ' + error.message, 'error');
                document.getElementById('tbodyProdutos').innerHTML = '<tr><td colspan="8" class="text-center text-muted">Erro ao carregar dados</td></tr>';
            } finally {
                mostrarLoading(false);
            }
        }

        async function carregarEstoqueProduto(produto) {
            try {
                const response = await fetch(
                    API_CONFIG.ESTOQUE_PRODUTO_FILIAIS(idEmpresa, produto.id_produto),
                    {
                        method: 'GET',
                        headers: API_CONFIG.getHeaders()
                    }
                );
                
                if (!response.ok) {
                    throw new Error(`Erro ${response.status}: ${response.statusText}`);
                }
                
                const estoqueFiliais = await response.json();
                
                // Calcular estoque total e valor total
                produto.quantidade_total = estoqueFiliais.reduce((total, item) => 
                    total + parseFloat(item.produto.quantidade || 0), 0);
                
                produto.valor_total = estoqueFiliais.reduce((total, item) => {
                    const quantidade = parseFloat(item.produto.quantidade || 0);
                    const precoCusto = parseFloat(item.produto.preco_custo || produto.preco_custo || 0);
                    return total + (quantidade * precoCusto);
                }, 0);
                
            } catch (error) {
                console.error(`Erro ao carregar estoque do produto ${produto.id_produto}:`, error);
                produto.quantidade_total = 0;
                produto.valor_total = 0;
            }
        }

        function exibirProdutos(listaProdutos) {
            const tbody = document.getElementById('tbodyProdutos');
            
            if (listaProdutos.length === 0) {
                tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">Nenhum produto encontrado</td></tr>';
                return;
            }
            
            tbody.innerHTML = listaProdutos.map(produto => `
                <tr>
                    <td>
                        <div class="fw-semibold">${produto.id_produto}</div>
                        <small class="text-muted">${produto.codigo_barras || 'Sem código'}</small>
                    </td>
                    <td>
                        <div class="fw-semibold">${produto.descricao}</div>
                        <small class="text-muted">${produto.id_categoria ? 'Categoria ' + produto.id_categoria : 'Sem categoria'}</small>
                    </td>
                    <td>${produto.id_categoria || 'N/A'}</td>
                    <td>${produto.unidade_medida}</td>
                    <td>
                        <div class="fw-semibold ${getStockLevelClass(produto.quantidade_total)}">
                            ${produto.quantidade_total || 0} ${produto.unidade_medida}
                        </div>
                    </td>
                    <td class="fw-semibold">R$ ${formatarPreco(produto.preco_custo)}</td>
                    <td>
                        <span class="status-badge status-${produto.ativo ? 'ativo' : 'inativo'}">
                            ${produto.ativo ? 'Ativo' : 'Inativo'}
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-primary btn-sm" onclick="abrirDetalhesEstoque(${produto.id_produto})">
                            <i class="bi bi-box-arrow-in-right me-1"></i>Estoque
                        </button>
                    </td>
                </tr>
            `).join('');
        }

        function getStockLevelClass(quantidade) {
            if (!quantidade || quantidade <= 0) return 'stock-level-danger';
            if (quantidade <= 10) return 'stock-level-warning';
            return 'stock-level-good';
        }

        function atualizarResumoEstoque(produtos) {
            const totalProdutos = produtos.length;
            const valorTotal = produtos.reduce((total, produto) => total + (produto.valor_total || 0), 0);
            const produtosAlerta = produtos.filter(produto => {
                const quantidade = produto.quantidade_total || 0;
                return quantidade <= 10;
            }).length;
            
            document.getElementById('totalProdutosResumo').textContent = totalProdutos;
            document.getElementById('valorTotalResumo').textContent = `R$ ${formatarPreco(valorTotal)}`;
            document.getElementById('produtosAlertaResumo').textContent = produtosAlerta;
            document.getElementById('totalProdutos').textContent = `${totalProdutos} produto(s) encontrado(s)`;
        }

        // ========== DETALHES DO ESTOQUE ==========
        async function abrirDetalhesEstoque(idProduto) {
            mostrarLoading(true);
            produtoSelecionado = idProduto;
            
            try {
                // Mostrar loading no modal
                document.getElementById('detalhesProdutoCarregando').style.display = 'block';
                document.getElementById('detalhesProdutoConteudo').style.display = 'none';
                
                // Buscar produto
                const produto = produtos.find(p => p.id_produto === idProduto);
                if (!produto) {
                    throw new Error('Produto não encontrado');
                }
                
                // Buscar estoque nas filiais
                const response = await fetch(
                    API_CONFIG.ESTOQUE_PRODUTO_FILIAIS(idEmpresa, idProduto),
                    {
                        method: 'GET',
                        headers: API_CONFIG.getHeaders()
                    }
                );
                
                if (!response.ok) {
                    throw new Error(`Erro ${response.status}: ${response.statusText}`);
                }
                
                const estoqueFiliais = await response.json();
                
                // Preencher dados do produto
                preencherDadosProduto(produto, estoqueFiliais);
                
                // Preencher lista de filiais
                preencherListaFiliais(estoqueFiliais);
                
                // Selecionar primeira filial por padrão
                if (estoqueFiliais.length > 0) {
                    selecionarFilial(estoqueFiliais[0].filial.id_filial);
                }
                
                // Mostrar conteúdo
                document.getElementById('detalhesProdutoCarregando').style.display = 'none';
                document.getElementById('detalhesProdutoConteudo').style.display = 'block';
                
                modalDetalhesEstoque.show();
                
            } catch (error) {
                console.error('Erro ao carregar detalhes do estoque:', error);
                mostrarNotificacao('Erro ao carregar detalhes: ' + error.message, 'error');
            } finally {
                mostrarLoading(false);
            }
        }

        function preencherDadosProduto(produto, estoqueFiliais) {
            document.getElementById('produtoDescricao').textContent = produto.descricao;
            document.getElementById('produtoCodigo').textContent = `Código: ${produto.id_produto}`;
            document.getElementById('produtoCodigoBarras').textContent = produto.codigo_barras || 'N/A';
            document.getElementById('produtoUnidade').textContent = produto.unidade_medida;
            document.getElementById('produtoNCM').textContent = produto.ncm || 'N/A';
            document.getElementById('produtoCategoria').textContent = produto.id_categoria || 'N/A';
            
            // Calcular totais
            const estoqueTotal = estoqueFiliais.reduce((total, item) => total + parseFloat(item.produto.quantidade || 0), 0);
            const valorTotal = estoqueFiliais.reduce((total, item) => {
                const quantidade = parseFloat(item.produto.quantidade || 0);
                const precoCusto = parseFloat(item.produto.preco_custo || produto.preco_custo || 0);
                return total + (quantidade * precoCusto);
            }, 0);
            
            const custoMedio = estoqueTotal > 0 ? valorTotal / estoqueTotal : parseFloat(produto.preco_custo || 0);
            
            document.getElementById('produtoEstoqueTotal').textContent = estoqueTotal;
            document.getElementById('produtoValorTotal').textContent = `R$ ${formatarPreco(valorTotal)}`;
            document.getElementById('produtoCustoMedio').textContent = `R$ ${formatarPreco(custoMedio)}`;
            document.getElementById('produtoReservado').textContent = '0';
        }

        function preencherListaFiliais(estoqueFiliais) {
            const listaFiliais = document.getElementById('listaFiliais');
            
            listaFiliais.innerHTML = estoqueFiliais.map(item => {
                const filial = item.filial;
                const produto = item.produto;
                const quantidade = parseFloat(produto.quantidade || 0);
                const estoqueMinimo = parseFloat(produto.estoque_minimo || 0);
                const estoqueMaximo = parseFloat(produto.estoque_maximo || 0);
                
                const stockLevelClass = getStockLevelClass(quantidade);
                const progressPercentage = estoqueMaximo > 0 ? (quantidade / estoqueMaximo) * 100 : 0;
                const progressColor = progressPercentage < 30 ? 'bg-danger' : progressPercentage < 70 ? 'bg-warning' : 'bg-success';
                
                return `
                    <div class="list-group-item branch-card" onclick="selecionarFilial(${filial.id_filial})" id="filial-${filial.id_filial}">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">${filial.nome_filial}</h6>
                                <small class="text-muted">${filial.endereco}, ${filial.cidade} - ${filial.estado}</small>
                            </div>
                            <div class="text-end">
                                <h5 class="mb-0 ${stockLevelClass}">${quantidade} ${produto.unidade_medida}</h5>
                                <small class="text-muted">Min: ${estoqueMinimo} | Max: ${estoqueMaximo}</small>
                            </div>
                        </div>
                        <div class="progress mt-2" style="height: 4px;">
                            <div class="progress-bar ${progressColor}" style="width: ${progressPercentage}%"></div>
                        </div>
                    </div>
                `;
            }).join('');
        }

        async function selecionarFilial(idFilial) {
            filialSelecionada = idFilial;
            
            // Atualizar seleção visual
            document.querySelectorAll('.branch-card').forEach(card => {
                card.classList.remove('selected');
            });
            document.getElementById(`filial-${idFilial}`).classList.add('selected');
            
            // Atualizar título
            const filialNome = document.querySelector(`#filial-${idFilial} h6`).textContent;
            document.getElementById('tituloFichaEstoque').textContent = `Ficha de Estoque - ${filialNome}`;
            
            // Carregar movimentações
            await carregarMovimentacoes(idFilial);
        }

        async function carregarMovimentacoes(idFilial) {
            try {
                const response = await fetch(
                    API_CONFIG.MOVIMENTACOES_PRODUTO_FILIAL(idEmpresa, idFilial, produtoSelecionado),
                    {
                        method: 'GET',
                        headers: API_CONFIG.getHeaders()
                    }
                );
                
                if (!response.ok) {
                    throw new Error(`Erro ${response.status}: ${response.statusText}`);
                }
                
                const movimentacoes = await response.json();
                
                // Ordenar por data (mais recente primeiro)
                movimentacoes.sort((a, b) => new Date(b.data_movimentacao) - new Date(a.data_movimentacao));
                
                // Atualizar informações do estoque atual
                let estoqueAtual = 0;
                let custoMedio = 0;
                let valorTotal = 0;
                let ultimaMovimentacao = '-';
                
                if (movimentacoes.length > 0) {
                    estoqueAtual = parseFloat(movimentacoes[0].saldo_atual);
                    custoMedio = parseFloat(movimentacoes[0].custo_unitario);
                    valorTotal = estoqueAtual * custoMedio;
                    ultimaMovimentacao = formatarData(movimentacoes[0].data_movimentacao);
                }
                
                document.getElementById('estoqueAtualFilial').textContent = estoqueAtual;
                document.getElementById('custoMedioFilial').textContent = `R$ ${formatarPreco(custoMedio)}`;
                document.getElementById('valorTotalFilial').textContent = `R$ ${formatarPreco(valorTotal)}`;
                document.getElementById('ultimaMovimentacao').textContent = ultimaMovimentacao;
                
                // Preencher tabela de movimentações
                preencherMovimentacoes(movimentacoes);
                
            } catch (error) {
                console.error('Erro ao carregar movimentações:', error);
                mostrarNotificacao('Erro ao carregar movimentações: ' + error.message, 'error');
            }
        }

        function preencherMovimentacoes(movimentacoes) {
            const tbody = document.getElementById('tbodyMovimentacoes');
            
            if (movimentacoes.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">Nenhuma movimentação encontrada</td></tr>';
                return;
            }
            
            tbody.innerHTML = movimentacoes.map(mov => {
                const tipoMovimentacao = mov.tipo_movimentacao;
                const badgeClass = {
                    'entrada': 'bg-success',
                    'saida': 'bg-danger',
                    'transferencia': 'bg-primary',
                    'ajuste': 'bg-warning'
                }[tipoMovimentacao] || 'bg-secondary';
                
                const rowClass = {
                    'entrada': 'movement-entry',
                    'saida': 'movement-exit',
                    'transferencia': 'movement-transfer',
                    'ajuste': 'movement-adjustment'
                }[tipoMovimentacao] || '';
                
                const sinal = tipoMovimentacao === 'entrada' || tipoMovimentacao === 'transferencia' ? '+' : '-';
                
                return `
                    <tr class="${rowClass}">
                        <td>${formatarData(mov.data_movimentacao)}</td>
                        <td>
                            <span class="badge ${badgeClass}">${formatarTipoMovimentacao(tipoMovimentacao)}</span>
                        </td>
                        <td>${mov.origem === 'nota_fiscal' ? 'NF-e ' : ''}${mov.id_referencia || mov.observacao || 'N/A'}</td>
                        <td>${sinal}${mov.quantidade}</td>
                        <td>R$ ${formatarPreco(mov.custo_unitario)}</td>
                        <td><strong>${mov.saldo_atual}</strong></td>
                        <td>R$ ${formatarPreco(mov.custo_unitario)}</td>
                    </tr>
                `;
            }).join('');
        }

        // ========== MODAIS ==========
        function abrirModalMovimentacao() {
            // Limpar formulário
            document.getElementById('formMovimentacao').reset();
            
            // Carregar opções de filiais e produtos
            carregarOpcoesFiliais('filialMovimentacao');
            carregarOpcoesProdutos('produtoMovimentacao');
            
            modalMovimentacao.show();
        }

        function abrirModalTransferencia() {
            // Limpar formulário
            document.getElementById('formTransferencia').reset();
            
            // Carregar opções
            carregarOpcoesProdutos('produtoTransferencia');
            carregarOpcoesFiliais('origemTransferencia');
            carregarOpcoesFiliais('destinoTransferencia');
            
            modalTransferencia.show();
        }

        function abrirModalInventario() {
            // Carregar opções
            carregarOpcoesFiliais('filialInventario');
            carregarProdutosParaInventario();
            
            modalInventario.show();
        }

        function abrirModalTransferenciaProduto() {
            if (!produtoSelecionado) return;
            
            // Pre-selecionar produto atual
            document.getElementById('produtoTransferencia').value = produtoSelecionado;
            
            abrirModalTransferencia();
        }

        function abrirModalAjusteEstoque() {
            // Implementar modal de ajuste de estoque
            mostrarNotificacao('Funcionalidade de ajuste de estoque em desenvolvimento', 'info');
        }

        // ========== FUNÇÕES AUXILIARES ==========
        async function carregarFiliais() {
            try {
                const response = await fetch(
                    API_CONFIG.FILIAIS_EMPRESA(idEmpresa),
                    {
                        method: 'GET',
                        headers: API_CONFIG.getHeaders()
                    }
                );
                
                if (!response.ok) {
                    throw new Error(`Erro ${response.status}: ${response.statusText}`);
                }
                
                filiais = await response.json();
                
            } catch (error) {
                console.error('Erro ao carregar filiais:', error);
                mostrarNotificacao('Erro ao carregar filiais: ' + error.message, 'error');
            }
        }

        function carregarOpcoesFiliais(selectId) {
            const select = document.getElementById(selectId);
            select.innerHTML = '<option value="">Selecione...</option>';
            
            filiais.forEach(filial => {
                select.innerHTML += `<option value="${filial.id_filial}">${filial.nome_filial}</option>`;
            });
        }

        function carregarOpcoesProdutos(selectId) {
            const select = document.getElementById(selectId);
            select.innerHTML = '<option value="">Selecione...</option>';
            
            produtos.forEach(produto => {
                select.innerHTML += `<option value="${produto.id_produto}">${produto.id_produto} - ${produto.descricao}</option>`;
            });
        }

        function carregarProdutosParaInventario() {
            const container = document.getElementById('listaProdutosInventario');
            container.innerHTML = '';
            
            produtos.forEach(produto => {
                const div = document.createElement('div');
                div.className = 'form-check';
                div.innerHTML = `
                    <input class="form-check-input" type="checkbox" value="${produto.id_produto}" id="prodInventario${produto.id_produto}">
                    <label class="form-check-label" for="prodInventario${produto.id_produto}">
                        ${produto.id_produto} - ${produto.descricao} (${produto.quantidade_total || 0} ${produto.unidade_medida})
                    </label>
                `;
                container.appendChild(div);
            });
        }

        function filtrarProdutos() {
            const termoBusca = document.getElementById('searchInput').value.toLowerCase();
            const statusFiltro = document.getElementById('filterStatus').value;
            const estoqueFiltro = document.getElementById('filterEstoque').value;
            
            let produtosFiltrados = produtos;
            
            // Aplicar filtros
            if (termoBusca) {
                produtosFiltrados = produtosFiltrados.filter(produto => 
                    produto.id_produto.toString().includes(termoBusca) ||
                    produto.descricao.toLowerCase().includes(termoBusca) ||
                    (produto.codigo_barras && produto.codigo_barras.toLowerCase().includes(termoBusca))
                );
            }
            
            if (statusFiltro) {
                produtosFiltrados = produtosFiltrados.filter(produto => 
                    (statusFiltro === 'ativo' && produto.ativo) ||
                    (statusFiltro === 'inativo' && !produto.ativo)
                );
            }
            
            if (estoqueFiltro) {
                produtosFiltrados = produtosFiltrados.filter(produto => {
                    const quantidade = produto.quantidade_total || 0;
                    
                    if (estoqueFiltro === 'baixo') {
                        return quantidade <= 10;
                    } else if (estoqueFiltro === 'zerado') {
                        return quantidade === 0;
                    } else if (estoqueFiltro === 'normal') {
                        return quantidade > 10;
                    }
                    return true;
                });
            }
            
            exibirProdutos(produtosFiltrados);
            atualizarTotalProdutos(produtosFiltrados.length);
        }

        function limparFiltros() {
            document.getElementById('searchInput').value = '';
            document.getElementById('filterStatus').value = '';
            document.getElementById('filterEstoque').value = '';
            
            exibirProdutos(produtos);
        }

        // ========== FUNÇÕES DE FORMATAÇÃO ==========
        function formatarPreco(preco) {
            if (!preco) return '0,00';
            return parseFloat(preco).toFixed(2).replace('.', ',');
        }

        function formatarData(data) {
            if (!data) return '';
            try {
                const date = new Date(data);
                return date.toLocaleDateString('pt-BR');
            } catch (e) {
                return data;
            }
        }

        function formatarTipoMovimentacao(tipo) {
            const tipos = {
                'entrada': 'Entrada',
                'saida': 'Saída',
                'transferencia': 'Transferência',
                'ajuste': 'Ajuste'
            };
            return tipos[tipo] || tipo;
        }

        function atualizarTotalProdutos(total) {
            document.getElementById('totalProdutos').textContent = `${total} produto(s) encontrado(s)`;
        }

        function mostrarLoading(mostrar) {
            document.getElementById('loadingOverlay').classList.toggle('d-none', !mostrar);
        }

        // ========== FUNÇÕES DE AÇÃO ==========
        function confirmarMovimentacao() {
            mostrarNotificacao('Movimentação registrada com sucesso!', 'success');
            modalMovimentacao.hide();
        }

        function confirmarTransferencia() {
            mostrarNotificacao('Transferência realizada com sucesso!', 'success');
            modalTransferencia.hide();
        }

        function iniciarInventario() {
            mostrarNotificacao('Inventário iniciado com sucesso!', 'success');
            modalInventario.hide();
        }

        function incluirEmInventario() {
            mostrarNotificacao('Produto incluído no inventário!', 'success');
        }

        function exportarFichaEstoque() {
            mostrarNotificacao('Ficha de estoque exportada com sucesso!', 'success');
        }

        function adicionarProdutoInventario() {
            mostrarNotificacao('Produto adicionado ao inventário!', 'success');
        }

        function selecionarTodosProdutos() {
            const checkboxes = document.querySelectorAll('#listaProdutosInventario input[type="checkbox"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = true;
            });
            mostrarNotificacao('Todos os produtos selecionados!', 'success');
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