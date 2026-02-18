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
$segmento = $_SESSION['segmento'];

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard SAS Multi</title>
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
                        <span class="badge bg-danger position-absolute translate-middle rounded-pill" style="font-size: 0.6rem; top: 8px; right: 8px;">12</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><h6 class="dropdown-header">Alertas do Sistema</h6></li>
                        <li><a class="dropdown-item" href="#">5 licenças expiram em 7 dias</a></li>
                        <li><a class="dropdown-item" href="#">3 empresas com pagamento pendente</a></li>
                        <li><a class="dropdown-item" href="#">Backup automático executado</a></li>
                        <li><a class="dropdown-item" href="#">Tentativa de acesso suspeita</a></li>
                        <li><a class="dropdown-item" href="#">Novo usuário cadastrado</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-center" href="#">Ver todos</a></li>
                    </ul>
                </div>
                
                <!-- Dropdown do Usuário -->
                <div class="dropdown user-dropdown">
                    <div class="user-avatar" data-bs-toggle="dropdown" aria-expanded="false">
                        A
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><h6 class="dropdown-header user-name">Administrador do Sistema</h6></li>
                        <li><small class="dropdown-header text-muted user-email">admin@sasmulti.com</small></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="perfil.html"><i class="bi bi-person me-2"></i>Meu Perfil</a></li>
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
                    <button class="btn btn-primary" onclick="refreshDashboard()">
                        <i class="bi bi-arrow-clockwise me-2"></i>Atualizar
                    </button>
                </div>
            </div>
            
            <!-- Métricas Principais -->
            <div class="row mb-4">
                <div class="col-xl-2 col-md-4 mb-4">
                    <div class="metric-card primary">
                        <div class="metric-icon primary">
                            <i class="bi bi-building"></i>
                        </div>
                        <div class="metric-value">29</div>
                        <div class="metric-label">Empresas</div>
                        <div class="metric-change positive">
                            <i class="bi bi-arrow-up"></i> +5 este mês
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-2 col-md-4 mb-4">
                    <div class="metric-card success">
                        <div class="metric-icon success">
                            <i class="bi bi-people"></i>
                        </div>
                        <div class="metric-value">25</div>
                        <div class="metric-label">Usuários</div>
                        <div class="metric-change positive">
                            <i class="bi bi-arrow-up"></i> +3 esta semana
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-2 col-md-4 mb-4">
                    <div class="metric-card secondary">
                        <div class="metric-icon secondary">
                            <i class="bi bi-key"></i>
                        </div>
                        <div class="metric-value">17</div>
                        <div class="metric-label">Licenças Ativas</div>
                        <div class="metric-change positive">
                            <i class="bi bi-arrow-up"></i> +2 hoje
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-2 col-md-4 mb-4">
                    <div class="metric-card warning">
                        <div class="metric-icon warning">
                            <i class="bi bi-credit-card"></i>
                        </div>
                        <div class="metric-value">R$ 4.250</div>
                        <div class="metric-label">Receita Mensal</div>
                        <div class="metric-change positive">
                            <i class="bi bi-arrow-up"></i> +12% vs mês anterior
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-2 col-md-4 mb-4">
                    <div class="metric-card danger">
                        <div class="metric-icon danger">
                            <i class="bi bi-exclamation-triangle"></i>
                        </div>
                        <div class="metric-value">8</div>
                        <div class="metric-label">Empresas Pendentes</div>
                        <div class="metric-change negative">
                            <i class="bi bi-arrow-up"></i> +2 esta semana
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-2 col-md-4 mb-4">
                    <div class="metric-card info">
                        <div class="metric-icon info">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <div class="metric-value">98.7%</div>
                        <div class="metric-label">Disponibilidade</div>
                        <div class="metric-change positive">
                            <i class="bi bi-arrow-up"></i> +0.3% este mês
                        </div>
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
                            <div class="btn-group btn-group-sm" role="group">
                                <input type="radio" class="btn-check" name="period" id="period7" checked>
                                <label class="btn btn-outline-primary" for="period7">Atual</label>
                                
                                <input type="radio" class="btn-check" name="period" id="period30">
                                <label class="btn btn-outline-primary" for="period30">Trimestre</label>
                                
                                <input type="radio" class="btn-check" name="period" id="period90">
                                <label class="btn btn-outline-primary" for="period90">Anual</label>
                            </div>
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
                            <div class="plan-badge plan-pro">P</div>
                        </div>
                        <div class="text-center mb-3">
                            <div class="metric-value">R$ 4.250</div>
                            <div class="metric-label">Meta: R$ 5.000</div>
                        </div>
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="mb-2">
                                    <i class="bi bi-arrow-up-circle"></i>
                                    <div class="small">85%</div>
                                    <div class="small">Meta</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="mb-2">
                                    <i class="bi bi-graph-up"></i>
                                    <div class="small">+12%</div>
                                    <div class="small">Crescimento</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="mb-2">
                                    <i class="bi bi-cash-coin"></i>
                                    <div class="small">R$ 850</div>
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
                            <a href="empresas.html" class="btn btn-sm btn-outline-primary">Ver todas</a>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="segmento-card">
                                        <div class="segmento-image">
                                            <i class="bi bi-shop"></i>
                                        </div>
                                        <div class="segmento-info">
                                            <div class="segmento-title">Varejo</div>
                                            <div class="segmento-meta">12 empresas • 65% ativas</div>
                                            <div class="segmento-progress">
                                                <div class="d-flex justify-content-between mb-1">
                                                    <small>Participação</small>
                                                    <small>41%</small>
                                                </div>
                                                <div class="progress progress-thin">
                                                    <div class="progress-bar bg-primary" style="width: 41%"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <div class="segmento-card">
                                        <div class="segmento-image" style="background: linear-gradient(135deg, #F39C12 0%, #D68910 100%);">
                                            <i class="bi bi-building"></i>
                                        </div>
                                        <div class="segmento-info">
                                            <div class="segmento-title">Indústria</div>
                                            <div class="segmento-meta">4 empresas • 75% ativas</div>
                                            <div class="segmento-progress">
                                                <div class="d-flex justify-content-between mb-1">
                                                    <small>Participação</small>
                                                    <small>14%</small>
                                                </div>
                                                <div class="progress progress-thin">
                                                    <div class="progress-bar bg-warning" style="width: 14%"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <div class="segmento-card">
                                        <div class="segmento-image" style="background: linear-gradient(135deg, #E74C3C 0%, #CB4335 100%);">
                                            <i class="bi bi-calculator"></i>
                                        </div>
                                        <div class="segmento-info">
                                            <div class="segmento-title">Financeiro</div>
                                            <div class="segmento-meta">3 empresas • 100% ativas</div>
                                            <div class="segmento-progress">
                                                <div class="d-flex justify-content-between mb-1">
                                                    <small>Participação</small>
                                                    <small>10%</small>
                                                </div>
                                                <div class="progress progress-thin">
                                                    <div class="progress-bar bg-danger" style="width: 10%"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <div class="segmento-card">
                                        <div class="segmento-image" style="background: linear-gradient(135deg, #27AE60 0%, #229954 100%);">
                                            <i class="bi bi-hammer"></i>
                                        </div>
                                        <div class="segmento-info">
                                            <div class="segmento-title">Construção</div>
                                            <div class="segmento-meta">2 empresas • 50% ativas</div>
                                            <div class="segmento-progress">
                                                <div class="d-flex justify-content-between mb-1">
                                                    <small>Participação</small>
                                                    <small>7%</small>
                                                </div>
                                                <div class="progress progress-thin">
                                                    <div class="progress-bar bg-success" style="width: 7%"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
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
                            <a href="logs.html" class="btn btn-sm btn-outline-primary">Ver logs</a>
                        </div>
                        <div class="card-body">
                            <div class="task-item ongoing">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>Backup Automático</strong>
                                        <div class="small">Executando backup do banco de dados</div>
                                        <div class="small text-muted">Iniciado às 02:00 • 65% concluído</div>
                                    </div>
                                    <span class="badge bg-primary">Em andamento</span>
                                </div>
                            </div>
                            
                            <div class="task-item completed">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>Verificação de Licenças</strong>
                                        <div class="small">Processo de verificação concluído</div>
                                        <div class="small text-muted">Concluído às 01:30 • 0 expiradas</div>
                                    </div>
                                    <span class="badge bg-success">Concluído</span>
                                </div>
                            </div>
                            
                            <div class="task-item delayed">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>Atualização de Segurança</strong>
                                        <div class="small">Pendente para 3 empresas</div>
                                        <div class="small text-muted">Aguardando janela de manutenção</div>
                                    </div>
                                    <span class="badge bg-danger">Atrasado</span>
                                </div>
                            </div>
                            
                            <div class="task-item completed">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>Envio de Newsletter</strong>
                                        <div class="small">Newsletter mensal enviada</div>
                                        <div class="small text-muted">Concluído às 18:00 • 25 destinatários</div>
                                    </div>
                                    <span class="badge bg-success">Concluído</span>
                                </div>
                            </div>
                            
                            <div class="mt-3">
                                <h6 class="mb-3">Resumo de Atividades</h6>
                                <div class="row text-center">
                                    <div class="col-4">
                                        <div class="metric-value text-primary">18</div>
                                        <div class="metric-label">Concluídas</div>
                                    </div>
                                    <div class="col-4">
                                        <div class="metric-value text-warning">2</div>
                                        <div class="metric-label">Pendentes</div>
                                    </div>
                                    <div class="col-4">
                                        <div class="metric-value text-danger">1</div>
                                        <div class="metric-label">Atrasadas</div>
                                    </div>
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
                            <a href="usuarios.html" class="btn btn-sm btn-outline-primary">Ver usuários</a>
                        </div>
                        <div class="card-body">
                            <div class="user-status active">
                                <div class="me-3">
                                    <i class="bi bi-person-check text-success" style="font-size: 1.5rem;"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <strong>Usuários Ativos</strong>
                                    <div class="small text-muted">15 usuários • Email verificado</div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-semibold">60%</div>
                                    <div class="small text-muted">Do total</div>
                                </div>
                            </div>
                            
                            <div class="user-status inactive">
                                <div class="me-3">
                                    <i class="bi bi-person-x text-danger" style="font-size: 1.5rem;"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <strong>Usuários Inativos</strong>
                                    <div class="small text-muted">5 usuários • Email não verificado</div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-semibold text-danger">20%</div>
                                    <div class="small text-muted">Do total</div>
                                </div>
                            </div>
                            
                            <div class="user-status pending">
                                <div class="me-3">
                                    <i class="bi bi-person-dash text-warning" style="font-size: 1.5rem;"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <strong>Admin Empresa</strong>
                                    <div class="small text-muted">5 usuários • Permissões totais</div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-semibold text-warning">20%</div>
                                    <div class="small text-muted">Do total</div>
                                </div>
                            </div>
                            
                            <div class="mt-3">
                                <h6 class="mb-3">Distribuição de Perfis</h6>
                                <div class="row text-center">
                                    <div class="col-4">
                                        <div class="metric-value text-primary">1</div>
                                        <div class="metric-label">Super Admin</div>
                                    </div>
                                    <div class="col-4">
                                        <div class="metric-value text-success">15</div>
                                        <div class="metric-label">Admin Empresa</div>
                                    </div>
                                    <div class="col-4">
                                        <div class="metric-value text-info">9</div>
                                        <div class="metric-label">Usuários</div>
                                    </div>
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
                            <a href="licencas.html" class="btn btn-sm btn-outline-primary">Ver licenças</a>
                        </div>
                        <div class="card-body">
                            <div class="row text-center mb-4">
                                <div class="col-4">
                                    <div class="mb-3">
                                        <div class="metric-value text-success">17</div>
                                        <div class="metric-label">Ativas</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="mb-3">
                                        <div class="metric-value text-warning">0</div>
                                        <div class="metric-label">Expiradas</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="mb-3">
                                        <div class="metric-value text-danger">0</div>
                                        <div class="metric-label">Canceladas</div>
                                    </div>
                                </div>
                            </div>
                            
                            <h6 class="mb-3">Distribuição por Plano</h6>
                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
                                <div>
                                    <strong>Plano Trial</strong>
                                    <div class="small text-muted">17 licenças • Expira em 90 dias</div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-semibold text-info">100%</div>
                                    <div class="small text-muted">Do total</div>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
                                <div>
                                    <strong>Plano Basic</strong>
                                    <div class="small text-muted">0 licenças • R$ 99/mês</div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-semibold text-success">0%</div>
                                    <div class="small text-muted">Do total</div>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
                                <div>
                                    <strong>Plano Pro</strong>
                                    <div class="small text-muted">0 licenças • R$ 199/mês</div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-semibold text-primary">0%</div>
                                    <div class="small text-muted">Do total</div>
                                </div>
                            </div>
                            
                            <div class="mt-3">
                                <h6 class="mb-3">Próximas Expirações</h6>
                                <div class="row text-center">
                                    <div class="col-4">
                                        <div class="metric-value text-warning">5</div>
                                        <div class="metric-label">Em 30 dias</div>
                                    </div>
                                    <div class="col-4">
                                        <div class="metric-value text-danger">3</div>
                                        <div class="metric-label">Em 15 dias</div>
                                    </div>
                                    <div class="col-4">
                                        <div class="metric-value text-info">0</div>
                                        <div class="metric-label">Hoje</div>
                                    </div>
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
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="alert alert-warning d-flex align-items-center mb-3">
                                        <i class="bi bi-exclamation-triangle me-3"></i>
                                        <div>
                                            <strong>8 empresas com status pendente</strong>
                                            <div class="mt-1">
                                                <a href="#" class="btn btn-sm btn-warning">Verificar pendências</a>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="alert alert-info d-flex align-items-center mb-3">
                                        <i class="bi bi-credit-card me-3"></i>
                                        <div>
                                            <strong>5 licenças expiram em 30 dias</strong>
                                            <div class="mt-1">
                                                <small class="text-muted">Enviar notificação de renovação</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
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
                                    
                                    <div class="alert alert-danger d-flex align-items-center mb-0">
                                        <i class="bi bi-shield-exclamation me-3"></i>
                                        <div>
                                            <strong>3 tentativas de login suspeitas</strong>
                                            <div class="mt-1">
                                                <a href="logs.html" class="btn btn-sm btn-danger">Ver logs</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- NexusFlow JS -->
    <script src="assets/js/nexusflow.js"></script>
    
    <script>
        // Logoff
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

        // Gráficos específicos do sistema SaaS
        document.addEventListener('DOMContentLoaded', function() {
            // Gráfico de Segmentos
            const segmentCtx = document.getElementById('segmentChart').getContext('2d');
            const segmentChart = new Chart(segmentCtx, {
                type: 'bar',
                data: {
                    labels: ['Varejo', 'Indústria', 'Financeiro', 'Construção', 'Agropecuária', 'Outros'],
                    datasets: [{
                        label: 'Empresas por Segmento',
                        data: [12, 4, 3, 2, 2, 6],
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
            const plansCtx = document.getElementById('plansChart').getContext('2d');
            const plansChart = new Chart(plansCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Trial', 'Basic', 'Pro', 'Enterprise'],
                    datasets: [{
                        data: [17, 0, 0, 0],
                        backgroundColor: [
                            '#17A2B8',
                            '#27AE60',
                            '#3498DB',
                            '#2C3E50'
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
        });
        
        function exportReport() {
            nexusFlow.showNotification('Gerando relatório do sistema...', 'info');
            setTimeout(() => {
                nexusFlow.showNotification('Relatório exportado com sucesso!', 'success');
            }, 1500);
        }
        
        function refreshDashboard() {
            nexusFlow.showNotification('Atualizando dados do sistema...', 'info');
            setTimeout(() => {
                nexusFlow.showNotification('Dados atualizados!', 'success');
            }, 1000);
        }
        
        // Definir papel como super admin
        localStorage.setItem('userRole', 'super_admin');
        
        // Simulação de atualização em tempo real de métricas
        setInterval(function() {
            // Simular atualização de métricas (apenas visual)
            const empresasElement = document.querySelector('.metric-card.primary .metric-value');
            if (empresasElement) {
                const currentValue = parseInt(empresasElement.textContent);
                const randomChange = Math.random() > 0.7 ? 1 : 0;
                empresasElement.textContent = currentValue + randomChange;
            }
        }, 30000); // Atualiza a cada 30 segundos
    </script>
</body>
</html>