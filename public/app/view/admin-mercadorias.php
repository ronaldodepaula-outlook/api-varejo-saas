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
    <title>Gestão de Categorias - SAS Multi</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
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
        
        .nav-tabs-custom {
            border-bottom: 2px solid #dee2e6;
        }
        
        .nav-tabs-custom .nav-link {
            border: none;
            font-weight: 600;
            color: #6c757d;
            padding: 12px 20px;
            position: relative;
        }
        
        .nav-tabs-custom .nav-link.active {
            color: var(--primary-color);
            background: transparent;
            border-bottom: 2px solid var(--primary-color);
        }
        
        .nav-tabs-custom .nav-link:hover {
            color: var(--primary-color);
            border-color: transparent;
        }
        
        .hierarchy-badge {
            padding: 4px 8px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            background: rgba(52, 152, 219, 0.1);
            color: var(--primary-color);
        }
        
        .hierarchy-item {
            padding: 8px 12px;
            border-radius: 6px;
            margin-bottom: 5px;
            background: #f8f9fa;
            border-left: 3px solid var(--primary-color);
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
                        <li class="breadcrumb-item active">Gestão de Categorias</li>
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
                        <li><h6 class="dropdown-header">Alertas do Sistema</h6></li>
                        <li><a class="dropdown-item" href="#">2 empresas com status pendente</a></li>
                        <li><a class="dropdown-item" href="#">1 nova empresa cadastrada</a></li>
                        <li><a class="dropdown-item" href="#">Backup executado com sucesso</a></li>
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
                    <h1 class="page-title">Gestão de Categorias</h1>
                    <p class="page-subtitle">Organize produtos em categorias, seções, grupos e subgrupos</p>
                </div>
            </div>
            
            <!-- Tabs de Navegação -->
            <ul class="nav nav-tabs nav-tabs-custom mb-4" id="hierarchyTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="categorias-tab" data-bs-toggle="tab" data-bs-target="#categorias" type="button" role="tab" aria-controls="categorias" aria-selected="true">
                        <i class="bi bi-tags me-2"></i>Categorias
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="secoes-tab" data-bs-toggle="tab" data-bs-target="#secoes" type="button" role="tab" aria-controls="secoes" aria-selected="false">
                        <i class="bi bi-collection me-2"></i>Seções
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="grupos-tab" data-bs-toggle="tab" data-bs-target="#grupos" type="button" role="tab" aria-controls="grupos" aria-selected="false">
                        <i class="bi bi-diagram-2 me-2"></i>Grupos
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="subgrupos-tab" data-bs-toggle="tab" data-bs-target="#subgrupos" type="button" role="tab" aria-controls="subgrupos" aria-selected="false">
                        <i class="bi bi-diagram-3 me-2"></i>Subgrupos
                    </button>
                </li>
            </ul>
            
            <!-- Conteúdo das Tabs -->
            <div class="tab-content" id="hierarchyTabsContent">
                <!-- Tab Categorias -->
                <div class="tab-pane fade show active" id="categorias" role="tabpanel" aria-labelledby="categorias-tab">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h5 class="mb-0">Categorias da Empresa</h5>
                            <p class="text-muted mb-0">Crie e gerencie categorias para organizar seus produtos</p>
                        </div>
                        <div>
                            <button class="btn btn-primary" onclick="abrirModalCategoria()">
                                <i class="bi bi-plus-circle me-2"></i>Nova Categoria
                            </button>
                        </div>
                    </div>
                    
                    <!-- Filtros e Busca -->
                    <div class="card-custom mb-4">
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <div class="search-box">
                                        <i class="bi bi-search"></i>
                                        <input type="text" class="form-control" id="searchCategoria" placeholder="Buscar por nome da categoria...">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <button class="btn btn-outline-primary w-100" onclick="carregarCategorias()">
                                        <i class="bi bi-arrow-clockwise me-2"></i>Atualizar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tabela de Categorias -->
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Lista de Categorias</h5>
                            <span class="text-muted" id="totalCategorias">Carregando...</span>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover" id="tabelaCategorias">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nome da Categoria</th>
                                            <th>Descrição</th>
                                            <th>Data Criação</th>
                                            <th width="120">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyCategorias">
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">Carregando categorias...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Tab Seções -->
                <div class="tab-pane fade" id="secoes" role="tabpanel" aria-labelledby="secoes-tab">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h5 class="mb-0">Seções por Categoria</h5>
                            <p class="text-muted mb-0">Organize seções dentro de cada categoria</p>
                        </div>
                        <div>
                            <button class="btn btn-primary" onclick="abrirModalSecao()">
                                <i class="bi bi-plus-circle me-2"></i>Nova Seção
                            </button>
                        </div>
                    </div>
                    
                    <!-- Filtros e Busca -->
                    <div class="card-custom mb-4">
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="search-box">
                                        <i class="bi bi-search"></i>
                                        <input type="text" class="form-control" id="searchSecao" placeholder="Buscar por nome da seção...">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <select class="form-select" id="filterCategoriaSecao">
                                        <option value="">Todas as categorias</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button class="btn btn-outline-primary w-100" onclick="carregarSecoes()">
                                        <i class="bi bi-arrow-clockwise"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tabela de Seções -->
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Lista de Seções</h5>
                            <span class="text-muted" id="totalSecoes">Carregando...</span>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover" id="tabelaSecoes">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nome da Seção</th>
                                            <th>Categoria</th>
                                            <th>Descrição</th>
                                            <th>Data Criação</th>
                                            <th width="120">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodySecoes">
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">Carregando seções...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Tab Grupos -->
                <div class="tab-pane fade" id="grupos" role="tabpanel" aria-labelledby="grupos-tab">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h5 class="mb-0">Grupos por Seção</h5>
                            <p class="text-muted mb-0">Crie grupos dentro de cada seção</p>
                        </div>
                        <div>
                            <button class="btn btn-primary" onclick="abrirModalGrupo()">
                                <i class="bi bi-plus-circle me-2"></i>Novo Grupo
                            </button>
                        </div>
                    </div>
                    
                    <!-- Filtros e Busca -->
                    <div class="card-custom mb-4">
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-5">
                                    <div class="search-box">
                                        <i class="bi bi-search"></i>
                                        <input type="text" class="form-control" id="searchGrupo" placeholder="Buscar por nome do grupo...">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <select class="form-select" id="filterCategoriaGrupo">
                                        <option value="">Todas as categorias</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select class="form-select" id="filterSecaoGrupo">
                                        <option value="">Todas as seções</option>
                                    </select>
                                </div>
                                <div class="col-md-1">
                                    <button class="btn btn-outline-primary w-100" onclick="carregarGrupos()">
                                        <i class="bi bi-arrow-clockwise"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tabela de Grupos -->
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Lista de Grupos</h5>
                            <span class="text-muted" id="totalGrupos">Carregando...</span>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover" id="tabelaGrupos">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nome do Grupo</th>
                                            <th>Seção</th>
                                            <th>Categoria</th>
                                            <th>Descrição</th>
                                            <th>Data Criação</th>
                                            <th width="120">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyGrupos">
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">Carregando grupos...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Tab Subgrupos -->
                <div class="tab-pane fade" id="subgrupos" role="tabpanel" aria-labelledby="subgrupos-tab">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h5 class="mb-0">Subgrupos por Grupo</h5>
                            <p class="text-muted mb-0">Organize subgrupos dentro de cada grupo</p>
                        </div>
                        <div>
                            <button class="btn btn-primary" onclick="abrirModalSubgrupo()">
                                <i class="bi bi-plus-circle me-2"></i>Novo Subgrupo
                            </button>
                        </div>
                    </div>
                    
                    <!-- Filtros e Busca -->
                    <div class="card-custom mb-4">
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="search-box">
                                        <i class="bi bi-search"></i>
                                        <input type="text" class="form-control" id="searchSubgrupo" placeholder="Buscar por nome do subgrupo...">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <select class="form-select" id="filterCategoriaSubgrupo">
                                        <option value="">Todas as categorias</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select class="form-select" id="filterGrupoSubgrupo">
                                        <option value="">Todos os grupos</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button class="btn btn-outline-primary w-100" onclick="carregarSubgrupos()">
                                        <i class="bi bi-arrow-clockwise"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tabela de Subgrupos -->
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Lista de Subgrupos</h5>
                            <span class="text-muted" id="totalSubgrupos">Carregando...</span>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover" id="tabelaSubgrupos">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nome do Subgrupo</th>
                                            <th>Grupo</th>
                                            <th>Seção</th>
                                            <th>Categoria</th>
                                            <th>Descrição</th>
                                            <th>Data Criação</th>
                                            <th width="120">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodySubgrupos">
                                        <tr>
                                            <td colspan="8" class="text-center text-muted">Carregando subgrupos...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal para Categoria -->
    <div class="modal fade" id="modalCategoria" tabindex="-1" aria-labelledby="modalCategoriaLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCategoriaLabel">Nova Categoria</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formCategoria">
                        <input type="hidden" id="categoriaId">
                        <div class="mb-3">
                            <label for="nome_categoria" class="form-label">Nome da Categoria *</label>
                            <input type="text" class="form-control" id="nome_categoria" required>
                            <div class="invalid-feedback">Por favor, informe o nome da categoria.</div>
                        </div>
                        <div class="mb-3">
                            <label for="descricao_categoria" class="form-label">Descrição</label>
                            <textarea class="form-control" id="descricao_categoria" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="salvarCategoria()">Salvar Categoria</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Seção -->
    <div class="modal fade" id="modalSecao" tabindex="-1" aria-labelledby="modalSecaoLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalSecaoLabel">Nova Seção</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formSecao">
                        <input type="hidden" id="secaoId">
                        <div class="mb-3">
                            <label for="nome_secao" class="form-label">Nome da Seção *</label>
                            <input type="text" class="form-control" id="nome_secao" required>
                            <div class="invalid-feedback">Por favor, informe o nome da seção.</div>
                        </div>
                        <div class="mb-3">
                            <label for="categoria_secao" class="form-label">Categoria *</label>
                            <select class="form-select" id="categoria_secao" required>
                                <option value="">Selecione uma categoria</option>
                            </select>
                            <div class="invalid-feedback">Por favor, selecione uma categoria.</div>
                        </div>
                        <div class="mb-3">
                            <label for="descricao_secao" class="form-label">Descrição</label>
                            <textarea class="form-control" id="descricao_secao" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="salvarSecao()">Salvar Seção</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Grupo -->
    <div class="modal fade" id="modalGrupo" tabindex="-1" aria-labelledby="modalGrupoLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalGrupoLabel">Novo Grupo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formGrupo">
                        <input type="hidden" id="grupoId">
                        <div class="mb-3">
                            <label for="nome_grupo" class="form-label">Nome do Grupo *</label>
                            <input type="text" class="form-control" id="nome_grupo" required>
                            <div class="invalid-feedback">Por favor, informe o nome do grupo.</div>
                        </div>
                        <div class="mb-3">
                            <label for="categoria_grupo" class="form-label">Categoria *</label>
                            <select class="form-select" id="categoria_grupo" required>
                                <option value="">Selecione uma categoria</option>
                            </select>
                            <div class="invalid-feedback">Por favor, selecione uma categoria.</div>
                        </div>
                        <div class="mb-3">
                            <label for="secao_grupo" class="form-label">Seção *</label>
                            <select class="form-select" id="secao_grupo" required>
                                <option value="">Selecione uma seção</option>
                            </select>
                            <div class="invalid-feedback">Por favor, selecione uma seção.</div>
                        </div>
                        <div class="mb-3">
                            <label for="descricao_grupo" class="form-label">Descrição</label>
                            <textarea class="form-control" id="descricao_grupo" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="salvarGrupo()">Salvar Grupo</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Subgrupo -->
    <div class="modal fade" id="modalSubgrupo" tabindex="-1" aria-labelledby="modalSubgrupoLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalSubgrupoLabel">Novo Subgrupo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formSubgrupo">
                        <input type="hidden" id="subgrupoId">
                        <div class="mb-3">
                            <label for="nome_subgrupo" class="form-label">Nome do Subgrupo *</label>
                            <input type="text" class="form-control" id="nome_subgrupo" required>
                            <div class="invalid-feedback">Por favor, informe o nome do subgrupo.</div>
                        </div>
                        <div class="mb-3">
                            <label for="categoria_subgrupo" class="form-label">Categoria *</label>
                            <select class="form-select" id="categoria_subgrupo" required>
                                <option value="">Selecione uma categoria</option>
                            </select>
                            <div class="invalid-feedback">Por favor, selecione uma categoria.</div>
                        </div>
                        <div class="mb-3">
                            <label for="grupo_subgrupo" class="form-label">Grupo *</label>
                            <select class="form-select" id="grupo_subgrupo" required>
                                <option value="">Selecione um grupo</option>
                            </select>
                            <div class="invalid-feedback">Por favor, selecione um grupo.</div>
                        </div>
                        <div class="mb-3">
                            <label for="descricao_subgrupo" class="form-label">Descrição</label>
                            <textarea class="form-control" id="descricao_subgrupo" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="salvarSubgrupo()">Salvar Subgrupo</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmação de Exclusão -->
    <div class="modal fade" id="modalConfirmacao" tabindex="-1" aria-labelledby="modalConfirmacaoLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalConfirmacaoLabel">Confirmar Exclusão</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Tem certeza que deseja excluir <strong id="nomeItemExcluir"></strong>?</p>
                    <p class="text-danger"><small>Esta ação não pode ser desfeita.</small></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="btnConfirmarExclusao">Excluir</button>
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
    
    <!-- Configuração da API -->
    <script>
        // config.js - Arquivo de configuração para URLs da API
        const API_CONFIG = {
            BASE_URL: 'http://localhost/saas-multiempresas-api/public',
            API_VERSION: 'v1',
            
            // Endpoints
            CATEGORIAS: '/api/v1/categorias',
            CATEGORIAS_EMPRESA: '/api/v1/categorias/empresa',
            SECOES: '/api/v1/secoes',
            SECOES_EMPRESA: '/api/v1/secoes/empresa',
            GRUPOS: '/api/v1/grupos',
            GRUPOS_EMPRESA: '/api/v1/grupos/empresa',
            SUBGRUPOS: '/api/v1/subgrupos',
            SUBGRUPOS_EMPRESA: '/api/v1/subgrupos/empresa',
            LOGIN: '/api/v1/login',
            LOGOUT: '/api/v1/logout',
            
            // Headers padrão
            getHeaders: function(token) {
                return {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                };
            },
            
            // URL completa para categorias
            getCategoriasUrl: function() {
                return `${this.BASE_URL}${this.CATEGORIAS}`;
            },
            
            // URL completa para categorias por empresa
            getCategoriasEmpresaUrl: function(idEmpresa) {
                return `${this.BASE_URL}${this.CATEGORIAS_EMPRESA}/${idEmpresa}`;
            },
            
            // URL completa para uma categoria específica
            getCategoriaUrl: function(id) {
                return `${this.BASE_URL}${this.CATEGORIAS}/${id}`;
            },
            
            // URL completa para seções
            getSecoesUrl: function() {
                return `${this.BASE_URL}${this.SECOES}`;
            },
            
            // URL completa para seções por empresa e categoria
            getSecoesEmpresaUrl: function(idEmpresa, idCategoria) {
                return `${this.BASE_URL}${this.SECOES_EMPRESA}/${idEmpresa}/categoria/${idCategoria}`;
            },
            
            // URL completa para uma seção específica
            getSecaoUrl: function(id) {
                return `${this.BASE_URL}${this.SECOES}/${id}`;
            },
            
            // URL completa para grupos
            getGruposUrl: function() {
                return `${this.BASE_URL}${this.GRUPOS}`;
            },
            
            // URL completa para grupos por empresa e seção
            getGruposEmpresaUrl: function(idEmpresa, idSecao) {
                return `${this.BASE_URL}${this.GRUPOS_EMPRESA}/${idEmpresa}/secao/${idSecao}`;
            },
            
            // URL completa para um grupo específico
            getGrupoUrl: function(id) {
                return `${this.BASE_URL}${this.GRUPOS}/${id}`;
            },
            
            // URL completa para subgrupos
            getSubgruposUrl: function() {
                return `${this.BASE_URL}${this.SUBGRUPOS}`;
            },
            
            // URL completa para subgrupos por empresa e grupo
            getSubgruposEmpresaUrl: function(idEmpresa, idGrupo) {
                return `${this.BASE_URL}${this.SUBGRUPOS_EMPRESA}/${idEmpresa}/grupo/${idGrupo}`;
            },
            
            // URL completa para um subgrupo específico
            getSubgrupoUrl: function(id) {
                return `${this.BASE_URL}${this.SUBGRUPOS}/${id}`;
            }
        };
    </script>
    
    <script>
        // Variáveis globais
        const idEmpresa = <?php echo $id_empresa; ?>;
        let categorias = [];
        let secoes = [];
        let grupos = [];
        let subgrupos = [];
        let itemEditando = null;
        let tipoItemEditando = '';
        let modalCategoria = null;
        let modalSecao = null;
        let modalGrupo = null;
        let modalSubgrupo = null;
        let modalConfirmacao = null;

        // Inicialização
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar modais
            modalCategoria = new bootstrap.Modal(document.getElementById('modalCategoria'));
            modalSecao = new bootstrap.Modal(document.getElementById('modalSecao'));
            modalGrupo = new bootstrap.Modal(document.getElementById('modalGrupo'));
            modalSubgrupo = new bootstrap.Modal(document.getElementById('modalSubgrupo'));
            modalConfirmacao = new bootstrap.Modal(document.getElementById('modalConfirmacao'));
            
            // Carregar dados iniciais
            carregarCategorias();
            
            // Configurar eventos de busca
            document.getElementById('searchCategoria').addEventListener('input', filtrarCategorias);
            document.getElementById('searchSecao').addEventListener('input', filtrarSecoes);
            document.getElementById('searchGrupo').addEventListener('input', filtrarGrupos);
            document.getElementById('searchSubgrupo').addEventListener('input', filtrarSubgrupos);
            
            // Logoff
            var logoutBtn = document.getElementById('logoutBtn');
            if (logoutBtn) {
                logoutBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    fazerLogout();
                });
            }
            
            // Eventos para atualizar selects dependentes
            document.getElementById('categoria_secao').addEventListener('change', function() {
                atualizarSecoesPorCategoria(this.value);
            });
            
            document.getElementById('categoria_grupo').addEventListener('change', function() {
                atualizarSecoesPorCategoria(this.value, 'secao_grupo');
            });
            
            document.getElementById('categoria_subgrupo').addEventListener('change', function() {
                atualizarGruposPorCategoria(this.value);
            });
            
            // Eventos para filtros
            document.getElementById('filterCategoriaSecao').addEventListener('change', filtrarSecoes);
            document.getElementById('filterCategoriaGrupo').addEventListener('change', function() {
                atualizarSecoesPorCategoria(this.value, 'filterSecaoGrupo');
                filtrarGrupos();
            });
            document.getElementById('filterSecaoGrupo').addEventListener('change', filtrarGrupos);
            document.getElementById('filterCategoriaSubgrupo').addEventListener('change', function() {
                atualizarGruposPorCategoria(this.value, 'filterGrupoSubgrupo');
                filtrarSubgrupos();
            });
            document.getElementById('filterGrupoSubgrupo').addEventListener('change', filtrarSubgrupos);
        });

        // Função para fazer logout
        async function fazerLogout() {
            try {
                const token = '<?php echo $token; ?>';
                const response = await fetch(API_CONFIG.BASE_URL + API_CONFIG.LOGOUT, {
                    method: 'POST',
                    headers: API_CONFIG.getHeaders(token)
                });
                
                window.location.href = 'login.php';
                
            } catch (error) {
                console.error('Erro no logout:', error);
                window.location.href = 'login.php';
            }
        }

        // ========== CATEGORIAS ==========
        async function carregarCategorias() {
            mostrarLoading(true);
            
            try {
                const token = '<?php echo $token; ?>';
                const response = await fetch(API_CONFIG.getCategoriasEmpresaUrl(idEmpresa), {
                    method: 'GET',
                    headers: API_CONFIG.getHeaders(token)
                });
                
                if (!response.ok) {
                    throw new Error(`Erro ${response.status}: ${response.statusText}`);
                }
                
                categorias = await response.json();
                exibirCategorias(categorias);
                atualizarTotalCategorias(categorias.length);
                
                // Atualizar selects de categorias em outras abas
                atualizarSelectCategorias();
                
            } catch (error) {
                console.error('Erro ao carregar categorias:', error);
                mostrarNotificacao('Erro ao carregar categorias: ' + error.message, 'error');
                document.getElementById('tbodyCategorias').innerHTML = '<tr><td colspan="5" class="text-center text-muted">Erro ao carregar dados</td></tr>';
            } finally {
                mostrarLoading(false);
            }
        }

        function exibirCategorias(listaCategorias) {
            const tbody = document.getElementById('tbodyCategorias');
            
            if (listaCategorias.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">Nenhuma categoria encontrada</td></tr>';
                return;
            }
            
            tbody.innerHTML = listaCategorias.map(categoria => `
                <tr>
                    <td>${categoria.id_categoria}</td>
                    <td class="fw-semibold">${categoria.nome_categoria}</td>
                    <td>${categoria.descricao || '-'}</td>
                    <td>${formatarData(categoria.created_at)}</td>
                    <td>
                        <div class="d-flex">
                            <button class="btn btn-action btn-outline-primary" onclick="editarCategoria(${categoria.id_categoria})" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-action btn-outline-danger" onclick="confirmarExclusao('categoria', ${categoria.id_categoria}, '${categoria.nome_categoria.replace(/'/g, "\\'")}')" title="Excluir">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `).join('');
        }

        function abrirModalCategoria() {
            itemEditando = null;
            tipoItemEditando = 'categoria';
            document.getElementById('modalCategoriaLabel').textContent = 'Nova Categoria';
            document.getElementById('formCategoria').reset();
            document.getElementById('categoriaId').value = '';
            document.getElementById('formCategoria').classList.remove('was-validated');
            modalCategoria.show();
        }

        async function editarCategoria(id) {
            mostrarLoading(true);
            
            try {
                const token = '<?php echo $token; ?>';
                const response = await fetch(API_CONFIG.getCategoriaUrl(id), {
                    method: 'GET',
                    headers: API_CONFIG.getHeaders(token)
                });
                
                if (!response.ok) {
                    throw new Error(`Erro ${response.status}: ${response.statusText}`);
                }
                
                const categoria = await response.json();
                itemEditando = categoria;
                tipoItemEditando = 'categoria';
                
                document.getElementById('modalCategoriaLabel').textContent = 'Editar Categoria';
                document.getElementById('categoriaId').value = categoria.id_categoria;
                document.getElementById('nome_categoria').value = categoria.nome_categoria || '';
                document.getElementById('descricao_categoria').value = categoria.descricao || '';
                
                document.getElementById('formCategoria').classList.remove('was-validated');
                modalCategoria.show();
                
            } catch (error) {
                console.error('Erro ao carregar categoria:', error);
                mostrarNotificacao('Erro ao carregar dados da categoria: ' + error.message, 'error');
            } finally {
                mostrarLoading(false);
            }
        }

        async function salvarCategoria() {
            const form = document.getElementById('formCategoria');
            
            if (!form.checkValidity()) {
                form.classList.add('was-validated');
                return;
            }
            
            mostrarLoading(true);
            
            const dadosCategoria = {
                id_empresa: idEmpresa,
                nome_categoria: document.getElementById('nome_categoria').value,
                descricao: document.getElementById('descricao_categoria').value
            };
            
            try {
                const token = '<?php echo $token; ?>';
                let response;
                
                if (itemEditando) {
                    response = await fetch(API_CONFIG.getCategoriaUrl(itemEditando.id_categoria), {
                        method: 'PUT',
                        headers: API_CONFIG.getHeaders(token),
                        body: JSON.stringify(dadosCategoria)
                    });
                } else {
                    response = await fetch(API_CONFIG.getCategoriasUrl(), {
                        method: 'POST',
                        headers: API_CONFIG.getHeaders(token),
                        body: JSON.stringify(dadosCategoria)
                    });
                }
                
                if (!response.ok) {
                    const errorData = await response.json().catch(() => ({}));
                    throw new Error(errorData.message || `Erro ${response.status}: ${response.statusText}`);
                }
                
                modalCategoria.hide();
                form.classList.remove('was-validated');
                
                mostrarNotificacao(
                    `Categoria ${itemEditando ? 'atualizada' : 'criada'} com sucesso!`, 
                    'success'
                );
                
                carregarCategorias();
                
            } catch (error) {
                console.error('Erro ao salvar categoria:', error);
                mostrarNotificacao('Erro ao salvar categoria: ' + error.message, 'error');
            } finally {
                mostrarLoading(false);
            }
        }

        // ========== SEÇÕES ==========
        async function carregarSecoes() {
            mostrarLoading(true);
            
            try {
                const token = '<?php echo $token; ?>';
                // Primeiro, carregamos todas as seções da empresa
                // Como a API retorna por categoria, precisamos fazer uma chamada para cada categoria
                let todasSecoes = [];
                
                for (const categoria of categorias) {
                    const response = await fetch(API_CONFIG.getSecoesEmpresaUrl(idEmpresa, categoria.id_categoria), {
                        method: 'GET',
                        headers: API_CONFIG.getHeaders(token)
                    });
                    
                    if (response.ok) {
                        const secoesCategoria = await response.json();
                        todasSecoes = [...todasSecoes, ...secoesCategoria];
                    }
                }
                
                secoes = todasSecoes;
                exibirSecoes(secoes);
                atualizarTotalSecoes(secoes.length);
                
            } catch (error) {
                console.error('Erro ao carregar seções:', error);
                mostrarNotificacao('Erro ao carregar seções: ' + error.message, 'error');
                document.getElementById('tbodySecoes').innerHTML = '<tr><td colspan="6" class="text-center text-muted">Erro ao carregar dados</td></tr>';
            } finally {
                mostrarLoading(false);
            }
        }

        function exibirSecoes(listaSecoes) {
            const tbody = document.getElementById('tbodySecoes');
            
            if (listaSecoes.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">Nenhuma seção encontrada</td></tr>';
                return;
            }
            
            tbody.innerHTML = listaSecoes.map(secao => `
                <tr>
                    <td>${secao.id_secao}</td>
                    <td class="fw-semibold">${secao.nome_secao}</td>
                    <td><span class="hierarchy-badge">${secao.categoria?.nome_categoria || 'N/A'}</span></td>
                    <td>${secao.descricao || '-'}</td>
                    <td>${formatarData(secao.created_at)}</td>
                    <td>
                        <div class="d-flex">
                            <button class="btn btn-action btn-outline-primary" onclick="editarSecao(${secao.id_secao})" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-action btn-outline-danger" onclick="confirmarExclusao('secao', ${secao.id_secao}, '${secao.nome_secao.replace(/'/g, "\\'")}')" title="Excluir">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `).join('');
        }

        function abrirModalSecao() {
            itemEditando = null;
            tipoItemEditando = 'secao';
            document.getElementById('modalSecaoLabel').textContent = 'Nova Seção';
            document.getElementById('formSecao').reset();
            document.getElementById('secaoId').value = '';
            document.getElementById('formSecao').classList.remove('was-validated');
            
            // Preencher select de categorias
            const selectCategoria = document.getElementById('categoria_secao');
            selectCategoria.innerHTML = '<option value="">Selecione uma categoria</option>';
            categorias.forEach(categoria => {
                selectCategoria.innerHTML += `<option value="${categoria.id_categoria}">${categoria.nome_categoria}</option>`;
            });
            
            modalSecao.show();
        }

        async function editarSecao(id) {
            mostrarLoading(true);
            
            try {
                const token = '<?php echo $token; ?>';
                // Para editar uma seção, precisamos primeiro encontrar em qual categoria ela está
                let secaoEncontrada = null;
                
                for (const categoria of categorias) {
                    const response = await fetch(API_CONFIG.getSecoesEmpresaUrl(idEmpresa, categoria.id_categoria), {
                        method: 'GET',
                        headers: API_CONFIG.getHeaders(token)
                    });
                    
                    if (response.ok) {
                        const secoesCategoria = await response.json();
                        secaoEncontrada = secoesCategoria.find(s => s.id_secao === id);
                        if (secaoEncontrada) break;
                    }
                }
                
                if (!secaoEncontrada) {
                    throw new Error('Seção não encontrada');
                }
                
                itemEditando = secaoEncontrada;
                tipoItemEditando = 'secao';
                
                document.getElementById('modalSecaoLabel').textContent = 'Editar Seção';
                document.getElementById('secaoId').value = secaoEncontrada.id_secao;
                document.getElementById('nome_secao').value = secaoEncontrada.nome_secao || '';
                document.getElementById('descricao_secao').value = secaoEncontrada.descricao || '';
                
                // Preencher select de categorias
                const selectCategoria = document.getElementById('categoria_secao');
                selectCategoria.innerHTML = '<option value="">Selecione uma categoria</option>';
                categorias.forEach(categoria => {
                    const selected = categoria.id_categoria === secaoEncontrada.id_categoria ? 'selected' : '';
                    selectCategoria.innerHTML += `<option value="${categoria.id_categoria}" ${selected}>${categoria.nome_categoria}</option>`;
                });
                
                document.getElementById('formSecao').classList.remove('was-validated');
                modalSecao.show();
                
            } catch (error) {
                console.error('Erro ao carregar seção:', error);
                mostrarNotificacao('Erro ao carregar dados da seção: ' + error.message, 'error');
            } finally {
                mostrarLoading(false);
            }
        }

        async function salvarSecao() {
            const form = document.getElementById('formSecao');
            
            if (!form.checkValidity()) {
                form.classList.add('was-validated');
                return;
            }
            
            mostrarLoading(true);
            
            const dadosSecao = {
                id_empresa: idEmpresa,
                id_categoria: document.getElementById('categoria_secao').value,
                nome_secao: document.getElementById('nome_secao').value,
                descricao: document.getElementById('descricao_secao').value
            };
            
            try {
                const token = '<?php echo $token; ?>';
                let response;
                
                if (itemEditando) {
                    response = await fetch(API_CONFIG.getSecaoUrl(itemEditando.id_secao), {
                        method: 'PUT',
                        headers: API_CONFIG.getHeaders(token),
                        body: JSON.stringify(dadosSecao)
                    });
                } else {
                    response = await fetch(API_CONFIG.getSecoesUrl(), {
                        method: 'POST',
                        headers: API_CONFIG.getHeaders(token),
                        body: JSON.stringify(dadosSecao)
                    });
                }
                
                if (!response.ok) {
                    const errorData = await response.json().catch(() => ({}));
                    throw new Error(errorData.message || `Erro ${response.status}: ${response.statusText}`);
                }
                
                modalSecao.hide();
                form.classList.remove('was-validated');
                
                mostrarNotificacao(
                    `Seção ${itemEditando ? 'atualizada' : 'criada'} com sucesso!`, 
                    'success'
                );
                
                carregarSecoes();
                
            } catch (error) {
                console.error('Erro ao salvar seção:', error);
                mostrarNotificacao('Erro ao salvar seção: ' + error.message, 'error');
            } finally {
                mostrarLoading(false);
            }
        }

        // ========== GRUPOS ==========
        async function carregarGrupos() {
            mostrarLoading(true);
            
            try {
                const token = '<?php echo $token; ?>';
                // Carregar todos os grupos da empresa
                let todosGrupos = [];
                
                for (const secao of secoes) {
                    const response = await fetch(API_CONFIG.getGruposEmpresaUrl(idEmpresa, secao.id_secao), {
                        method: 'GET',
                        headers: API_CONFIG.getHeaders(token)
                    });
                    
                    if (response.ok) {
                        const gruposSecao = await response.json();
                        todosGrupos = [...todosGrupos, ...gruposSecao];
                    }
                }
                
                grupos = todosGrupos;
                exibirGrupos(grupos);
                atualizarTotalGrupos(grupos.length);
                
            } catch (error) {
                console.error('Erro ao carregar grupos:', error);
                mostrarNotificacao('Erro ao carregar grupos: ' + error.message, 'error');
                document.getElementById('tbodyGrupos').innerHTML = '<tr><td colspan="7" class="text-center text-muted">Erro ao carregar dados</td></tr>';
            } finally {
                mostrarLoading(false);
            }
        }

        function exibirGrupos(listaGrupos) {
            const tbody = document.getElementById('tbodyGrupos');
            
            if (listaGrupos.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">Nenhum grupo encontrado</td></tr>';
                return;
            }
            
            tbody.innerHTML = listaGrupos.map(grupo => `
                <tr>
                    <td>${grupo.id_grupo}</td>
                    <td class="fw-semibold">${grupo.nome_grupo}</td>
                    <td><span class="hierarchy-badge">${grupo.secao?.nome_secao || 'N/A'}</span></td>
                    <td><span class="hierarchy-badge">${grupo.secao?.categoria?.nome_categoria || 'N/A'}</span></td>
                    <td>${grupo.descricao || '-'}</td>
                    <td>${formatarData(grupo.created_at)}</td>
                    <td>
                        <div class="d-flex">
                            <button class="btn btn-action btn-outline-primary" onclick="editarGrupo(${grupo.id_grupo})" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-action btn-outline-danger" onclick="confirmarExclusao('grupo', ${grupo.id_grupo}, '${grupo.nome_grupo.replace(/'/g, "\\'")}')" title="Excluir">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `).join('');
        }

        function abrirModalGrupo() {
            itemEditando = null;
            tipoItemEditando = 'grupo';
            document.getElementById('modalGrupoLabel').textContent = 'Novo Grupo';
            document.getElementById('formGrupo').reset();
            document.getElementById('grupoId').value = '';
            document.getElementById('formGrupo').classList.remove('was-validated');
            
            // Preencher select de categorias
            const selectCategoria = document.getElementById('categoria_grupo');
            selectCategoria.innerHTML = '<option value="">Selecione uma categoria</option>';
            categorias.forEach(categoria => {
                selectCategoria.innerHTML += `<option value="${categoria.id_categoria}">${categoria.nome_categoria}</option>`;
            });
            
            // Limpar select de seções
            document.getElementById('secao_grupo').innerHTML = '<option value="">Selecione uma seção</option>';
            
            modalGrupo.show();
        }

        async function editarGrupo(id) {
            mostrarLoading(true);
            
            try {
                const token = '<?php echo $token; ?>';
                // Encontrar o grupo
                let grupoEncontrado = null;
                
                for (const secao of secoes) {
                    const response = await fetch(API_CONFIG.getGruposEmpresaUrl(idEmpresa, secao.id_secao), {
                        method: 'GET',
                        headers: API_CONFIG.getHeaders(token)
                    });
                    
                    if (response.ok) {
                        const gruposSecao = await response.json();
                        grupoEncontrado = gruposSecao.find(g => g.id_grupo === id);
                        if (grupoEncontrado) break;
                    }
                }
                
                if (!grupoEncontrado) {
                    throw new Error('Grupo não encontrado');
                }
                
                itemEditando = grupoEncontrado;
                tipoItemEditando = 'grupo';
                
                document.getElementById('modalGrupoLabel').textContent = 'Editar Grupo';
                document.getElementById('grupoId').value = grupoEncontrado.id_grupo;
                document.getElementById('nome_grupo').value = grupoEncontrado.nome_grupo || '';
                document.getElementById('descricao_grupo').value = grupoEncontrado.descricao || '';
                
                // Preencher select de categorias
                const selectCategoria = document.getElementById('categoria_grupo');
                selectCategoria.innerHTML = '<option value="">Selecione uma categoria</option>';
                categorias.forEach(categoria => {
                    const selected = categoria.id_categoria === grupoEncontrado.secao?.id_categoria ? 'selected' : '';
                    selectCategoria.innerHTML += `<option value="${categoria.id_categoria}" ${selected}>${categoria.nome_categoria}</option>`;
                });
                
                // Preencher select de seções
                await atualizarSecoesPorCategoria(grupoEncontrado.secao?.id_categoria, 'secao_grupo', grupoEncontrado.id_secao);
                
                document.getElementById('formGrupo').classList.remove('was-validated');
                modalGrupo.show();
                
            } catch (error) {
                console.error('Erro ao carregar grupo:', error);
                mostrarNotificacao('Erro ao carregar dados do grupo: ' + error.message, 'error');
            } finally {
                mostrarLoading(false);
            }
        }

        async function salvarGrupo() {
            const form = document.getElementById('formGrupo');
            
            if (!form.checkValidity()) {
                form.classList.add('was-validated');
                return;
            }
            
            mostrarLoading(true);
            
            const dadosGrupo = {
                id_empresa: idEmpresa,
                id_secao: document.getElementById('secao_grupo').value,
                nome_grupo: document.getElementById('nome_grupo').value,
                descricao: document.getElementById('descricao_grupo').value
            };
            
            try {
                const token = '<?php echo $token; ?>';
                let response;
                
                if (itemEditando) {
                    response = await fetch(API_CONFIG.getGrupoUrl(itemEditando.id_grupo), {
                        method: 'PUT',
                        headers: API_CONFIG.getHeaders(token),
                        body: JSON.stringify(dadosGrupo)
                    });
                } else {
                    response = await fetch(API_CONFIG.getGruposUrl(), {
                        method: 'POST',
                        headers: API_CONFIG.getHeaders(token),
                        body: JSON.stringify(dadosGrupo)
                    });
                }
                
                if (!response.ok) {
                    const errorData = await response.json().catch(() => ({}));
                    throw new Error(errorData.message || `Erro ${response.status}: ${response.statusText}`);
                }
                
                modalGrupo.hide();
                form.classList.remove('was-validated');
                
                mostrarNotificacao(
                    `Grupo ${itemEditando ? 'atualizada' : 'criada'} com sucesso!`, 
                    'success'
                );
                
                carregarGrupos();
                
            } catch (error) {
                console.error('Erro ao salvar grupo:', error);
                mostrarNotificacao('Erro ao salvar grupo: ' + error.message, 'error');
            } finally {
                mostrarLoading(false);
            }
        }

        // ========== SUBGRUPOS ==========
        async function carregarSubgrupos() {
            mostrarLoading(true);
            
            try {
                const token = '<?php echo $token; ?>';
                // Carregar todos os subgrupos da empresa
                let todosSubgrupos = [];
                
                for (const grupo of grupos) {
                    const response = await fetch(API_CONFIG.getSubgruposEmpresaUrl(idEmpresa, grupo.id_grupo), {
                        method: 'GET',
                        headers: API_CONFIG.getHeaders(token)
                    });
                    
                    if (response.ok) {
                        const subgruposGrupo = await response.json();
                        todosSubgrupos = [...todosSubgrupos, ...subgruposGrupo];
                    }
                }
                
                subgrupos = todosSubgrupos;
                exibirSubgrupos(subgrupos);
                atualizarTotalSubgrupos(subgrupos.length);
                
            } catch (error) {
                console.error('Erro ao carregar subgrupos:', error);
                mostrarNotificacao('Erro ao carregar subgrupos: ' + error.message, 'error');
                document.getElementById('tbodySubgrupos').innerHTML = '<tr><td colspan="8" class="text-center text-muted">Erro ao carregar dados</td></tr>';
            } finally {
                mostrarLoading(false);
            }
        }

        function exibirSubgrupos(listaSubgrupos) {
            const tbody = document.getElementById('tbodySubgrupos');
            
            if (listaSubgrupos.length === 0) {
                tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">Nenhum subgrupo encontrado</td></tr>';
                return;
            }
            
            tbody.innerHTML = listaSubgrupos.map(subgrupo => `
                <tr>
                    <td>${subgrupo.id_subgrupo}</td>
                    <td class="fw-semibold">${subgrupo.nome_subgrupo}</td>
                    <td><span class="hierarchy-badge">${subgrupo.grupo?.nome_grupo || 'N/A'}</span></td>
                    <td><span class="hierarchy-badge">${subgrupo.grupo?.secao?.nome_secao || 'N/A'}</span></td>
                    <td><span class="hierarchy-badge">${subgrupo.grupo?.secao?.categoria?.nome_categoria || 'N/A'}</span></td>
                    <td>${subgrupo.descricao || '-'}</td>
                    <td>${formatarData(subgrupo.created_at)}</td>
                    <td>
                        <div class="d-flex">
                            <button class="btn btn-action btn-outline-primary" onclick="editarSubgrupo(${subgrupo.id_subgrupo})" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-action btn-outline-danger" onclick="confirmarExclusao('subgrupo', ${subgrupo.id_subgrupo}, '${subgrupo.nome_subgrupo.replace(/'/g, "\\'")}')" title="Excluir">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `).join('');
        }

        function abrirModalSubgrupo() {
            itemEditando = null;
            tipoItemEditando = 'subgrupo';
            document.getElementById('modalSubgrupoLabel').textContent = 'Novo Subgrupo';
            document.getElementById('formSubgrupo').reset();
            document.getElementById('subgrupoId').value = '';
            document.getElementById('formSubgrupo').classList.remove('was-validated');
            
            // Preencher select de categorias
            const selectCategoria = document.getElementById('categoria_subgrupo');
            selectCategoria.innerHTML = '<option value="">Selecione uma categoria</option>';
            categorias.forEach(categoria => {
                selectCategoria.innerHTML += `<option value="${categoria.id_categoria}">${categoria.nome_categoria}</option>`;
            });
            
            // Limpar select de grupos
            document.getElementById('grupo_subgrupo').innerHTML = '<option value="">Selecione um grupo</option>';
            
            modalSubgrupo.show();
        }

        async function editarSubgrupo(id) {
            mostrarLoading(true);
            
            try {
                const token = '<?php echo $token; ?>';
                // Encontrar o subgrupo
                let subgrupoEncontrado = null;
                
                for (const grupo of grupos) {
                    const response = await fetch(API_CONFIG.getSubgruposEmpresaUrl(idEmpresa, grupo.id_grupo), {
                        method: 'GET',
                        headers: API_CONFIG.getHeaders(token)
                    });
                    
                    if (response.ok) {
                        const subgruposGrupo = await response.json();
                        subgrupoEncontrado = subgruposGrupo.find(sg => sg.id_subgrupo === id);
                        if (subgrupoEncontrado) break;
                    }
                }
                
                if (!subgrupoEncontrado) {
                    throw new Error('Subgrupo não encontrado');
                }
                
                itemEditando = subgrupoEncontrado;
                tipoItemEditando = 'subgrupo';
                
                document.getElementById('modalSubgrupoLabel').textContent = 'Editar Subgrupo';
                document.getElementById('subgrupoId').value = subgrupoEncontrado.id_subgrupo;
                document.getElementById('nome_subgrupo').value = subgrupoEncontrado.nome_subgrupo || '';
                document.getElementById('descricao_subgrupo').value = subgrupoEncontrado.descricao || '';
                
                // Preencher select de categorias
                const selectCategoria = document.getElementById('categoria_subgrupo');
                selectCategoria.innerHTML = '<option value="">Selecione uma categoria</option>';
                categorias.forEach(categoria => {
                    const selected = categoria.id_categoria === subgrupoEncontrado.grupo?.secao?.id_categoria ? 'selected' : '';
                    selectCategoria.innerHTML += `<option value="${categoria.id_categoria}" ${selected}>${categoria.nome_categoria}</option>`;
                });
                
                // Preencher select de grupos
                await atualizarGruposPorCategoria(subgrupoEncontrado.grupo?.secao?.id_categoria, 'grupo_subgrupo', subgrupoEncontrado.id_grupo);
                
                document.getElementById('formSubgrupo').classList.remove('was-validated');
                modalSubgrupo.show();
                
            } catch (error) {
                console.error('Erro ao carregar subgrupo:', error);
                mostrarNotificacao('Erro ao carregar dados do subgrupo: ' + error.message, 'error');
            } finally {
                mostrarLoading(false);
            }
        }

        async function salvarSubgrupo() {
            const form = document.getElementById('formSubgrupo');
            
            if (!form.checkValidity()) {
                form.classList.add('was-validated');
                return;
            }
            
            mostrarLoading(true);
            
            const dadosSubgrupo = {
                id_empresa: idEmpresa,
                id_grupo: document.getElementById('grupo_subgrupo').value,
                nome_subgrupo: document.getElementById('nome_subgrupo').value,
                descricao: document.getElementById('descricao_subgrupo').value
            };
            
            try {
                const token = '<?php echo $token; ?>';
                let response;
                
                if (itemEditando) {
                    response = await fetch(API_CONFIG.getSubgrupoUrl(itemEditando.id_subgrupo), {
                        method: 'PUT',
                        headers: API_CONFIG.getHeaders(token),
                        body: JSON.stringify(dadosSubgrupo)
                    });
                } else {
                    response = await fetch(API_CONFIG.getSubgruposUrl(), {
                        method: 'POST',
                        headers: API_CONFIG.getHeaders(token),
                        body: JSON.stringify(dadosSubgrupo)
                    });
                }
                
                if (!response.ok) {
                    const errorData = await response.json().catch(() => ({}));
                    throw new Error(errorData.message || `Erro ${response.status}: ${response.statusText}`);
                }
                
                modalSubgrupo.hide();
                form.classList.remove('was-validated');
                
                mostrarNotificacao(
                    `Subgrupo ${itemEditando ? 'atualizada' : 'criada'} com sucesso!`, 
                    'success'
                );
                
                carregarSubgrupos();
                
            } catch (error) {
                console.error('Erro ao salvar subgrupo:', error);
                mostrarNotificacao('Erro ao salvar subgrupo: ' + error.message, 'error');
            } finally {
                mostrarLoading(false);
            }
        }

        // ========== FUNÇÕES AUXILIARES ==========
        async function atualizarSecoesPorCategoria(idCategoria, selectId = 'secao_secao', idSelecionado = null) {
            if (!idCategoria) {
                document.getElementById(selectId).innerHTML = '<option value="">Selecione uma seção</option>';
                return;
            }
            
            try {
                const token = '<?php echo $token; ?>';
                const response = await fetch(API_CONFIG.getSecoesEmpresaUrl(idEmpresa, idCategoria), {
                    method: 'GET',
                    headers: API_CONFIG.getHeaders(token)
                });
                
                if (response.ok) {
                    const secoesCategoria = await response.json();
                    const select = document.getElementById(selectId);
                    select.innerHTML = '<option value="">Selecione uma seção</option>';
                    
                    secoesCategoria.forEach(secao => {
                        const selected = idSelecionado && secao.id_secao === idSelecionado ? 'selected' : '';
                        select.innerHTML += `<option value="${secao.id_secao}" ${selected}>${secao.nome_secao}</option>`;
                    });
                }
            } catch (error) {
                console.error('Erro ao carregar seções:', error);
            }
        }

        async function atualizarGruposPorCategoria(idCategoria, selectId = 'grupo_subgrupo', idSelecionado = null) {
            if (!idCategoria) {
                document.getElementById(selectId).innerHTML = '<option value="">Selecione um grupo</option>';
                return;
            }
            
            try {
                // Primeiro, encontramos as seções desta categoria
                const token = '<?php echo $token; ?>';
                const responseSecoes = await fetch(API_CONFIG.getSecoesEmpresaUrl(idEmpresa, idCategoria), {
                    method: 'GET',
                    headers: API_CONFIG.getHeaders(token)
                });
                
                if (responseSecoes.ok) {
                    const secoesCategoria = await responseSecoes.json();
                    const select = document.getElementById(selectId);
                    select.innerHTML = '<option value="">Selecione um grupo</option>';
                    
                    // Para cada seção, carregamos os grupos
                    for (const secao of secoesCategoria) {
                        const responseGrupos = await fetch(API_CONFIG.getGruposEmpresaUrl(idEmpresa, secao.id_secao), {
                            method: 'GET',
                            headers: API_CONFIG.getHeaders(token)
                        });
                        
                        if (responseGrupos.ok) {
                            const gruposSecao = await responseGrupos.json();
                            gruposSecao.forEach(grupo => {
                                const selected = idSelecionado && grupo.id_grupo === idSelecionado ? 'selected' : '';
                                select.innerHTML += `<option value="${grupo.id_grupo}" ${selected}>${grupo.nome_grupo}</option>`;
                            });
                        }
                    }
                }
            } catch (error) {
                console.error('Erro ao carregar grupos:', error);
            }
        }

        function atualizarSelectCategorias() {
            // Atualizar todos os selects de categorias na página
            const selects = [
                'filterCategoriaSecao', 'filterCategoriaGrupo', 'filterCategoriaSubgrupo'
            ];
            
            selects.forEach(selectId => {
                const select = document.getElementById(selectId);
                if (select) {
                    select.innerHTML = '<option value="">Todas as categorias</option>';
                    categorias.forEach(categoria => {
                        select.innerHTML += `<option value="${categoria.id_categoria}">${categoria.nome_categoria}</option>`;
                    });
                }
            });
        }

        // Função para confirmar exclusão
        function confirmarExclusao(tipo, id, nome) {
            document.getElementById('nomeItemExcluir').textContent = nome;
            
            const btnConfirmar = document.getElementById('btnConfirmarExclusao');
            btnConfirmar.replaceWith(btnConfirmar.cloneNode(true));
            document.getElementById('btnConfirmarExclusao').onclick = () => excluirItem(tipo, id);
            
            modalConfirmacao.show();
        }

        // Função para excluir item
        async function excluirItem(tipo, id) {
            mostrarLoading(true);
            
            try {
                const token = '<?php echo $token; ?>';
                let url;
                
                switch(tipo) {
                    case 'categoria':
                        url = API_CONFIG.getCategoriaUrl(id);
                        break;
                    case 'secao':
                        url = API_CONFIG.getSecaoUrl(id);
                        break;
                    case 'grupo':
                        url = API_CONFIG.getGrupoUrl(id);
                        break;
                    case 'subgrupo':
                        url = API_CONFIG.getSubgrupoUrl(id);
                        break;
                    default:
                        throw new Error('Tipo de item inválido');
                }
                
                const response = await fetch(url, {
                    method: 'DELETE',
                    headers: API_CONFIG.getHeaders(token)
                });
                
                if (!response.ok && response.status !== 204) {
                    throw new Error(`Erro ${response.status}: ${response.statusText}`);
                }
                
                modalConfirmacao.hide();
                mostrarNotificacao(`${tipo.charAt(0).toUpperCase() + tipo.slice(1)} excluído com sucesso!`, 'success');
                
                // Recarregar dados
                switch(tipo) {
                    case 'categoria':
                        carregarCategorias();
                        break;
                    case 'secao':
                        carregarSecoes();
                        break;
                    case 'grupo':
                        carregarGrupos();
                        break;
                    case 'subgrupo':
                        carregarSubgrupos();
                        break;
                }
                
            } catch (error) {
                console.error(`Erro ao excluir ${tipo}:`, error);
                mostrarNotificacao(`Erro ao excluir ${tipo}: ` + error.message, 'error');
                modalConfirmacao.hide();
            } finally {
                mostrarLoading(false);
            }
        }

        // Funções de filtro
        function filtrarCategorias() {
            const termoBusca = document.getElementById('searchCategoria').value.toLowerCase();
            
            const categoriasFiltradas = categorias.filter(categoria => {
                return !termoBusca || 
                    categoria.nome_categoria.toLowerCase().includes(termoBusca) ||
                    (categoria.descricao && categoria.descricao.toLowerCase().includes(termoBusca));
            });
            
            exibirCategorias(categoriasFiltradas);
            atualizarTotalCategorias(categoriasFiltradas.length);
        }

        function filtrarSecoes() {
            const termoBusca = document.getElementById('searchSecao').value.toLowerCase();
            const categoriaFiltro = document.getElementById('filterCategoriaSecao').value;
            
            const secoesFiltradas = secoes.filter(secao => {
                const matchBusca = !termoBusca || 
                    secao.nome_secao.toLowerCase().includes(termoBusca) ||
                    (secao.descricao && secao.descricao.toLowerCase().includes(termoBusca));
                
                const matchCategoria = !categoriaFiltro || secao.id_categoria == categoriaFiltro;
                
                return matchBusca && matchCategoria;
            });
            
            exibirSecoes(secoesFiltradas);
            atualizarTotalSecoes(secoesFiltradas.length);
        }

        function filtrarGrupos() {
            const termoBusca = document.getElementById('searchGrupo').value.toLowerCase();
            const categoriaFiltro = document.getElementById('filterCategoriaGrupo').value;
            const secaoFiltro = document.getElementById('filterSecaoGrupo').value;
            
            const gruposFiltrados = grupos.filter(grupo => {
                const matchBusca = !termoBusca || 
                    grupo.nome_grupo.toLowerCase().includes(termoBusca) ||
                    (grupo.descricao && grupo.descricao.toLowerCase().includes(termoBusca));
                
                const matchCategoria = !categoriaFiltro || grupo.secao?.id_categoria == categoriaFiltro;
                const matchSecao = !secaoFiltro || grupo.id_secao == secaoFiltro;
                
                return matchBusca && matchCategoria && matchSecao;
            });
            
            exibirGrupos(gruposFiltrados);
            atualizarTotalGrupos(gruposFiltrados.length);
        }

        function filtrarSubgrupos() {
            const termoBusca = document.getElementById('searchSubgrupo').value.toLowerCase();
            const categoriaFiltro = document.getElementById('filterCategoriaSubgrupo').value;
            const grupoFiltro = document.getElementById('filterGrupoSubgrupo').value;
            
            const subgruposFiltrados = subgrupos.filter(subgrupo => {
                const matchBusca = !termoBusca || 
                    subgrupo.nome_subgrupo.toLowerCase().includes(termoBusca) ||
                    (subgrupo.descricao && subgrupo.descricao.toLowerCase().includes(termoBusca));
                
                const matchCategoria = !categoriaFiltro || subgrupo.grupo?.secao?.id_categoria == categoriaFiltro;
                const matchGrupo = !grupoFiltro || subgrupo.id_grupo == grupoFiltro;
                
                return matchBusca && matchCategoria && matchGrupo;
            });
            
            exibirSubgrupos(subgruposFiltrados);
            atualizarTotalSubgrupos(subgruposFiltrados.length);
        }

        // Funções auxiliares
        function formatarData(data) {
            if (!data) return '';
            try {
                const date = new Date(data);
                return date.toLocaleDateString('pt-BR');
            } catch (e) {
                return data;
            }
        }

        function atualizarTotalCategorias(total) {
            document.getElementById('totalCategorias').textContent = `${total} categoria(s)`;
        }

        function atualizarTotalSecoes(total) {
            document.getElementById('totalSecoes').textContent = `${total} seção(ões)`;
        }

        function atualizarTotalGrupos(total) {
            document.getElementById('totalGrupos').textContent = `${total} grupo(s)`;
        }

        function atualizarTotalSubgrupos(total) {
            document.getElementById('totalSubgrupos').textContent = `${total} subgrupo(s)`;
        }

        function mostrarLoading(mostrar) {
            document.getElementById('loadingOverlay').classList.toggle('d-none', !mostrar);
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