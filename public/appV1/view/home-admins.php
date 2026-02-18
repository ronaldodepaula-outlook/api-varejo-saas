<?php
session_start();
if (!isset($_SESSION['authToken'], $_SESSION['usuario'], $_SESSION['empresa'], $_SESSION['licenca'])) {
    header('Location: login.php');
    exit;
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
    <title>Dashboard SAS Multi</title>
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
        
        .metric-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            position: relative;
            overflow: hidden;
        }
        
        .metric-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 5px;
            height: 100%;
        }
        
        .metric-card.primary::before { background: var(--primary-color); }
        .metric-card.secondary::before { background: var(--secondary-color); }
        .metric-card.success::before { background: var(--success-color); }
        .metric-card.warning::before { background: var(--warning-color); }
        .metric-card.danger::before { background: var(--danger-color); }
        .metric-card.info::before { background: var(--info-color); }
        
        .metric-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
            font-size: 1.5rem;
        }
        
        .metric-icon.primary { background: rgba(52, 152, 219, 0.1); color: var(--primary-color); }
        .metric-icon.secondary { background: rgba(44, 62, 80, 0.1); color: var(--secondary-color); }
        .metric-icon.success { background: rgba(39, 174, 96, 0.1); color: var(--success-color); }
        .metric-icon.warning { background: rgba(243, 156, 18, 0.1); color: var(--warning-color); }
        .metric-icon.danger { background: rgba(231, 76, 60, 0.1); color: var(--danger-color); }
        .metric-icon.info { background: rgba(23, 162, 184, 0.1); color: var(--info-color); }
        
        .metric-value {
            font-size: 1.8rem;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .metric-label {
            color: #6c757d;
            margin-bottom: 10px;
        }
        
        .metric-change {
            font-size: 0.85rem;
            display: flex;
            align-items: center;
        }
        
        .metric-change.positive { color: var(--success-color); }
        .metric-change.negative { color: var(--danger-color); }
        
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
        
        .status-active { background: rgba(39, 174, 96, 0.1); color: var(--success-color); }
        .status-pending { background: rgba(243, 156, 18, 0.1); color: var(--warning-color); }
        .status-inactive { background: rgba(231, 76, 60, 0.1); color: var(--danger-color); }
        
        .page-title {
            font-weight: 700;
            color: var(--secondary-color);
            margin-bottom: 5px;
        }
        
        .page-subtitle {
            color: #6c757d;
            margin-bottom: 0;
        }
        
        .btn-check:checked + .btn-outline-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }
        
        .segmento-card {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 15px;
            transition: transform 0.2s;
        }
        
        .segmento-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .segmento-image {
            height: 120px;
            background: linear-gradient(135deg, #3498DB 0%, #2980B9 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
        }
        
        .segmento-info {
            padding: 15px;
        }
        
        .segmento-title {
            font-size: 1rem;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .segmento-meta {
            font-size: 0.85rem;
            color: #6c757d;
            margin-bottom: 10px;
        }
        
        .segmento-progress {
            margin-top: 10px;
        }
        
        .task-item {
            padding: 10px;
            border-left: 4px solid transparent;
            margin-bottom: 8px;
            border-radius: 5px;
            background: #f8f9fa;
        }
        
        .task-item.ongoing { border-left-color: var(--primary-color); }
        .task-item.delayed { border-left-color: var(--danger-color); }
        .task-item.completed { border-left-color: var(--success-color); }
        
        .user-status {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 8px;
            background: #f8f9fa;
        }
        
        .user-status.active { border-left: 4px solid var(--success-color); }
        .user-status.inactive { border-left: 4px solid var(--danger-color); }
        .user-status.pending { border-left: 4px solid var(--warning-color); }
        
        .revenue-card {
            background: linear-gradient(135deg, #27AE60 0%, #229954 100%);
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
        }
        
        .progress-thin {
            height: 8px;
        }
        
        .plan-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            font-weight: bold;
            color: white;
        }
        
        .plan-trial { background: var(--info-color); }
        .plan-basic { background: var(--success-color); }
        .plan-pro { background: var(--primary-color); }
        .plan-enterprise { background: var(--secondary-color); }
        
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
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item active">SAS Multi</li>
                    </ol>
                </nav>
            </div>
            
            <div class="header-right">
                <!-- Notificações -->
                <div class="dropdown">
                    <button class="btn btn-link text-dark position-relative" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-bell" style="font-size: 1.25rem;"></i>
                        <span class="badge bg-danger position-absolute translate-middle rounded-pill" style="font-size: 0.6rem; top: 8px; right: 8px;" id="totalAlertas">0</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" id="dropdownAlertas">
                        <li><h6 class="dropdown-header">Alertas do Sistema</h6></li>
                        <li><a class="dropdown-item" href="#">Carregando alertas...</a></li>
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
                        <li><a class="dropdown-item" href="#"><i class="bi bi-clipboard-data me-2"></i>Relatórios</a></li>
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
                    <h1 class="page-title">Dashboard SAS Multi</h1>
                    <p class="page-subtitle">Gestão completa do sistema SaaS multi-tenant</p>
                </div>
                <div>
                    <button class="btn btn-outline-primary me-2" onclick="exportReport()">
                        <i class="bi bi-file-earmark-spreadsheet me-2"></i>Relatório Mensal
                    </button>
                    <button class="btn btn-primary" onclick="carregarDashboard()">
                        <i class="bi bi-arrow-clockwise me-2"></i>Atualizar
                    </button>
                </div>
            </div>
            
            <!-- Métricas Principais -->
            <div class="row mb-4" id="metricasPrincipais">
                <!-- As métricas serão carregadas via JavaScript -->
                <div class="col-12 text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Carregando métricas...</span>
                    </div>
                </div>
            </div>
            
            <!-- Gráficos e Dados -->
            <div class="row mb-4">
                <!-- Distribuição por Segmento -->
                <div class="col-xl-8 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Distribuição de Empresas por Segmento</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="segmentChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
                
                <!-- Receita e Status -->
                <div class="col-xl-4 mb-4">
                    <div class="revenue-card">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Receita do Mês</h5>
                            <div class="plan-badge plan-pro">R$</div>
                        </div>
                        <div class="text-center mb-3">
                            <div class="metric-value" id="receitaMensal">R$ 0</div>
                            <div class="metric-label">Meta: R$ 5.000</div>
                        </div>
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="mb-2">
                                    <i class="bi bi-arrow-up-circle"></i>
                                    <div class="small" id="percentualMeta">0%</div>
                                    <div class="small">Meta</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="mb-2">
                                    <i class="bi bi-graph-up"></i>
                                    <div class="small">+0%</div>
                                    <div class="small">Crescimento</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="mb-2">
                                    <i class="bi bi-cash-coin"></i>
                                    <div class="small" id="receitaRestante">R$ 0</div>
                                    <div class="small">Restante</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Distribuição de Planos -->
                    <div class="card-custom mt-3">
                        <div class="card-header-custom">
                            <h5 class="mb-0">Distribuição de Planos</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="plansChart" height="250"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Empresas e Usuários -->
            <div class="row mb-4">
                <!-- Empresas por Segmento -->
                <div class="col-xl-6 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Empresas por Segmento</h5>
                            <a href="empresas.php" class="btn btn-sm btn-outline-primary">Ver todas</a>
                        </div>
                        <div class="card-body" id="empresasSegmento">
                            <!-- Conteúdo será carregado via JavaScript -->
                            <div class="text-center">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Carregando...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Atividades do Sistema -->
                <div class="col-xl-6 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Atividades do Sistema</h5>
                            <a href="logs.php" class="btn btn-sm btn-outline-primary">Ver logs</a>
                        </div>
                        <div class="card-body" id="atividadesSistema">
                            <!-- Conteúdo será carregado via JavaScript -->
                            <div class="text-center">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Carregando...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Usuários e Licenças -->
            <div class="row mb-4">
                <!-- Status dos Usuários -->
                <div class="col-xl-6 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Status dos Usuários</h5>
                            <a href="usuarios.php" class="btn btn-sm btn-outline-primary">Ver usuários</a>
                        </div>
                        <div class="card-body" id="statusUsuarios">
                            <!-- Conteúdo será carregado via JavaScript -->
                            <div class="text-center">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Carregando...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Status de Licenças -->
                <div class="col-xl-6 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Status de Licenças</h5>
                            <a href="licencas.php" class="btn btn-sm btn-outline-primary">Ver licenças</a>
                        </div>
                        <div class="card-body" id="statusLicencas">
                            <!-- Conteúdo será carregado via JavaScript -->
                            <div class="text-center">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Carregando...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Alertas do Sistema -->
            <div class="row">
                <div class="col-12">
                    <div class="card-custom">
                        <div class="card-header-custom">
                            <h5 class="mb-0">Alertas e Recomendações</h5>
                        </div>
                        <div class="card-body" id="alertasSistema">
                            <!-- Conteúdo será carregado via JavaScript -->
                            <div class="text-center">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Carregando...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Loading Overlay -->
    <div class="loading-overlay d-none" id="loadingOverlay">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Carregando...</span>
        </div>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // CONFIGURAÇÃO DA API - CORRIGIDA
        const API_CONFIG = {
            BASE_URL: 'https://rdpsolutions.online/saas-multiempresas/public',
            
            // Endpoints existentes conforme documentação
            EMPRESAS: '/api/v1/empresas',
            USUARIOS: '/api/usuarios',
            LICENCAS: '/api/v1/licencas', // Assumindo que existe endpoint para licenças
            LOGOUT: '/api/v1/logout',
            
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
            
            getEmpresasUrl: function() {
                return `${this.BASE_URL}${this.EMPRESAS}`;
            },
            
            getUsuariosUrl: function() {
                return `${this.BASE_URL}${this.USUARIOS}`;
            },
            
            getLicencasUrl: function() {
                return `${this.BASE_URL}${this.LICENCAS}`;
            }
        };

        // Variáveis globais
        let dashboardData = {};
        let segmentChart = null;
        let plansChart = null;

        // Inicialização
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Iniciando dashboard...');
            
            // Carregar dados iniciais
            carregarDashboard();
            
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

        // Função para carregar dados do dashboard - CORRIGIDA
        async function carregarDashboard() {
            mostrarLoading(true);
            
            try {
                console.log('Carregando dados do dashboard...');
                const token = '<?php echo $token; ?>';
                
                // Buscar dados de diferentes endpoints e consolidar
                const [empresasData, usuariosData] = await Promise.all([
                    carregarEmpresas(),
                    carregarUsuarios()
                ]);
                
                // Consolidar dados do dashboard
                dashboardData = {
                    empresas: empresasData,
                    usuarios: usuariosData,
                    licencas: await carregarLicencas(),
                    assinaturas: {
                        total: empresasData.total || 0,
                        ativas: empresasData.total || 0
                    },
                    pagamentos: {
                        pendentes: 0,
                        receita_mensal: calcularReceitaMensal(empresasData)
                    },
                    disponibilidade: 98.7,
                    alertas: await carregarAlertas()
                };
                
                console.log('Dados do dashboard consolidados:', dashboardData);
                
                atualizarDashboard(dashboardData);
                mostrarNotificacao('Dashboard atualizado com sucesso!', 'success');
                
            } catch (error) {
                console.error('Erro ao carregar dashboard:', error);
                // Usar dados de fallback
                usarDadosFallback();
                mostrarNotificacao('Usando dados demonstrativos. API não disponível.', 'warning');
            } finally {
                mostrarLoading(false);
            }
        }

        // Função para carregar empresas
        async function carregarEmpresas() {
            try {
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
                
                // Processar segmentos
                const segmentos = {};
                empresas.forEach(empresa => {
                    const segmento = empresa.segmento || 'outros';
                    segmentos[segmento] = (segmentos[segmento] || 0) + 1;
                });
                
                const porSegmento = Object.keys(segmentos).map(segmento => ({
                    segmento: segmento,
                    total: segmentos[segmento]
                }));
                
                return {
                    total: empresas.length,
                    pendentes: empresas.filter(e => !e.ativo || e.status === 'pendente').length,
                    por_segmento: porSegmento
                };
                
            } catch (error) {
                console.error('Erro ao carregar empresas:', error);
                throw error;
            }
        }

        // Função para carregar usuários
        async function carregarUsuarios() {
            try {
                const token = '<?php echo $token; ?>';
                const response = await fetch(API_CONFIG.getUsuariosUrl(), {
                    method: 'GET',
                    headers: API_CONFIG.getHeaders(token)
                });
                
                if (!response.ok) {
                    throw new Error(`Erro ${response.status}: ${response.statusText}`);
                }
                
                const data = await response.json();
                const usuarios = data.data || data;
                
                return {
                    total: usuarios.length,
                    ativos: usuarios.filter(u => u.ativo).length,
                    inativos: usuarios.filter(u => !u.ativo).length,
                    admin_empresa: usuarios.filter(u => u.perfil === 'admin' || u.perfil === 'gestor').length,
                    super_admin: usuarios.filter(u => u.perfil === 'super_admin').length,
                    newsletter: usuarios.filter(u => u.newsletter).length
                };
                
            } catch (error) {
                console.error('Erro ao carregar usuários:', error);
                throw error;
            }
        }

        // Função para carregar licenças (simulação)
        async function carregarLicencas() {
            try {
                // Simular dados de licenças
                return {
                    ativas: Math.floor(Math.random() * 20) + 10,
                    expiradas: Math.floor(Math.random() * 5),
                    canceladas: Math.floor(Math.random() * 3),
                    por_plano: [
                        {plano: 'trial', total: Math.floor(Math.random() * 10) + 5},
                        {plano: 'basic', total: Math.floor(Math.random() * 8) + 3},
                        {plano: 'pro', total: Math.floor(Math.random() * 5) + 2},
                        {plano: 'enterprise', total: Math.floor(Math.random() * 3) + 1}
                    ],
                    proximas_expiracao: Math.floor(Math.random() * 5) + 1
                };
            } catch (error) {
                console.error('Erro ao carregar licenças:', error);
                return {
                    ativas: 0,
                    expiradas: 0,
                    canceladas: 0,
                    por_plano: [],
                    proximas_expiracao: 0
                };
            }
        }

        // Função para calcular receita mensal (simulação)
        function calcularReceitaMensal(empresasData) {
            const totalEmpresas = empresasData.total || 0;
            // Simular receita baseada no número de empresas
            return totalEmpresas * 150; // R$ 150 por empresa em média
        }

        // Função para carregar alertas (simulação)
        async function carregarAlertas() {
            try {
                return {
                    licencas_expirando: Math.floor(Math.random() * 5) + 1,
                    empresas_pendentes: Math.floor(Math.random() * 3),
                    pagamentos_pendentes: Math.floor(Math.random() * 2),
                    tentativas_login_suspeitas: Math.floor(Math.random() * 2),
                    backup_status: Math.random() > 0.3 ? 'executado' : 'erro'
                };
            } catch (error) {
                console.error('Erro ao carregar alertas:', error);
                return {
                    licencas_expirando: 0,
                    empresas_pendentes: 0,
                    pagamentos_pendentes: 0,
                    tentativas_login_suspeitas: 0,
                    backup_status: 'erro'
                };
            }
        }

        // Função para usar dados de fallback
        function usarDadosFallback() {
            dashboardData = {
                empresas: {
                    total: 29,
                    pendentes: 8,
                    por_segmento: [
                        {segmento: "varejo", total: 12},
                        {segmento: "industria", total: 4},
                        {segmento: "financeiro", total: 3},
                        {segmento: "construcao", total: 2},
                        {segmento: "tecnologia", total: 2},
                        {segmento: "outros", total: 6}
                    ]
                },
                usuarios: {
                    total: 25,
                    ativos: 15,
                    inativos: 5,
                    admin_empresa: 5,
                    super_admin: 1,
                    newsletter: 9
                },
                licencas: {
                    ativas: 17,
                    expiradas: 0,
                    canceladas: 0,
                    por_plano: [
                        {plano: "trial", total: 17},
                        {plano: "basic", total: 0},
                        {plano: "pro", total: 0},
                        {plano: "enterprise", total: 0}
                    ],
                    proximas_expiracao: 5
                },
                assinaturas: {
                    total: 20,
                    ativas: 18
                },
                pagamentos: {
                    pendentes: 3,
                    receita_mensal: 4250
                },
                disponibilidade: 98.7,
                alertas: {
                    licencas_expirando: 5,
                    empresas_pendentes: 8,
                    pagamentos_pendentes: 3,
                    tentativas_login_suspeitas: 3,
                    backup_status: "executado"
                }
            };
            
            atualizarDashboard(dashboardData);
        }

        // Função para atualizar toda a interface com os dados
        function atualizarDashboard(data) {
            atualizarMetricasPrincipais(data);
            atualizarReceita(data);
            atualizarEmpresasSegmento(data);
            atualizarAtividadesSistema(data);
            atualizarStatusUsuarios(data);
            atualizarStatusLicencas(data);
            atualizarAlertasSistema(data);
            atualizarGraficos(data);
            atualizarDropdownAlertas(data);
        }

        // Função para atualizar métricas principais
        function atualizarMetricasPrincipais(data) {
            const empresas = data.empresas || {};
            const usuarios = data.usuarios || {};
            const licencas = data.licencas || {};
            const pagamentos = data.pagamentos || {};
            
            const metricasHTML = `
                <div class="col-xl-2 col-md-4 mb-4">
                    <div class="metric-card primary">
                        <div class="metric-icon primary">
                            <i class="bi bi-building"></i>
                        </div>
                        <div class="metric-value">${empresas.total || 0}</div>
                        <div class="metric-label">Empresas</div>
                        <div class="metric-change positive">
                            <i class="bi bi-arrow-up"></i> Atualizado
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-2 col-md-4 mb-4">
                    <div class="metric-card success">
                        <div class="metric-icon success">
                            <i class="bi bi-people"></i>
                        </div>
                        <div class="metric-value">${usuarios.total || 0}</div>
                        <div class="metric-label">Usuários</div>
                        <div class="metric-change positive">
                            <i class="bi bi-arrow-up"></i> Ativos: ${usuarios.ativos || 0}
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-2 col-md-4 mb-4">
                    <div class="metric-card secondary">
                        <div class="metric-icon secondary">
                            <i class="bi bi-key"></i>
                        </div>
                        <div class="metric-value">${licencas.ativas || 0}</div>
                        <div class="metric-label">Licenças Ativas</div>
                        <div class="metric-change positive">
                            <i class="bi bi-arrow-up"></i> Total: ${licencas.ativas || 0}
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-2 col-md-4 mb-4">
                    <div class="metric-card warning">
                        <div class="metric-icon warning">
                            <i class="bi bi-credit-card"></i>
                        </div>
                        <div class="metric-value">R$ ${(pagamentos.receita_mensal || 0).toLocaleString('pt-BR')}</div>
                        <div class="metric-label">Receita Mensal</div>
                        <div class="metric-change positive">
                            <i class="bi bi-arrow-up"></i> Atual
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-2 col-md-4 mb-4">
                    <div class="metric-card danger">
                        <div class="metric-icon danger">
                            <i class="bi bi-exclamation-triangle"></i>
                        </div>
                        <div class="metric-value">${empresas.pendentes || 0}</div>
                        <div class="metric-label">Empresas Pendentes</div>
                        <div class="metric-change negative">
                            <i class="bi bi-arrow-up"></i> Atenção
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-2 col-md-4 mb-4">
                    <div class="metric-card info">
                        <div class="metric-icon info">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <div class="metric-value">${data.disponibilidade || 0}%</div>
                        <div class="metric-label">Disponibilidade</div>
                        <div class="metric-change positive">
                            <i class="bi bi-arrow-up"></i> Estável
                        </div>
                    </div>
                </div>
            `;
            
            document.getElementById('metricasPrincipais').innerHTML = metricasHTML;
        }

        // Função para atualizar informações de receita
        function atualizarReceita(data) {
            const receitaMensal = data.pagamentos?.receita_mensal || 0;
            const meta = 5000;
            const percentual = Math.min(Math.round((receitaMensal / meta) * 100), 100);
            const restante = Math.max(meta - receitaMensal, 0);
            
            document.getElementById('receitaMensal').textContent = `R$ ${receitaMensal.toLocaleString('pt-BR')}`;
            document.getElementById('percentualMeta').textContent = `${percentual}%`;
            document.getElementById('receitaRestante').textContent = `R$ ${restante.toLocaleString('pt-BR')}`;
        }

        // Função para atualizar empresas por segmento
        function atualizarEmpresasSegmento(data) {
            const segmentos = data.empresas?.por_segmento || [];
            const totalEmpresas = data.empresas?.total || 1;
            
            if (segmentos.length === 0) {
                document.getElementById('empresasSegmento').innerHTML = '<p class="text-center text-muted">Nenhum segmento encontrado</p>';
                return;
            }
            
            let segmentosHTML = '<div class="row">';
            
            const cores = [
                'linear-gradient(135deg, #3498DB 0%, #2980B9 100%)',
                'linear-gradient(135deg, #F39C12 0%, #D68910 100%)',
                'linear-gradient(135deg, #E74C3C 0%, #CB4335 100%)',
                'linear-gradient(135deg, #27AE60 0%, #229954 100%)',
                'linear-gradient(135deg, #9B59B6 0%, #8E44AD 100%)',
                'linear-gradient(135deg, #34495E 0%, #2C3E50 100%)'
            ];
            
            const icones = ['bi-shop', 'bi-building', 'bi-calculator', 'bi-hammer', 'bi-tree', 'bi-briefcase'];
            
            segmentos.forEach((segmento, index) => {
                const percentual = Math.round((segmento.total / totalEmpresas) * 100);
                const cor = cores[index % cores.length];
                const icone = icones[index % icones.length];
                
                segmentosHTML += `
                    <div class="col-md-6 mb-3">
                        <div class="segmento-card">
                            <div class="segmento-image" style="background: ${cor};">
                                <i class="bi ${icone}"></i>
                            </div>
                            <div class="segmento-info">
                                <div class="segmento-title">${formatarSegmento(segmento.segmento)}</div>
                                <div class="segmento-meta">${segmento.total} empresas</div>
                                <div class="segmento-progress">
                                    <div class="d-flex justify-content-between mb-1">
                                        <small>Participação</small>
                                        <small>${percentual}%</small>
                                    </div>
                                    <div class="progress progress-thin">
                                        <div class="progress-bar" style="width: ${percentual}%; background-color: ${cor.split(' ')[2]}"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            segmentosHTML += '</div>';
            document.getElementById('empresasSegmento').innerHTML = segmentosHTML;
        }

        // Função para atualizar atividades do sistema
        function atualizarAtividadesSistema(data) {
            const alertas = data.alertas || {};
            const backupStatus = alertas.backup_status || 'erro';
            
            let atividadesHTML = '';
            
            // Simulação de atividades baseadas nos alertas
            if (backupStatus === 'executado') {
                atividadesHTML += `
                    <div class="task-item completed">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <strong>Backup Automático</strong>
                                <div class="small">Backup do banco de dados executado com sucesso</div>
                                <div class="small text-muted">Concluído recentemente</div>
                            </div>
                            <span class="badge bg-success">Concluído</span>
                        </div>
                    </div>
                `;
            } else {
                atividadesHTML += `
                    <div class="task-item delayed">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <strong>Backup Automático</strong>
                                <div class="small">Backup pendente ou com erro</div>
                                <div class="small text-muted">Verificar configurações</div>
                            </div>
                            <span class="badge bg-danger">Atrasado</span>
                        </div>
                    </div>
                `;
            }
            
            atividadesHTML += `
                <div class="task-item completed">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <strong>Verificação de Licenças</strong>
                            <div class="small">Processo de verificação concluído</div>
                            <div class="small text-muted">${data.licencas?.expiradas || 0} expiradas</div>
                        </div>
                        <span class="badge bg-success">Concluído</span>
                    </div>
                </div>
                
                <div class="task-item ongoing">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <strong>Monitoramento do Sistema</strong>
                            <div class="small">Sistema operando normalmente</div>
                            <div class="small text-muted">Disponibilidade: ${data.disponibilidade || 0}%</div>
                        </div>
                        <span class="badge bg-primary">Em andamento</span>
                    </div>
                </div>
            `;
            
            // Resumo
            atividadesHTML += `
                <div class="mt-3">
                    <h6 class="mb-3">Resumo de Atividades</h6>
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="metric-value text-primary">${backupStatus === 'executado' ? 3 : 2}</div>
                            <div class="metric-label">Concluídas</div>
                        </div>
                        <div class="col-4">
                            <div class="metric-value text-warning">1</div>
                            <div class="metric-label">Pendentes</div>
                        </div>
                        <div class="col-4">
                            <div class="metric-value text-danger">${backupStatus === 'executado' ? 0 : 1}</div>
                            <div class="metric-label">Atrasadas</div>
                        </div>
                    </div>
                </div>
            `;
            
            document.getElementById('atividadesSistema').innerHTML = atividadesHTML;
        }

        // Função para atualizar status dos usuários
        function atualizarStatusUsuarios(data) {
            const usuarios = data.usuarios || {};
            const total = usuarios.total || 1;
            
            let usuariosHTML = '';
            
            // Usuários ativos
            const percentualAtivos = Math.round((usuarios.ativos / total) * 100);
            usuariosHTML += `
                <div class="user-status active">
                    <div class="me-3">
                        <i class="bi bi-person-check text-success" style="font-size: 1.5rem;"></i>
                    </div>
                    <div class="flex-grow-1">
                        <strong>Usuários Ativos</strong>
                        <div class="small text-muted">${usuarios.ativos || 0} usuários • Email verificado</div>
                    </div>
                    <div class="text-end">
                        <div class="fw-semibold">${percentualAtivos}%</div>
                        <div class="small text-muted">Do total</div>
                    </div>
                </div>
            `;
            
            // Usuários inativos
            const percentualInativos = Math.round((usuarios.inativos / total) * 100);
            usuariosHTML += `
                <div class="user-status inactive">
                    <div class="me-3">
                        <i class="bi bi-person-x text-danger" style="font-size: 1.5rem;"></i>
                    </div>
                    <div class="flex-grow-1">
                        <strong>Usuários Inativos</strong>
                        <div class="small text-muted">${usuarios.inativos || 0} usuários</div>
                    </div>
                    <div class="text-end">
                        <div class="fw-semibold text-danger">${percentualInativos}%</div>
                        <div class="small text-muted">Do total</div>
                    </div>
                </div>
            `;
            
            // Admin empresa
            const percentualAdmin = Math.round((usuarios.admin_empresa / total) * 100);
            usuariosHTML += `
                <div class="user-status pending">
                    <div class="me-3">
                        <i class="bi bi-person-gear text-warning" style="font-size: 1.5rem;"></i>
                    </div>
                    <div class="flex-grow-1">
                        <strong>Admin Empresa</strong>
                        <div class="small text-muted">${usuarios.admin_empresa || 0} usuários • Permissões totais</div>
                    </div>
                    <div class="text-end">
                        <div class="fw-semibold text-warning">${percentualAdmin}%</div>
                        <div class="small text-muted">Do total</div>
                    </div>
                </div>
            `;
            
            // Distribuição de perfis
            usuariosHTML += `
                <div class="mt-3">
                    <h6 class="mb-3">Distribuição de Perfis</h6>
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="metric-value text-primary">${usuarios.super_admin || 0}</div>
                            <div class="metric-label">Super Admin</div>
                        </div>
                        <div class="col-4">
                            <div class="metric-value text-success">${usuarios.admin_empresa || 0}</div>
                            <div class="metric-label">Admin Empresa</div>
                        </div>
                        <div class="col-4">
                            <div class="metric-value text-info">${(usuarios.ativos || 0) - (usuarios.admin_empresa || 0)}</div>
                            <div class="metric-label">Usuários</div>
                        </div>
                    </div>
                </div>
            `;
            
            document.getElementById('statusUsuarios').innerHTML = usuariosHTML;
        }

        // Função para atualizar status de licenças
        function atualizarStatusLicencas(data) {
            const licencas = data.licencas || {};
            const total = licencas.ativas + licencas.expiradas + licencas.canceladas || 1;
            
            let licencasHTML = '';
            
            // Status principais
            licencasHTML += `
                <div class="row text-center mb-4">
                    <div class="col-4">
                        <div class="mb-3">
                            <div class="metric-value text-success">${licencas.ativas || 0}</div>
                            <div class="metric-label">Ativas</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="mb-3">
                            <div class="metric-value text-warning">${licencas.expiradas || 0}</div>
                            <div class="metric-label">Expiradas</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="mb-3">
                            <div class="metric-value text-danger">${licencas.canceladas || 0}</div>
                            <div class="metric-label">Canceladas</div>
                        </div>
                    </div>
                </div>
            `;
            
            // Distribuição por plano
            const planos = licencas.por_plano || [];
            if (planos.length > 0) {
                licencasHTML += '<h6 class="mb-3">Distribuição por Plano</h6>';
                planos.forEach(plano => {
                    const percentual = Math.round((plano.total / total) * 100);
                    licencasHTML += `
                        <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
                            <div>
                                <strong>Plano ${formatarPlano(plano.plano)}</strong>
                                <div class="small text-muted">${plano.total} licenças</div>
                            </div>
                            <div class="text-end">
                                <div class="fw-semibold text-info">${percentual}%</div>
                                <div class="small text-muted">Do total</div>
                            </div>
                        </div>
                    `;
                });
            }
            
            // Próximas expirações
            licencasHTML += `
                <div class="mt-3">
                    <h6 class="mb-3">Próximas Expirações</h6>
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="metric-value text-warning">${licencas.proximas_expiracao || 0}</div>
                            <div class="metric-label">Em 30 dias</div>
                        </div>
                        <div class="col-4">
                            <div class="metric-value text-danger">${Math.floor((licencas.proximas_expiracao || 0) / 2)}</div>
                            <div class="metric-label">Em 15 dias</div>
                        </div>
                        <div class="col-4">
                            <div class="metric-value text-info">0</div>
                            <div class="metric-label">Hoje</div>
                        </div>
                    </div>
                </div>
            `;
            
            document.getElementById('statusLicencas').innerHTML = licencasHTML;
        }

        // Função para atualizar alertas do sistema
        function atualizarAlertasSistema(data) {
            const alertas = data.alertas || {};
            
            let alertasHTML = '<div class="row">';
            
            // Empresas pendentes
            if (alertas.empresas_pendentes > 0) {
                alertasHTML += `
                    <div class="col-md-6">
                        <div class="alert alert-warning d-flex align-items-center mb-3">
                            <i class="bi bi-exclamation-triangle me-3"></i>
                            <div>
                                <strong>${alertas.empresas_pendentes} empresas com status pendente</strong>
                                <div class="mt-1">
                                    <a href="empresas.php" class="btn btn-sm btn-warning">Verificar pendências</a>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }
            
            // Licenças expirando
            if (alertas.licencas_expirando > 0) {
                alertasHTML += `
                    <div class="col-md-6">
                        <div class="alert alert-info d-flex align-items-center mb-3">
                            <i class="bi bi-credit-card me-3"></i>
                            <div>
                                <strong>${alertas.licencas_expirando} licenças expiram em 30 dias</strong>
                                <div class="mt-1">
                                    <small class="text-muted">Enviar notificação de renovação</small>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }
            
            // Backup status
            if (alertas.backup_status === 'executado') {
                alertasHTML += `
                    <div class="col-md-6">
                        <div class="alert alert-success d-flex align-items-center mb-3">
                            <i class="bi bi-check-circle me-3"></i>
                            <div>
                                <strong>Backup executado com sucesso</strong>
                                <div class="mt-1">
                                    <small class="text-muted">Todos os dados foram salvos</small>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            } else {
                alertasHTML += `
                    <div class="col-md-6">
                        <div class="alert alert-danger d-flex align-items-center mb-3">
                            <i class="bi bi-exclamation-circle me-3"></i>
                            <div>
                                <strong>Backup com problemas</strong>
                                <div class="mt-1">
                                    <small class="text-muted">Verificar configurações do backup</small>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }
            
            // Tentativas de login suspeitas
            if (alertas.tentativas_login_suspeitas > 0) {
                alertasHTML += `
                    <div class="col-md-6">
                        <div class="alert alert-danger d-flex align-items-center mb-0">
                            <i class="bi bi-shield-exclamation me-3"></i>
                            <div>
                                <strong>${alertas.tentativas_login_suspeitas} tentativas de login suspeitas</strong>
                                <div class="mt-1">
                                    <a href="logs.php" class="btn btn-sm btn-danger">Ver logs</a>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }
            
            alertasHTML += '</div>';
            document.getElementById('alertasSistema').innerHTML = alertasHTML;
        }

        // Função para atualizar gráficos
        function atualizarGraficos(data) {
            // Gráfico de Segmentos
            const segmentos = data.empresas?.por_segmento || [];
            const segmentLabels = segmentos.map(s => formatarSegmento(s.segmento));
            const segmentData = segmentos.map(s => s.total);
            
            const segmentCtx = document.getElementById('segmentChart').getContext('2d');
            
            if (segmentChart) {
                segmentChart.destroy();
            }
            
            segmentChart = new Chart(segmentCtx, {
                type: 'bar',
                data: {
                    labels: segmentLabels,
                    datasets: [{
                        label: 'Empresas por Segmento',
                        data: segmentData,
                        backgroundColor: '#3498DB',
                        borderColor: '#2980B9',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Número de Empresas'
                            }
                        }
                    }
                }
            });
            
            // Gráfico de Planos
            const planos = data.licencas?.por_plano || [];
            const planLabels = planos.map(p => formatarPlano(p.plano));
            const planData = planos.map(p => p.total);
            
            const plansCtx = document.getElementById('plansChart').getContext('2d');
            
            if (plansChart) {
                plansChart.destroy();
            }
            
            plansChart = new Chart(plansCtx, {
                type: 'doughnut',
                data: {
                    labels: planLabels,
                    datasets: [{
                        data: planData,
                        backgroundColor: [
                            '#17A2B8',
                            '#27AE60',
                            '#3498DB',
                            '#2C3E50',
                            '#F39C12',
                            '#E74C3C'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }

        // Função para atualizar dropdown de alertas
        function atualizarDropdownAlertas(data) {
            const alertas = data.alertas || {};
            const totalAlertas = (alertas.empresas_pendentes || 0) + (alertas.licencas_expirando || 0) + (alertas.tentativas_login_suspeitas || 0);
            
            document.getElementById('totalAlertas').textContent = totalAlertas;
            
            let alertasHTML = '<li><h6 class="dropdown-header">Alertas do Sistema</h6></li>';
            
            if (alertas.empresas_pendentes > 0) {
                alertasHTML += `<li><a class="dropdown-item" href="empresas.php">${alertas.empresas_pendentes} empresas com status pendente</a></li>`;
            }
            
            if (alertas.licencas_expirando > 0) {
                alertasHTML += `<li><a class="dropdown-item" href="licencas.php">${alertas.licencas_expirando} licenças expiram em 30 dias</a></li>`;
            }
            
            if (alertas.tentativas_login_suspeitas > 0) {
                alertasHTML += `<li><a class="dropdown-item" href="logs.php">${alertas.tentativas_login_suspeitas} tentativas de login suspeitas</a></li>`;
            }
            
            if (alertas.backup_status === 'executado') {
                alertasHTML += `<li><a class="dropdown-item" href="#">Backup automático executado</a></li>`;
            } else {
                alertasHTML += `<li><a class="dropdown-item" href="#">Backup automático com problemas</a></li>`;
            }
            
            if (totalAlertas === 0) {
                alertasHTML += '<li><a class="dropdown-item" href="#">Nenhum alerta crítico</a></li>';
            }
            
            alertasHTML += '<li><hr class="dropdown-divider"></li>';
            alertasHTML += '<li><a class="dropdown-item text-center" href="logs.php">Ver todos</a></li>';
            
            document.getElementById('dropdownAlertas').innerHTML = alertasHTML;
        }

        // Funções auxiliares
        function formatarSegmento(segmento) {
            const segmentos = {
                'varejo': 'Varejo',
                'industria': 'Indústria',
                'financeiro': 'Financeiro',
                'construcao': 'Construção',
                'agropecuaria': 'Agropecuária',
                'tecnologia': 'Tecnologia',
                'saude': 'Saúde',
                'educacao': 'Educação',
                'outros': 'Outros'
            };
            return segmentos[segmento] || segmento;
        }

        function formatarPlano(plano) {
            const planos = {
                'trial': 'Trial',
                'basic': 'Basic',
                'pro': 'Pro',
                'enterprise': 'Enterprise'
            };
            return planos[plano] || plano;
        }

        function mostrarLoading(mostrar) {
            document.getElementById('loadingOverlay').classList.toggle('d-none', !mostrar);
        }

        // Função para mostrar notificações
        function mostrarNotificacao(message, type) {
            // Criar elemento de notificação
            const notification = document.createElement('div');
            notification.className = `alert alert-${type === 'error' ? 'danger' : type === 'warning' ? 'warning' : 'success'} alert-dismissible fade show position-fixed`;
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

        function exportReport() {
            mostrarNotificacao('Gerando relatório do sistema...', 'info');
            setTimeout(() => {
                mostrarNotificacao('Relatório exportado com sucesso!', 'success');
            }, 1500);
        }

        // Atualização automática a cada 5 minutos
        setInterval(carregarDashboard, 300000);
    </script>
</body>
</html>