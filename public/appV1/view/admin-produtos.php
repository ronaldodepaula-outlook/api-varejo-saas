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
    <title>Gestão de Produtos - SAS Multi</title>
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
        
        .price-badge {
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
                        <li class="breadcrumb-item active">Gestão de Produtos</li>
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
                        <li><a class="dropdown-item" href="#">2 produtos com estoque baixo</a></li>
                        <li><a class="dropdown-item" href="#">1 novo produto cadastrado</a></li>
                        <li><a class="dropdown-item" href="#">Atualização de preços pendente</a></li>
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
                    <h1 class="page-title">Gestão de Produtos</h1>
                    <p class="page-subtitle">Cadastre e gerencie os produtos do seu catálogo</p>
                </div>
                <div>
                    <button class="btn btn-primary" onclick="abrirModalProduto()">
                        <i class="bi bi-plus-circle me-2"></i>Novo Produto
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
                                <input type="text" class="form-control" id="searchInput" placeholder="Buscar por descrição ou código de barras...">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="filterCategoria">
                                <option value="">Todas as categorias</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="filterStatus">
                                <option value="">Todos os status</option>
                                <option value="ativo">Ativo</option>
                                <option value="inativo">Inativo</option>
                            </select>
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
                                    <th>ID</th>
                                    <th>Descrição</th>
                                    <th>Código Barras</th>
                                    <th>Unidade</th>
                                    <th>Preço Custo</th>
                                    <th>Preço Venda</th>
                                    <th>Status</th>
                                    <th>Data Cadastro</th>
                                    <th width="120">Ações</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyProdutos">
                                <tr>
                                    <td colspan="9" class="text-center text-muted">Carregando produtos...</td>
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

    <!-- Modal para Adicionar/Editar Produto -->
    <div class="modal fade" id="modalProduto" tabindex="-1" aria-labelledby="modalProdutoLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalProdutoLabel">Novo Produto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formProduto">
                        <input type="hidden" id="produtoId">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="descricao" class="form-label">Descrição do Produto *</label>
                                <input type="text" class="form-control" id="descricao" required>
                                <div class="invalid-feedback">Por favor, informe a descrição do produto.</div>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="codigo_barras" class="form-label">Código de Barras</label>
                                <input type="text" class="form-control" id="codigo_barras">
                            </div>
                            
                            <div class="col-md-6">
                                <label for="unidade_medida" class="form-label">Unidade de Medida *</label>
                                <select class="form-select" id="unidade_medida" required>
                                    <option value="">Selecione a unidade</option>
                                    <option value="UN">Unidade (UN)</option>
                                    <option value="KG">Quilograma (KG)</option>
                                    <option value="GR">Grama (GR)</option>
                                    <option value="LT">Litro (LT)</option>
                                    <option value="ML">Mililitro (ML)</option>
                                    <option value="M">Metro (M)</option>
                                    <option value="CM">Centímetro (CM)</option>
                                    <option value="CX">Caixa (CX)</option>
                                    <option value="PC">Pacote (PC)</option>
                                    <option value="SC">Saco (SC)</option>
                                    <option value="DZ">Dúzia (DZ)</option>
                                    <option value="PCT">Pacote (PCT)</option>
                                    <option value="FD">Fardo (FD)</option>
                                    <option value="KIT">Kit (KIT)</option>
                                    <option value="PAR">Par (PAR)</option>
                                    <option value="HR">Hora (HR)</option>
                                    <option value="M2">Metro Quadrado (M²)</option>
                                    <option value="M3">Metro Cúbico (M³)</option>
                                </select>
                                <div class="invalid-feedback">Por favor, selecione a unidade de medida.</div>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="preco_custo" class="form-label">Preço de Custo *</label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <input type="number" class="form-control" id="preco_custo" step="0.01" min="0" required>
                                </div>
                                <div class="invalid-feedback">Por favor, informe o preço de custo.</div>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="preco_venda" class="form-label">Preço de Venda *</label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <input type="number" class="form-control" id="preco_venda" step="0.01" min="0" required>
                                </div>
                                <div class="invalid-feedback">Por favor, informe o preço de venda.</div>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="id_categoria" class="form-label">Categoria *</label>
                                <select class="form-select" id="id_categoria" required>
                                    <option value="">Selecione uma categoria</option>
                                </select>
                                <div class="invalid-feedback">Por favor, selecione uma categoria.</div>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="id_secao" class="form-label">Seção *</label>
                                <select class="form-select" id="id_secao" required>
                                    <option value="">Selecione uma seção</option>
                                </select>
                                <div class="invalid-feedback">Por favor, selecione uma seção.</div>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="id_grupo" class="form-label">Grupo *</label>
                                <select class="form-select" id="id_grupo" required>
                                    <option value="">Selecione um grupo</option>
                                </select>
                                <div class="invalid-feedback">Por favor, selecione um grupo.</div>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="id_subgrupo" class="form-label">Subgrupo *</label>
                                <select class="form-select" id="id_subgrupo" required>
                                    <option value="">Selecione um subgrupo</option>
                                </select>
                                <div class="invalid-feedback">Por favor, selecione um subgrupo.</div>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="ativo" class="form-label">Status *</label>
                                <select class="form-select" id="ativo" required>
                                    <option value="1">Ativo</option>
                                    <option value="0">Inativo</option>
                                </select>
                                <div class="invalid-feedback">Por favor, selecione o status.</div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="salvarProduto()">Salvar Produto</button>
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
                    <p>Tem certeza que deseja excluir o produto <strong id="nomeProdutoExcluir"></strong>?</p>
                    <p class="text-danger"><small>Esta ação não pode ser desfeita.</small></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="btnConfirmarExclusao">Excluir Produto</button>
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
            BASE_URL: 'https://rdpsolutions.online/saas-multiempresas/public',
            API_VERSION: 'v1',
            
            // Endpoints
            PRODUTOS: '/api/v1/produtos',
            PRODUTOS_EMPRESA: '/api/v1/empresas',
            CATEGORIAS_EMPRESA: '/api/v1/categorias/empresa',
            SECOES_EMPRESA: '/api/v1/secoes/empresa',
            GRUPOS_EMPRESA: '/api/v1/grupos/empresa',
            SUBGRUPOS_EMPRESA: '/api/v1/subgrupos/empresa',
            LOGIN: '/api/v1/login',
            LOGOUT: '/api/v1/logout',
            
            // Headers padrão
            getHeaders: function(token) {
                return {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                };
            },

            getJsonHeaders: function(token) {
                return {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                };
            },
            
            // URL completa para produtos por empresa (LISTAGEM)
            getProdutosEmpresaUrl: function(idEmpresa, page = 1) {
                return `${this.BASE_URL}${this.PRODUTOS_EMPRESA}/${idEmpresa}/produtos?page=${page}`;
            },
            
            // URL completa para produtos (CRIAÇÃO)
            getProdutosUrl: function() {
                return `${this.BASE_URL}${this.PRODUTOS}`;
            },
            
            // URL completa para um produto específico (EDIÇÃO/EXCLUSÃO)
            getProdutoUrl: function(id) {
                return `${this.BASE_URL}${this.PRODUTOS}/${id}`;
            },
            
            // URL para categorias da empresa
            getCategoriasEmpresaUrl: function(idEmpresa) {
                return `${this.BASE_URL}${this.CATEGORIAS_EMPRESA}/${idEmpresa}`;
            },
            
            // URL para seções da empresa por categoria
            getSecoesEmpresaUrl: function(idEmpresa, idCategoria) {
                return `${this.BASE_URL}${this.SECOES_EMPRESA}/${idEmpresa}/categoria/${idCategoria}`;
            },
            
            // URL para grupos da empresa por seção
            getGruposEmpresaUrl: function(idEmpresa, idSecao) {
                return `${this.BASE_URL}${this.GRUPOS_EMPRESA}/${idEmpresa}/secao/${idSecao}`;
            },
            
            // URL para subgrupos da empresa por grupo
            getSubgruposEmpresaUrl: function(idEmpresa, idGrupo) {
                return `${this.BASE_URL}${this.SUBGRUPOS_EMPRESA}/${idEmpresa}/grupo/${idGrupo}`;
            }
        };
    </script>
    
    <script>
        // Variáveis globais
        const idEmpresa = <?php echo $id_empresa; ?>;
        let produtos = [];
        let categorias = [];
        let secoes = [];
        let grupos = [];
        let subgrupos = [];
        let produtoEditando = null;
        let modalProduto = null;
        let modalConfirmacao = null;
        let currentPage = 1;
        let lastPage = 1;

        // Inicialização
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar modais
            modalProduto = new bootstrap.Modal(document.getElementById('modalProduto'));
            modalConfirmacao = new bootstrap.Modal(document.getElementById('modalConfirmacao'));
            
            // Carregar dados iniciais
            carregarProdutos();
            carregarCategorias();
            
            // Configurar eventos
            document.getElementById('searchInput').addEventListener('input', filtrarProdutos);
            document.getElementById('filterCategoria').addEventListener('change', filtrarProdutos);
            document.getElementById('filterStatus').addEventListener('change', filtrarProdutos);
            
            // Eventos para selects dependentes
            document.getElementById('id_categoria').addEventListener('change', function() {
                const categoriaId = this.value;
                if (categoriaId) {
                    carregarSecoes(categoriaId);
                } else {
                    document.getElementById('id_secao').innerHTML = '<option value="">Selecione uma seção</option>';
                    document.getElementById('id_grupo').innerHTML = '<option value="">Selecione um grupo</option>';
                    document.getElementById('id_subgrupo').innerHTML = '<option value="">Selecione um subgrupo</option>';
                }
            });
            
            document.getElementById('id_secao').addEventListener('change', function() {
                const secaoId = this.value;
                if (secaoId) {
                    carregarGrupos(secaoId);
                } else {
                    document.getElementById('id_grupo').innerHTML = '<option value="">Selecione um grupo</option>';
                    document.getElementById('id_subgrupo').innerHTML = '<option value="">Selecione um subgrupo</option>';
                }
            });
            
            document.getElementById('id_grupo').addEventListener('change', function() {
                const grupoId = this.value;
                if (grupoId) {
                    carregarSubgrupos(grupoId);
                } else {
                    document.getElementById('id_subgrupo').innerHTML = '<option value="">Selecione um subgrupo</option>';
                }
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

        // ========== PRODUTOS ==========
        async function carregarProdutos(page = 1) {
            mostrarLoading(true);
            
            try {
                const token = '<?php echo $token; ?>';
                const response = await fetch(API_CONFIG.getProdutosEmpresaUrl(idEmpresa, page), {
                    method: 'GET',
                    headers: API_CONFIG.getHeaders(token)
                });
                
                if (!response.ok) {
                    throw new Error(`Erro ${response.status}: ${response.statusText}`);
                }
                
                const data = await response.json();
                produtos = data.data;
                currentPage = data.current_page;
                lastPage = data.last_page;
                
                exibirProdutos(produtos);
                atualizarTotalProdutos(data.total);
                atualizarPaginacao(data);
                
            } catch (error) {
                console.error('Erro ao carregar produtos:', error);
                mostrarNotificacao('Erro ao carregar produtos: ' + error.message, 'error');
                document.getElementById('tbodyProdutos').innerHTML = '<tr><td colspan="9" class="text-center text-muted">Erro ao carregar dados</td></tr>';
            } finally {
                mostrarLoading(false);
            }
        }

        function exibirProdutos(listaProdutos) {
            const tbody = document.getElementById('tbodyProdutos');
            
            if (listaProdutos.length === 0) {
                tbody.innerHTML = '<tr><td colspan="9" class="text-center text-muted">Nenhum produto encontrado</td></tr>';
                return;
            }
            
            tbody.innerHTML = listaProdutos.map(produto => `
                <tr>
                    <td>${produto.id_produto}</td>
                    <td>
                        <div class="fw-semibold">${produto.descricao}</div>
                        ${produto.codigo_barras ? `<small class="text-muted">Cód: ${produto.codigo_barras}</small>` : ''}
                    </td>
                    <td>${produto.codigo_barras || '-'}</td>
                    <td><span class="price-badge">${produto.unidade_medida}</span></td>
                    <td class="text-nowrap">R$ ${formatarPreco(produto.preco_custo)}</td>
                    <td class="text-nowrap fw-semibold text-success">R$ ${formatarPreco(produto.preco_venda)}</td>
                    <td><span class="status-badge status-${produto.ativo ? 'ativo' : 'inativo'}">${produto.ativo ? 'Ativo' : 'Inativo'}</span></td>
                    <td>${formatarData(produto.created_at)}</td>
                    <td>
                        <div class="d-flex">
                            <button class="btn btn-action btn-outline-primary" onclick="editarProduto(${produto.id_produto})" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-action btn-outline-danger" onclick="confirmarExclusao(${produto.id_produto}, '${produto.descricao.replace(/'/g, "\\'")}')" title="Excluir">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `).join('');
        }

        function atualizarPaginacao(data) {
            const paginationContainer = document.getElementById('paginationContainer');
            const paginationUl = paginationContainer.querySelector('.pagination');
            
            if (data.last_page <= 1) {
                paginationContainer.style.display = 'none';
                return;
            }
            
            paginationContainer.style.display = 'block';
            
            let paginationHTML = '';
            
            // Botão anterior
            if (data.current_page > 1) {
                paginationHTML += `
                    <li class="page-item">
                        <a class="page-link" href="#" onclick="carregarProdutos(${data.current_page - 1})" aria-label="Anterior">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                `;
            }
            
            // Páginas
            for (let i = 1; i <= data.last_page; i++) {
                if (i === data.current_page) {
                    paginationHTML += `
                        <li class="page-item active">
                            <span class="page-link">${i}</span>
                        </li>
                    `;
                } else {
                    paginationHTML += `
                        <li class="page-item">
                            <a class="page-link" href="#" onclick="carregarProdutos(${i})">${i}</a>
                        </li>
                    `;
                }
            }
            
            // Botão próximo
            if (data.current_page < data.last_page) {
                paginationHTML += `
                    <li class="page-item">
                        <a class="page-link" href="#" onclick="carregarProdutos(${data.current_page + 1})" aria-label="Próximo">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                `;
            }
            
            paginationUl.innerHTML = paginationHTML;
        }

        function abrirModalProduto() {
            produtoEditando = null;
            document.getElementById('modalProdutoLabel').textContent = 'Novo Produto';
            document.getElementById('formProduto').reset();
            document.getElementById('produtoId').value = '';
            
            // Limpar selects dependentes
            document.getElementById('id_secao').innerHTML = '<option value="">Selecione uma seção</option>';
            document.getElementById('id_grupo').innerHTML = '<option value="">Selecione um grupo</option>';
            document.getElementById('id_subgrupo').innerHTML = '<option value="">Selecione um subgrupo</option>';
            
            // Limpar validação
            document.getElementById('formProduto').classList.remove('was-validated');
            
            modalProduto.show();
        }

        async function editarProduto(id) {
            mostrarLoading(true);
            
            try {
                const token = '<?php echo $token; ?>';
                const response = await fetch(API_CONFIG.getProdutoUrl(id), {
                    method: 'GET',
                    headers: API_CONFIG.getHeaders(token)
                });
                
                if (!response.ok) {
                    throw new Error(`Erro ${response.status}: ${response.statusText}`);
                }
                
                const produto = await response.json();
                produtoEditando = produto;
                
                // Preencher formulário
                document.getElementById('modalProdutoLabel').textContent = 'Editar Produto';
                document.getElementById('produtoId').value = produto.id_produto;
                document.getElementById('descricao').value = produto.descricao || '';
                document.getElementById('codigo_barras').value = produto.codigo_barras || '';
                document.getElementById('unidade_medida').value = produto.unidade_medida || '';
                document.getElementById('preco_custo').value = produto.preco_custo || '';
                document.getElementById('preco_venda').value = produto.preco_venda || '';
                document.getElementById('ativo').value = produto.ativo;
                
                // Carregar hierarquia
                await carregarCategoriaParaProduto(produto.id_categoria);
                await carregarSecaoParaProduto(produto.id_categoria, produto.id_secao);
                await carregarGrupoParaProduto(produto.id_secao, produto.id_grupo);
                await carregarSubgrupoParaProduto(produto.id_grupo, produto.id_subgrupo);
                
                // Limpar validação
                document.getElementById('formProduto').classList.remove('was-validated');
                
                modalProduto.show();
                
            } catch (error) {
                console.error('Erro ao carregar produto:', error);
                mostrarNotificacao('Erro ao carregar dados do produto: ' + error.message, 'error');
            } finally {
                mostrarLoading(false);
            }
        }

        async function salvarProduto() {
            const form = document.getElementById('formProduto');
            
            if (!form.checkValidity()) {
                form.classList.add('was-validated');
                return;
            }
            
            mostrarLoading(true);
            
            const dadosProduto = {
                id_empresa: idEmpresa,
                descricao: document.getElementById('descricao').value,
                codigo_barras: document.getElementById('codigo_barras').value,
                unidade_medida: document.getElementById('unidade_medida').value,
                preco_custo: parseFloat(document.getElementById('preco_custo').value),
                preco_venda: parseFloat(document.getElementById('preco_venda').value),
                id_categoria: parseInt(document.getElementById('id_categoria').value),
                id_secao: parseInt(document.getElementById('id_secao').value),
                id_grupo: parseInt(document.getElementById('id_grupo').value),
                id_subgrupo: parseInt(document.getElementById('id_subgrupo').value),
                ativo: parseInt(document.getElementById('ativo').value)
            };
            
            try {
                const token = '<?php echo $token; ?>';
                let response;
                
                if (produtoEditando) {
                    // Editar produto existente
                    response = await fetch(API_CONFIG.getProdutoUrl(produtoEditando.id_produto), {
                        method: 'PUT',
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Accept': 'application/json',
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: new URLSearchParams(dadosProduto)
                    });
                } else {
                    // Criar novo produto
                    response = await fetch(API_CONFIG.getProdutosUrl(), {
                        method: 'POST',
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Accept': 'application/json',
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: new URLSearchParams(dadosProduto)
                    });
                }
                
                if (!response.ok) {
                    const errorData = await response.json().catch(() => ({}));
                    throw new Error(errorData.message || `Erro ${response.status}: ${response.statusText}`);
                }
                
                modalProduto.hide();
                form.classList.remove('was-validated');
                
                mostrarNotificacao(
                    `Produto ${produtoEditando ? 'atualizado' : 'criado'} com sucesso!`, 
                    'success'
                );
                
                // Recarregar lista
                carregarProdutos(currentPage);
                
            } catch (error) {
                console.error('Erro ao salvar produto:', error);
                mostrarNotificacao('Erro ao salvar produto: ' + error.message, 'error');
            } finally {
                mostrarLoading(false);
            }
        }

        // ========== CATEGORIAS E HIERARQUIA ==========
        async function carregarCategorias() {
            try {
                const token = '<?php echo $token; ?>';
                const response = await fetch(API_CONFIG.getCategoriasEmpresaUrl(idEmpresa), {
                    method: 'GET',
                    headers: API_CONFIG.getHeaders(token)
                });
                
                if (response.ok) {
                    categorias = await response.json();
                    atualizarSelectCategorias();
                }
            } catch (error) {
                console.error('Erro ao carregar categorias:', error);
            }
        }

        async function carregarSecoes(idCategoria) {
            try {
                const token = '<?php echo $token; ?>';
                const response = await fetch(API_CONFIG.getSecoesEmpresaUrl(idEmpresa, idCategoria), {
                    method: 'GET',
                    headers: API_CONFIG.getHeaders(token)
                });
                
                if (response.ok) {
                    secoes = await response.json();
                    const select = document.getElementById('id_secao');
                    select.innerHTML = '<option value="">Selecione uma seção</option>';
                    
                    secoes.forEach(secao => {
                        select.innerHTML += `<option value="${secao.id_secao}">${secao.nome_secao}</option>`;
                    });
                    
                    // Limpar selects dependentes
                    document.getElementById('id_grupo').innerHTML = '<option value="">Selecione um grupo</option>';
                    document.getElementById('id_subgrupo').innerHTML = '<option value="">Selecione um subgrupo</option>';
                }
            } catch (error) {
                console.error('Erro ao carregar seções:', error);
            }
        }

        async function carregarGrupos(idSecao) {
            try {
                const token = '<?php echo $token; ?>';
                const response = await fetch(API_CONFIG.getGruposEmpresaUrl(idEmpresa, idSecao), {
                    method: 'GET',
                    headers: API_CONFIG.getHeaders(token)
                });
                
                if (response.ok) {
                    grupos = await response.json();
                    const select = document.getElementById('id_grupo');
                    select.innerHTML = '<option value="">Selecione um grupo</option>';
                    
                    grupos.forEach(grupo => {
                        select.innerHTML += `<option value="${grupo.id_grupo}">${grupo.nome_grupo}</option>`;
                    });
                    
                    // Limpar select dependente
                    document.getElementById('id_subgrupo').innerHTML = '<option value="">Selecione um subgrupo</option>';
                }
            } catch (error) {
                console.error('Erro ao carregar grupos:', error);
            }
        }

        async function carregarSubgrupos(idGrupo) {
            try {
                const token = '<?php echo $token; ?>';
                const response = await fetch(API_CONFIG.getSubgruposEmpresaUrl(idEmpresa, idGrupo), {
                    method: 'GET',
                    headers: API_CONFIG.getHeaders(token)
                });
                
                if (response.ok) {
                    subgrupos = await response.json();
                    const select = document.getElementById('id_subgrupo');
                    select.innerHTML = '<option value="">Selecione um subgrupo</option>';
                    
                    subgrupos.forEach(subgrupo => {
                        select.innerHTML += `<option value="${subgrupo.id_subgrupo}">${subgrupo.nome_subgrupo}</option>`;
                    });
                }
            } catch (error) {
                console.error('Erro ao carregar subgrupos:', error);
            }
        }

        // Funções auxiliares para edição
        async function carregarCategoriaParaProduto(idCategoria) {
            const select = document.getElementById('id_categoria');
            select.innerHTML = '<option value="">Selecione uma categoria</option>';
            
            categorias.forEach(categoria => {
                const selected = categoria.id_categoria === idCategoria ? 'selected' : '';
                select.innerHTML += `<option value="${categoria.id_categoria}" ${selected}>${categoria.nome_categoria}</option>`;
            });
        }

        async function carregarSecaoParaProduto(idCategoria, idSecao) {
            await carregarSecoes(idCategoria);
            const select = document.getElementById('id_secao');
            const option = select.querySelector(`option[value="${idSecao}"]`);
            if (option) {
                option.selected = true;
            }
        }

        async function carregarGrupoParaProduto(idSecao, idGrupo) {
            await carregarGrupos(idSecao);
            const select = document.getElementById('id_grupo');
            const option = select.querySelector(`option[value="${idGrupo}"]`);
            if (option) {
                option.selected = true;
            }
        }

        async function carregarSubgrupoParaProduto(idGrupo, idSubgrupo) {
            await carregarSubgrupos(idGrupo);
            const select = document.getElementById('id_subgrupo');
            const option = select.querySelector(`option[value="${idSubgrupo}"]`);
            if (option) {
                option.selected = true;
            }
        }

        function atualizarSelectCategorias() {
            // Atualizar select do modal
            const selectModal = document.getElementById('id_categoria');
            selectModal.innerHTML = '<option value="">Selecione uma categoria</option>';
            categorias.forEach(categoria => {
                selectModal.innerHTML += `<option value="${categoria.id_categoria}">${categoria.nome_categoria}</option>`;
            });
            
            // Atualizar select do filtro
            const selectFiltro = document.getElementById('filterCategoria');
            selectFiltro.innerHTML = '<option value="">Todas as categorias</option>';
            categorias.forEach(categoria => {
                selectFiltro.innerHTML += `<option value="${categoria.id_categoria}">${categoria.nome_categoria}</option>`;
            });
        }

        // ========== EXCLUSÃO ==========
        function confirmarExclusao(id, nome) {
            document.getElementById('nomeProdutoExcluir').textContent = nome;
            
            const btnConfirmar = document.getElementById('btnConfirmarExclusao');
            btnConfirmar.replaceWith(btnConfirmar.cloneNode(true));
            document.getElementById('btnConfirmarExclusao').onclick = () => excluirProduto(id);
            
            modalConfirmacao.show();
        }

        async function excluirProduto(id) {
            mostrarLoading(true);
            
            try {
                const token = '<?php echo $token; ?>';
                const response = await fetch(API_CONFIG.getProdutoUrl(id), {
                    method: 'DELETE',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    }
                });
                
                if (!response.ok && response.status !== 204) {
                    throw new Error(`Erro ${response.status}: ${response.statusText}`);
                }
                
                modalConfirmacao.hide();
                mostrarNotificacao('Produto excluído com sucesso!', 'success');
                
                // Recarregar lista
                carregarProdutos(currentPage);
                
            } catch (error) {
                console.error('Erro ao excluir produto:', error);
                mostrarNotificacao('Erro ao excluir produto: ' + error.message, 'error');
                modalConfirmacao.hide();
            } finally {
                mostrarLoading(false);
            }
        }

        // ========== FILTROS ==========
        function filtrarProdutos() {
            const termoBusca = document.getElementById('searchInput').value.toLowerCase();
            const categoriaFiltro = document.getElementById('filterCategoria').value;
            const statusFiltro = document.getElementById('filterStatus').value;
            
            // Se não há filtros ativos, mostrar todos os produtos
            if (!termoBusca && !categoriaFiltro && !statusFiltro) {
                exibirProdutos(produtos);
                atualizarTotalProdutos(produtos.length);
                return;
            }
            
            const produtosFiltrados = produtos.filter(produto => {
                const matchBusca = !termoBusca || 
                    produto.descricao.toLowerCase().includes(termoBusca) ||
                    (produto.codigo_barras && produto.codigo_barras.toLowerCase().includes(termoBusca));
                
                const matchCategoria = !categoriaFiltro || produto.id_categoria == categoriaFiltro;
                const matchStatus = !statusFiltro || 
                    (statusFiltro === 'ativo' && produto.ativo) ||
                    (statusFiltro === 'inativo' && !produto.ativo);
                
                return matchBusca && matchCategoria && matchStatus;
            });
            
            exibirProdutos(produtosFiltrados);
            atualizarTotalProdutos(produtosFiltrados.length);
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

        function atualizarTotalProdutos(total) {
            document.getElementById('totalProdutos').textContent = `${total} produto(s) encontrado(s)`;
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