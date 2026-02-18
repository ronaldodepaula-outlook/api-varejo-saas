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
    <title>Gestão de Notas Fiscais - SAS Multi</title>
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
        
        .status-importada { background: rgba(39, 174, 96, 0.1); color: var(--success-color); }
        .status-pendente { background: rgba(243, 156, 18, 0.1); color: var(--warning-color); }
        .status-erro { background: rgba(231, 76, 60, 0.1); color: var(--danger-color); }
        
        .value-badge {
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            background: rgba(52, 152, 219, 0.1);
            color: var(--primary-color);
        }
        
        .pagination-custom .page-link {
            color: var(--primary-color);
            border: 1px solid #dee2e6;
        }
        
        .pagination-custom .page-item.active .page-link {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .drop-zone {
            border: 2px dashed #dee2e6;
            border-radius: 10px;
            padding: 40px 20px;
            text-align: center;
            background: #f8f9fa;
            transition: all 0.3s;
            cursor: pointer;
        }
        
        .drop-zone:hover, .drop-zone.dragover {
            border-color: var(--primary-color);
            background: rgba(52, 152, 219, 0.05);
        }
        
        .file-list {
            max-height: 200px;
            overflow-y: auto;
        }
        
        .file-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 12px;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            margin-bottom: 8px;
            background: white;
        }
        
        .file-item.success {
            border-color: var(--success-color);
            background: rgba(39, 174, 96, 0.05);
        }
        
        .file-item.error {
            border-color: var(--danger-color);
            background: rgba(231, 76, 60, 0.05);
        }
        
        .tab-content {
            padding: 20px 0;
        }
        
        .nfe-detail-section {
            margin-bottom: 25px;
        }
        
        .nfe-detail-section h6 {
            color: var(--secondary-color);
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 8px;
            margin-bottom: 15px;
        }
        
        .detail-row {
            display: flex;
            margin-bottom: 8px;
        }
        
        .detail-label {
            font-weight: 600;
            color: #6c757d;
            min-width: 150px;
        }
        
        .detail-value {
            flex: 1;
            color: var(--secondary-color);
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
                        <li class="breadcrumb-item active">Gestão de Notas Fiscais</li>
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
                        <li><h6 class="dropdown-header">Alertas do Sistema</h6></li>
                        <li><a class="dropdown-item" href="#">5 notas fiscais pendentes de processamento</a></li>
                        <li><a class="dropdown-item" href="#">1 XML com erro de validação</a></li>
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
                    <h1 class="page-title">Gestão de Notas Fiscais</h1>
                    <p class="page-subtitle">Importe, visualize e gerencie suas notas fiscais</p>
                </div>
                <div>
                    <button class="btn btn-primary" onclick="abrirModalImportacao()">
                        <i class="bi bi-cloud-upload me-2"></i>Importar XML
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
                                <input type="text" class="form-control" id="searchInput" placeholder="Buscar por número NF, chave, emitente...">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <input type="date" class="form-control" id="filterDataInicio" placeholder="Data Início">
                        </div>
                        <div class="col-md-2">
                            <input type="date" class="form-control" id="filterDataFim" placeholder="Data Fim">
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" id="filterTipo">
                                <option value="">Todos os tipos</option>
                                <option value="entrada">Entrada</option>
                                <option value="saida">Saída</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" id="filterStatus">
                                <option value="">Todos os status</option>
                                <option value="importada">Importada</option>
                                <option value="pendente">Pendente</option>
                                <option value="erro">Erro</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tabela de Notas Fiscais -->
            <div class="card-custom">
                <div class="card-header-custom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Lista de Notas Fiscais</h5>
                    <div class="d-flex align-items-center">
                        <span class="text-muted me-3" id="totalNotas">Carregando...</span>
                        <button class="btn btn-sm btn-outline-primary" onclick="carregarNotasFiscais()">
                            <i class="bi bi-arrow-clockwise"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="tabelaNotasFiscais">
                            <thead>
                                <tr>
                                    <th>Número</th>
                                    <th>Série</th>
                                    <th>Emitente</th>
                                    <th>Destinatário</th>
                                    <th>Data Emissão</th>
                                    <th>Valor Total</th>
                                    <th>Status</th>
                                    <th width="120">Ações</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyNotasFiscais">
                                <tr>
                                    <td colspan="8" class="text-center text-muted">Carregando notas fiscais...</td>
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

    <!-- Modal para Importação de XML -->
    <div class="modal fade" id="modalImportacao" tabindex="-1" aria-labelledby="modalImportacaoLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalImportacaoLabel">Importar Notas Fiscais</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-4">
                        <div class="drop-zone" id="dropZone">
                            <i class="bi bi-cloud-upload display-4 text-muted mb-3"></i>
                            <h5>Arraste arquivos XML aqui</h5>
                            <p class="text-muted">ou clique para selecionar</p>
                            <input type="file" id="fileInput" multiple accept=".xml" style="display: none;">
                        </div>
                        <div class="file-list mt-3" id="fileList">
                            <!-- Lista de arquivos será adicionada dinamicamente -->
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="id_filial" class="form-label">Filial *</label>
                        <select class="form-select" id="id_filial" required>
                            <option value="">Selecione a filial</option>
                            <!-- Opções serão carregadas dinamicamente -->
                        </select>
                    </div>
                    
                    <div class="progress mb-3 d-none" id="uploadProgress">
                        <div class="progress-bar" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="processarImportacao()" id="btnProcessar">
                        <i class="bi bi-cloud-upload me-2"></i>Processar Importação
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Detalhes da NF -->
    <div class="modal fade" id="modalDetalhesNF" tabindex="-1" aria-labelledby="modalDetalhesNFLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalDetalhesNFLabel">Detalhes da Nota Fiscal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-tabs" id="nfeTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="geral-tab" data-bs-toggle="tab" data-bs-target="#geral" type="button" role="tab">Informações Gerais</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="itens-tab" data-bs-toggle="tab" data-bs-target="#itens" type="button" role="tab">Itens</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="emitente-tab" data-bs-toggle="tab" data-bs-target="#emitente" type="button" role="tab">Emitente</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="destinatario-tab" data-bs-toggle="tab" data-bs-target="#destinatario" type="button" role="tab">Destinatário</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="transporte-tab" data-bs-toggle="tab" data-bs-target="#transporte" type="button" role="tab">Transporte</button>
                        </li>
                    </ul>
                    
                    <div class="tab-content" id="nfeTabContent">
                        <div class="tab-pane fade show active" id="geral" role="tabpanel">
                            <!-- Conteúdo será carregado dinamicamente -->
                        </div>
                        <div class="tab-pane fade" id="itens" role="tabpanel">
                            <!-- Conteúdo será carregado dinamicamente -->
                        </div>
                        <div class="tab-pane fade" id="emitente" role="tabpanel">
                            <!-- Conteúdo será carregado dinamicamente -->
                        </div>
                        <div class="tab-pane fade" id="destinatario" role="tabpanel">
                            <!-- Conteúdo será carregado dinamicamente -->
                        </div>
                        <div class="tab-pane fade" id="transporte" role="tabpanel">
                            <!-- Conteúdo será carregado dinamicamente -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
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
            NFE_IMPORTAR: '/api/nfe/importar',
            NFE_EMPRESA: '/api/nfe/empresa',
            NFE_EMPRESA_FILIAL: '/api/nfe/empresa',
            NFE_EMPRESA_CNF: '/api/nfe/empresa',
            NFE_EMPRESA_PERIODO: '/api/nfe/empresa',
            NFE_EMITENTE: '/api/nfe/emitente',
            NFE_DESTINATARIO: '/api/nfe/destinatario',
            
            // Headers padrão
            getHeaders: function(token, isFormData = false) {
                const headers = {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json',
                    'X-ID-EMPRESA': '<?php echo $id_empresa; ?>'
                };
                
                if (!isFormData) {
                    headers['Content-Type'] = 'application/json';
                }
                
                return headers;
            },
            
            // URL completa para notas fiscais da empresa
            getNotasFiscaisUrl: function(idEmpresa) {
                return `${this.BASE_URL}${this.NFE_EMPRESA}/${idEmpresa}`;
            },
            
            // URL para notas fiscais por filial
            getNotasFiscaisFilialUrl: function(idEmpresa, idFilial) {
                return `${this.BASE_URL}${this.NFE_EMPRESA_FILIAL}/${idEmpresa}/filial/${idFilial}`;
            },
            
            // URL para importação
            getImportarUrl: function() {
                return `${this.BASE_URL}${this.NFE_IMPORTAR}`;
            },
            
            // URL para notas por período
            getNotasPeriodoUrl: function(idEmpresa, dataInicio, dataFim) {
                return `${this.BASE_URL}${this.NFE_EMPRESA_PERIODO}/${idEmpresa}/periodo?data_inicio=${dataInicio}&data_fim=${dataFim}`;
            },
            
            // URL para emitente
            getEmitenteUrl: function(cnpj) {
                return `${this.BASE_URL}${this.NFE_EMITENTE}/${cnpj}`;
            },
            
            // URL para destinatário
            getDestinatarioUrl: function(cnpj) {
                return `${this.BASE_URL}${this.NFE_DESTINATARIO}/${cnpj}`;
            }
        };
    </script>
    
    <script>
        // Variáveis globais
        const idEmpresa = <?php echo $id_empresa; ?>;
        let notasFiscais = [];
        let filesToUpload = [];
        let modalImportacao = null;
        let modalDetalhesNF = null;
        let currentPage = 1;
        let lastPage = 1;

        // Inicialização
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar modais
            modalImportacao = new bootstrap.Modal(document.getElementById('modalImportacao'));
            modalDetalhesNF = new bootstrap.Modal(document.getElementById('modalDetalhesNF'));
            
            // Carregar dados iniciais
            carregarNotasFiscais();
            carregarFiliais();
            
            // Configurar eventos
            document.getElementById('searchInput').addEventListener('input', filtrarNotasFiscais);
            document.getElementById('filterDataInicio').addEventListener('change', filtrarNotasFiscais);
            document.getElementById('filterDataFim').addEventListener('change', filtrarNotasFiscais);
            document.getElementById('filterTipo').addEventListener('change', filtrarNotasFiscais);
            document.getElementById('filterStatus').addEventListener('change', filtrarNotasFiscais);
            
            // Configurar drag and drop
            const dropZone = document.getElementById('dropZone');
            const fileInput = document.getElementById('fileInput');
            
            dropZone.addEventListener('click', () => fileInput.click());
            
            dropZone.addEventListener('dragover', (e) => {
                e.preventDefault();
                dropZone.classList.add('dragover');
            });
            
            dropZone.addEventListener('dragleave', () => {
                dropZone.classList.remove('dragover');
            });
            
            dropZone.addEventListener('drop', (e) => {
                e.preventDefault();
                dropZone.classList.remove('dragover');
                const files = e.dataTransfer.files;
                handleFiles(files);
            });
            
            fileInput.addEventListener('change', (e) => {
                handleFiles(e.target.files);
            });
            
            // Logoff
            var logoutBtn = document.getElementById('logoutBtn');
            if (logoutBtn) {
                logoutBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    fazerLogout();
                });
            }
        });

        // Função para fazer logout
        async function fazerLogout() {
            try {
                const token = '<?php echo $token; ?>';
                const response = await fetch(API_CONFIG.BASE_URL + '/api/v1/logout', {
                    method: 'POST',
                    headers: API_CONFIG.getHeaders(token)
                });
                
                window.location.href = 'login.php';
                
            } catch (error) {
                console.error('Erro no logout:', error);
                window.location.href = 'login.php';
            }
        }

        // ========== NOTAS FISCAIS ==========
        async function carregarNotasFiscais() {
            mostrarLoading(true);
            
            try {
                const token = '<?php echo $token; ?>';
                const response = await fetch(API_CONFIG.getNotasFiscaisUrl(idEmpresa), {
                    method: 'GET',
                    headers: API_CONFIG.getHeaders(token)
                });
                
                if (!response.ok) {
                    throw new Error(`Erro ${response.status}: ${response.statusText}`);
                }
                
                const data = await response.json();
                notasFiscais = data;
                
                exibirNotasFiscais(notasFiscais);
                atualizarTotalNotas(notasFiscais.length);
                
            } catch (error) {
                console.error('Erro ao carregar notas fiscais:', error);
                mostrarNotificacao('Erro ao carregar notas fiscais: ' + error.message, 'error');
                document.getElementById('tbodyNotasFiscais').innerHTML = '<tr><td colspan="8" class="text-center text-muted">Erro ao carregar dados</td></tr>';
            } finally {
                mostrarLoading(false);
            }
        }

        function exibirNotasFiscais(listaNotas) {
            const tbody = document.getElementById('tbodyNotasFiscais');
            
            if (listaNotas.length === 0) {
                tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">Nenhuma nota fiscal encontrada</td></tr>';
                return;
            }
            
            tbody.innerHTML = listaNotas.map(nota => `
                <tr>
                    <td>
                        <div class="fw-semibold">${nota.nNF}</div>
                        <small class="text-muted">Série ${nota.serie}</small>
                    </td>
                    <td>${nota.serie}</td>
                    <td>
                        <div class="fw-semibold">${nota.emitente?.xNome || 'N/A'}</div>
                        <small class="text-muted">${nota.emitente?.CNPJ || ''}</small>
                    </td>
                    <td>
                        <div class="fw-semibold">${nota.destinatario?.xNome || 'N/A'}</div>
                        <small class="text-muted">${nota.destinatario?.CNPJ || ''}</small>
                    </td>
                    <td>${formatarData(nota.dhEmi)}</td>
                    <td class="text-nowrap fw-semibold text-success">R$ ${formatarPreco(nota.valor_total)}</td>
                    <td><span class="status-badge status-${nota.status}">${formatarStatus(nota.status)}</span></td>
                    <td>
                        <div class="d-flex">
                            <button class="btn btn-action btn-outline-primary" onclick="verDetalhesNF(${nota.id_nfe})" title="Visualizar">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button class="btn btn-action btn-outline-info" onclick="baixarXML(${nota.id_nfe})" title="Baixar XML">
                                <i class="bi bi-download"></i>
                            </button>
                            <button class="btn btn-action btn-outline-danger" onclick="excluirNF(${nota.id_nfe})" title="Excluir">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `).join('');
        }

        async function verDetalhesNF(idNfe) {
            mostrarLoading(true);
            
            try {
                // Encontrar a nota fiscal na lista
                const nota = notasFiscais.find(n => n.id_nfe === idNfe);
                if (!nota) {
                    throw new Error('Nota fiscal não encontrada');
                }
                
                // Preencher as abas do modal
                preencherAbaGeral(nota);
                preencherAbaItens(nota);
                preencherAbaEmitente(nota);
                preencherAbaDestinatario(nota);
                preencherAbaTransporte(nota);
                
                modalDetalhesNF.show();
                
            } catch (error) {
                console.error('Erro ao carregar detalhes da NF:', error);
                mostrarNotificacao('Erro ao carregar detalhes: ' + error.message, 'error');
            } finally {
                mostrarLoading(false);
            }
        }

        function preencherAbaGeral(nota) {
            const content = document.getElementById('geral');
            content.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <div class="nfe-detail-section">
                            <h6>Informações da Nota</h6>
                            <div class="detail-row">
                                <span class="detail-label">Número:</span>
                                <span class="detail-value">${nota.nNF}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Série:</span>
                                <span class="detail-value">${nota.serie}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Data Emissão:</span>
                                <span class="detail-value">${formatarDataHora(nota.dhEmi)}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Valor Total:</span>
                                <span class="detail-value fw-semibold text-success">R$ ${formatarPreco(nota.valor_total)}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Status:</span>
                                <span class="detail-value"><span class="status-badge status-${nota.status}">${formatarStatus(nota.status)}</span></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="nfe-detail-section">
                            <h6>Operação</h6>
                            <div class="detail-row">
                                <span class="detail-label">Natureza:</span>
                                <span class="detail-value">${nota.natOp}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Modelo:</span>
                                <span class="detail-value">${nota.mods}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Tipo NF:</span>
                                <span class="detail-value">${nota.tpNF === 1 ? 'Saída' : 'Entrada'}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Finalidade:</span>
                                <span class="detail-value">${formatarFinalidade(nota.finNFe)}</span>
                            </div>
                        </div>
                    </div>
                </div>
                ${nota.informacoes_adicionais ? `
                <div class="nfe-detail-section">
                    <h6>Informações Adicionais</h6>
                    <div class="alert alert-info">
                        ${nota.informacoes_adicionais.infCpl || 'Nenhuma informação adicional'}
                    </div>
                </div>
                ` : ''}
            `;
        }

        function preencherAbaItens(nota) {
            const content = document.getElementById('itens');
            
            if (!nota.itens || nota.itens.length === 0) {
                content.innerHTML = '<div class="alert alert-info">Nenhum item encontrado</div>';
                return;
            }
            
            content.innerHTML = `
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Código</th>
                                <th>Descrição</th>
                                <th>NCM</th>
                                <th>Qtd</th>
                                <th>Unidade</th>
                                <th>Valor Unit.</th>
                                <th>Valor Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${nota.itens.map(item => `
                                <tr>
                                    <td>${item.nItem}</td>
                                    <td>${item.cProd}</td>
                                    <td>${item.xProd}</td>
                                    <td>${item.NCM}</td>
                                    <td>${formatarQuantidade(item.qCom)}</td>
                                    <td>${item.uCom}</td>
                                    <td>R$ ${formatarPreco(item.vUnCom)}</td>
                                    <td class="fw-semibold">R$ ${formatarPreco(item.vProd)}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
            `;
        }

        function preencherAbaEmitente(nota) {
            const content = document.getElementById('emitente');
            
            if (!nota.emitente) {
                content.innerHTML = '<div class="alert alert-info">Dados do emitente não disponíveis</div>';
                return;
            }
            
            content.innerHTML = `
                <div class="nfe-detail-section">
                    <h6>Dados do Emitente</h6>
                    <div class="detail-row">
                        <span class="detail-label">CNPJ:</span>
                        <span class="detail-value">${formatarCNPJ(nota.emitente.CNPJ)}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Razão Social:</span>
                        <span class="detail-value">${nota.emitente.xNome}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Nome Fantasia:</span>
                        <span class="detail-value">${nota.emitente.xFant || 'N/A'}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">IE:</span>
                        <span class="detail-value">${nota.emitente.IE}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Endereço:</span>
                        <span class="detail-value">${nota.emitente.xLgr}, ${nota.emitente.nro} - ${nota.emitente.xBairro}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Município:</span>
                        <span class="detail-value">${nota.emitente.xMun} - ${nota.emitente.UF}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">CEP:</span>
                        <span class="detail-value">${formatarCEP(nota.emitente.CEP)}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Telefone:</span>
                        <span class="detail-value">${formatarTelefone(nota.emitente.fone)}</span>
                    </div>
                </div>
            `;
        }

        function preencherAbaDestinatario(nota) {
            const content = document.getElementById('destinatario');
            
            if (!nota.destinatario) {
                content.innerHTML = '<div class="alert alert-info">Dados do destinatário não disponíveis</div>';
                return;
            }
            
            content.innerHTML = `
                <div class="nfe-detail-section">
                    <h6>Dados do Destinatário</h6>
                    <div class="detail-row">
                        <span class="detail-label">CNPJ:</span>
                        <span class="detail-value">${formatarCNPJ(nota.destinatario.CNPJ)}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Razão Social:</span>
                        <span class="detail-value">${nota.destinatario.xNome}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">IE:</span>
                        <span class="detail-value">${nota.destinatario.IE}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Endereço:</span>
                        <span class="detail-value">${nota.destinatario.xLgr}, ${nota.destinatario.nro} - ${nota.destinatario.xBairro}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Município:</span>
                        <span class="detail-value">${nota.destinatario.xMun} - ${nota.destinatario.UF}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">CEP:</span>
                        <span class="detail-value">${formatarCEP(nota.destinatario.CEP)}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Telefone:</span>
                        <span class="detail-value">${formatarTelefone(nota.destinatario.fone)}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Email:</span>
                        <span class="detail-value">${nota.destinatario.email || 'N/A'}</span>
                    </div>
                </div>
            `;
        }

        function preencherAbaTransporte(nota) {
            const content = document.getElementById('transporte');
            
            if (!nota.transporte) {
                content.innerHTML = '<div class="alert alert-info">Dados de transporte não disponíveis</div>';
                return;
            }
            
            content.innerHTML = `
                <div class="nfe-detail-section">
                    <h6>Dados do Transporte</h6>
                    <div class="detail-row">
                        <span class="detail-label">Modalidade:</span>
                        <span class="detail-value">${formatarModalidadeFrete(nota.transporte.modFrete)}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Transportadora:</span>
                        <span class="detail-value">${nota.transporte.xNome || 'N/A'}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">CNPJ:</span>
                        <span class="detail-value">${nota.transporte.CNPJ ? formatarCNPJ(nota.transporte.CNPJ) : 'N/A'}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Peso Bruto:</span>
                        <span class="detail-value">${nota.transporte.pesoB || '0'} kg</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Peso Líquido:</span>
                        <span class="detail-value">${nota.transporte.pesoL || '0'} kg</span>
                    </div>
                </div>
            `;
        }

        // ========== IMPORTAÇÃO DE XML ==========
        function abrirModalImportacao() {
            filesToUpload = [];
            document.getElementById('fileList').innerHTML = '';
            document.getElementById('uploadProgress').classList.add('d-none');
            document.getElementById('btnProcessar').disabled = false;
            modalImportacao.show();
        }

        function handleFiles(files) {
            for (let file of files) {
                if (file.type === 'text/xml' || file.name.toLowerCase().endsWith('.xml')) {
                    if (!filesToUpload.some(f => f.name === file.name && f.size === file.size)) {
                        filesToUpload.push(file);
                        adicionarArquivoNaLista(file);
                    }
                }
            }
        }

        function adicionarArquivoNaLista(file) {
            const fileList = document.getElementById('fileList');
            const fileItem = document.createElement('div');
            fileItem.className = 'file-item';
            fileItem.innerHTML = `
                <div>
                    <i class="bi bi-file-earmark-text text-primary me-2"></i>
                    <span class="file-name">${file.name}</span>
                    <small class="text-muted ms-2">(${formatarTamanhoArquivo(file.size)})</small>
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removerArquivo('${file.name}', ${file.size})">
                    <i class="bi bi-x"></i>
                </button>
            `;
            fileList.appendChild(fileItem);
        }

        function removerArquivo(nome, tamanho) {
            filesToUpload = filesToUpload.filter(f => !(f.name === nome && f.size === tamanho));
            const fileList = document.getElementById('fileList');
            fileList.innerHTML = '';
            filesToUpload.forEach(file => adicionarArquivoNaLista(file));
        }

        async function processarImportacao() {
            const idFilial = document.getElementById('id_filial').value;
            
            if (filesToUpload.length === 0) {
                mostrarNotificacao('Selecione pelo menos um arquivo XML para importar', 'warning');
                return;
            }
            
            if (!idFilial) {
                mostrarNotificacao('Selecione a filial para importação', 'warning');
                return;
            }
            
            mostrarLoading(true);
            document.getElementById('btnProcessar').disabled = true;
            document.getElementById('uploadProgress').classList.remove('d-none');
            
            const progressBar = document.getElementById('uploadProgress').querySelector('.progress-bar');
            let successCount = 0;
            let errorCount = 0;
            
            for (let i = 0; i < filesToUpload.length; i++) {
                const file = filesToUpload[i];
                const formData = new FormData();
                formData.append('xml_file', file);
                formData.append('id_filial', idFilial);
                formData.append('id_empresa', idEmpresa);
                
                try {
                    const token = '<?php echo $token; ?>';
                    const response = await fetch(API_CONFIG.getImportarUrl(), {
                        method: 'POST',
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'X-ID-EMPRESA': idEmpresa.toString()
                        },
                        body: formData
                    });
                    
                    if (response.ok) {
                        successCount++;
                        atualizarStatusArquivo(file.name, file.size, 'success');
                    } else {
                        errorCount++;
                        atualizarStatusArquivo(file.name, file.size, 'error');
                    }
                } catch (error) {
                    console.error('Erro ao importar arquivo:', error);
                    errorCount++;
                    atualizarStatusArquivo(file.name, file.size, 'error');
                }
                
                // Atualizar barra de progresso
                const progress = ((i + 1) / filesToUpload.length) * 100;
                progressBar.style.width = `${progress}%`;
                progressBar.setAttribute('aria-valuenow', progress);
            }
            
            mostrarLoading(false);
            
            if (errorCount === 0) {
                mostrarNotificacao(`${successCount} arquivo(s) importado(s) com sucesso!`, 'success');
                modalImportacao.hide();
                carregarNotasFiscais();
            } else {
                mostrarNotificacao(`${successCount} arquivo(s) importado(s) com sucesso, ${errorCount} com erro.`, 'warning');
            }
        }

        function atualizarStatusArquivo(nome, tamanho, status) {
            const fileItems = document.querySelectorAll('.file-item');
            fileItems.forEach(item => {
                const fileName = item.querySelector('.file-name').textContent;
                if (fileName === nome) {
                    item.className = `file-item ${status}`;
                    const icon = item.querySelector('i');
                    if (status === 'success') {
                        icon.className = 'bi bi-check-circle text-success me-2';
                    } else {
                        icon.className = 'bi bi-x-circle text-danger me-2';
                    }
                }
            });
        }

        // ========== FILTROS ==========
        function filtrarNotasFiscais() {
            const termoBusca = document.getElementById('searchInput').value.toLowerCase();
            const dataInicio = document.getElementById('filterDataInicio').value;
            const dataFim = document.getElementById('filterDataFim').value;
            const tipoFiltro = document.getElementById('filterTipo').value;
            const statusFiltro = document.getElementById('filterStatus').value;
            
            let notasFiltradas = notasFiscais;
            
            // Aplicar filtros
            if (termoBusca) {
                notasFiltradas = notasFiltradas.filter(nota => 
                    nota.nNF.toString().includes(termoBusca) ||
                    (nota.emitente?.xNome && nota.emitente.xNome.toLowerCase().includes(termoBusca)) ||
                    (nota.destinatario?.xNome && nota.destinatario.xNome.toLowerCase().includes(termoBusca))
                );
            }
            
            if (dataInicio) {
                notasFiltradas = notasFiltradas.filter(nota => 
                    new Date(nota.dhEmi) >= new Date(dataInicio)
                );
            }
            
            if (dataFim) {
                notasFiltradas = notasFiltradas.filter(nota => 
                    new Date(nota.dhEmi) <= new Date(dataFim + 'T23:59:59')
                );
            }
            
            if (tipoFiltro) {
                notasFiltradas = notasFiltradas.filter(nota => 
                    (tipoFiltro === 'entrada' && nota.tpNF === 0) ||
                    (tipoFiltro === 'saida' && nota.tpNF === 1)
                );
            }
            
            if (statusFiltro) {
                notasFiltradas = notasFiltradas.filter(nota => 
                    nota.status === statusFiltro
                );
            }
            
            exibirNotasFiscais(notasFiltradas);
            atualizarTotalNotas(notasFiltradas.length);
        }

        // ========== FUNÇÕES AUXILIARES ==========
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

        function formatarDataHora(data) {
            if (!data) return '';
            try {
                const date = new Date(data);
                return date.toLocaleString('pt-BR');
            } catch (e) {
                return data;
            }
        }

        function formatarStatus(status) {
            const statusMap = {
                'importada': 'Importada',
                'pendente': 'Pendente',
                'erro': 'Erro'
            };
            return statusMap[status] || status;
        }

        function formatarFinalidade(finNFe) {
            const finalidades = {
                1: 'NF-e normal',
                2: 'NF-e complementar',
                3: 'NF-e de ajuste',
                4: 'Devolução de mercadoria'
            };
            return finalidades[finNFe] || finNFe;
        }

        function formatarModalidadeFrete(modFrete) {
            const modalidades = {
                0: 'Contratação do Frete por conta do Remetente (CIF)',
                1: 'Contratação do Frete por conta do Destinatário (FOB)',
                2: 'Contratação do Frete por conta de Terceiros',
                3: 'Transporte Próprio por conta do Remetente',
                4: 'Transporte Próprio por conta do Destinatário',
                9: 'Sem Ocorrência de Transporte'
            };
            return modalidades[modFrete] || modFrete;
        }

        function formatarCNPJ(cnpj) {
            if (!cnpj) return 'N/A';
            return cnpj.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, '$1.$2.$3/$4-$5');
        }

        function formatarCEP(cep) {
            if (!cep) return 'N/A';
            return cep.replace(/(\d{5})(\d{3})/, '$1-$2');
        }

        function formatarTelefone(telefone) {
            if (!telefone) return 'N/A';
            const cleaned = telefone.replace(/\D/g, '');
            if (cleaned.length === 11) {
                return cleaned.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
            } else if (cleaned.length === 10) {
                return cleaned.replace(/(\d{2})(\d{4})(\d{4})/, '($1) $2-$3');
            }
            return telefone;
        }

        function formatarQuantidade(qtd) {
            if (!qtd) return '0';
            return parseFloat(qtd).toLocaleString('pt-BR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 4
            });
        }

        function formatarTamanhoArquivo(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        function atualizarTotalNotas(total) {
            document.getElementById('totalNotas').textContent = `${total} nota(s) fiscal(is) encontrada(s)`;
        }

        function mostrarLoading(mostrar) {
            document.getElementById('loadingOverlay').classList.toggle('d-none', !mostrar);
        }

        async function carregarFiliais() {
            // Simulação - em uma implementação real, você buscaria as filiais da empresa
            const select = document.getElementById('id_filial');
            select.innerHTML = '<option value="">Selecione a filial</option>';
            
            // Exemplo de filiais - substitua por chamada à API real
            const filiais = [
                { id: 1, nome: 'Matriz' },
                { id: 2, nome: 'Filial 1' },
                { id: 3, nome: 'Filial 2' }
            ];
            
            filiais.forEach(filial => {
                select.innerHTML += `<option value="${filial.id}">${filial.nome}</option>`;
            });
        }

        function baixarXML(idNfe) {
            // Implementar download do XML
            mostrarNotificacao('Funcionalidade de download em desenvolvimento', 'info');
        }

        function excluirNF(idNfe) {
            if (confirm('Tem certeza que deseja excluir esta nota fiscal?')) {
                // Implementar exclusão
                mostrarNotificacao('Funcionalidade de exclusão em desenvolvimento', 'info');
            }
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