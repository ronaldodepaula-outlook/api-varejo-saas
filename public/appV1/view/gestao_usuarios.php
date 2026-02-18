<?php
session_start();
if (!isset($_SESSION['authToken'], $_SESSION['usuario'], $_SESSION['empresa'], $_SESSION['licenca'])) {
    header('Location: login.php');
    exit;
}

// Verificar se o ID da empresa foi passado via GET
if (!isset($_GET['id_empresa']) || empty($_GET['id_empresa'])) {
    header('Location: empresas.php');
    exit;
}

$id_empresa = (int)$_GET['id_empresa'];

// Verificar e converter o tipo de dados das variáveis de sessão
$usuario = is_array($_SESSION['usuario']) ? $_SESSION['usuario'] : ['nome' => $_SESSION['usuario']];
$empresa = $_SESSION['empresa'];
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
    <title>Gestão de Usuários - SAS Multi</title>
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
        
        .status-badge {
            padding: 4px 8px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .status-ativo {
            background: rgba(39, 174, 96, 0.1);
            color: var(--success-color);
        }
        
        .status-inativo {
            background: rgba(231, 76, 60, 0.1);
            color: var(--danger-color);
        }
        
        .perfil-badge {
            padding: 4px 8px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            background: rgba(52, 152, 219, 0.1);
            color: var(--primary-color);
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
        
        .form-check-input:checked {
            background-color: var(--success-color);
            border-color: var(--success-color);
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
                        <li class="breadcrumb-item"><a href="?view=home">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="?view=admin-empresas">Empresas</a></li>
                        <li class="breadcrumb-item active">Gestão de Usuários</li>
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
                        <li><a class="dropdown-item" href="#">Novo usuário cadastrado</a></li>
                        <li><a class="dropdown-item" href="#">Usuário aguardando ativação</a></li>
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
                    <h1 class="page-title">Gestão de Usuários</h1>
                    <p class="page-subtitle">Cadastre e gerencie os usuários da empresa</p>
                    <div class="mt-2">
                        <span class="badge bg-primary" id="nomeEmpresa">Carregando...</span>
                    </div>
                </div>
                <div>
                    <button class="btn btn-primary" onclick="abrirModalUsuario()">
                        <i class="bi bi-person-plus me-2"></i>Novo Usuário
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
                                <input type="text" class="form-control" id="searchInput" placeholder="Buscar por nome ou email...">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="filterPerfil">
                                <option value="">Todos os perfis</option>
                                <option value="admin">Administrador</option>
                                <option value="user">Usuário</option>
                                <option value="manager">Gestor</option>
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
            
            <!-- Tabela de Usuários -->
            <div class="card-custom">
                <div class="card-header-custom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Lista de Usuários</h5>
                    <div class="d-flex align-items-center">
                        <span class="text-muted me-3" id="totalUsuarios">Carregando...</span>
                        <button class="btn btn-sm btn-outline-primary" onclick="carregarUsuarios()">
                            <i class="bi bi-arrow-clockwise"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="tabelaUsuarios">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nome</th>
                                    <th>Email</th>
                                    <th>Perfil</th>
                                    <th>Status</th>
                                    <th>Termos</th>
                                    <th>Newsletter</th>
                                    <th>Data Cadastro</th>
                                    <th width="120">Ações</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyUsuarios">
                                <tr>
                                    <td colspan="9" class="text-center text-muted">Carregando usuários...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal para Adicionar/Editar Usuário -->
    <div class="modal fade" id="modalUsuario" tabindex="-1" aria-labelledby="modalUsuarioLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalUsuarioLabel">Novo Usuário</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formUsuario">
                        <input type="hidden" id="usuarioId">
                        <input type="hidden" id="id_empresa" value="<?php echo $id_empresa; ?>">
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="nome" class="form-label">Nome Completo *</label>
                                <input type="text" class="form-control" id="nome" required>
                                <div class="invalid-feedback">Por favor, informe o nome completo.</div>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="email" required>
                                <div class="invalid-feedback">Por favor, informe um email válido.</div>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="senha" class="form-label" id="labelSenha">Senha *</label>
                                <input type="password" class="form-control" id="senha" minlength="6">
                                <div class="invalid-feedback">A senha deve ter pelo menos 6 caracteres.</div>
                                <small class="form-text text-muted" id="textoAjudaSenha">Mínimo 6 caracteres</small>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="perfil" class="form-label">Perfil *</label>
                                <select class="form-select" id="perfil" required>
                                    <option value="">Selecione o perfil</option>
                                    <option value="admin">Administrador</option>
                                    <option value="user">Usuário</option>
                                    <option value="manager">Gestor</option>
                                </select>
                                <div class="invalid-feedback">Por favor, selecione o perfil.</div>
                            </div>
                            
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-check form-switch mt-4">
                                            <input class="form-check-input" type="checkbox" id="ativo" checked>
                                            <label class="form-check-label" for="ativo">Usuário Ativo</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check form-switch mt-4">
                                            <input class="form-check-input" type="checkbox" id="aceitou_termos">
                                            <label class="form-check-label" for="aceitou_termos">Aceitou Termos</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check form-switch mt-4">
                                            <input class="form-check-input" type="checkbox" id="newsletter">
                                            <label class="form-check-label" for="newsletter">Newsletter</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="salvarUsuario()">Salvar Usuário</button>
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
                    <p>Tem certeza que deseja excluir o usuário <strong id="nomeUsuarioExcluir"></strong>?</p>
                    <p class="text-danger"><small>Esta ação não pode ser desfeita.</small></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="btnConfirmarExclusao">Excluir Usuário</button>
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
        // CONFIGURAÇÃO DA API
        const API_CONFIG = {
            BASE_URL: 'https://rdpsolutions.online/saas-multiempresas/public',
            
            // Endpoints
            USUARIOS: '/api/usuarios',
            USUARIOS_EMPRESA: '/api/usuarios/empresa',
            EMPRESAS: '/api/v1/empresas',
            LOGOUT: '/api/v1/logout',
            
            // Headers padrão (para GET/HEAD) - não inclui Content-Type para evitar preflights desnecessários
            getHeaders: function(token) {
                return {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                };
            },

            // Headers para requisições com corpo JSON (POST/PUT/PATCH)
            getJsonHeaders: function(token) {
                return {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                };
            },
            
            // URL completa para usuários
            getUsuariosUrl: function() {
                return `${this.BASE_URL}${this.USUARIOS}`;
            },
            
            // URL completa para usuários de uma empresa
            getUsuariosEmpresaUrl: function(idEmpresa) {
                return `${this.BASE_URL}${this.USUARIOS_EMPRESA}/${idEmpresa}`;
            },
            
            // URL completa para um usuário específico
            getUsuarioUrl: function(id) {
                return `${this.BASE_URL}${this.USUARIOS}/${id}`;
            },
            
            // URL completa para empresas
            getEmpresasUrl: function() {
                return `${this.BASE_URL}${this.EMPRESAS}`;
            }
        };

        // Variáveis globais
        let usuarios = [];
        let empresaAtual = null;
        let modalUsuario = null;
        let modalConfirmacao = null;
        const idEmpresa = <?php echo $id_empresa; ?>;

        // Inicialização
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Iniciando gestão de usuários...');
            
            // Inicializar modais
            modalUsuario = new bootstrap.Modal(document.getElementById('modalUsuario'));
            modalConfirmacao = new bootstrap.Modal(document.getElementById('modalConfirmacao'));
            
            // Carregar dados iniciais
            carregarEmpresa().then(() => {
                console.log('Empresa carregada, agora carregando usuários...');
                carregarUsuarios();
            }).catch(error => {
                console.error('Erro ao carregar dados iniciais:', error);
                mostrarNotificacao('Erro ao carregar dados iniciais: ' + error.message, 'error');
            });
            
            // Configurar eventos
            document.getElementById('searchInput').addEventListener('input', filtrarUsuarios);
            document.getElementById('filterPerfil').addEventListener('change', filtrarUsuarios);
            document.getElementById('filterStatus').addEventListener('change', filtrarUsuarios);
            
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

        // Função para carregar dados da empresa
        async function carregarEmpresa() {
            try {
                console.log('Carregando dados da empresa...');
                const token = '<?php echo $token; ?>';
                
                const response = await fetch(API_CONFIG.getEmpresasUrl(), {
                    method: 'GET',
                    headers: API_CONFIG.getHeaders(token)
                });
                
                if (!response.ok) {
                    throw new Error(`Erro ${response.status}: ${response.statusText}`);
                }
                
                const data = await response.json();
                const empresas = data.data || data;
                
                // Encontrar a empresa atual
                empresaAtual = empresas.find(empresa => empresa.id_empresa == idEmpresa);
                
                if (empresaAtual) {
                    document.getElementById('nomeEmpresa').textContent = empresaAtual.nome_empresa;
                } else {
                    document.getElementById('nomeEmpresa').textContent = 'Empresa não encontrada';
                    mostrarNotificacao('Empresa não encontrada!', 'error');
                }
                
            } catch (error) {
                console.error('Erro ao carregar empresa:', error);
                document.getElementById('nomeEmpresa').textContent = 'Erro ao carregar';
                throw error;
            }
        }

        // Função para carregar usuários da empresa
        async function carregarUsuarios() {
            mostrarLoading(true);
            
            try {
                console.log('Carregando usuários da empresa:', idEmpresa);
                const token = '<?php echo $token; ?>';
                const url = API_CONFIG.getUsuariosEmpresaUrl(idEmpresa);
                
                const response = await fetch(url, {
                    method: 'GET',
                    headers: API_CONFIG.getHeaders(token)
                });
                
                console.log('Resposta usuários - Status:', response.status);
                
                if (!response.ok) {
                    throw new Error(`Erro ${response.status}: ${response.statusText}`);
                }
                
                const data = await response.json();
                usuarios = data.data || data;
                console.log('Usuários carregados:', usuarios);
                
                exibirUsuarios(usuarios);
                atualizarTotalUsuarios(usuarios.length);
                
            } catch (error) {
                console.error('Erro ao carregar usuários:', error);
                mostrarNotificacao('Erro ao carregar usuários: ' + error.message, 'error');
                document.getElementById('tbodyUsuarios').innerHTML = '<tr><td colspan="9" class="text-center text-muted">Erro ao carregar dados</td></tr>';
            } finally {
                mostrarLoading(false);
            }
        }

        // Função para exibir usuários na tabela - CORRIGIDA
        function exibirUsuarios(listaUsuarios) {
            const tbody = document.getElementById('tbodyUsuarios');
            
            if (listaUsuarios.length === 0) {
                tbody.innerHTML = '<tr><td colspan="9" class="text-center text-muted">Nenhum usuário encontrado</td></tr>';
                return;
            }
            
            tbody.innerHTML = listaUsuarios.map(usuario => {
                // CORREÇÃO: Usar id_usuario ou id conforme retornado pela API
                const usuarioId = usuario.id_usuario || usuario.id;
                
                return `
                <tr>
                    <td>${usuarioId}</td>
                    <td>
                        <div class="fw-semibold">${usuario.nome}</div>
                    </td>
                    <td>${usuario.email}</td>
                    <td>
                        <span class="perfil-badge">${formatarPerfil(usuario.perfil)}</span>
                    </td>
                    <td>
                        <span class="status-badge ${usuario.ativo ? 'status-ativo' : 'status-inativo'}">
                            ${usuario.ativo ? 'Ativo' : 'Inativo'}
                        </span>
                    </td>
                    <td>
                        <i class="bi ${usuario.aceitou_termos ? 'bi-check-circle text-success' : 'bi-x-circle text-danger'}"></i>
                    </td>
                    <td>
                        <i class="bi ${usuario.newsletter ? 'bi-check-circle text-success' : 'bi-x-circle text-danger'}"></i>
                    </td>
                    <td>${formatarData(usuario.created_at)}</td>
                    <td>
                        <div class="d-flex">
                            <button class="btn btn-action btn-outline-primary" onclick="editarUsuario(${usuarioId})" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-action btn-outline-danger" onclick="confirmarExclusao(${usuarioId}, '${usuario.nome.replace(/'/g, "\\'")}')" title="Excluir">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                `;
            }).join('');
        }

        // Função para abrir modal de novo usuário
        function abrirModalUsuario() {
            document.getElementById('modalUsuarioLabel').textContent = 'Novo Usuário';
            document.getElementById('formUsuario').reset();
            document.getElementById('usuarioId').value = '';
            document.getElementById('senha').required = true;
            document.getElementById('labelSenha').innerHTML = 'Senha *';
            document.getElementById('textoAjudaSenha').style.display = 'block';
            
            // Limpar validação
            document.getElementById('formUsuario').classList.remove('was-validated');
            
            modalUsuario.show();
        }

        // Função para editar usuário - CORRIGIDA
        async function editarUsuario(id) {
            if (!id || id === 'undefined') {
                console.error('ID do usuário inválido:', id);
                mostrarNotificacao('Erro: ID do usuário inválido', 'error');
                return;
            }

            mostrarLoading(true);
            
            try {
                console.log('Editando usuário ID:', id);
                const token = '<?php echo $token; ?>';
                const url = API_CONFIG.getUsuarioUrl(id);
                
                const response = await fetch(url, {
                    method: 'GET',
                    headers: API_CONFIG.getHeaders(token)
                });
                
                console.log('Resposta edição - Status:', response.status);
                
                if (!response.ok) {
                    throw new Error(`Erro ${response.status}: ${response.statusText}`);
                }
                
                const data = await response.json();
                const usuario = data.data || data;
                console.log('Dados do usuário para edição:', usuario);
                
                // CORREÇÃO: Usar id_usuario ou id conforme retornado pela API
                const usuarioId = usuario.id_usuario || usuario.id;
                
                // Preencher formulário
                document.getElementById('modalUsuarioLabel').textContent = 'Editar Usuário';
                document.getElementById('usuarioId').value = usuarioId;
                document.getElementById('nome').value = usuario.nome || '';
                document.getElementById('email').value = usuario.email || '';
                document.getElementById('perfil').value = usuario.perfil || '';
                document.getElementById('ativo').checked = usuario.ativo || false;
                document.getElementById('aceitou_termos').checked = usuario.aceitou_termos || false;
                document.getElementById('newsletter').checked = usuario.newsletter || false;
                
                // Para edição, senha não é obrigatória
                document.getElementById('senha').required = false;
                document.getElementById('labelSenha').innerHTML = 'Senha <small class="text-muted">(opcional)</small>';
                document.getElementById('textoAjudaSenha').style.display = 'none';
                document.getElementById('senha').value = '';
                
                // Limpar validação
                document.getElementById('formUsuario').classList.remove('was-validated');
                
                modalUsuario.show();
                
            } catch (error) {
                console.error('Erro ao carregar usuário:', error);
                mostrarNotificacao('Erro ao carregar dados do usuário: ' + error.message, 'error');
            } finally {
                mostrarLoading(false);
            }
        }

        // Função para salvar usuário (criar ou editar) - CORRIGIDA
        async function salvarUsuario() {
            const form = document.getElementById('formUsuario');
            
            // Validação dos campos obrigatórios
            const nome = document.getElementById('nome').value.trim();
            const email = document.getElementById('email').value.trim();
            const perfil = document.getElementById('perfil').value;
            const senha = document.getElementById('senha').value;
            const usuarioId = document.getElementById('usuarioId').value;
            
            // Validações básicas
            if (!nome || !email || !perfil) {
                form.classList.add('was-validated');
                mostrarNotificacao('Por favor, preencha todos os campos obrigatórios.', 'error');
                return;
            }
            
            // Para novo usuário, senha é obrigatória e deve ter pelo menos 6 caracteres
            if (!usuarioId) {
                if (!senha) {
                    document.getElementById('senha').focus();
                    mostrarNotificacao('Para novo usuário, a senha é obrigatória.', 'error');
                    return;
                }
                if (senha.length < 6) {
                    document.getElementById('senha').focus();
                    mostrarNotificacao('A senha deve ter pelo menos 6 caracteres.', 'error');
                    return;
                }
            }
            
            // Para edição, se senha foi informada, deve ter pelo menos 6 caracteres
            if (usuarioId && senha && senha.length < 6) {
                document.getElementById('senha').focus();
                mostrarNotificacao('A senha deve ter pelo menos 6 caracteres.', 'error');
                return;
            }
            
            mostrarLoading(true);
            
            // Preparar dados - CORREÇÃO: Usar valores curtos para perfil
            const dadosUsuario = {
                id_empresa: idEmpresa,
                nome: nome,
                email: email,
                perfil: perfil, // Agora usando valores curtos: admin, user, manager
                ativo: document.getElementById('ativo').checked ? 1 : 0,
                aceitou_termos: document.getElementById('aceitou_termos').checked ? 1 : 0,
                newsletter: document.getElementById('newsletter').checked ? 1 : 0
            };
            
            // CORREÇÃO: Incluir senha apenas se foi informada (e tem pelo menos 6 caracteres)
            // E apenas para novo usuário ou se foi alterada
            if ((!usuarioId || senha) && senha.length >= 6) {
                dadosUsuario.senha = senha;
            }
            
            console.log('Dados a serem enviados:', dadosUsuario);
            
            try {
                const token = '<?php echo $token; ?>';
                let response;
                
                if (usuarioId) {
                    // Editar usuário existente
                    response = await fetch(API_CONFIG.getUsuarioUrl(usuarioId), {
                        method: 'PUT',
                        headers: API_CONFIG.getHeaders(token),
                        body: JSON.stringify(dadosUsuario)
                    });
                } else {
                    // Criar novo usuário
                    response = await fetch(API_CONFIG.getUsuariosUrl(), {
                        method: 'POST',
                        headers: API_CONFIG.getHeaders(token),
                        body: JSON.stringify(dadosUsuario)
                    });
                }
                
                console.log('Status da resposta:', response.status);
                
                if (!response.ok) {
                    let errorMessage = `Erro ${response.status}: ${response.statusText}`;
                    
                    try {
                        const errorData = await response.json();
                        console.log('Resposta de erro:', errorData);
                        
                        if (errorData.errors) {
                            const allErrors = [];
                            for (const [field, errors] of Object.entries(errorData.errors)) {
                                allErrors.push(`${field}: ${errors.join(', ')}`);
                            }
                            errorMessage = allErrors.join('; ');
                        } else if (errorData.message) {
                            errorMessage = errorData.message;
                        }
                    } catch (parseError) {
                        const errorText = await response.text();
                        errorMessage = `Erro ${response.status}: ${errorText || response.statusText}`;
                    }
                    
                    // CORREÇÃO: Tratamento específico para erro de email duplicado
                    if (errorMessage.includes('email') && errorMessage.includes('already been taken')) {
                        errorMessage = 'Este email já está em uso. Por favor, utilize outro email.';
                    }
                    
                    // CORREÇÃO: Tratamento específico para erro de truncamento de dados
                    if (errorMessage.includes('Data truncated') || errorMessage.includes('perfil')) {
                        errorMessage = 'Erro no campo perfil. Verifique se o valor é válido.';
                    }
                    
                    throw new Error(errorMessage);
                }
                
                const resultado = await response.json();
                console.log('Usuário salvo com sucesso:', resultado);
                
                modalUsuario.hide();
                form.classList.remove('was-validated');
                
                mostrarNotificacao(
                    `Usuário "${nome}" ${usuarioId ? 'atualizado' : 'criado'} com sucesso!`, 
                    'success'
                );
                
                // Recarregar lista
                carregarUsuarios();
                
            } catch (error) {
                console.error('Erro ao salvar usuário:', error);
                mostrarNotificacao('Erro ao salvar usuário: ' + error.message, 'error');
            } finally {
                mostrarLoading(false);
            }
        }

        // Função para confirmar exclusão
        function confirmarExclusao(id, nome) {
            if (!id || id === 'undefined') {
                console.error('ID do usuário inválido para exclusão:', id);
                mostrarNotificacao('Erro: ID do usuário inválido', 'error');
                return;
            }

            document.getElementById('nomeUsuarioExcluir').textContent = nome;
            
            // Configurar o botão de confirmação
            const btnConfirmar = document.getElementById('btnConfirmarExclusao');
            btnConfirmar.onclick = function() {
                excluirUsuario(id);
            };
            
            modalConfirmacao.show();
        }

        // Função para excluir usuário - CORRIGIDA
        async function excluirUsuario(id) {
            if (!id || id === 'undefined') {
                console.error('ID do usuário inválido para exclusão:', id);
                mostrarNotificacao('Erro: ID do usuário inválido', 'error');
                return;
            }

            mostrarLoading(true);
            
            try {
                const token = '<?php echo $token; ?>';
                const response = await fetch(API_CONFIG.getUsuarioUrl(id), {
                    method: 'DELETE',
                    headers: API_CONFIG.getHeaders(token)
                });
                
                console.log('Status da exclusão:', response.status);
                
                if (!response.ok) {
                    let errorMessage = `Erro ${response.status}: ${response.statusText}`;
                    
                    try {
                        const errorData = await response.json();
                        if (errorData.message) {
                            errorMessage = errorData.message;
                        }
                    } catch (parseError) {
                        // Ignora erro de parse
                    }
                    
                    throw new Error(errorMessage);
                }
                
                modalConfirmacao.hide();
                mostrarNotificacao('Usuário excluído com sucesso!', 'success');
                
                // Recarregar lista
                carregarUsuarios();
                
            } catch (error) {
                console.error('Erro ao excluir usuário:', error);
                mostrarNotificacao('Erro ao excluir usuário: ' + error.message, 'error');
                modalConfirmacao.hide();
            } finally {
                mostrarLoading(false);
            }
        }

        // Função para filtrar usuários
        function filtrarUsuarios() {
            const termoBusca = document.getElementById('searchInput').value.toLowerCase();
            const perfilFiltro = document.getElementById('filterPerfil').value;
            const statusFiltro = document.getElementById('filterStatus').value;
            
            const usuariosFiltrados = usuarios.filter(usuario => {
                const matchBusca = !termoBusca || 
                    usuario.nome.toLowerCase().includes(termoBusca) ||
                    usuario.email.toLowerCase().includes(termoBusca);
                
                const matchPerfil = !perfilFiltro || usuario.perfil === perfilFiltro;
                const matchStatus = !statusFiltro || 
                    (statusFiltro === 'ativo' && usuario.ativo) ||
                    (statusFiltro === 'inativo' && !usuario.ativo);
                
                return matchBusca && matchPerfil && matchStatus;
            });
            
            exibirUsuarios(usuariosFiltrados);
            atualizarTotalUsuarios(usuariosFiltrados.length);
        }

        // Funções auxiliares - CORRIGIDA
        function formatarPerfil(perfil) {
            const perfis = {
                'admin': 'Administrador',
                'user': 'Usuário',
                'manager': 'Gestor',
                'usuario': 'Usuário', // Para compatibilidade com dados antigos
                'gestor': 'Gestor' // Para compatibilidade com dados antigos
            };
            return perfis[perfil] || perfil;
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

        function atualizarTotalUsuarios(total) {
            document.getElementById('totalUsuarios').textContent = `${total} usuário(s) encontrado(s)`;
        }

        function mostrarLoading(mostrar) {
            document.getElementById('loadingOverlay').classList.toggle('d-none', !mostrar);
        }

        // Função para mostrar notificações
        function mostrarNotificacao(message, type) {
            // Criar elemento de notificação
            const notification = document.createElement('div');
            notification.className = `alert alert-${type === 'error' ? 'danger' : 'success'} alert-dismissible fade show position-fixed`;
            notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            notification.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            document.body.appendChild(notification);
            
            // Remover automaticamente após 5 segundos
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 5000);
        }

        // Função para debug - ver estrutura dos dados
        window.debugUsuarios = function() {
            console.log('=== DEBUG USUÁRIOS ===');
            console.log('Usuários:', usuarios);
            if (usuarios.length > 0) {
                console.log('Primeiro usuário:', usuarios[0]);
                console.log('Keys do primeiro usuário:', Object.keys(usuarios[0]));
            }
            console.log('=== FIM DEBUG ===');
        };
    </script>
</body>
</html>