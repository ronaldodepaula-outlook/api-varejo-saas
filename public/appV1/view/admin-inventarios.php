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

$id_usuario = $_SESSION['user_id']; // = $data['usuario']['id_usuario'];

// Extrair nome do usuário de forma segura
$nomeUsuario = '';
if (is_array($usuario)) {
    $nomeUsuario = $usuario['nome'] ?? $usuario['name'] ?? 'Usuário';
} else {
    $nomeUsuario = (string)$usuario;
}

// Extrair ID do usuário da sessão
$id_usuario = $_SESSION['user_id'] ?? ($usuario['id'] ?? 1);

// Primeira letra para o avatar
$inicialUsuario = strtoupper(substr($nomeUsuario, 0, 1));

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Inventários - SAS Multi</title>
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
        
        .status-em_andamento { background: rgba(52, 152, 219, 0.1); color: var(--primary-color); }
        .status-concluido { background: rgba(39, 174, 96, 0.1); color: var(--success-color); }
        .status-cancelado { background: rgba(231, 76, 60, 0.1); color: var(--danger-color); }
        
        .inventory-card {
            transition: all 0.3s ease;
            border-left: 4px solid #dee2e6;
        }
        .inventory-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .inventory-card.status-em_andamento { border-left-color: var(--primary-color); }
        .inventory-card.status-concluido { border-left-color: var(--success-color); }
        .inventory-card.status-cancelado { border-left-color: var(--danger-color); }
        
        .product-item {
            border-left: 3px solid transparent;
            transition: all 0.3s ease;
        }
        .product-item:hover {
            background-color: #f8f9fa;
            border-left-color: var(--primary-color);
        }
        .product-item.counted {
            background-color: #d4edda;
            border-left-color: var(--success-color);
        }
        .product-item.difference {
            background-color: #fff3cd;
            border-left-color: var(--warning-color);
        }
        
        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2rem;
        }
        .step {
            flex: 1;
            text-align: center;
            position: relative;
        }
        .step::after {
            content: '';
            position: absolute;
            top: 20px;
            left: 50%;
            width: 100%;
            height: 2px;
            background: #dee2e6;
            z-index: -1;
        }
        .step:last-child::after {
            display: none;
        }
        .step.active .step-circle {
            background: var(--primary-color);
            color: white;
        }
        .step.completed .step-circle {
            background: var(--success-color);
            color: white;
        }
        .step-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #dee2e6;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            font-weight: bold;
        }
        
        .difference-positive { color: var(--success-color); }
        .difference-negative { color: var(--danger-color); }
        
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
                        <li class="breadcrumb-item active">Gestão de Inventários - <?PHP ECHO $id_usuario;?></li>
                    </ol>
                </nav>
            </div>
            
            <div class="header-right">
                <!-- Notificações -->
                <div class="dropdown">
                    <button class="btn btn-link text-dark position-relative" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-bell" style="font-size: 1.25rem;"></i>
                        <span class="badge bg-danger position-absolute translate-middle rounded-pill" style="font-size: 0.6rem; top: 8px; right: 8px;">2</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><h6 class="dropdown-header">Alertas de Inventário</h6></li>
                        <li><a class="dropdown-item" href="#">1 inventário em andamento</a></li>
                        <li><a class="dropdown-item" href="#">3 inventários pendentes</a></li>
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
                    <h1 class="page-title">Gestão de Inventários</h1>
                    <p class="page-subtitle">Controle, execute e acompanhe seus inventários</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary" onclick="exportarRelatorio()">
                        <i class="bi bi-file-earmark-text me-2"></i>Relatório
                    </button>
                    <button class="btn btn-primary" onclick="abrirModalNovaCapaInventario()">
                        <i class="bi bi-plus-circle me-2"></i>Novo Inventário
                    </button>
                    <!-- No card de inventário, na seção de botões -->

                </div>
            </div>
            
            <!-- Resumo do Inventário -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card-custom">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="text-primary mb-0" id="totalInventariosResumo">0</h5>
                                    <p class="text-muted mb-0">Total de Inventários</p>
                                </div>
                                <div class="bg-primary text-white rounded p-3">
                                    <i class="bi bi-clipboard-data" style="font-size: 1.5rem;"></i>
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
                                    <h5 class="text-warning mb-0" id="inventariosAndamentoResumo">0</h5>
                                    <p class="text-muted mb-0">Em Andamento</p>
                                </div>
                                <div class="bg-warning text-white rounded p-3">
                                    <i class="bi bi-hourglass-split" style="font-size: 1.5rem;"></i>
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
                                    <h5 class="text-success mb-0" id="inventariosConcluidosResumo">0</h5>
                                    <p class="text-muted mb-0">Concluídos</p>
                                </div>
                                <div class="bg-success text-white rounded p-3">
                                    <i class="bi bi-check-circle" style="font-size: 1.5rem;"></i>
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
                                    <h5 class="text-danger mb-0" id="inventariosCanceladosResumo">0</h5>
                                    <p class="text-muted mb-0">Cancelados</p>
                                </div>
                                <div class="bg-danger text-white rounded p-3">
                                    <i class="bi bi-x-circle" style="font-size: 1.5rem;"></i>
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
                        <div class="col-md-3">
                            <div class="search-box">
                                <i class="bi bi-search"></i>
                                <input type="text" class="form-control" id="searchInput" placeholder="Buscar por código, descrição...">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" id="filterFilial">
                                <option value="">Todas as filiais</option>
                                <!-- Filiais serão carregadas dinamicamente -->
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" id="filterStatus">
                                <option value="">Todos os status</option>
                                <option value="em_andamento">Em Andamento</option>
                                <option value="concluido">Concluído</option>
                                <option value="cancelado">Cancelado</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Período</label>
                            <div class="input-group">
                                <input type="date" class="form-control" id="filterDataInicio">
                                <span class="input-group-text">até</span>
                                <input type="date" class="form-control" id="filterDataFim">
                            </div>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button class="btn btn-outline-secondary w-100" onclick="limparFiltros()">
                                <i class="bi bi-arrow-clockwise"></i> Limpar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Lista de Inventários -->
            <div class="row" id="listaInventarios">
                <!-- Os cards de inventários serão carregados dinamicamente aqui -->
                <div class="col-12 text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Carregando...</span>
                    </div>
                    <p class="mt-2 text-muted">Carregando inventários...</p>
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

    <!-- Modal para Nova Capa de Inventário -->
    <div class="modal fade" id="modalNovaCapaInventario" tabindex="-1" aria-labelledby="modalNovaCapaInventarioLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalNovaCapaInventarioLabel">Nova Capa de Inventário</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Step Indicator -->
                    <div class="step-indicator">
                        <div class="step active">
                            <div class="step-circle">1</div>
                            <div>Configuração</div>
                        </div>
                        <div class="step">
                            <div class="step-circle">2</div>
                            <div>Seleção de Produtos</div>
                        </div>
                        <div class="step">
                            <div class="step-circle">3</div>
                            <div>Confirmação</div>
                        </div>
                    </div>

                    <!-- Step 1: Configuração -->
                    <div id="step1" class="step-content">
                        <form id="formCapaInventario">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Descrição *</label>
                                    <input type="text" class="form-control" id="descricaoCapa" placeholder="Ex: Inventário Mensal - Janeiro 2024" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Filial *</label>
                                    <select class="form-select" id="filialCapa" required>
                                        <option value="">Selecione...</option>
                                        <!-- Filiais serão carregadas dinamicamente -->
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Data de Início *</label>
                                    <input type="date" class="form-control" id="dataInicioCapa" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Status *</label>
                                    <select class="form-select" id="statusCapa" required>
                                        <option value="em_andamento">Em Andamento</option>
                                        <option value="concluido">Concluído</option>
                                        <option value="cancelado">Cancelado</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Observações</label>
                                    <textarea class="form-control" id="observacoesCapa" rows="3" placeholder="Observações sobre o inventário..."></textarea>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Step 2: Seleção de Produtos -->
                    <div id="step2" class="step-content" style="display: none;">
                        <div class="mb-3">
                            <label class="form-label">Selecionar Produtos</label>
                            <div class="row g-2 mb-3">
                                <div class="col-md-6">
                                    <select class="form-select" id="tipoSelecaoProdutos">
                                        <option value="todos">Todos os produtos</option>
                                        <option value="categoria">Por categoria</option>
                                        <option value="personalizada">Seleção personalizada</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <select class="form-select" id="categoriaProdutos" style="display: none;">
                                        <option value="">Todas as categorias</option>
                                        <!-- Categorias serão carregadas dinamicamente -->
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="selectAllProdutos">
                                <label class="form-check-label fw-bold" for="selectAllProdutos">
                                    Selecionar todos os produtos
                                </label>
                            </div>
                            <hr>
                            <div id="listaProdutosSelecao">
                                <!-- Lista de produtos será carregada dinamicamente -->
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <small class="text-muted">
                                <i class="bi bi-info-circle me-1"></i>
                                <span id="contadorProdutosSelecionados">0</span> produtos selecionados para o inventário
                            </small>
                        </div>
                    </div>

                    <!-- Step 3: Confirmação -->
                    <div id="step3" class="step-content" style="display: none;">
                        <div class="alert alert-info">
                            <h6><i class="bi bi-info-circle me-2"></i>Resumo do Inventário</h6>
                            <ul class="mb-0" id="resumoInventario">
                                <!-- Resumo será preenchido dinamicamente -->
                            </ul>
                        </div>
                        
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="confirmarInventario" required>
                            <label class="form-check-label" for="confirmarInventario">
                                Confirmo que todas as informações estão corretas e desejo criar o inventário
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-outline-primary" id="prevBtn" style="display: none;" onclick="anteriorPasso()">Anterior</button>
                    <button type="button" class="btn btn-primary" id="nextBtn" onclick="proximoPasso()">Próximo</button>
                    <button type="button" class="btn btn-success" id="createBtn" style="display: none;" onclick="criarCapaInventario()">Criar Inventário</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Detalhes do Inventário -->
    <div class="modal fade" id="modalDetalhesInventario" tabindex="-1" aria-labelledby="modalDetalhesInventarioLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalDetalhesInventarioLabel">Detalhes do Inventário</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="detalhesInventarioCarregando" class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Carregando...</span>
                        </div>
                        <p class="mt-2">Carregando detalhes do inventário...</p>
                    </div>
                    
                    <div id="detalhesInventarioConteudo" style="display: none;">
                        <!-- Header do Inventário -->
                        <div class="row mb-4">
                            <div class="col-md-8">
                                <h4 id="inventarioDescricao"></h4>
                                <p class="text-muted" id="inventarioCodigo"></p>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <small class="text-muted">Filial:</small>
                                        <div><strong id="inventarioFilial"></strong></div>
                                    </div>
                                    <div class="col-md-4">
                                        <small class="text-muted">Data de Início:</small>
                                        <div><strong id="inventarioDataInicio"></strong></div>
                                    </div>
                                    <div class="col-md-4">
                                        <small class="text-muted">Status:</small>
                                        <div><strong id="inventarioStatus"></strong></div>
                                    </div>
                                </div>
                                <div class="row g-3 mt-2">
                                    <div class="col-md-6">
                                        <small class="text-muted">Responsável:</small>
                                        <div><strong id="inventarioUsuario"></strong></div>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted">Data de Fechamento:</small>
                                        <div><strong id="inventarioDataFechamento"></strong></div>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <small class="text-muted">Observações:</small>
                                    <div><strong id="inventarioObservacoes"></strong></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <div class="card bg-primary text-white text-center">
                                            <div class="card-body py-2">
                                                <h6 class="card-title mb-1">Total de Itens</h6>
                                                <h4 class="mb-0" id="inventarioTotalItens">0</h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="card bg-success text-white text-center">
                                            <div class="card-body py-2">
                                                <h6 class="card-title mb-1">Contados</h6>
                                                <h4 class="mb-0" id="inventarioContados">0</h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="card bg-warning text-white text-center">
                                            <div class="card-body py-2">
                                                <h6 class="card-title mb-1">Diferenças</h6>
                                                <h4 class="mb-0" id="inventarioDiferencas">0</h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="card bg-info text-white text-center">
                                            <div class="card-body py-2">
                                                <h6 class="card-title mb-1">Progresso</h6>
                                                <h4 class="mb-0" id="inventarioProgresso">0%</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Ações -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="d-flex gap-2">
                                    <button class="btn btn-outline-primary" onclick="iniciarContagem()">
                                        <i class="bi bi-play-circle me-1"></i>Iniciar Contagem
                                    </button>
                                    <button class="btn btn-outline-success" onclick="finalizarInventario()">
                                        <i class="bi bi-check-circle me-1"></i>Finalizar Inventário
                                    </button>
                                    <button class="btn btn-outline-danger" onclick="cancelarInventario()">
                                        <i class="bi bi-x-circle me-1"></i>Cancelar
                                    </button>
                                    <button class="btn btn-outline-secondary" onclick="exportarDetalhes()">
                                        <i class="bi bi-download me-1"></i>Exportar
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Itens do Inventário -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Itens do Inventário</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Produto</th>
                                                <th>Descrição</th>
                                                <th>Qtd. Sistema</th>
                                                <th>Qtd. Física</th>
                                                <th>Diferença</th>
                                                <th>Motivo</th>
                                                <th>Status</th>
                                                <th>Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbodyItensInventario">
                                            <!-- Itens serão carregados dinamicamente -->
                                        </tbody>
                                    </table>
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

    <!-- Modal para Contagem de Item -->
    <div class="modal fade" id="modalContagemItem" tabindex="-1" aria-labelledby="modalContagemItemLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalContagemItemLabel">Registrar Contagem</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formContagemItem">
                        <input type="hidden" id="idInventarioItem">
                        <input type="hidden" id="idCapaInventario">
                        <div class="mb-3">
                            <label class="form-label">Produto</label>
                            <p class="form-control-plaintext fw-bold" id="produtoContagem"></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Quantidade no Sistema</label>
                            <p class="form-control-plaintext" id="quantidadeSistemaContagem"></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Quantidade Física *</label>
                            <input type="number" class="form-control" id="quantidadeFisicaContagem" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Motivo da Diferença (se houver)</label>
                            <textarea class="form-control" id="motivoContagem" rows="3" placeholder="Descreva o motivo da diferença, se aplicável..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="salvarContagem()">Salvar Contagem</button>
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
        
        let capasInventario = [];
        let filiais = [];
        let produtos = [];
        let categorias = [];
        let capaSelecionada = null;
        let modalNovaCapaInventario = null;
        let modalDetalhesInventario = null;
        let modalContagemItem = null;
        let passoAtual = 1;
        const totalPassos = 3;

        // Configuração da API
        const API_CONFIG = {
            // Endpoints para Capa de Inventário
            CAPAS_INVENTARIO_EMPRESA: (idEmpresa) => 
                `${BASE_URL}/api/v1/capas-inventario/empresa/${idEmpresa}`,
            
            CAPA_INVENTARIO: (idCapa) => 
                `${BASE_URL}/api/capa-inventarios/${idCapa}`,
            
            CAPA_INVENTARIO_CREATE: () => 
                `${BASE_URL}/api/capa-inventarios`,
            
            CAPA_INVENTARIO_UPDATE: (idCapa) => 
                `${BASE_URL}/api/capa-inventarios/${idCapa}`,
            
            CAPA_INVENTARIO_DELETE: (idCapa) => 
                `${BASE_URL}/api/capa-inventarios/${idCapa}`,
            
            // Endpoints para Inventário (Itens)
            INVENTARIOS_CAPA: (idCapa) => 
                `${BASE_URL}/api/inventarios/capa/${idCapa}`,
            
            INVENTARIO_CREATE: () => 
                `${BASE_URL}/api/inventarios`,
            
            INVENTARIO_UPDATE: (idInventario) => 
                `${BASE_URL}/api/inventarios/${idInventario}`,
            
            // Outros endpoints
            FILIAIS_EMPRESA: (idEmpresa) => 
                `${BASE_URL}/api/filiais/empresa/${idEmpresa}`,
            
            PRODUTOS_EMPRESA: (idEmpresa) => 
                `${BASE_URL}/api/v1/empresas/${idEmpresa}/produtos`,
            
            getHeaders: function() {
                return {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-ID-EMPRESA': idEmpresa.toString()
                };
            }
        };

        // Inicialização
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar modais
            modalNovaCapaInventario = new bootstrap.Modal(document.getElementById('modalNovaCapaInventario'));
            modalDetalhesInventario = new bootstrap.Modal(document.getElementById('modalDetalhesInventario'));
            modalContagemItem = new bootstrap.Modal(document.getElementById('modalContagemItem'));
            
            // Carregar dados iniciais
            carregarCapasInventario();
            carregarFiliais();
            carregarProdutos();
            
            // Configurar eventos
            document.getElementById('searchInput').addEventListener('input', filtrarInventarios);
            document.getElementById('filterStatus').addEventListener('change', filtrarInventarios);
            document.getElementById('filterFilial').addEventListener('change', filtrarInventarios);
            document.getElementById('filterDataInicio').addEventListener('change', filtrarInventarios);
            document.getElementById('filterDataFim').addEventListener('change', filtrarInventarios);
            document.getElementById('tipoSelecaoProdutos').addEventListener('change', toggleCategoriaSelecao);
            document.getElementById('selectAllProdutos').addEventListener('change', selecionarTodosProdutos);
            
            // Logoff
            var logoutBtn = document.getElementById('logoutBtn');
            if (logoutBtn) {
                logoutBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    fazerLogoff();
                });
            }
            
            // Configurar data atual para o campo de data de início
            const hoje = new Date().toISOString().split('T')[0];
            document.getElementById('dataInicioCapa').value = hoje;
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

        // ========== CAPAS DE INVENTÁRIO ==========
        async function carregarCapasInventario() {
            mostrarLoading(true);
            
            try {
                const response = await fetch(
                    API_CONFIG.CAPAS_INVENTARIO_EMPRESA(idEmpresa),
                    {
                        method: 'GET',
                        headers: API_CONFIG.getHeaders()
                    }
                );
                
                if (!response.ok) {
                    throw new Error(`Erro ${response.status}: ${response.statusText}`);
                }
                
                const data = await response.json();
                capasInventario = data || [];
                
                exibirCapasInventario(capasInventario);
                atualizarResumoInventarios(capasInventario);
                
            } catch (error) {
                console.error('Erro ao carregar capas de inventário:', error);
                mostrarNotificacao('Erro ao carregar inventários: ' + error.message, 'error');
                document.getElementById('listaInventarios').innerHTML = '<div class="col-12 text-center py-5 text-muted">Erro ao carregar dados</div>';
            } finally {
                mostrarLoading(false);
            }
        }

        function exibirCapasInventario(listaCapas) {
            const container = document.getElementById('listaInventarios');
            
            if (listaCapas.length === 0) {
                container.innerHTML = '<div class="col-12 text-center py-5 text-muted">Nenhum inventário encontrado</div>';
                return;
            }
            
            container.innerHTML = listaCapas.map(capa => {
                const statusClass = `status-${capa.status}`;
                const statusText = formatarStatus(capa.status);
                const dataInicio = formatarData(capa.data_inicio);
                const dataFechamento = capa.data_fechamento ? formatarData(capa.data_fechamento) : '-';
                const totalItens = capa.inventarios ? capa.inventarios.length : 0;
                const itensContados = capa.inventarios ? capa.inventarios.filter(item => item.quantidade_fisica !== null).length : 0;
                const progresso = totalItens > 0 ? Math.round((itensContados / totalItens) * 100) : 0;
                
                return `
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="card inventory-card ${statusClass} h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">#${capa.id_capa_inventario}</h6>
                                <span class="status-badge ${statusClass}">${statusText}</span>
                            </div>
                            <div class="card-body">
                                <h6 class="card-title">${capa.descricao}</h6>
                                <p class="card-text text-muted small">
                                    <i class="bi bi-geo-alt me-1"></i>${capa.filial ? capa.filial.nome_filial : 'N/A'}<br>
                                    <i class="bi bi-calendar me-1"></i>Iniciado em: ${dataInicio}<br>
                                    <i class="bi bi-person me-1"></i>Responsável: ${capa.usuario ? capa.usuario.nome : 'N/A'}
                                </p>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <small class="text-muted">Total de Itens:</small>
                                        <div><strong>${totalItens}</strong></div>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">Contados:</small>
                                        <div><strong>${itensContados}</strong></div>
                                    </div>
                                </div>
                                <div class="progress mb-3" style="height: 6px;">
                                    <div class="progress-bar ${getProgressBarColor(progresso)}" style="width: ${progresso}%"></div>
                                </div>
                                <div class="d-flex gap-1">
                                    <button class="btn btn-primary btn-sm flex-fill" onclick="abrirDetalhesInventario(${capa.id_capa_inventario})">
                                        <i class="bi bi-eye"></i> Abrir
                                    </button>
                                    ${capa.status === 'em_andamento' ? `
                                        <button class="btn btn-outline-success btn-sm flex-fill" onclick="finalizarInventario(${capa.id_capa_inventario})">
                                            <i class="bi bi-check"></i> Finalizar
                                        </button>
                                        <button class="btn btn-outline-info btn-sm" onclick="irParaContagem(${capa.id_capa_inventario})">
    <i class="bi bi-clipboard-check"></i> Contagem
</button>
                                        <button class="btn btn-outline-danger btn-sm" onclick="cancelarInventario(${capa.id_capa_inventario})">
                                            <i class="bi bi-x"></i>
                                        </button>
                                    ` : `
                                        <button class="btn btn-outline-info btn-sm flex-fill" onclick="exportarRelatorioInventario(${capa.id_capa_inventario})">
                                            <i class="bi bi-file-text"></i> Relatório
                                        </button>
                                        <button class="btn btn-outline-secondary btn-sm">
                                            <i class="bi bi-download"></i>
                                        </button>
                                    `}
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        }

        function getProgressBarColor(progresso) {
            if (progresso < 30) return 'bg-danger';
            if (progresso < 70) return 'bg-warning';
            return 'bg-success';
        }

        function atualizarResumoInventarios(capas) {
            const total = capas.length;
            const emAndamento = capas.filter(c => c.status === 'em_andamento').length;
            const concluidos = capas.filter(c => c.status === 'concluido').length;
            const cancelados = capas.filter(c => c.status === 'cancelado').length;
            
            document.getElementById('totalInventariosResumo').textContent = total;
            document.getElementById('inventariosAndamentoResumo').textContent = emAndamento;
            document.getElementById('inventariosConcluidosResumo').textContent = concluidos;
            document.getElementById('inventariosCanceladosResumo').textContent = cancelados;
        }

        // ========== DETALHES DO INVENTÁRIO ==========
        async function abrirDetalhesInventario(idCapa) {
            mostrarLoading(true);
            capaSelecionada = idCapa;
            
            try {
                // Mostrar loading no modal
                document.getElementById('detalhesInventarioCarregando').style.display = 'block';
                document.getElementById('detalhesInventarioConteudo').style.display = 'none';
                
                // Buscar capa de inventário
                const responseCapa = await fetch(
                    API_CONFIG.CAPA_INVENTARIO(idCapa),
                    {
                        method: 'GET',
                        headers: API_CONFIG.getHeaders()
                    }
                );
                
                if (!responseCapa.ok) {
                    throw new Error(`Erro ${responseCapa.status}: ${responseCapa.statusText}`);
                }
                
                const capa = await responseCapa.json();
                
                // Buscar itens do inventário
                const responseItens = await fetch(
                    API_CONFIG.INVENTARIOS_CAPA(idCapa),
                    {
                        method: 'GET',
                        headers: API_CONFIG.getHeaders()
                    }
                );
                
                if (!responseItens.ok) {
                    throw new Error(`Erro ${responseItens.status}: ${responseItens.statusText}`);
                }
                
                const itens = await responseItens.json();
                
                // Preencher dados da capa
                preencherDadosCapa(capa);
                
                // Preencher itens do inventário
                preencherItensInventario(itens);
                
                // Mostrar conteúdo
                document.getElementById('detalhesInventarioCarregando').style.display = 'none';
                document.getElementById('detalhesInventarioConteudo').style.display = 'block';
                
                modalDetalhesInventario.show();
                
            } catch (error) {
                console.error('Erro ao carregar detalhes do inventário:', error);
                mostrarNotificacao('Erro ao carregar detalhes: ' + error.message, 'error');
            } finally {
                mostrarLoading(false);
            }
        }

        function preencherDadosCapa(capa) {
            document.getElementById('inventarioDescricao').textContent = capa.descricao;
            document.getElementById('inventarioCodigo').textContent = `Código: #${capa.id_capa_inventario}`;
            document.getElementById('inventarioFilial').textContent = capa.filial ? capa.filial.nome_filial : 'N/A';
            document.getElementById('inventarioDataInicio').textContent = formatarData(capa.data_inicio);
            document.getElementById('inventarioStatus').textContent = formatarStatus(capa.status);
            document.getElementById('inventarioUsuario').textContent = capa.usuario ? capa.usuario.nome : 'N/A';
            document.getElementById('inventarioDataFechamento').textContent = capa.data_fechamento ? formatarData(capa.data_fechamento) : '-';
            document.getElementById('inventarioObservacoes').textContent = capa.observacao || 'Nenhuma observação';
            
            // Calcular estatísticas
            const totalItens = capa.inventarios ? capa.inventarios.length : 0;
            const itensContados = capa.inventarios ? capa.inventarios.filter(item => item.quantidade_fisica !== null).length : 0;
            const itensComDiferenca = capa.inventarios ? capa.inventarios.filter(item => {
                const diferenca = parseFloat(item.diferenca || 0);
                return diferenca !== 0;
            }).length : 0;
            const progresso = totalItens > 0 ? Math.round((itensContados / totalItens) * 100) : 0;
            
            document.getElementById('inventarioTotalItens').textContent = totalItens;
            document.getElementById('inventarioContados').textContent = itensContados;
            document.getElementById('inventarioDiferencas').textContent = itensComDiferenca;
            document.getElementById('inventarioProgresso').textContent = `${progresso}%`;
        }

        function preencherItensInventario(itens) {
            const tbody = document.getElementById('tbodyItensInventario');
            
            if (itens.length === 0) {
                tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">Nenhum item encontrado</td></tr>';
                return;
            }
            
            tbody.innerHTML = itens.map(item => {
                const produto = item.produto;
                const quantidadeSistema = parseFloat(item.quantidade_sistema || 0);
                const quantidadeFisica = parseFloat(item.quantidade_fisica || 0);
                const diferenca = parseFloat(item.diferenca || 0);
                const diferencaClass = diferenca > 0 ? 'difference-positive' : diferenca < 0 ? 'difference-negative' : '';
                const diferencaSinal = diferenca > 0 ? '+' : '';
                const status = item.quantidade_fisica !== null ? 'counted' : '';
                const statusText = item.quantidade_fisica !== null ? 'Contado' : 'Pendente';
                const statusBadge = item.quantidade_fisica !== null ? 'bg-success' : 'bg-warning';
                
                return `
                    <tr class="product-item ${status}">
                        <td>
                            <div class="fw-semibold">${produto ? produto.id_produto : 'N/A'}</div>
                            <small class="text-muted">${produto ? produto.codigo_barras || 'Sem código' : 'N/A'}</small>
                        </td>
                        <td>${produto ? produto.descricao : 'N/A'}</td>
                        <td>${quantidadeSistema} ${produto ? produto.unidade_medida : ''}</td>
                        <td>${item.quantidade_fisica !== null ? quantidadeFisica : '-'}</td>
                        <td class="${diferencaClass}">${item.quantidade_fisica !== null ? `${diferencaSinal}${diferenca}` : '-'}</td>
                        <td>${item.motivo || '-'}</td>
                        <td>
                            <span class="badge ${statusBadge}">${statusText}</span>
                        </td>
                        <td>
                            <button class="btn btn-primary btn-sm" onclick="abrirModalContagemItem(${item.id_inventario}, ${produto ? produto.id_produto : 0})" ${item.quantidade_fisica !== null ? 'disabled' : ''}>
                                <i class="bi bi-pencil"></i>
                            </button>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        // ========== MODAIS ==========
        function abrirModalNovaCapaInventario() {
            // Limpar formulário
            document.getElementById('formCapaInventario').reset();
            
            // Resetar passos
            passoAtual = 1;
            document.querySelectorAll('.step-content').forEach(step => step.style.display = 'none');
            document.getElementById('step1').style.display = 'block';
            document.querySelectorAll('.step').forEach(step => {
                step.classList.remove('active', 'completed');
            });
            document.querySelector('.step:first-child').classList.add('active');
            atualizarBotoesPasso();
            
            // Carregar opções de filiais
            carregarOpcoesFiliais('filialCapa');
            
            modalNovaCapaInventario.show();
        }

        function abrirModalContagemItem(idInventario, idProduto) {
            // Buscar dados do item
            const produto = produtos.find(p => p.id_produto === idProduto);
            const item = capaSelecionada ? capaSelecionada.inventarios.find(i => i.id_inventario === idInventario) : null;
            
            if (!produto) {
                mostrarNotificacao('Produto não encontrado', 'error');
                return;
            }
            
            document.getElementById('idInventarioItem').value = idInventario;
            document.getElementById('idCapaInventario').value = capaSelecionada;
            document.getElementById('produtoContagem').textContent = `${produto.id_produto} - ${produto.descricao}`;
            document.getElementById('quantidadeSistemaContagem').textContent = `${item ? item.quantidade_sistema : 0} ${produto.unidade_medida}`;
            document.getElementById('quantidadeFisicaContagem').value = '';
            document.getElementById('motivoContagem').value = '';
            
            modalContagemItem.show();
        }

        // ========== FUNÇÕES DE NAVEGAÇÃO DO MODAL ==========
        function proximoPasso() {
            if (passoAtual < totalPassos) {
                // Validar passo atual
                if (passoAtual === 1 && !validarPasso1()) {
                    return;
                }
                
                document.getElementById(`step${passoAtual}`).style.display = 'none';
                document.querySelector(`.step:nth-child(${passoAtual})`).classList.remove('active');
                document.querySelector(`.step:nth-child(${passoAtual})`).classList.add('completed');
                
                passoAtual++;
                
                document.getElementById(`step${passoAtual}`).style.display = 'block';
                document.querySelector(`.step:nth-child(${passoAtual})`).classList.add('active');
                
                // Carregar dados específicos do passo
                if (passoAtual === 2) {
                    carregarProdutosParaSelecao();
                } else if (passoAtual === 3) {
                    gerarResumoInventario();
                }
                
                atualizarBotoesPasso();
            }
        }

        function anteriorPasso() {
            if (passoAtual > 1) {
                document.getElementById(`step${passoAtual}`).style.display = 'none';
                document.querySelector(`.step:nth-child(${passoAtual})`).classList.remove('active');
                
                passoAtual--;
                
                document.getElementById(`step${passoAtual}`).style.display = 'block';
                document.querySelector(`.step:nth-child(${passoAtual})`).classList.remove('completed');
                document.querySelector(`.step:nth-child(${passoAtual})`).classList.add('active');
                
                atualizarBotoesPasso();
            }
        }

        function atualizarBotoesPasso() {
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');
            const createBtn = document.getElementById('createBtn');
            
            prevBtn.style.display = passoAtual > 1 ? 'inline-block' : 'none';
            nextBtn.style.display = passoAtual < totalPassos ? 'inline-block' : 'none';
            createBtn.style.display = passoAtual === totalPassos ? 'inline-block' : 'none';
        }

        function validarPasso1() {
            const descricao = document.getElementById('descricaoCapa').value;
            const filial = document.getElementById('filialCapa').value;
            const dataInicio = document.getElementById('dataInicioCapa').value;
            
            if (!descricao || !filial || !dataInicio) {
                mostrarNotificacao('Preencha todos os campos obrigatórios', 'error');
                return false;
            }
            
            return true;
        }

        // ========== FUNÇÕES DE AÇÃO ==========
        async function criarCapaInventario() {
            if (!document.getElementById('confirmarInventario').checked) {
                mostrarNotificacao('Confirme as informações antes de criar o inventário', 'error');
                return;
            }
            
            mostrarLoading(true);
            
            try {
                // Dados da capa
                const descricao = document.getElementById('descricaoCapa').value;
                const idFilial = document.getElementById('filialCapa').value;
                const dataInicio = document.getElementById('dataInicioCapa').value;
                const status = document.getElementById('statusCapa').value;
                const observacao = document.getElementById('observacoesCapa').value;
                
                // Criar capa de inventário
                const responseCapa = await fetch(API_CONFIG.CAPA_INVENTARIO_CREATE(), {
                    method: 'POST',
                    headers: API_CONFIG.getHeaders(),
                    body: JSON.stringify({
                        id_empresa: idEmpresa,
                        id_filial: parseInt(idFilial),
                        descricao: descricao,
                        data_inicio: dataInicio,
                        status: status,
                        observacao: observacao,
                        id_usuario: idUsuario
                    })
                });
                
                if (!responseCapa.ok) {
                    const errorData = await responseCapa.json();
                    throw new Error(`Erro ${responseCapa.status}: ${JSON.stringify(errorData)}`);
                }
                
                const capaCriada = await responseCapa.json();
                
                // Criar itens do inventário
                const produtosSelecionados = obterProdutosSelecionados();
                const promises = [];
                
                for (const produto of produtosSelecionados) {
                    // Buscar estoque atual do produto
                    const estoqueAtual = await buscarEstoqueProduto(produto.id_produto, idFilial);
                    
                    const promise = fetch(API_CONFIG.INVENTARIO_CREATE(), {
                        method: 'POST',
                        headers: API_CONFIG.getHeaders(),
                        body: JSON.stringify({
                            id_capa_inventario: parseInt(capaCriada.id_capa_inventario),
                            id_empresa: parseInt(idEmpresa),
                            id_filial: parseInt(idFilial),
                            id_produto: parseInt(produto.id_produto),
                            quantidade_fisica: 0, // Inicialmente 0, será atualizado na contagem
                            quantidade_sistema: parseFloat(estoqueAtual),
                            motivo: null,
                            data_inventario: dataInicio + 'T00:00:00Z',
                            id_usuario: idUsuario
                        })
                    });
                    promises.push(promise);
                }
                
                // Aguardar todas as criações de itens
                await Promise.all(promises);
                
                mostrarNotificacao('Inventário criado com sucesso!', 'success');
                modalNovaCapaInventario.hide();
                
                // Recarregar lista de inventários
                carregarCapasInventario();
                
            } catch (error) {
                console.error('Erro ao criar inventário:', error);
                mostrarNotificacao('Erro ao criar inventário: ' + error.message, 'error');
            } finally {
                mostrarLoading(false);
            }
        }

        async function buscarEstoqueProduto(idProduto, idFilial) {
            try {
                const response = await fetch(
                    `${BASE_URL}/api/estoques/empresa/${idEmpresa}/produto/${idProduto}/filiais`,
                    {
                        method: 'GET',
                        headers: API_CONFIG.getHeaders()
                    }
                );
                
                if (response.ok) {
                    const estoqueFiliais = await response.json();
                    const estoqueFilial = estoqueFiliais.find(item => item.filial.id_filial == idFilial);
                    return estoqueFilial ? parseFloat(estoqueFilial.produto.quantidade || 0) : 0;
                }
                return 0;
            } catch (error) {
                console.error('Erro ao buscar estoque do produto:', error);
                return 0;
            }
        }

        async function salvarContagem() {
            const idInventario = document.getElementById('idInventarioItem').value;
            const quantidadeFisica = document.getElementById('quantidadeFisicaContagem').value;
            const motivo = document.getElementById('motivoContagem').value;
            
            if (!quantidadeFisica) {
                mostrarNotificacao('Informe a quantidade física', 'error');
                return;
            }
            
            mostrarLoading(true);
            
            try {
                const response = await fetch(API_CONFIG.INVENTARIO_UPDATE(idInventario), {
                    method: 'PUT',
                    headers: API_CONFIG.getHeaders(),
                    body: JSON.stringify({
                        quantidade_fisica: parseFloat(quantidadeFisica),
                        motivo: motivo || null
                    })
                });
                
                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(`Erro ${response.status}: ${JSON.stringify(errorData)}`);
                }
                
                mostrarNotificacao('Contagem registrada com sucesso!', 'success');
                modalContagemItem.hide();
                
                // Recarregar detalhes do inventário
                if (capaSelecionada) {
                    abrirDetalhesInventario(capaSelecionada);
                }
                
            } catch (error) {
                console.error('Erro ao salvar contagem:', error);
                mostrarNotificacao('Erro ao salvar contagem: ' + error.message, 'error');
            } finally {
                mostrarLoading(false);
            }
        }

        async function finalizarInventario(idCapa) {
            if (!confirm('Deseja finalizar este inventário? Esta ação não pode ser desfeita.')) {
                return;
            }
            
            mostrarLoading(true);
            
            try {
                const id = idCapa || capaSelecionada;
                const response = await fetch(API_CONFIG.CAPA_INVENTARIO_UPDATE(id), {
                    method: 'PUT',
                    headers: API_CONFIG.getHeaders(),
                    body: JSON.stringify({
                        status: 'concluido',
                        data_fechamento: new Date().toISOString().split('T')[0] + 'T00:00:00Z'
                    })
                });
                
                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(`Erro ${response.status}: ${JSON.stringify(errorData)}`);
                }
                
                mostrarNotificacao('Inventário finalizado com sucesso!', 'success');
                
                if (modalDetalhesInventario) {
                    modalDetalhesInventario.hide();
                }
                
                // Recarregar lista de inventários
                carregarCapasInventario();
                
            } catch (error) {
                console.error('Erro ao finalizar inventário:', error);
                mostrarNotificacao('Erro ao finalizar inventário: ' + error.message, 'error');
            } finally {
                mostrarLoading(false);
            }
        }

        async function cancelarInventario(idCapa) {
            if (!confirm('Deseja cancelar este inventário? Esta ação não pode ser desfeita.')) {
                return;
            }
            
            mostrarLoading(true);
            
            try {
                const id = idCapa || capaSelecionada;
                const response = await fetch(API_CONFIG.CAPA_INVENTARIO_UPDATE(id), {
                    method: 'PUT',
                    headers: API_CONFIG.getHeaders(),
                    body: JSON.stringify({
                        status: 'cancelado',
                        data_fechamento: new Date().toISOString().split('T')[0] + 'T00:00:00Z'
                    })
                });
                
                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(`Erro ${response.status}: ${JSON.stringify(errorData)}`);
                }
                
                mostrarNotificacao('Inventário cancelado com sucesso!', 'success');
                
                if (modalDetalhesInventario) {
                    modalDetalhesInventario.hide();
                }
                
                // Recarregar lista de inventários
                carregarCapasInventario();
                
            } catch (error) {
                console.error('Erro ao cancelar inventário:', error);
                mostrarNotificacao('Erro ao cancelar inventário: ' + error.message, 'error');
            } finally {
                mostrarLoading(false);
            }
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
                
                // Preencher filtro de filiais
                const filterFilial = document.getElementById('filterFilial');
                filterFilial.innerHTML = '<option value="">Todas as filiais</option>';
                
                filiais.forEach(filial => {
                    filterFilial.innerHTML += `<option value="${filial.id_filial}">${filial.nome_filial}</option>`;
                });
                
            } catch (error) {
                console.error('Erro ao carregar filiais:', error);
            }
        }

        async function carregarProdutos() {
            try {
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
                produtos = data.data || [];
                
            } catch (error) {
                console.error('Erro ao carregar produtos:', error);
            }
        }

        function carregarOpcoesFiliais(selectId) {
            const select = document.getElementById(selectId);
            select.innerHTML = '<option value="">Selecione...</option>';
            
            filiais.forEach(filial => {
                select.innerHTML += `<option value="${filial.id_filial}">${filial.nome_filial}</option>`;
            });
        }

        function carregarProdutosParaSelecao() {
            const container = document.getElementById('listaProdutosSelecao');
            container.innerHTML = '';
            
            produtos.forEach(produto => {
                const div = document.createElement('div');
                div.className = 'form-check mb-2';
                div.innerHTML = `
                    <input class="form-check-input produto-checkbox" type="checkbox" value="${produto.id_produto}" id="prodSelecao${produto.id_produto}">
                    <label class="form-check-label" for="prodSelecao${produto.id_produto}">
                        <strong>${produto.id_produto}</strong> - ${produto.descricao}
                        <small class="text-muted d-block">Estoque atual: ${produto.quantidade_total || 0} ${produto.unidade_medida}</small>
                    </label>
                `;
                container.appendChild(div);
            });
            
            // Atualizar contador
            atualizarContadorProdutosSelecionados();
            
            // Adicionar event listeners aos checkboxes
            document.querySelectorAll('.produto-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', atualizarContadorProdutosSelecionados);
            });
        }

        function toggleCategoriaSelecao() {
            const tipoSelecao = document.getElementById('tipoSelecaoProdutos').value;
            const categoriaSelect = document.getElementById('categoriaProdutos');
            
            categoriaSelect.style.display = tipoSelecao === 'categoria' ? 'block' : 'none';
        }

        function selecionarTodosProdutos() {
            const selectAll = document.getElementById('selectAllProdutos').checked;
            const checkboxes = document.querySelectorAll('.produto-checkbox');
            
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAll;
            });
            
            atualizarContadorProdutosSelecionados();
        }

        function atualizarContadorProdutosSelecionados() {
            const checkboxes = document.querySelectorAll('.produto-checkbox');
            const selecionados = Array.from(checkboxes).filter(cb => cb.checked).length;
            
            document.getElementById('contadorProdutosSelecionados').textContent = selecionados;
        }

        function obterProdutosSelecionados() {
            const checkboxes = document.querySelectorAll('.produto-checkbox:checked');
            const produtosSelecionados = [];
            
            checkboxes.forEach(checkbox => {
                const idProduto = parseInt(checkbox.value);
                const produto = produtos.find(p => p.id_produto === idProduto);
                if (produto) {
                    produtosSelecionados.push(produto);
                }
            });
            
            return produtosSelecionados;
        }

        function gerarResumoInventario() {
            const descricao = document.getElementById('descricaoCapa').value;
            const filialId = document.getElementById('filialCapa').value;
            const filial = filiais.find(f => f.id_filial == filialId);
            const dataInicio = document.getElementById('dataInicioCapa').value;
            const status = document.getElementById('statusCapa').value;
            const produtosSelecionados = obterProdutosSelecionados();
            
            const resumo = document.getElementById('resumoInventario');
            resumo.innerHTML = `
                <li><strong>Código:</strong> Novo</li>
                <li><strong>Descrição:</strong> ${descricao}</li>
                <li><strong>Filial:</strong> ${filial ? filial.nome_filial : 'N/A'}</li>
                <li><strong>Data de Início:</strong> ${formatarData(dataInicio)}</li>
                <li><strong>Status:</strong> ${formatarStatus(status)}</li>
                <li><strong>Produtos:</strong> ${produtosSelecionados.length} selecionados</li>
            `;
        }

        function filtrarInventarios() {
            const termoBusca = document.getElementById('searchInput').value.toLowerCase();
            const statusFiltro = document.getElementById('filterStatus').value;
            const filialFiltro = document.getElementById('filterFilial').value;
            const dataInicioFiltro = document.getElementById('filterDataInicio').value;
            const dataFimFiltro = document.getElementById('filterDataFim').value;
            
            let capasFiltradas = capasInventario;
            
            // Aplicar filtros
            if (termoBusca) {
                capasFiltradas = capasFiltradas.filter(capa => 
                    capa.id_capa_inventario.toString().includes(termoBusca) ||
                    capa.descricao.toLowerCase().includes(termoBusca)
                );
            }
            
            if (statusFiltro) {
                capasFiltradas = capasFiltradas.filter(capa => capa.status === statusFiltro);
            }
            
            if (filialFiltro) {
                capasFiltradas = capasFiltradas.filter(capa => capa.id_filial == filialFiltro);
            }
            
            if (dataInicioFiltro) {
                capasFiltradas = capasFiltradas.filter(capa => 
                    new Date(capa.data_inicio) >= new Date(dataInicioFiltro)
                );
            }
            
            if (dataFimFiltro) {
                capasFiltradas = capasFiltradas.filter(capa => 
                    new Date(capa.data_inicio) <= new Date(dataFimFiltro)
                );
            }
            
            exibirCapasInventario(capasFiltradas);
        }

        function limparFiltros() {
            document.getElementById('searchInput').value = '';
            document.getElementById('filterStatus').value = '';
            document.getElementById('filterFilial').value = '';
            document.getElementById('filterDataInicio').value = '';
            document.getElementById('filterDataFim').value = '';
            
            exibirCapasInventario(capasInventario);
        }

        // ========== FUNÇÕES DE FORMATAÇÃO ==========
        function formatarData(data) {
            if (!data) return '';
            try {
                const date = new Date(data);
                return date.toLocaleDateString('pt-BR');
            } catch (e) {
                return data;
            }
        }

        function formatarStatus(status) {
            const statusMap = {
                'em_andamento': 'Em Andamento',
                'concluido': 'Concluído',
                'cancelado': 'Cancelado'
            };
            return statusMap[status] || status;
        }

        function mostrarLoading(mostrar) {
            document.getElementById('loadingOverlay').classList.toggle('d-none', !mostrar);
        }

        // ========== FUNÇÕES DE RELATÓRIO ==========
        function exportarRelatorio() {
            mostrarNotificacao('Relatório exportado com sucesso!', 'success');
        }

        function exportarRelatorioInventario(idCapa) {
            mostrarNotificacao('Relatório do inventário exportado com sucesso!', 'success');
        }

        function exportarDetalhes() {
            mostrarNotificacao('Detalhes exportados com sucesso!', 'success');
        }

        function iniciarContagem() {
            mostrarNotificacao('Contagem iniciada!', 'success');
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
        <script>
        // Adicione esta função ao script do admin-inventarios.php
function irParaContagem(idCapaInventario) {
    window.location.href = `?view=admin-contagem_inventario&id_inventario=${idCapaInventario}`;
}
    </script>
</body>
</html>