<?php
session_start();
if (!isset($_SESSION['authToken'], $_SESSION['usuario'], $_SESSION['empresa'], $_SESSION['licenca'])) {
    header('Location: login.php');
    exit;
}

// Capturar o ID da empresa da URL
$id_empresa = $_GET['id_empresas'] ?? null;
if (!$id_empresa) {
    die("ID da empresa não especificado.");
}

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
    <title>Gestão de Filiais - SAS Multi</title>
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
        
        .location-badge {
            padding: 4px 8px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            background: rgba(52, 152, 219, 0.1);
            color: var(--primary-color);
        }
        
        .empresa-info {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        
        .empresa-nome {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .empresa-detalhes {
            font-size: 0.9rem;
            opacity: 0.9;
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
                        <li class="breadcrumb-item active">Gestão de Filiais</li>
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
                        <li><a class="dropdown-item" href="#">Nova filial cadastrada</a></li>
                        <li><a class="dropdown-item" href="#">Atualização de endereços pendente</a></li>
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

        <!-- Informações da Empresa -->
        <div class="empresa-info">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="empresa-nome" id="empresaNome">Carregando...</div>
                    <div class="empresa-detalhes" id="empresaDetalhes"></div>
                </div>
                <div>
                    <span class="badge bg-light text-dark" id="totalFiliaisHeader">0 filiais</span>
                </div>
            </div>
        </div>
        
        <!-- Área de Conteúdo -->
        <div class="content-area">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="page-title">Gestão de Filiais</h1>
                    <p class="page-subtitle">Cadastre e gerencie as filiais da empresa</p>
                </div>
                <div>
                    <button class="btn btn-primary" onclick="abrirModalFilial()">
                        <i class="bi bi-plus-circle me-2"></i>Nova Filial
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
                                <input type="text" class="form-control" id="searchInput" placeholder="Buscar por nome da filial ou cidade...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <select class="form-select" id="filterEstado">
                                <option value="">Todos os estados</option>
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
                    </div>
                </div>
            </div>
            
            <!-- Tabela de Filiais -->
            <div class="card-custom">
                <div class="card-header-custom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Lista de Filiais</h5>
                    <div class="d-flex align-items-center">
                        <span class="text-muted me-3" id="totalFiliais">Carregando...</span>
                        <button class="btn btn-sm btn-outline-primary" onclick="carregarFiliais()">
                            <i class="bi bi-arrow-clockwise"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="tabelaFiliais">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nome da Filial</th>
                                    <th>Localização</th>
                                    <th>CEP</th>
                                    <th>Data Cadastro</th>
                                    <th width="120">Ações</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyFiliais">
                                <tr>
                                    <td colspan="6" class="text-center text-muted">Carregando filiais...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal para Adicionar/Editar Filial -->
    <div class="modal fade" id="modalFilial" tabindex="-1" aria-labelledby="modalFilialLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalFilialLabel">Nova Filial</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formFilial">
                        <input type="hidden" id="filialId">
                        <input type="hidden" id="id_empresa" value="<?php echo $id_empresa; ?>">
                        
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            Filial será associada à empresa: <strong id="nomeEmpresaAssociada">Carregando...</strong>
                        </div>
                        
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="nome_filial" class="form-label">Nome da Filial *</label>
                                <input type="text" class="form-control" id="nome_filial" required>
                                <div class="invalid-feedback">Por favor, informe o nome da filial.</div>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="endereco" class="form-label">Endereço</label>
                                <input type="text" class="form-control" id="endereco">
                            </div>
                            
                            <div class="col-md-6">
                                <label for="cidade" class="form-label">Cidade *</label>
                                <input type="text" class="form-control" id="cidade" required>
                                <div class="invalid-feedback">Por favor, informe a cidade.</div>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="estado" class="form-label">Estado *</label>
                                <select class="form-select" id="estado" required>
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
                                <div class="invalid-feedback">Por favor, selecione um estado.</div>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="cep" class="form-label">CEP</label>
                                <input type="text" class="form-control" id="cep">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="salvarFilial()">Salvar Filial</button>
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
                    <p>Tem certeza que deseja excluir a filial <strong id="nomeFilialExcluir"></strong>?</p>
                    <p class="text-danger"><small>Esta ação não pode ser desfeita.</small></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="btnConfirmarExclusao">Excluir Filial</button>
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
            EMPRESAS: '/api/empresas',
            FILIAIS: '/api/filiais',
            FILIAIS_EMPRESA: '/api/filiais/empresa',
            LOGIN: '/api/login',
            LOGOUT: '/api/logout',
            
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
            
            // URL completa para filiais por empresa
            getFiliaisEmpresaUrl: function(idEmpresa) {
                return `${this.BASE_URL}${this.FILIAIS_EMPRESA}/${idEmpresa}`;
            },
            
            // URL completa para uma filial específica
            getFilialUrl: function(id) {
                return `${this.BASE_URL}${this.FILIAIS}/${id}`;
            }
        };

        // Variáveis globais
        let filiais = [];
        let empresaAtual = null;
        let modalFilial = null;
        let modalConfirmacao = null;
        const idEmpresa = <?php echo $id_empresa; ?>;

        // Inicialização
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Iniciando aplicação...');
            console.log('ID da empresa:', idEmpresa);
            
            // Inicializar modais
            modalFilial = new bootstrap.Modal(document.getElementById('modalFilial'));
            modalConfirmacao = new bootstrap.Modal(document.getElementById('modalConfirmacao'));
            
            // Carregar dados iniciais
            carregarEmpresaAtual().then(() => {
                console.log('Empresa carregada, agora carregando filiais...');
                carregarFiliais();
            }).catch(error => {
                console.error('Erro ao carregar dados iniciais:', error);
                mostrarNotificacao('Erro ao carregar dados iniciais: ' + error.message, 'error');
            });
            
            // Configurar eventos
            document.getElementById('searchInput').addEventListener('input', filtrarFiliais);
            document.getElementById('filterEstado').addEventListener('change', filtrarFiliais);
            
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

        // Função para carregar dados da empresa atual
        async function carregarEmpresaAtual() {
            try {
                console.log('Carregando dados da empresa...');
                const token = '<?php echo $token; ?>';
                
                // Como já temos o ID da empresa, podemos usar a primeira filial para obter os dados da empresa
                const response = await fetch(API_CONFIG.getFiliaisEmpresaUrl(idEmpresa), {
                    method: 'GET',
                    headers: API_CONFIG.getHeaders(token)
                });
                
                console.log('Resposta empresa - Status:', response.status);
                
                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('Erro na resposta empresa:', errorText);
                    throw new Error(`Erro ${response.status}: ${response.statusText}`);
                }
                
                const data = await response.json();
                console.log('Dados da empresa carregados:', data);
                
                if (data.length > 0 && data[0].empresa) {
                    empresaAtual = data[0].empresa;
                    atualizarInfoEmpresa(empresaAtual);
                } else {
                    // Fallback: usar dados da sessão se disponível
                    empresaAtual = {
                        nome_empresa: '<?php echo is_array($empresa) ? ($empresa["nome_empresa"] ?? "Empresa") : "Empresa"; ?>',
                        cnpj: '<?php echo is_array($empresa) ? ($empresa["cnpj"] ?? "") : ""; ?>',
                        cidade: '<?php echo is_array($empresa) ? ($empresa["cidade"] ?? "") : ""; ?>',
                        estado: '<?php echo is_array($empresa) ? ($empresa["estado"] ?? "") : ""; ?>'
                    };
                    atualizarInfoEmpresa(empresaAtual);
                }
                
            } catch (error) {
                console.error('Erro ao carregar empresa:', error);
                // Usar fallback em caso de erro
                empresaAtual = {
                    nome_empresa: 'Empresa ID: ' + idEmpresa,
                    cnpj: '',
                    cidade: '',
                    estado: ''
                };
                atualizarInfoEmpresa(empresaAtual);
                throw error;
            }
        }

        // Função para atualizar informações da empresa na interface
        function atualizarInfoEmpresa(empresa) {
            document.getElementById('empresaNome').textContent = empresa.nome_empresa;
            
            let detalhes = '';
            if (empresa.cnpj) {
                detalhes += `CNPJ: ${empresa.cnpj} | `;
            }
            if (empresa.cidade && empresa.estado) {
                detalhes += `Localização: ${empresa.cidade} - ${empresa.estado}`;
            }
            document.getElementById('empresaDetalhes').textContent = detalhes;
            
            // Atualizar também no modal
            document.getElementById('nomeEmpresaAssociada').textContent = empresa.nome_empresa;
        }

        // Função para carregar filiais da API
        async function carregarFiliais() {
            mostrarLoading(true);
            
            try {
                console.log('Carregando filiais para empresa:', idEmpresa);
                const token = '<?php echo $token; ?>';
                
                const response = await fetch(API_CONFIG.getFiliaisEmpresaUrl(idEmpresa), {
                    method: 'GET',
                    headers: API_CONFIG.getHeaders(token)
                });
                
                console.log('Resposta filiais - Status:', response.status);
                
                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('Erro na resposta filiais:', errorText);
                    throw new Error(`Erro ${response.status}: ${response.statusText}`);
                }
                
                const data = await response.json();
                filiais = data; // A resposta já é um array de filiais
                console.log('Filiais carregadas:', filiais);
                
                exibirFiliais(filiais);
                atualizarTotalFiliais(filiais.length);
                
            } catch (error) {
                console.error('Erro ao carregar filiais:', error);
                mostrarNotificacao('Erro ao carregar filiais: ' + error.message, 'error');
                document.getElementById('tbodyFiliais').innerHTML = '<tr><td colspan="6" class="text-center text-muted">Erro ao carregar dados</td></tr>';
            } finally {
                mostrarLoading(false);
            }
        }

        // Função para exibir filiais na tabela
        function exibirFiliais(listaFiliais) {
            const tbody = document.getElementById('tbodyFiliais');
            
            if (listaFiliais.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">Nenhuma filial encontrada</td></tr>';
                return;
            }
            
            tbody.innerHTML = listaFiliais.map(filial => {
                return `
                <tr>
                    <td>${filial.id_filial}</td>
                    <td>
                        <div class="fw-semibold">${filial.nome_filial}</div>
                        ${filial.endereco ? `<small class="text-muted">${filial.endereco}</small>` : ''}
                    </td>
                    <td>
                        <span class="location-badge">
                            <i class="bi bi-geo-alt me-1"></i>
                            ${filial.cidade || ''}${filial.estado ? ` - ${filial.estado}` : ''}
                        </span>
                    </td>
                    <td>${formatarCEP(filial.cep)}</td>
                    <td>${formatarData(filial.data_cadastro || filial.created_at)}</td>
                    <td>
                        <div class="d-flex">
                            <button class="btn btn-action btn-outline-primary" onclick="editarFilial(${filial.id_filial})" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-action btn-outline-danger" onclick="confirmarExclusao(${filial.id_filial}, '${filial.nome_filial.replace(/'/g, "\\'")}')" title="Excluir">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                `;
            }).join('');
        }

        // Função para abrir modal de nova filial
        function abrirModalFilial() {
            document.getElementById('modalFilialLabel').textContent = 'Nova Filial';
            document.getElementById('formFilial').reset();
            document.getElementById('filialId').value = '';
            
            // Limpar validação
            document.getElementById('formFilial').classList.remove('was-validated');
            
            modalFilial.show();
        }

        // Função para editar filial
        async function editarFilial(id) {
            mostrarLoading(true);
            
            try {
                console.log('Editando filial ID:', id);
                const token = '<?php echo $token; ?>';
                const url = API_CONFIG.getFilialUrl(id);
                
                const response = await fetch(url, {
                    method: 'GET',
                    headers: API_CONFIG.getHeaders(token)
                });
                
                console.log('Resposta edição - Status:', response.status);
                
                if (!response.ok) {
                    throw new Error(`Erro ${response.status}: ${response.statusText}`);
                }
                
                const data = await response.json();
                const filial = data.data || data; // Suporte para diferentes formatos
                console.log('Dados da filial para edição:', filial);
                
                // Preencher formulário
                document.getElementById('modalFilialLabel').textContent = 'Editar Filial';
                document.getElementById('filialId').value = filial.id_filial;
                document.getElementById('nome_filial').value = filial.nome_filial || '';
                document.getElementById('endereco').value = filial.endereco || '';
                document.getElementById('cidade').value = filial.cidade || '';
                document.getElementById('estado').value = filial.estado || '';
                document.getElementById('cep').value = filial.cep || '';
                
                // Limpar validação
                document.getElementById('formFilial').classList.remove('was-validated');
                
                modalFilial.show();
                
            } catch (error) {
                console.error('Erro ao carregar filial:', error);
                mostrarNotificacao('Erro ao carregar dados da filial: ' + error.message, 'error');
            } finally {
                mostrarLoading(false);
            }
        }

        // Função para formatar data no formato Y-m-d H:i:s
        function formatarDataParaAPI(data) {
            const date = new Date(data);
            const ano = date.getFullYear();
            const mes = String(date.getMonth() + 1).padStart(2, '0');
            const dia = String(date.getDate()).padStart(2, '0');
            const horas = String(date.getHours()).padStart(2, '0');
            const minutos = String(date.getMinutes()).padStart(2, '0');
            const segundos = String(date.getSeconds()).padStart(2, '0');
            
            return `${ano}-${mes}-${dia} ${horas}:${minutos}:${segundos}`;
        }

        // Função para salvar filial (criar ou editar)
        async function salvarFilial() {
            const form = document.getElementById('formFilial');
            
            // Validação dos campos obrigatórios
            const nomeFilial = document.getElementById('nome_filial').value.trim();
            const cidade = document.getElementById('cidade').value.trim();
            const estado = document.getElementById('estado').value;
            
            if (!nomeFilial || !cidade || !estado) {
                form.classList.add('was-validated');
                mostrarNotificacao('Por favor, preencha todos os campos obrigatórios.', 'error');
                return;
            }
            
            mostrarLoading(true);
            
            // Preparar dados - ID da empresa vem do parâmetro GET
            const dadosFilial = {
                id_empresa: idEmpresa, // Usa o ID da empresa da URL
                nome_filial: nomeFilial,
                endereco: document.getElementById('endereco').value.trim() || null,
                cidade: cidade,
                estado: estado,
                cep: document.getElementById('cep').value.trim() || null
            };
            
            const filialId = document.getElementById('filialId').value;
            
            // Apenas adicionar data_cadastro para novas filiais (não para edição)
            if (!filialId) {
                dadosFilial.data_cadastro = formatarDataParaAPI(new Date());
            }
            
            console.log('Dados a serem enviados:', dadosFilial);
            
            try {
                const token = '<?php echo $token; ?>';
                let response;
                
                if (filialId) {
                    // Editar filial existente
                    response = await fetch(API_CONFIG.getFilialUrl(filialId), {
                        method: 'PUT',
                        headers: API_CONFIG.getHeaders(token),
                        body: JSON.stringify(dadosFilial)
                    });
                } else {
                    // Criar nova filial
                    response = await fetch(API_CONFIG.BASE_URL + API_CONFIG.FILIAIS, {
                        method: 'POST',
                        headers: API_CONFIG.getHeaders(token),
                        body: JSON.stringify(dadosFilial)
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
                    
                    throw new Error(errorMessage);
                }
                
                const resultado = await response.json();
                console.log('Filial salva com sucesso:', resultado);
                
                modalFilial.hide();
                form.classList.remove('was-validated');
                
                mostrarNotificacao(
                    `Filial "${nomeFilial}" ${filialId ? 'atualizada' : 'criada'} com sucesso!`, 
                    'success'
                );
                
                // Recarregar lista
                carregarFiliais();
                
            } catch (error) {
                console.error('Erro ao salvar filial:', error);
                mostrarNotificacao('Erro ao salvar filial: ' + error.message, 'error');
            } finally {
                mostrarLoading(false);
            }
        }

        // Função para confirmar exclusão
        function confirmarExclusao(id, nome) {
            document.getElementById('nomeFilialExcluir').textContent = nome;
            
            // Configurar o botão de confirmação
            const btnConfirmar = document.getElementById('btnConfirmarExclusao');
            btnConfirmar.onclick = function() {
                excluirFilial(id);
            };
            
            modalConfirmacao.show();
        }

        // Função para excluir filial
        async function excluirFilial(id) {
            mostrarLoading(true);
            
            try {
                const token = '<?php echo $token; ?>';
                const response = await fetch(API_CONFIG.getFilialUrl(id), {
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
                mostrarNotificacao('Filial excluída com sucesso!', 'success');
                
                // Recarregar lista
                carregarFiliais();
                
            } catch (error) {
                console.error('Erro ao excluir filial:', error);
                mostrarNotificacao('Erro ao excluir filial: ' + error.message, 'error');
                modalConfirmacao.hide();
            } finally {
                mostrarLoading(false);
            }
        }

        // Função para filtrar filiais
        function filtrarFiliais() {
            const termoBusca = document.getElementById('searchInput').value.toLowerCase();
            const estadoFiltro = document.getElementById('filterEstado').value;
            
            const filiaisFiltradas = filiais.filter(filial => {
                const matchBusca = !termoBusca || 
                    filial.nome_filial.toLowerCase().includes(termoBusca) ||
                    (filial.cidade && filial.cidade.toLowerCase().includes(termoBusca));
                
                const matchEstado = !estadoFiltro || filial.estado === estadoFiltro;
                
                return matchBusca && matchEstado;
            });
            
            exibirFiliais(filiaisFiltradas);
            atualizarTotalFiliais(filiaisFiltradas.length);
        }

        // Funções auxiliares
        function formatarCEP(cep) {
            if (!cep) return '';
            cep = cep.replace(/\D/g, '');
            if (cep.length === 8) {
                return cep.replace(/(\d{5})(\d{3})/, '$1-$2');
            }
            return cep;
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

        function atualizarTotalFiliais(total) {
            document.getElementById('totalFiliais').textContent = `${total} filial(is) encontrada(s)`;
            document.getElementById('totalFiliaisHeader').textContent = `${total} filial(is)`;
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
    </script>
</body>
</html>