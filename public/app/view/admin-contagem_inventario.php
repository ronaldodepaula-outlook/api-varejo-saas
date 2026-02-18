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

// Extrair ID do usuário da sessão
$id_usuario = $_SESSION['usuario_id'] ?? ($usuario['id'] ?? 1);

// Primeira letra para o avatar
$inicialUsuario = strtoupper(substr($nomeUsuario, 0, 1));

// Obter o id_inventario da URL
$id_inventario = $_GET['id_inventario'] ?? 0;
if (!$id_inventario) {
    header('Location: ?view=admin-inventarios');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contagem de Inventário - SAS Multi</title>
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
        
        .status-badge {
            padding: 4px 8px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .status-em_andamento { background: rgba(52, 152, 219, 0.1); color: var(--primary-color); }
        .status-concluido { background: rgba(39, 174, 96, 0.1); color: var(--success-color); }
        .status-cancelado { background: rgba(231, 76, 60, 0.1); color: var(--danger-color); }
        
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
        .product-item.negative-difference {
            background-color: #f8d7da;
            border-left-color: var(--danger-color);
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
        .difference-zero { color: #6c757d; }
        
        .count-input {
            width: 100px;
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
                        <li class="breadcrumb-item"><a href="?view=admin-inventarios">Gestão de Inventários</a></li>
                        <li class="breadcrumb-item active">Contagem de Inventário</li>
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
                    <h1 class="page-title" id="tituloInventario">Contagem de Inventário</h1>
                    <p class="page-subtitle" id="subtituloInventario">Carregando informações do inventário...</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-info" onclick="exportarRelatorio()">
                        <i class="bi bi-download me-2"></i>Exportar
                    </button>
                    <button class="btn btn-success" onclick="finalizarInventario()">
                        <i class="bi bi-check-circle me-2"></i>Finalizar Inventário
                    </button>
                </div>
            </div>
            
            <!-- Resumo do Inventário -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card-custom">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <h5>Informações do Inventário</h5>
                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <small class="text-muted">Código:</small>
                                            <div><strong id="codigoInventario">-</strong></div>
                                        </div>
                                        <div class="col-md-3">
                                            <small class="text-muted">Status:</small>
                                            <div><span class="badge" id="statusInventario">-</span></div>
                                        </div>
                                        <div class="col-md-3">
                                            <small class="text-muted">Responsável:</small>
                                            <div><strong id="responsavelInventario">-</strong></div>
                                        </div>
                                        <div class="col-md-3">
                                            <small class="text-muted">Data Abertura:</small>
                                            <div><strong id="dataAberturaInventario">-</strong></div>
                                        </div>
                                    </div>
                                    <div class="row g-3 mt-2">
                                        <div class="col-md-6">
                                            <small class="text-muted">Descrição:</small>
                                            <div><strong id="descricaoInventario">-</strong></div>
                                        </div>
                                        <div class="col-md-6">
                                            <small class="text-muted">Filial:</small>
                                            <div><strong id="filialInventario">-</strong></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <div class="card bg-primary text-white text-center">
                                                <div class="card-body py-2">
                                                    <h6 class="card-title mb-1">Total Itens</h6>
                                                    <h4 class="mb-0" id="totalItens">0</h4>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="card bg-success text-white text-center">
                                                <div class="card-body py-2">
                                                    <h6 class="card-title mb-1">Contados</h6>
                                                    <h4 class="mb-0" id="itensContados">0</h4>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="card bg-warning text-white text-center">
                                                <div class="card-body py-2">
                                                    <h6 class="card-title mb-1">Pendentes</h6>
                                                    <h4 class="mb-0" id="itensPendentes">0</h4>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="card bg-info text-white text-center">
                                                <div class="card-body py-2">
                                                    <h6 class="card-title mb-1">Diferenças</h6>
                                                    <h4 class="mb-0" id="itensComDiferenca">0</h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Barra de Progresso -->
                            <div class="mt-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted">Progresso da Contagem</span>
                                    <span class="text-muted" id="textoProgresso">0% (0/0)</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar" id="barraProgresso" style="width: 0%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtros e Ações -->
            <div class="card-custom mb-4">
                <div class="card-body">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label">Buscar Produto</label>
                            <input type="text" class="form-control" id="filtroBusca" placeholder="Nome ou código">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Status</label>
                            <select class="form-select" id="filtroStatus">
                                <option value="">Todos</option>
                                <option value="pendente">Pendentes</option>
                                <option value="contado">Contados</option>
                                <option value="diferenca">Com Diferença</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Categoria</label>
                            <select class="form-select" id="filtroCategoria">
                                <option value="">Todas</option>
                                <!-- Categorias serão carregadas dinamicamente -->
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-primary me-2" onclick="filtrarProdutos()">
                                <i class="bi bi-search me-1"></i>
                                Filtrar
                            </button>
                            <button class="btn btn-outline-secondary me-2" onclick="limparFiltros()">
                                <i class="bi bi-arrow-clockwise"></i>
                            </button>
                            <button class="btn btn-outline-success" onclick="contarTodosVisiveis()">
                                <i class="bi bi-check-all me-1"></i>
                                Contar Visíveis
                            </button>
                        </div>
                        <div class="col-md-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="apenasDiferencas">
                                <label class="form-check-label" for="apenasDiferencas">
                                    Apenas diferenças
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lista de Produtos -->
            <div class="card-custom">
                <div class="card-header-custom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Produtos do Inventário</h5>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-primary btn-sm" onclick="salvarProgresso()">
                            <i class="bi bi-save me-1"></i>
                            Salvar Progresso
                        </button>
                        <button class="btn btn-outline-info btn-sm" onclick="imprimirFolhaContagem()">
                            <i class="bi bi-printer me-1"></i>
                            Folha de Contagem
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Código</th>
                                    <th>Produto</th>
                                    <th>Localização</th>
                                    <th>Estoque Sistema</th>
                                    <th>Quantidade Contada</th>
                                    <th>Diferença</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody id="tabelaProdutos">
                                <!-- Produtos serão carregados dinamicamente -->
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Carregando...</span>
                                        </div>
                                        <p class="mt-2">Carregando produtos...</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted" id="infoPaginacao">Mostrando 0 de 0 produtos</small>
                        <nav>
                            <ul class="pagination pagination-sm mb-0" id="paginacao">
                                <!-- Paginação será gerada dinamicamente -->
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal de Observação -->
    <div class="modal fade" id="modalObservacao" tabindex="-1" aria-labelledby="modalObservacaoLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalObservacaoLabel">Adicionar Observação</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Produto</label>
                        <input type="text" class="form-control" id="nomeProdutoObservacao" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Observação</label>
                        <textarea class="form-control" id="textoObservacao" rows="4" placeholder="Digite sua observação sobre este item..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="salvarObservacao()">Salvar Observação</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Contagem Rápida -->
    <div class="modal fade" id="modalContagemRapida" tabindex="-1" aria-labelledby="modalContagemRapidaLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalContagemRapidaLabel">Contagem Rápida</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formContagemRapida">
                        <input type="hidden" id="idProdutoContagem">
                        <input type="hidden" id="idInventarioContagem">
                        <div class="mb-3">
                            <label class="form-label">Produto</label>
                            <p class="form-control-plaintext fw-bold" id="produtoContagemRapida"></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Quantidade no Sistema</label>
                            <p class="form-control-plaintext" id="quantidadeSistemaContagemRapida"></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Quantidade Física *</label>
                            <input type="number" class="form-control" id="quantidadeFisicaContagemRapida" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tipo de Operação *</label>
                            <select class="form-select" id="tipoOperacaoContagem" required>
                                <option value="Adicionar" selected>Adicionar</option>
                                <option value="Substituir" >Substituir</option>
                                <option value="Excluir">Excluir</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Observação</label>
                            <textarea class="form-control" id="observacaoContagemRapida" rows="3" placeholder="Observações sobre a contagem..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="salvarContagemRapida()">Salvar Contagem</button>
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
        const idInventario = <?php echo $id_inventario; ?>;
        const BASE_URL = 'http://localhost/saas-multiempresas-api/public';
        
        let inventario = null;
        let produtosInventario = [];
        let contagens = [];
        let produtosFiltrados = [];
        let produtosPaginados = [];
        let itensPorPagina = 10;
        let paginaAtual = 1;
        let produtoSelecionadoParaObservacao = null;
        let modalObservacao = null;
        let modalContagemRapida = null;

        // Configuração da API
        const API_CONFIG = {
            // Endpoint para obter a capa do inventário
            CAPA_INVENTARIO: (idCapa) => 
                `${BASE_URL}/api/capa-inventarios/${idCapa}`,
            
            // Endpoints para contagem
            CONTAGENS_INVENTARIO: (idInventario) => 
                `${BASE_URL}/api/contagens/inventario/${idInventario}`,
            
            CONTAGENS_CREATE: () => 
                `${BASE_URL}/api/contagens`,
            
            CONTAGENS_UPDATE: (idContagem) => 
                `${BASE_URL}/api/contagens/${idContagem}`,
            
            CONTAGENS_DELETE: (idContagem) => 
                `${BASE_URL}/api/contagens/${idContagem}`,
            
            // Endpoint para obter os itens do inventário
            INVENTARIOS_CAPA: (idCapa) => 
                `${BASE_URL}/api/v1/inventarios/capa/${idCapa}`,
            
            // Endpoint para finalizar inventário
            CAPA_INVENTARIO_UPDATE: (idCapa) => 
                `${BASE_URL}/api/capa-inventarios/${idCapa}`,
            
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
            modalObservacao = new bootstrap.Modal(document.getElementById('modalObservacao'));
            modalContagemRapida = new bootstrap.Modal(document.getElementById('modalContagemRapida'));
            
            // Carregar dados iniciais
            carregarDadosInventario();
            
            // Configurar eventos
            document.getElementById('filtroBusca').addEventListener('input', filtrarProdutos);
            document.getElementById('filtroStatus').addEventListener('change', filtrarProdutos);
            document.getElementById('filtroCategoria').addEventListener('change', filtrarProdutos);
            document.getElementById('apenasDiferencas').addEventListener('change', filtrarProdutos);
            
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

        // ========== CARREGAR DADOS DO INVENTÁRIO ==========
        async function carregarDadosInventario() {
            mostrarLoading(true);
            
            try {
                // Buscar capa do inventário
                const responseCapa = await fetch(
                    API_CONFIG.CAPA_INVENTARIO(idInventario),
                    {
                        method: 'GET',
                        headers: API_CONFIG.getHeaders()
                    }
                );
                
                if (!responseCapa.ok) {
                    throw new Error(`Erro ${responseCapa.status}: ${responseCapa.statusText}`);
                }
                
                inventario = await responseCapa.json();
                
                // Buscar itens do inventário
                const responseItens = await fetch(
                    API_CONFIG.INVENTARIOS_CAPA(idInventario),
                    {
                        method: 'GET',
                        headers: API_CONFIG.getHeaders()
                    }
                );
                
                if (!responseItens.ok) {
                    throw new Error(`Erro ${responseItens.status}: ${responseItens.statusText}`);
                }
                
                const itens = await responseItens.json();
                produtosInventario = itens || [];
                
                // Buscar contagens existentes
                await carregarContagens();
                
                // Atualizar interface
                preencherDadosInventario();
                exibirProdutos(produtosInventario);
                atualizarResumo();
                
            } catch (error) {
                console.error('Erro ao carregar dados do inventário:', error);
                mostrarNotificacao('Erro ao carregar dados: ' + error.message, 'error');
            } finally {
                mostrarLoading(false);
            }
        }

        async function carregarContagens() {
            try {
                const response = await fetch(
                    API_CONFIG.CONTAGENS_INVENTARIO(idInventario),
                    {
                        method: 'GET',
                        headers: API_CONFIG.getHeaders()
                    }
                );
                
                if (response.ok) {
                    contagens = await response.json();
                    
                    // Associar contagens aos produtos
                    produtosInventario.forEach(produto => {
                        const contagem = contagens.find(c => c.id_produto === produto.id_produto);
                        if (contagem) {
                            produto.contagem = contagem;
                            produto.quantidade_fisica = parseFloat(contagem.quantidade);
                            produto.observacao = contagem.observacao;
                            produto.tipo_operacao = contagem.tipo_operacao;
                        }
                    });
                }
            } catch (error) {
                console.error('Erro ao carregar contagens:', error);
            }
        }

        function preencherDadosInventario() {
            document.getElementById('tituloInventario').textContent = `Contagem de Inventário - ${inventario.descricao}`;
            document.getElementById('subtituloInventario').textContent = `Inventário ${inventario.descricao} - ${inventario.filial ? inventario.filial.nome_filial : 'N/A'}`;
            document.getElementById('codigoInventario').textContent = `#${inventario.id_capa_inventario}`;
            document.getElementById('statusInventario').textContent = formatarStatus(inventario.status);
            document.getElementById('statusInventario').className = `status-badge status-${inventario.status}`;
            document.getElementById('responsavelInventario').textContent = inventario.usuario ? inventario.usuario.nome : 'N/A';
            document.getElementById('dataAberturaInventario').textContent = formatarData(inventario.data_inicio);
            document.getElementById('descricaoInventario').textContent = inventario.descricao;
            document.getElementById('filialInventario').textContent = inventario.filial ? inventario.filial.nome_filial : 'N/A';
        }

        // ========== EXIBIÇÃO DE PRODUTOS ==========
        function exibirProdutos(produtos) {
            produtosFiltrados = produtos;
            atualizarPaginacao();
            renderizarProdutos();
        }

        function renderizarProdutos() {
            const tbody = document.getElementById('tabelaProdutos');
            
            if (produtosPaginados.length === 0) {
                tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted py-4">Nenhum produto encontrado</td></tr>';
                return;
            }
            
            tbody.innerHTML = produtosPaginados.map(produto => {
                const produtoInfo = produto.produto;
                const quantidadeSistema = parseFloat(produto.quantidade_sistema || 0);
                const quantidadeFisica = produto.quantidade_fisica || 0;
                const diferenca = quantidadeFisica ? (quantidadeFisica - quantidadeSistema) : 0;
                
                let status = 'pendente';
                let statusClass = '';
                let statusBadge = 'bg-secondary';
                let statusText = 'Pendente';
                
                if (quantidadeFisica !== 0) {
                    status = 'contado';
                    statusClass = 'counted';
                    statusBadge = 'bg-success';
                    statusText = 'Contado';
                    
                    if (diferenca !== 0) {
                        status = 'diferenca';
                        statusClass = 'difference';
                        statusBadge = 'bg-warning';
                        statusText = 'Diferença';
                        
                        if (diferenca < 0) {
                            statusClass += ' negative-difference';
                        }
                    }
                }
                
                const diferencaClass = diferenca > 0 ? 'difference-positive' : 
                                      diferenca < 0 ? 'difference-negative' : 'difference-zero';
                const diferencaTexto = quantidadeFisica ? 
                    (diferenca > 0 ? `+${diferenca}` : diferenca) : '-';
                
                return `
                    <tr class="product-item ${statusClass}" data-id-produto="${produto.id_produto}">
                        <td>
                            <strong>${produtoInfo ? produtoInfo.id_produto : 'N/A'}</strong>
                            <br>
                            <small class="text-muted">${produtoInfo ? (produtoInfo.codigo_barras || 'Sem código') : 'N/A'}</small>
                        </td>
                        <td>
                            <div>
                                <strong>${produtoInfo ? produtoInfo.descricao : 'N/A'}</strong>
                                <br>
                                <small class="text-muted">${produtoInfo ? produtoInfo.categoria : 'Sem categoria'}</small>
                            </div>
                        </td>
                        <td>
                            <small>${produtoInfo ? (produtoInfo.localizacao || 'N/A') : 'N/A'}</small>
                        </td>
                        <td><strong>${quantidadeSistema}</strong> ${produtoInfo ? produtoInfo.unidade_medida : ''}</td>
                        <td>
                            <input type="number" class="form-control count-input" 
                                   value="${quantidadeFisica || ''}" 
                                   onchange="atualizarContagem(this, ${produto.id_produto})"
                                   step="0.01" min="0">
                        </td>
                        <td>
                            <span class="${diferencaClass}">${diferencaTexto}</span>
                        </td>
                        <td>
                            <span class="badge ${statusBadge}">${statusText}</span>
                        </td>
                        <td>
                            <button class="btn btn-outline-primary btn-sm" onclick="abrirModalObservacao(${produto.id_produto})">
                                <i class="bi bi-chat-text"></i>
                            </button>
                            <button class="btn btn-outline-warning btn-sm" onclick="abrirModalContagemRapida(${produto.id_produto})">
                                <i class="bi bi-pencil"></i>
                            </button>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        function atualizarPaginacao() {
            const totalPaginas = Math.ceil(produtosFiltrados.length / itensPorPagina);
            paginaAtual = Math.min(paginaAtual, totalPaginas);
            
            const inicio = (paginaAtual - 1) * itensPorPagina;
            const fim = inicio + itensPorPagina;
            produtosPaginados = produtosFiltrados.slice(inicio, fim);
            
            // Atualizar informações de paginação
            document.getElementById('infoPaginacao').textContent = 
                `Mostrando ${inicio + 1} a ${Math.min(fim, produtosFiltrados.length)} de ${produtosFiltrados.length} produtos`;
            
            // Gerar controles de paginação
            const paginacao = document.getElementById('paginacao');
            paginacao.innerHTML = '';
            
            if (totalPaginas <= 1) return;
            
            // Botão anterior
            const liAnterior = document.createElement('li');
            liAnterior.className = `page-item ${paginaAtual === 1 ? 'disabled' : ''}`;
            liAnterior.innerHTML = `<a class="page-link" href="#" onclick="mudarPagina(${paginaAtual - 1})">Anterior</a>`;
            paginacao.appendChild(liAnterior);
            
            // Páginas
            for (let i = 1; i <= totalPaginas; i++) {
                const li = document.createElement('li');
                li.className = `page-item ${paginaAtual === i ? 'active' : ''}`;
                li.innerHTML = `<a class="page-link" href="#" onclick="mudarPagina(${i})">${i}</a>`;
                paginacao.appendChild(li);
            }
            
            // Botão próximo
            const liProximo = document.createElement('li');
            liProximo.className = `page-item ${paginaAtual === totalPaginas ? 'disabled' : ''}`;
            liProximo.innerHTML = `<a class="page-link" href="#" onclick="mudarPagina(${paginaAtual + 1})">Próximo</a>`;
            paginacao.appendChild(liProximo);
        }

        function mudarPagina(pagina) {
            paginaAtual = pagina;
            renderizarProdutos();
        }

        // ========== FILTROS ==========
        function filtrarProdutos() {
            const termoBusca = document.getElementById('filtroBusca').value.toLowerCase();
            const statusFiltro = document.getElementById('filtroStatus').value;
            const categoriaFiltro = document.getElementById('filtroCategoria').value;
            const apenasDiferencas = document.getElementById('apenasDiferencas').checked;
            
            let produtosFiltrados = produtosInventario;
            
            // Aplicar filtros
            if (termoBusca) {
                produtosFiltrados = produtosFiltrados.filter(produto => {
                    const produtoInfo = produto.produto;
                    return (
                        produtoInfo.id_produto.toString().includes(termoBusca) ||
                        produtoInfo.descricao.toLowerCase().includes(termoBusca) ||
                        (produtoInfo.codigo_barras && produtoInfo.codigo_barras.toLowerCase().includes(termoBusca))
                    );
                });
            }
            
            if (statusFiltro) {
                produtosFiltrados = produtosFiltrados.filter(produto => {
                    const quantidadeFisica = produto.quantidade_fisica || 0;
                    const quantidadeSistema = parseFloat(produto.quantidade_sistema || 0);
                    const diferenca = quantidadeFisica - quantidadeSistema;
                    
                    switch (statusFiltro) {
                        case 'pendente':
                            return quantidadeFisica === 0;
                        case 'contado':
                            return quantidadeFisica !== 0 && diferenca === 0;
                        case 'diferenca':
                            return quantidadeFisica !== 0 && diferenca !== 0;
                        default:
                            return true;
                    }
                });
            }
            
            if (categoriaFiltro) {
                produtosFiltrados = produtosFiltrados.filter(produto => 
                    produto.produto && produto.produto.categoria === categoriaFiltro
                );
            }
            
            if (apenasDiferencas) {
                produtosFiltrados = produtosFiltrados.filter(produto => {
                    const quantidadeFisica = produto.quantidade_fisica || 0;
                    const quantidadeSistema = parseFloat(produto.quantidade_sistema || 0);
                    return quantidadeFisica !== 0 && (quantidadeFisica - quantidadeSistema) !== 0;
                });
            }
            
            exibirProdutos(produtosFiltrados);
        }

        function limparFiltros() {
            document.getElementById('filtroBusca').value = '';
            document.getElementById('filtroStatus').value = '';
            document.getElementById('filtroCategoria').value = '';
            document.getElementById('apenasDiferencas').checked = false;
            
            exibirProdutos(produtosInventario);
        }

        // ========== CONTAGEM ==========
        async function atualizarContagem(input, idProduto) {
            const quantidadeFisica = parseFloat(input.value) || 0;
            const produto = produtosInventario.find(p => p.id_produto === idProduto);
            
            if (!produto) {
                mostrarNotificacao('Produto não encontrado', 'error');
                return;
            }
            
            // Atualizar localmente
            produto.quantidade_fisica = quantidadeFisica;
            
            // Calcular diferença
            const quantidadeSistema = parseFloat(produto.quantidade_sistema || 0);
            const diferenca = quantidadeFisica - quantidadeSistema;
            
            // Atualizar interface
            const row = input.closest('tr');
            const diferencaCell = row.cells[5];
            const statusCell = row.cells[6];
            
            let statusClass = '';
            let statusBadge = 'bg-secondary';
            let statusText = 'Pendente';
            
            if (quantidadeFisica !== 0) {
                statusClass = 'counted';
                statusBadge = 'bg-success';
                statusText = 'Contado';
                
                if (diferenca !== 0) {
                    statusClass = 'difference';
                    statusBadge = 'bg-warning';
                    statusText = 'Diferença';
                    
                    if (diferenca < 0) {
                        statusClass += ' negative-difference';
                    }
                }
            }
            
            const diferencaClass = diferenca > 0 ? 'difference-positive' : 
                                  diferenca < 0 ? 'difference-negative' : 'difference-zero';
            const diferencaTexto = quantidadeFisica ? 
                (diferenca > 0 ? `+${diferenca}` : diferenca) : '-';
            
            // Atualizar células
            diferencaCell.innerHTML = `<span class="${diferencaClass}">${diferencaTexto}</span>`;
            statusCell.innerHTML = `<span class="badge ${statusBadge}">${statusText}</span>`;
            
            // Atualizar classe da linha
            row.className = `product-item ${statusClass}`;
            row.setAttribute('data-id-produto', idProduto);
            
            // Salvar no servidor
            await salvarContagem(produto, quantidadeFisica);
            
            // Atualizar resumo
            atualizarResumo();
        }

        async function salvarContagem(produto, quantidadeFisica) {
            const contagemExistente = contagens.find(c => 
                c.id_inventario === idInventario && c.id_produto === produto.id_produto
            );
            
            // Formatar data no formato MySQL (YYYY-MM-DD HH:MM:SS)
            const agora = new Date();
            const dataFormatada = agora.toISOString().slice(0, 19).replace('T', ' ');
            
            const dadosContagem = {
                id_inventario: parseInt(produto.id_inventario),
                id_empresa: parseInt(idEmpresa),
                id_filial: parseInt(produto.id_filial),
                id_produto: parseInt(produto.id_produto),
                tipo_operacao: 'Substituir',
                quantidade: quantidadeFisica,
                observacao: produto.observacao || null,
                id_usuario: parseInt(idUsuario),
                data_contagem: dataFormatada
            };
            
            try {
                let response;
                
                if (contagemExistente) {
                    // Atualizar contagem existente
                    response = await fetch(
                        API_CONFIG.CONTAGENS_UPDATE(contagemExistente.id_contagem),
                        {
                            method: 'PUT',
                            headers: API_CONFIG.getHeaders(),
                            body: JSON.stringify(dadosContagem)
                        }
                    );
                } else {
                    // Criar nova contagem
                    response = await fetch(
                        API_CONFIG.CONTAGENS_CREATE(),
                        {
                            method: 'POST',
                            headers: API_CONFIG.getHeaders(),
                            body: JSON.stringify(dadosContagem)
                        }
                    );
                }
                
                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(`Erro ${response.status}: ${JSON.stringify(errorData)}`);
                }
                
                // Atualizar lista de contagens
                if (!contagemExistente) {
                    const novaContagem = await response.json();
                    contagens.push(novaContagem);
                    produto.contagem = novaContagem;
                }
                
            } catch (error) {
                console.error('Erro ao salvar contagem:', error);
                mostrarNotificacao('Erro ao salvar contagem: ' + error.message, 'error');
            }
        }

        function contarTodosVisiveis() {
            produtosPaginados.forEach(produto => {
                if (!produto.quantidade_fisica) {
                    const input = document.querySelector(`tr[data-id-produto="${produto.id_produto}"] .count-input`);
                    if (input) {
                        input.value = produto.quantidade_sistema;
                        atualizarContagem(input, produto.id_produto);
                    }
                }
            });
            
            mostrarNotificacao('Contagem automática realizada para todos os produtos visíveis!', 'success');
        }

        // ========== CONTAGEM RÁPIDA ==========
        function abrirModalContagemRapida(idProduto) {
            const produto = produtosInventario.find(p => p.id_produto === idProduto);
            if (!produto) return;
            
            const produtoInfo = produto.produto;
            
            document.getElementById('idProdutoContagem').value = idProduto;
            document.getElementById('idInventarioContagem').value = produto.id_inventario;
            document.getElementById('produtoContagemRapida').textContent = 
                `${produtoInfo.id_produto} - ${produtoInfo.descricao}`;
            document.getElementById('quantidadeSistemaContagemRapida').textContent = 
                `${produto.quantidade_sistema} ${produtoInfo.unidade_medida}`;
            document.getElementById('quantidadeFisicaContagemRapida').value = produto.quantidade_fisica || '';
            document.getElementById('tipoOperacaoContagem').value = produto.tipo_operacao || 'Substituir';
            document.getElementById('observacaoContagemRapida').value = produto.observacao || '';
            
            modalContagemRapida.show();
        }

        async function salvarContagemRapida() {
            const idProduto = document.getElementById('idProdutoContagem').value;
            const idInventarioContagem = document.getElementById('idInventarioContagem').value;
            const quantidadeFisica = parseFloat(document.getElementById('quantidadeFisicaContagemRapida').value) || 0;
            const tipoOperacao = document.getElementById('tipoOperacaoContagem').value;
            const observacao = document.getElementById('observacaoContagemRapida').value;
            
            if (!quantidadeFisica && tipoOperacao !== 'Excluir') {
                mostrarNotificacao('Informe a quantidade física', 'error');
                return;
            }
            
            const produto = produtosInventario.find(p => p.id_produto == idProduto);
            if (!produto) {
                mostrarNotificacao('Produto não encontrado', 'error');
                return;
            }
            
            mostrarLoading(true);
            
            try {
                // Formatar data no formato MySQL (YYYY-MM-DD HH:MM:SS)
                const agora = new Date();
                const dataFormatada = agora.toISOString().slice(0, 19).replace('T', ' ');
                
                const dadosContagem = {
                    id_inventario: parseInt(idInventarioContagem),
                    id_empresa: parseInt(idEmpresa),
                    id_filial: parseInt(produto.id_filial),
                    id_produto: parseInt(idProduto),
                    tipo_operacao: tipoOperacao,
                    quantidade: quantidadeFisica,
                    observacao: observacao || null,
                    id_usuario: parseInt(idUsuario),
                    data_contagem: dataFormatada
                };
                
                const contagemExistente = contagens.find(c => 
                    c.id_inventario === idInventario && c.id_produto == idProduto
                );
                
                let response;
                
                if (contagemExistente) {
                    response = await fetch(
                        API_CONFIG.CONTAGENS_UPDATE(contagemExistente.id_contagem),
                        {
                            method: 'PUT',
                            headers: API_CONFIG.getHeaders(),
                            body: JSON.stringify(dadosContagem)
                        }
                    );
                } else {
                    response = await fetch(
                        API_CONFIG.CONTAGENS_CREATE(),
                        {
                            method: 'POST',
                            headers: API_CONFIG.getHeaders(),
                            body: JSON.stringify(dadosContagem)
                        }
                    );
                }
                
                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(`Erro ${response.status}: ${JSON.stringify(errorData)}`);
                }
                
                // Atualizar dados locais
                produto.quantidade_fisica = quantidadeFisica;
                produto.tipo_operacao = tipoOperacao;
                produto.observacao = observacao;
                
                if (!contagemExistente) {
                    const novaContagem = await response.json();
                    contagens.push(novaContagem);
                    produto.contagem = novaContagem;
                }
                
                mostrarNotificacao('Contagem salva com sucesso!', 'success');
                modalContagemRapida.hide();
                
                // Atualizar interface
                exibirProdutos(produtosFiltrados);
                atualizarResumo();
                
            } catch (error) {
                console.error('Erro ao salvar contagem:', error);
                mostrarNotificacao('Erro ao salvar contagem: ' + error.message, 'error');
            } finally {
                mostrarLoading(false);
            }
        }

        // ========== OBSERVAÇÕES ==========
        function abrirModalObservacao(idProduto) {
            const produto = produtosInventario.find(p => p.id_produto === idProduto);
            if (!produto) return;
            
            produtoSelecionadoParaObservacao = produto;
            const produtoInfo = produto.produto;
            
            document.getElementById('nomeProdutoObservacao').value = 
                `${produtoInfo.id_produto} - ${produtoInfo.descricao}`;
            document.getElementById('textoObservacao').value = produto.observacao || '';
            
            modalObservacao.show();
        }

        async function salvarObservacao() {
            if (!produtoSelecionadoParaObservacao) return;
            
            const observacao = document.getElementById('textoObservacao').value;
            produtoSelecionadoParaObservacao.observacao = observacao;
            
            // Se já existe uma contagem, atualizar
            if (produtoSelecionadoParaObservacao.quantidade_fisica) {
                await salvarContagem(
                    produtoSelecionadoParaObservacao, 
                    produtoSelecionadoParaObservacao.quantidade_fisica
                );
            }
            
            modalObservacao.hide();
            mostrarNotificacao('Observação salva com sucesso!', 'success');
        }

        // ========== ATUALIZAR RESUMO ==========
        function atualizarResumo() {
            const totalItens = produtosInventario.length;
            const itensContados = produtosInventario.filter(p => p.quantidade_fisica).length;
            const itensPendentes = totalItens - itensContados;
            const itensComDiferenca = produtosInventario.filter(p => {
                if (!p.quantidade_fisica) return false;
                const diferenca = p.quantidade_fisica - parseFloat(p.quantidade_sistema || 0);
                return diferenca !== 0;
            }).length;
            
            const progresso = totalItens > 0 ? Math.round((itensContados / totalItens) * 100) : 0;
            
            document.getElementById('totalItens').textContent = totalItens;
            document.getElementById('itensContados').textContent = itensContados;
            document.getElementById('itensPendentes').textContent = itensPendentes;
            document.getElementById('itensComDiferenca').textContent = itensComDiferenca;
            document.getElementById('textoProgresso').textContent = `${progresso}% (${itensContados}/${totalItens})`;
            document.getElementById('barraProgresso').style.width = `${progresso}%`;
            document.getElementById('barraProgresso').className = `progress-bar ${getProgressBarColor(progresso)}`;
        }

        function getProgressBarColor(progresso) {
            if (progresso < 30) return 'bg-danger';
            if (progresso < 70) return 'bg-warning';
            return 'bg-success';
        }

        // ========== OUTRAS AÇÕES ==========
        function salvarProgresso() {
            mostrarNotificacao('Progresso salvo com sucesso!', 'success');
        }

        function imprimirFolhaContagem() {
            mostrarNotificacao('Gerando folha de contagem...', 'info');
        }

        function exportarRelatorio() {
            mostrarNotificacao('Exportando dados do inventário...', 'info');
        }

        async function finalizarInventario() {
            if (!confirm('Tem certeza que deseja finalizar este inventário? Esta ação não pode ser desfeita.')) {
                return;
            }
            
            mostrarLoading(true);
            
            try {
                const response = await fetch(
                    API_CONFIG.CAPA_INVENTARIO_UPDATE(idInventario),
                    {
                        method: 'PUT',
                        headers: API_CONFIG.getHeaders(),
                        body: JSON.stringify({
                            status: 'concluido',
                            data_fechamento: new Date().toISOString().split('T')[0] + 'T00:00:00Z'
                        })
                    }
                );
                
                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(`Erro ${response.status}: ${JSON.stringify(errorData)}`);
                }
                
                mostrarNotificacao('Inventário finalizado com sucesso!', 'success');
                
                // Redirecionar para a lista de inventários após um breve delay
                setTimeout(() => {
                    window.location.href = '?view=admin-inventarios';
                }, 2000);
                
            } catch (error) {
                console.error('Erro ao finalizar inventário:', error);
                mostrarNotificacao('Erro ao finalizar inventário: ' + error.message, 'error');
            } finally {
                mostrarLoading(false);
            }
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