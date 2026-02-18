<?php
session_start();
if (!isset($_SESSION['authToken'], $_SESSION['usuario'], $_SESSION['empresa'], $_SESSION['licenca'])) {
    header('Location: login.php');
    exit;
}

// Verificar e converter o tipo de dados das variáveis de sessão
$usuario = is_array($_SESSION['usuario']) ? $_SESSION['usuario'] : ['nome' => $_SESSION['usuario']];
$_SESSION['user_id'] = $_SESSION['user_id'] ?? null;
$empresa = $_SESSION['empresa'];
$id_empresa = $_SESSION['id_empresa'];
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
    <title>Gestão de Empresas - SAS Multi</title>
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
        
        .status-ativa { background: rgba(39, 174, 96, 0.1); color: var(--success-color); }
        .status-pendente { background: rgba(243, 156, 18, 0.1); color: var(--warning-color); }
        .status-inativa { background: rgba(231, 76, 60, 0.1); color: var(--danger-color); }
        
        .segmento-badge {
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
                        <li class="breadcrumb-item active">Gestão de Empresas</li>
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
                    <h1 class="page-title">Gestão de Empresas</h1>
                    <p class="page-subtitle">Cadastre e gerencie as empresas do sistema</p>
                </div>
                <div>
                    <button class="btn btn-primary" onclick="abrirModalEmpresa()">
                        <i class="bi bi-plus-circle me-2"></i>Nova Empresa
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
                                <input type="text" class="form-control" id="searchInput" placeholder="Buscar por nome da empresa ou CNPJ...">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="filterSegmento">
                                <option value="">Todos os segmentos</option>
                                <option value="varejo">Varejo</option>
                                <option value="industria">Indústria</option>
                                <option value="construcao">Construção</option>
                                <option value="financeiro">Financeiro</option>
                                <option value="seguranca">Segurança</option>
                                <option value="outros">Outros</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="filterStatus">
                                <option value="">Todos os status</option>
                                <option value="ativa">Ativa</option>
                                <option value="pendente">Pendente</option>
                                <option value="inativa">Inativa</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tabela de Empresas -->
            <div class="card-custom">
                <div class="card-header-custom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Lista de Empresas</h5>
                    <div class="d-flex align-items-center">
                        <span class="text-muted me-3" id="totalEmpresas">Carregando...</span>
                        <button class="btn btn-sm btn-outline-primary" onclick="carregarEmpresas()">
                            <i class="bi bi-arrow-clockwise"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="tabelaEmpresas">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nome da Empresa</th>
                                    <th>CNPJ</th>
                                    <th>Segmento</th>
                                    <th>Status</th>
                                    <th>Data Cadastro</th>
                                    <th width="120">Ações</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyEmpresas">
                                <tr>
                                    <td colspan="7" class="text-center text-muted">Carregando empresas...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal para Adicionar/Editar Empresa -->
    <div class="modal fade" id="modalEmpresa" tabindex="-1" aria-labelledby="modalEmpresaLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEmpresaLabel">Nova Empresa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formEmpresa">
                        <input type="hidden" id="empresaId">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="nome_empresa" class="form-label">Nome da Empresa *</label>
                                <input type="text" class="form-control" id="nome_empresa" required>
                                <div class="invalid-feedback">Por favor, informe o nome da empresa.</div>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="cnpj" class="form-label">CNPJ *</label>
                                <input type="text" class="form-control" id="cnpj" required>
                                <div class="invalid-feedback">Por favor, informe um CNPJ válido.</div>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="segmento" class="form-label">Segmento *</label>
                                <select class="form-select" id="segmento" required>
                                    <option value="">Selecione um segmento</option>
                                    <option value="varejo">Varejo</option>
                                    <option value="ecommerce">E-commerce</option>
                                    <option value="alimentacao">Alimentação</option>
                                    <option value="turismo_hotelaria">Turismo e Hotelaria</option>
                                    <option value="imobiliario">Imobiliário</option>
                                    <option value="esportes_lazer">Esportes e Lazer</option>
                                    <option value="midia_entretenimento">Mídia e Entretenimento</option>
                                    <option value="industria">Indústria</option>
                                    <option value="construcao">Construção</option>
                                    <option value="agropecuaria">Agropecuária</option>
                                    <option value="energia_utilities">Energia e Utilities</option>
                                    <option value="logistica_transporte">Logística e Transporte</option>
                                    <option value="financeiro">Financeiro</option>
                                    <option value="contabilidade_auditoria">Contabilidade e Auditoria</option>
                                    <option value="seguros">Seguros</option>
                                    <option value="marketing">Marketing</option>
                                    <option value="saude">Saúde</option>
                                    <option value="educacao">Educação</option>
                                    <option value="ciencia_pesquisa">Ciência e Pesquisa</option>
                                    <option value="rh_recrutamento">RH e Recrutamento</option>
                                    <option value="juridico">Jurídico</option>
                                    <option value="ongs_terceiro_setor">ONGs e Terceiro Setor</option>
                                    <option value="seguranca">Segurança</option>
                                    <option value="outros">Outros</option>
                                </select>
                                <div class="invalid-feedback">Por favor, selecione um segmento.</div>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="email_empresa" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email_empresa">
                            </div>
                            
                            <div class="col-md-6">
                                <label for="telefone" class="form-label">Telefone</label>
                                <input type="text" class="form-control" id="telefone">
                            </div>
                            
                            <div class="col-md-12">
                                <label for="website" class="form-label">Website</label>
                                <input type="url" class="form-control" id="website">
                            </div>
                            
                            <div class="col-md-8">
                                <label for="endereco" class="form-label">Endereço</label>
                                <input type="text" class="form-control" id="endereco">
                            </div>
                            
                            <div class="col-md-4">
                                <label for="cep" class="form-label">CEP</label>
                                <input type="text" class="form-control" id="cep">
                            </div>
                            
                            <div class="col-md-6">
                                <label for="cidade" class="form-label">Cidade</label>
                                <input type="text" class="form-control" id="cidade">
                            </div>
                            
                            <div class="col-md-6">
                                <label for="estado" class="form-label">Estado</label>
                                <select class="form-select" id="estado">
                                    <option value="">Selecione</option>
                                    <option value="AC">Acre</option>
                                    <option value="AL">Alagoas</option>
                                    <option value="AP">Amapá</option>
                                    <option value="AM">Amazonas</option>
                                    <option value="BA">Bahia</option>
                                    <option value="CE">Ceará</option>
                                    <option value="DF">Distrito Federal</option>
                                    <option value="ES">Espírito Santo</option>
                                    <option value="GO">Goiás</option>
                                    <option value="MA">Maranhão</option>
                                    <option value="MT">Mato Grosso</option>
                                    <option value="MS">Mato Grosso do Sul</option>
                                    <option value="MG">Minas Gerais</option>
                                    <option value="PA">Pará</option>
                                    <option value="PB">Paraíba</option>
                                    <option value="PR">Paraná</option>
                                    <option value="PE">Pernambuco</option>
                                    <option value="PI">Piauí</option>
                                    <option value="RJ">Rio de Janeiro</option>
                                    <option value="RN">Rio Grande do Norte</option>
                                    <option value="RS">Rio Grande do Sul</option>
                                    <option value="RO">Rondônia</option>
                                    <option value="RR">Roraima</option>
                                    <option value="SC">Santa Catarina</option>
                                    <option value="SP">São Paulo</option>
                                    <option value="SE">Sergipe</option>
                                    <option value="TO">Tocantins</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="status" class="form-label">Status *</label>
                                <select class="form-select" id="status" required>
                                    <option value="ativa">Ativa</option>
                                    <option value="pendente">Pendente</option>
                                    <option value="inativa">Inativa</option>
                                </select>
                                <div class="invalid-feedback">Por favor, selecione um status.</div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="salvarEmpresa()">Salvar Empresa</button>
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
                    <p>Tem certeza que deseja excluir a empresa <strong id="nomeEmpresaExcluir"></strong>?</p>
                    <p class="text-danger"><small>Esta ação não pode ser desfeita.</small></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="btnConfirmarExclusao">Excluir Empresa</button>
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
            EMPRESAS: '/api/v1/empresas',
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
            
            // URL completa para empresas
            getEmpresasUrl: function() {
                return `${this.BASE_URL}${this.EMPRESAS}`;
            },
            
            // URL completa para uma empresa específica
            getEmpresaUrl: function(id) {
                return `${this.BASE_URL}${this.EMPRESAS}/${id}`;
            }
        };
    </script>
    
    <script>
        // Variáveis globais
        let empresas = [];
        let empresaEditando = null;
        let modalEmpresa = null;
        let modalConfirmacao = null;

        // Inicialização
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar modais
            modalEmpresa = new bootstrap.Modal(document.getElementById('modalEmpresa'));
            modalConfirmacao = new bootstrap.Modal(document.getElementById('modalConfirmacao'));
            
            // Carregar empresas
            carregarEmpresas();
            
            // Configurar eventos
            document.getElementById('searchInput').addEventListener('input', filtrarEmpresas);
            document.getElementById('filterSegmento').addEventListener('change', filtrarEmpresas);
            document.getElementById('filterStatus').addEventListener('change', filtrarEmpresas);
            
            // Logoff
            var logoutBtn = document.getElementById('logoutBtn');
            if (logoutBtn) {
                logoutBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    // Usando a função de logout da API
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
                
                // Redirecionar para login independente da resposta
                window.location.href = 'login.php';
                
            } catch (error) {
                console.error('Erro no logout:', error);
                // Mesmo com erro, redireciona para login
                window.location.href = 'login.php';
            }
        }

        // Função para carregar empresas da API
        async function carregarEmpresas() {
            mostrarLoading(true);
            
            try {
                const token = '<?php echo $token; ?>';
                console.log('Token sendo usado:', token); // Para debug
                
                const response = await fetch(API_CONFIG.getEmpresasUrl(), {
                    method: 'GET',
                    headers: API_CONFIG.getHeaders(token)
                });
                
                console.log('Response status:', response.status); // Para debug
                
                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('Erro na resposta:', errorText);
                    throw new Error(`Erro ${response.status}: ${response.statusText}`);
                }
                
                empresas = await response.json();
                console.log('Empresas carregadas:', empresas); // Para debug
                
                exibirEmpresas(empresas);
                atualizarTotalEmpresas(empresas.length);
                
            } catch (error) {
                console.error('Erro ao carregar empresas:', error);
                mostrarNotificacao('Erro ao carregar empresas: ' + error.message, 'error');
                document.getElementById('tbodyEmpresas').innerHTML = '<tr><td colspan="7" class="text-center text-muted">Erro ao carregar dados</td></tr>';
            } finally {
                mostrarLoading(false);
            }
        }

        // Função para exibir empresas na tabela
        function exibirEmpresas(listaEmpresas) {
            const tbody = document.getElementById('tbodyEmpresas');
            
            if (listaEmpresas.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">Nenhuma empresa encontrada</td></tr>';
                return;
            }
            
            tbody.innerHTML = listaEmpresas.map(empresa => `
                <tr>
                    <td>${empresa.id_empresa}</td>
                    <td>
                        <div class="fw-semibold">${empresa.nome_empresa}</div>
                        ${empresa.email_empresa ? `<small class="text-muted">${empresa.email_empresa}</small>` : ''}
                    </td>
                    <td>${formatarCNPJ(empresa.cnpj)}</td>
                    <td><span class="segmento-badge">${formatarSegmento(empresa.segmento)}</span></td>
                    <td><span class="status-badge status-${empresa.status}">${formatarStatus(empresa.status)}</span></td>
                    <td>${formatarData(empresa.data_cadastro || empresa.created_at)}</td>
                    <td>
                        <div class="d-flex">
                            <button class="btn btn-action btn-outline-primary" onclick="editarEmpresa(${empresa.id_empresa})" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </button>
                             <a href="?view=gestao_usuarios&id_empresa=${empresa.id_empresa}" class="btn btn-action btn-outline-primary" onclick="editarEmpresa(${empresa.id_empresa})" title="Gestão de Usuarios">
                                <i class="bi bi-people"></i>
                            </a>   
                             <a href="?view=admin-filiais_empresa&id_empresas=${empresa.id_empresa}" class="btn btn-action btn-outline-primary" onclick="editarEmpresa(${empresa.id_empresa})" title="Gestão de Filiais">
                                <i class="bi bi-building"></i>
                            </a>                                                        
                            <button class="btn btn-action btn-outline-danger" onclick="confirmarExclusao(${empresa.id_empresa}, '${empresa.nome_empresa.replace(/'/g, "\\'")}')" title="Excluir">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `).join('');
        }

        // Função para abrir modal de nova empresa
        function abrirModalEmpresa() {
            empresaEditando = null;
            document.getElementById('modalEmpresaLabel').textContent = 'Nova Empresa';
            document.getElementById('formEmpresa').reset();
            document.getElementById('empresaId').value = '';
            
            // Limpar validação
            document.getElementById('formEmpresa').classList.remove('was-validated');
            
            modalEmpresa.show();
        }

        // Função para editar empresa
        async function editarEmpresa(id) {
            mostrarLoading(true);
            
            try {
                const token = '<?php echo $token; ?>';
                const response = await fetch(API_CONFIG.getEmpresaUrl(id), {
                    method: 'GET',
                    headers: API_CONFIG.getHeaders(token)
                });
                
                if (!response.ok) {
                    throw new Error(`Erro ${response.status}: ${response.statusText}`);
                }
                
                const empresa = await response.json();
                empresaEditando = empresa;
                
                // Preencher formulário
                document.getElementById('modalEmpresaLabel').textContent = 'Editar Empresa';
                document.getElementById('empresaId').value = empresa.id_empresa;
                document.getElementById('nome_empresa').value = empresa.nome_empresa || '';
                document.getElementById('cnpj').value = empresa.cnpj || '';
                document.getElementById('segmento').value = empresa.segmento || '';
                document.getElementById('email_empresa').value = empresa.email_empresa || '';
                document.getElementById('telefone').value = empresa.telefone || '';
                document.getElementById('website').value = empresa.website || '';
                document.getElementById('endereco').value = empresa.endereco || '';
                document.getElementById('cep').value = empresa.cep || '';
                document.getElementById('cidade').value = empresa.cidade || '';
                document.getElementById('estado').value = empresa.estado || '';
                document.getElementById('status').value = empresa.status || 'ativa';
                
                // Limpar validação
                document.getElementById('formEmpresa').classList.remove('was-validated');
                
                modalEmpresa.show();
                
            } catch (error) {
                console.error('Erro ao carregar empresa:', error);
                mostrarNotificacao('Erro ao carregar dados da empresa: ' + error.message, 'error');
            } finally {
                mostrarLoading(false);
            }
        }

        // Função para salvar empresa (criar ou editar)
        async function salvarEmpresa() {
            const form = document.getElementById('formEmpresa');
            
            if (!form.checkValidity()) {
                form.classList.add('was-validated');
                return;
            }
            
            mostrarLoading(true);
            
            const dadosEmpresa = {
                nome_empresa: document.getElementById('nome_empresa').value,
                cnpj: document.getElementById('cnpj').value,
                segmento: document.getElementById('segmento').value,
                email_empresa: document.getElementById('email_empresa').value,
                telefone: document.getElementById('telefone').value,
                website: document.getElementById('website').value,
                endereco: document.getElementById('endereco').value,
                cep: document.getElementById('cep').value,
                cidade: document.getElementById('cidade').value,
                estado: document.getElementById('estado').value,
                status: document.getElementById('status').value
            };
            
            try {
                const token = '<?php echo $token; ?>';
                let response;
                let url;
                
                if (empresaEditando) {
                    // Editar empresa existente
                    url = API_CONFIG.getEmpresaUrl(empresaEditando.id_empresa);
                    response = await fetch(url, {
                        method: 'PUT',
                        headers: API_CONFIG.getHeaders(token),
                        body: JSON.stringify(dadosEmpresa)
                    });
                } else {
                    // Criar nova empresa
                    url = API_CONFIG.getEmpresasUrl();
                    response = await fetch(url, {
                        method: 'POST',
                        headers: API_CONFIG.getHeaders(token),
                        body: JSON.stringify(dadosEmpresa)
                    });
                }
                
                if (!response.ok) {
                    const errorData = await response.json().catch(() => ({}));
                    throw new Error(errorData.message || `Erro ${response.status}: ${response.statusText}`);
                }
                
                const empresaSalva = await response.json();
                
                modalEmpresa.hide();
                form.classList.remove('was-validated');
                
                mostrarNotificacao(
                    `Empresa ${empresaEditando ? 'atualizada' : 'criada'} com sucesso!`, 
                    'success'
                );
                
                // Recarregar lista
                carregarEmpresas();
                
            } catch (error) {
                console.error('Erro ao salvar empresa:', error);
                mostrarNotificacao('Erro ao salvar empresa: ' + error.message, 'error');
            } finally {
                mostrarLoading(false);
            }
        }

        // Função para confirmar exclusão
        function confirmarExclusao(id, nome) {
            document.getElementById('nomeEmpresaExcluir').textContent = nome;
            
            const btnConfirmar = document.getElementById('btnConfirmarExclusao');
            // Remover event listeners anteriores
            btnConfirmar.replaceWith(btnConfirmar.cloneNode(true));
            // Adicionar novo event listener
            document.getElementById('btnConfirmarExclusao').onclick = () => excluirEmpresa(id);
            
            modalConfirmacao.show();
        }

        // Função para excluir empresa
        async function excluirEmpresa(id) {
            mostrarLoading(true);
            
            try {
                const token = '<?php echo $token; ?>';
                const response = await fetch(API_CONFIG.getEmpresaUrl(id), {
                    method: 'DELETE',
                    headers: API_CONFIG.getHeaders(token)
                });
                
                if (!response.ok && response.status !== 204) {
                    throw new Error(`Erro ${response.status}: ${response.statusText}`);
                }
                
                modalConfirmacao.hide();
                mostrarNotificacao('Empresa excluída com sucesso!', 'success');
                
                // Recarregar lista
                carregarEmpresas();
                
            } catch (error) {
                console.error('Erro ao excluir empresa:', error);
                mostrarNotificacao('Erro ao excluir empresa: ' + error.message, 'error');
                modalConfirmacao.hide();
            } finally {
                mostrarLoading(false);
            }
        }

        // Função para filtrar empresas
        function filtrarEmpresas() {
            const termoBusca = document.getElementById('searchInput').value.toLowerCase();
            const segmentoFiltro = document.getElementById('filterSegmento').value;
            const statusFiltro = document.getElementById('filterStatus').value;
            
            const empresasFiltradas = empresas.filter(empresa => {
                const matchBusca = !termoBusca || 
                    empresa.nome_empresa.toLowerCase().includes(termoBusca) ||
                    (empresa.cnpj && empresa.cnpj.toLowerCase().includes(termoBusca));
                
                const matchSegmento = !segmentoFiltro || empresa.segmento === segmentoFiltro;
                const matchStatus = !statusFiltro || empresa.status === statusFiltro;
                
                return matchBusca && matchSegmento && matchStatus;
            });
            
            exibirEmpresas(empresasFiltradas);
            atualizarTotalEmpresas(empresasFiltradas.length);
        }

        // Funções auxiliares
        function formatarCNPJ(cnpj) {
            if (!cnpj) return '';
            // Remove qualquer caractere não numérico
            cnpj = cnpj.replace(/\D/g, '');
            // Aplica a formatação se tiver 14 dígitos
            if (cnpj.length === 14) {
                return cnpj.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, '$1.$2.$3/$4-$5');
            }
            return cnpj;
        }

        function formatarSegmento(segmento) {
            const segmentos = {
                'varejo': 'Varejo',
                'industria': 'Indústria',
                'construcao': 'Construção',
                'financeiro': 'Financeiro',
                'seguranca': 'Segurança',
                'outros': 'Outros'
            };
            return segmentos[segmento] || segmento;
        }

        function formatarStatus(status) {
            const statusMap = {
                'ativa': 'Ativa',
                'pendente': 'Pendente',
                'inativa': 'Inativa'
            };
            return statusMap[status] || status;
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

        function atualizarTotalEmpresas(total) {
            document.getElementById('totalEmpresas').textContent = `${total} empresa(s) encontrada(s)`;
        }

        function mostrarLoading(mostrar) {
            document.getElementById('loadingOverlay').classList.toggle('d-none', !mostrar);
        }

        // Função para mostrar notificações
        function mostrarNotificacao(message, type) {
            // Criar elemento de notificação
            const notification = document.createElement('div');
            notification.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
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
    </script>
</body>
</html>