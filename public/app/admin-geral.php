<?php
session_start();
if (!isset($_SESSION['authToken'], $_SESSION['usuario'], $_SESSION['empresa'], $_SESSION['licenca'])) {
    header('Location: login.php');
    exit;
}
$usuario = $_SESSION['usuario'];
$empresa = $_SESSION['empresa'];
$licenca = $_SESSION['licenca'];
$token = $_SESSION['authToken'];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Geral - NexusFlow</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- NexusFlow CSS -->
    <link href="assets/css/nexusflow.css" rel="stylesheet">
</head>
<body>
    <div class="main-wrapper">
        <!-- Sidebar -->
        <nav class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <div class="logo-icon">
                        <i class="bi bi-diagram-3"></i>
                    </div>
                    <div class="logo-text">NexusFlow</div>
                </div>
            </div>
            
            <div class="sidebar-nav">
                <!-- Dashboard -->
                <div class="nav-section">
                    <div class="nav-item">
                        <a href="admin-geral.html" class="nav-link active">
                            <i class="bi bi-speedometer2 nav-icon"></i>
                            <span class="nav-text">Dashboard Geral</span>
                        </a>
                    </div>
                </div>
                
                <!-- Gestão do Sistema (Super Admin) -->
                <div class="nav-section" data-role="super_admin">
                    <div class="nav-header">
                        <span class="nav-header-text">Administração</span>
                    </div>
                    <div class="nav-item">
                        <a href="gerenciar-empresas.html" class="nav-link">
                            <i class="bi bi-building nav-icon"></i>
                            <span class="nav-text">Empresas</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="gerenciar-usuarios.html" class="nav-link">
                            <i class="bi bi-people nav-icon"></i>
                            <span class="nav-text">Usuários</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="planos-assinaturas.html" class="nav-link">
                            <i class="bi bi-credit-card nav-icon"></i>
                            <span class="nav-text">Planos & Assinaturas</span>
                        </a>
                    </div>
                </div>
                
                <!-- Relatórios -->
                <div class="nav-section">
                    <div class="nav-header">
                        <span class="nav-header-text">Relatórios</span>
                    </div>
                    <div class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="bi bi-graph-up nav-icon"></i>
                            <span class="nav-text">Financeiro</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="bi bi-bar-chart nav-icon"></i>
                            <span class="nav-text">Uso da Plataforma</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="bi bi-pie-chart nav-icon"></i>
                            <span class="nav-text">Analytics</span>
                        </a>
                    </div>
                </div>
                
                <!-- Sistema -->
                <div class="nav-section">
                    <div class="nav-header">
                        <span class="nav-header-text">Sistema</span>
                    </div>
                    <div class="nav-item">
                        <a href="perfil.html" class="nav-link">
                            <i class="bi bi-person-circle nav-icon"></i>
                            <span class="nav-text">Meu Perfil</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="bi bi-gear nav-icon"></i>
                            <span class="nav-text">Configurações</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="bi bi-question-circle nav-icon"></i>
                            <span class="nav-text">Suporte</span>
                        </a>
                    </div>
                </div>
            </div>
        </nav>
        
        <!-- Conteúdo Principal -->
        <main class="main-content">
            <!-- Header -->
            <header class="main-header">
                <div class="header-left">
                    <button class="sidebar-toggle" type="button">
                        <i class="bi bi-list"></i>
                    </button>
                    
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb breadcrumb-custom">
                            <li class="breadcrumb-item active">Dashboard Geral</li>
                        </ol>
                    </nav>
                </div>
                
                <div class="header-right">
                    <!-- Notificações -->
                    <div class="dropdown">
                        <button class="btn btn-link text-dark position-relative" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-bell" style="font-size: 1.25rem;"></i>
                            <span class="badge bg-danger position-absolute translate-middle rounded-pill" style="font-size: 0.6rem; top: 8px; right: 8px;">5</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><h6 class="dropdown-header">Notificações do Sistema</h6></li>
                            <li><a class="dropdown-item" href="#">Nova empresa cadastrada</a></li>
                            <li><a class="dropdown-item" href="#">Licença expirando em 3 dias</a></li>
                            <li><a class="dropdown-item" href="#">Pagamento pendente</a></li>
                            <li><a class="dropdown-item" href="#">Novo usuário ativo</a></li>
                            <li><a class="dropdown-item" href="#">Backup concluído</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-center" href="#">Ver todas</a></li>
                        </ul>
                    </div>
                    
                    <!-- Dropdown do Usuário -->
                    <div class="dropdown user-dropdown">
                        <div class="user-avatar" data-bs-toggle="dropdown" aria-expanded="false">
                            A
                        </div>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><h6 class="dropdown-header user-name">Admin Sistema</h6></li>
                            <li><small class="dropdown-header text-muted user-email">admin@nexusflow.com</small></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="perfil.html"><i class="bi bi-person me-2"></i>Meu Perfil</a></li>
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
                        <h1 class="page-title">Dashboard Geral do Sistema</h1>
                        <p class="page-subtitle">Visão geral de todas as empresas e métricas do sistema</p>
                    </div>
                    <div>
                        <button class="btn btn-outline-primary me-2" onclick="exportReport()">
                            <i class="bi bi-download me-2"></i>Exportar Relatório
                        </button>
                        <button class="btn btn-primary" onclick="refreshDashboard()">
                            <i class="bi bi-arrow-clockwise me-2"></i>Atualizar
                        </button>
                    </div>
                </div>
                
                <!-- Métricas Principais -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="metric-card primary">
                            <div class="metric-icon primary">
                                <i class="bi bi-building"></i>
                            </div>
                            <div class="metric-value">247</div>
                            <div class="metric-label">Empresas Ativas</div>
                            <div class="metric-change positive">
                                <i class="bi bi-arrow-up"></i> +12 este mês
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="metric-card success">
                            <div class="metric-icon success">
                                <i class="bi bi-people"></i>
                            </div>
                            <div class="metric-value">3.847</div>
                            <div class="metric-label">Usuários Ativos</div>
                            <div class="metric-change positive">
                                <i class="bi bi-arrow-up"></i> +156 este mês
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="metric-card warning">
                            <div class="metric-icon warning">
                                <i class="bi bi-currency-dollar"></i>
                            </div>
                            <div class="metric-value">R$ 89.450</div>
                            <div class="metric-label">Receita Mensal</div>
                            <div class="metric-change positive">
                                <i class="bi bi-arrow-up"></i> +8.5% vs mês anterior
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="metric-card danger">
                            <div class="metric-icon danger">
                                <i class="bi bi-exclamation-triangle"></i>
                            </div>
                            <div class="metric-value">23</div>
                            <div class="metric-label">Licenças Expirando</div>
                            <div class="metric-change negative">
                                <i class="bi bi-arrow-down"></i> Próximos 30 dias
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Gráficos e Dados -->
                <div class="row mb-4">
                    <!-- Gráfico de Crescimento -->
                    <div class="col-xl-8 mb-4">
                        <div class="card-custom">
                            <div class="card-header-custom d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Crescimento de Empresas</h5>
                                <div class="btn-group btn-group-sm" role="group">
                                    <input type="radio" class="btn-check" name="period" id="period7" checked>
                                    <label class="btn btn-outline-primary" for="period7">7 dias</label>
                                    
                                    <input type="radio" class="btn-check" name="period" id="period30">
                                    <label class="btn btn-outline-primary" for="period30">30 dias</label>
                                    
                                    <input type="radio" class="btn-check" name="period" id="period90">
                                    <label class="btn btn-outline-primary" for="period90">90 dias</label>
                                </div>
                            </div>
                            <div class="card-body">
                                <canvas id="growthChart" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Distribuição por Segmento -->
                    <div class="col-xl-4 mb-4">
                        <div class="card-custom">
                            <div class="card-header-custom">
                                <h5 class="mb-0">Empresas por Segmento</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="segmentChart" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Tabelas de Dados -->
                <div class="row">
                    <!-- Empresas Recentes -->
                    <div class="col-xl-6 mb-4">
                        <div class="card-custom">
                            <div class="card-header-custom d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Empresas Cadastradas Recentemente</h5>
                                <a href="gerenciar-empresas.html" class="btn btn-sm btn-outline-primary">Ver todas</a>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-custom mb-0">
                                        <thead>
                                            <tr>
                                                <th>Empresa</th>
                                                <th>Segmento</th>
                                                <th>Status</th>
                                                <th>Data</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="me-3">
                                                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                                                TI
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <div class="fw-semibold">Tech Inovação Ltda</div>
                                                            <small class="text-muted">tech@inovacao.com</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><span class="badge bg-info">Tecnologia</span></td>
                                                <td><span class="status-badge status-active">Ativa</span></td>
                                                <td>Hoje</td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="me-3">
                                                            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                                                CV
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <div class="fw-semibold">Construtora Vanguarda</div>
                                                            <small class="text-muted">contato@vanguarda.com</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><span class="badge bg-warning">Construção</span></td>
                                                <td><span class="status-badge status-pending">Pendente</span></td>
                                                <td>Ontem</td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="me-3">
                                                            <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                                                MF
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <div class="fw-semibold">Moda & Fashion</div>
                                                            <small class="text-muted">vendas@modafashion.com</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><span class="badge bg-secondary">Varejo</span></td>
                                                <td><span class="status-badge status-active">Ativa</span></td>
                                                <td>2 dias</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Alertas do Sistema -->
                    <div class="col-xl-6 mb-4">
                        <div class="card-custom">
                            <div class="card-header-custom">
                                <h5 class="mb-0">Alertas e Notificações</h5>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-warning d-flex align-items-center mb-3">
                                    <i class="bi bi-exclamation-triangle me-3"></i>
                                    <div>
                                        <strong>23 licenças</strong> expiram nos próximos 30 dias
                                        <div class="mt-1">
                                            <a href="planos-assinaturas.html" class="btn btn-sm btn-warning">Ver detalhes</a>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="alert alert-info d-flex align-items-center mb-3">
                                    <i class="bi bi-info-circle me-3"></i>
                                    <div>
                                        <strong>Backup automático</strong> concluído com sucesso
                                        <div class="mt-1">
                                            <small class="text-muted">Última execução: hoje às 03:00</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="alert alert-success d-flex align-items-center mb-3">
                                    <i class="bi bi-check-circle me-3"></i>
                                    <div>
                                        <strong>Sistema atualizado</strong> para versão 2.1.3
                                        <div class="mt-1">
                                            <small class="text-muted">Novas funcionalidades disponíveis</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="alert alert-danger d-flex align-items-center mb-0">
                                    <i class="bi bi-shield-exclamation me-3"></i>
                                    <div>
                                        <strong>5 tentativas</strong> de login suspeitas detectadas
                                        <div class="mt-1">
                                            <a href="#" class="btn btn-sm btn-danger">Investigar</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- NexusFlow JS -->
    <script src="assets/js/nexusflow.js"></script>
    
    <script>
        // Logoff: destruir sessão PHP e redirecionar
        document.addEventListener('DOMContentLoaded', function() {
            var logoutBtn = document.getElementById('logoutBtn');
            if (logoutBtn) {
                logoutBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    fetch('logout.php', { method: 'POST' })
                        .then(() => { window.location.href = 'login.php'; });
                });
            }
        });
        // Configurar gráficos
        document.addEventListener('DOMContentLoaded', function() {
            // Gráfico de Crescimento
            const growthCtx = document.getElementById('growthChart').getContext('2d');
            const growthChart = new Chart(growthCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun'],
                    datasets: [{
                        label: 'Empresas Cadastradas',
                        data: [12, 19, 15, 25, 22, 30],
                        borderColor: '#3498DB',
                        backgroundColor: 'rgba(52, 152, 219, 0.1)',
                        tension: 0.4,
                        fill: true
                    }, {
                        label: 'Usuários Ativos',
                        data: [65, 89, 102, 134, 156, 189],
                        borderColor: '#2ECC71',
                        backgroundColor: 'rgba(46, 204, 113, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
            
            // Gráfico de Segmentos
            const segmentCtx = document.getElementById('segmentChart').getContext('2d');
            const segmentChart = new Chart(segmentCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Varejo', 'Tecnologia', 'Construção', 'Financeiro', 'Marketing', 'Outros'],
                    datasets: [{
                        data: [45, 32, 28, 21, 18, 103],
                        backgroundColor: [
                            '#3498DB',
                            '#2ECC71',
                            '#F39C12',
                            '#E74C3C',
                            '#9B59B6',
                            '#95A5A6'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        }
                    }
                }
            });
        });
        
        // Funções específicas da página
        function exportReport() {
            nexusFlow.showNotification('Exportando relatório...', 'info');
            setTimeout(() => {
                nexusFlow.showNotification('Relatório exportado com sucesso!', 'success');
            }, 2000);
        }
        
        function refreshDashboard() {
            nexusFlow.showNotification('Atualizando dashboard...', 'info');
            setTimeout(() => {
                nexusFlow.showNotification('Dashboard atualizado!', 'success');
                location.reload();
            }, 1000);
        }
        
        // Validação de autenticação feita apenas em PHP (session_start no topo do arquivo)
        
        // Definir papel como super admin
        localStorage.setItem('userRole', 'super_admin');
    </script>
</body>
</html>
