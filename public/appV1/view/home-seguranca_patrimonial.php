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
    <title>Dashboard Segurança Patrimonial</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #2C3E50;
            --secondary-color: #3498DB;
            --warning-color: #F39C12;
            --danger-color: #E74C3C;
            --success-color: #27AE60;
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
        .metric-card.success::before { background: var(--success-color); }
        .metric-card.warning::before { background: var(--warning-color); }
        .metric-card.danger::before { background: var(--danger-color); }
        
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
        
        .metric-icon.primary { background: rgba(44, 62, 80, 0.1); color: var(--primary-color); }
        .metric-icon.success { background: rgba(39, 174, 96, 0.1); color: var(--success-color); }
        .metric-icon.warning { background: rgba(243, 156, 18, 0.1); color: var(--warning-color); }
        .metric-icon.danger { background: rgba(231, 76, 60, 0.1); color: var(--danger-color); }
        
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
        .status-warning { background: rgba(231, 76, 60, 0.1); color: var(--danger-color); }
        
        .page-title {
            font-weight: 700;
            color: var(--primary-color);
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
        
        .site-card {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 15px;
            transition: transform 0.2s;
        }
        
        .site-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .site-image {
            height: 120px;
            background: linear-gradient(135deg, #2C3E50 0%, #34495E 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
        }
        
        .site-info {
            padding: 15px;
        }
        
        .site-title {
            font-size: 1rem;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .site-meta {
            font-size: 0.85rem;
            color: #6c757d;
            margin-bottom: 10px;
        }
        
        .site-progress {
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
        
        .device-status {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 8px;
            background: #f8f9fa;
        }
        
        .device-status.online { border-left: 4px solid var(--success-color); }
        .device-status.offline { border-left: 4px solid var(--danger-color); }
        .device-status.warning { border-left: 4px solid var(--warning-color); }
        
        .incident-card {
            background: linear-gradient(135deg, #E74C3C 0%, #C0392B 100%);
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
        }
        
        .progress-thin {
            height: 8px;
        }
        
        .security-level {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            font-weight: bold;
            color: white;
        }
        
        .level-high { background: var(--danger-color); }
        .level-medium { background: var(--warning-color); }
        .level-low { background: var(--success-color); }
        
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
                        <li class="breadcrumb-item active">Segurança Patrimonial</li>
                    </ol>
                </nav>
            </div>
            
            <div class="header-right">
                <!-- Notificações -->
                <div class="dropdown">
                    <button class="btn btn-link text-dark position-relative" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-bell" style="font-size: 1.25rem;"></i>
                        <span class="badge bg-danger position-absolute translate-middle rounded-pill" style="font-size: 0.6rem; top: 8px; right: 8px;">8</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><h6 class="dropdown-header">Alertas de Segurança</h6></li>
                        <li><a class="dropdown-item" href="#">Câmera offline - Setor B</a></li>
                        <li><a class="dropdown-item" href="#">Tentativa de acesso não autorizado</a></li>
                        <li><a class="dropdown-item" href="#">Sensor de movimento ativado</a></li>
                        <li><a class="dropdown-item" href="#">Relatório de ronda pendente</a></li>
                        <li><a class="dropdown-item" href="#">Alarme disparado - Galpão 3</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-center" href="#">Ver todos</a></li>
                    </ul>
                </div>
                
                <!-- Dropdown do Usuário -->
                <div class="dropdown user-dropdown">
                    <div class="user-avatar" data-bs-toggle="dropdown" aria-expanded="false">
                        S
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><h6 class="dropdown-header user-name">Supervisor de Segurança</h6></li>
                        <li><small class="dropdown-header text-muted user-email">seguranca@empresa.com</small></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="perfil.html"><i class="bi bi-person me-2"></i>Meu Perfil</a></li>
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
                    <h1 class="page-title">Dashboard Segurança Patrimonial</h1>
                    <p class="page-subtitle">Monitoramento e gestão de segurança empresarial</p>
                </div>
                <div>
                    <button class="btn btn-outline-primary me-2" onclick="exportReport()">
                        <i class="bi bi-file-earmark-spreadsheet me-2"></i>Relatório Diário
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
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <div class="metric-value">98.5%</div>
                        <div class="metric-label">Taxa de Disponibilidade</div>
                        <div class="metric-change positive">
                            <i class="bi bi-arrow-up"></i> +0.3% este mês
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="metric-card success">
                        <div class="metric-icon success">
                            <i class="bi bi-camera-video"></i>
                        </div>
                        <div class="metric-value">247</div>
                        <div class="metric-label">Câmeras Ativas</div>
                        <div class="metric-change positive">
                            <i class="bi bi-arrow-up"></i> +12 este ano
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="metric-card warning">
                        <div class="metric-icon warning">
                            <i class="bi bi-exclamation-triangle"></i>
                        </div>
                        <div class="metric-value">3</div>
                        <div class="metric-label">Incidentes Ativos</div>
                        <div class="metric-change negative">
                            <i class="bi bi-arrow-up"></i> +1 hoje
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="metric-card danger">
                        <div class="metric-icon danger">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <div class="metric-value">5</div>
                        <div class="metric-label">Rondas Pendentes</div>
                        <div class="metric-change positive">
                            <i class="bi bi-arrow-down"></i> -2 esta semana
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Gráficos e Dados -->
            <div class="row mb-4">
                <!-- Incidentes por Tipo -->
                <div class="col-xl-8 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Incidentes de Segurança - Últimos 30 Dias</h5>
                            <div class="btn-group btn-group-sm" role="group">
                                <input type="radio" class="btn-check" name="period" id="period7" checked>
                                <label class="btn btn-outline-primary" for="period7">30 Dias</label>
                                
                                <input type="radio" class="btn-check" name="period" id="period30">
                                <label class="btn btn-outline-primary" for="period30">Trimestre</label>
                                
                                <input type="radio" class="btn-check" name="period" id="period90">
                                <label class="btn btn-outline-primary" for="period90">Anual</label>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="incidentsChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
                
                <!-- Status de Segurança -->
                <div class="col-xl-4 mb-4">
                    <div class="incident-card">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Status de Segurança</h5>
                            <div class="security-level level-high">A</div>
                        </div>
                        <div class="text-center mb-3">
                            <div class="metric-value">ALERTA</div>
                            <div class="metric-label">Nível Alto de Risco</div>
                        </div>
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="mb-2">
                                    <i class="bi bi-shield-exclamation"></i>
                                    <div class="small">3</div>
                                    <div class="small">Incidentes</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="mb-2">
                                    <i class="bi bi-camera-video-off"></i>
                                    <div class="small">5</div>
                                    <div class="small">Câmeras</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="mb-2">
                                    <i class="bi bi-person-check"></i>
                                    <div class="small">18</div>
                                    <div class="small">Agentes</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Distribuição de Dispositivos -->
                    <div class="card-custom mt-3">
                        <div class="card-header-custom">
                            <h5 class="mb-0">Status dos Dispositivos</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="devicesChart" height="250"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Sites e Atividades -->
            <div class="row mb-4">
                <!-- Sites Monitorados -->
                <div class="col-xl-6 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Sites Monitorados</h5>
                            <a href="sites.html" class="btn btn-sm btn-outline-primary">Ver todos</a>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="site-card">
                                        <div class="site-image">
                                            <i class="bi bi-building"></i>
                                        </div>
                                        <div class="site-info">
                                            <div class="site-title">Matriz Centro</div>
                                            <div class="site-meta">48 câmeras • Nível Alto</div>
                                            <div class="site-progress">
                                                <div class="d-flex justify-content-between mb-1">
                                                    <small>Cobertura</small>
                                                    <small>92%</small>
                                                </div>
                                                <div class="progress progress-thin">
                                                    <div class="progress-bar bg-primary" style="width: 92%"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <div class="site-card">
                                        <div class="site-image" style="background: linear-gradient(135deg, #3498DB 0%, #2980B9 100%);">
                                            <i class="bi bi-shop"></i>
                                        </div>
                                        <div class="site-info">
                                            <div class="site-title">Filial Norte</div>
                                            <div class="site-meta">32 câmeras • Nível Médio</div>
                                            <div class="site-progress">
                                                <div class="d-flex justify-content-between mb-1">
                                                    <small>Cobertura</small>
                                                    <small>85%</small>
                                                </div>
                                                <div class="progress progress-thin">
                                                    <div class="progress-bar bg-warning" style="width: 85%"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <div class="site-card">
                                        <div class="site-image" style="background: linear-gradient(135deg, #E74C3C 0%, #CB4335 100%);">
                                            <i class="bi bi-house-door"></i>
                                        </div>
                                        <div class="site-info">
                                            <div class="site-title">Galpão Logístico</div>
                                            <div class="site-meta">28 câmeras • Nível Alto</div>
                                            <div class="site-progress">
                                                <div class="d-flex justify-content-between mb-1">
                                                    <small>Cobertura</small>
                                                    <small>78%</small>
                                                </div>
                                                <div class="progress progress-thin">
                                                    <div class="progress-bar bg-danger" style="width: 78%"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <div class="site-card">
                                        <div class="site-image" style="background: linear-gradient(135deg, #27AE60 0%, #229954 100%);">
                                            <i class="bi bi-building-fill"></i>
                                        </div>
                                        <div class="site-info">
                                            <div class="site-title">Escritório Regional</div>
                                            <div class="site-meta">24 câmeras • Nível Baixo</div>
                                            <div class="site-progress">
                                                <div class="d-flex justify-content-between mb-1">
                                                    <small>Cobertura</small>
                                                    <small>96%</small>
                                                </div>
                                                <div class="progress progress-thin">
                                                    <div class="progress-bar bg-success" style="width: 96%"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Atividades do Turno -->
                <div class="col-xl-6 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Atividades do Turno</h5>
                            <a href="cronograma.html" class="btn btn-sm btn-outline-primary">Cronograma</a>
                        </div>
                        <div class="card-body">
                            <div class="task-item ongoing">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>Ronda Perimetral - Setor A</strong>
                                        <div class="small">Agente Silva • Viatura 02</div>
                                        <div class="small text-muted">20:00 - 22:00 • 12 pontos de verificação</div>
                                    </div>
                                    <span class="badge bg-primary">Em andamento</span>
                                </div>
                            </div>
                            
                            <div class="task-item ongoing">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>Monitoramento CFTV</strong>
                                        <div class="small">Central de Operações</div>
                                        <div class="small text-muted">3 operadores • Turno noturno</div>
                                    </div>
                                    <span class="badge bg-primary">Em andamento</span>
                                </div>
                            </div>
                            
                            <div class="task-item delayed">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>Relatório de Incidente</strong>
                                        <div class="small">Tentativa de invasão - Galpão 3</div>
                                        <div class="small text-muted">Aguardando informações da PM</div>
                                    </div>
                                    <span class="badge bg-danger">Atrasado</span>
                                </div>
                            </div>
                            
                            <div class="task-item completed">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>Manutenção Preventiva</strong>
                                        <div class="small">Sistema de alarme - Filial Norte</div>
                                        <div class="small text-muted">Concluído às 16:30</div>
                                    </div>
                                    <span class="badge bg-success">Concluído</span>
                                </div>
                            </div>
                            
                            <div class="mt-3">
                                <h6 class="mb-3">Resumo do Turno</h6>
                                <div class="row text-center">
                                    <div class="col-4">
                                        <div class="metric-value text-primary">18</div>
                                        <div class="metric-label">Agentes</div>
                                    </div>
                                    <div class="col-4">
                                        <div class="metric-value text-success">24</div>
                                        <div class="metric-label">Atividades</div>
                                    </div>
                                    <div class="col-4">
                                        <div class="metric-value text-warning">2</div>
                                        <div class="metric-label">Atrasos</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Dispositivos e Equipamentos -->
            <div class="row mb-4">
                <!-- Status dos Dispositivos -->
                <div class="col-xl-6 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Status dos Dispositivos</h5>
                            <a href="dispositivos.html" class="btn btn-sm btn-outline-primary">Ver todos</a>
                        </div>
                        <div class="card-body">
                            <div class="device-status online">
                                <div class="me-3">
                                    <i class="bi bi-wifi text-success" style="font-size: 1.5rem;"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <strong>Câmeras de Segurança</strong>
                                    <div class="small text-muted">247 dispositivos • IP: 192.168.1.0/24</div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-semibold">98%</div>
                                    <div class="small text-muted">Online</div>
                                </div>
                            </div>
                            
                            <div class="device-status online">
                                <div class="me-3">
                                    <i class="bi bi-shield-check text-success" style="font-size: 1.5rem;"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <strong>Controladoras de Acesso</strong>
                                    <div class="small text-muted">48 dispositivos • 12 portarias</div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-semibold">100%</div>
                                    <div class="small text-muted">Online</div>
                                </div>
                            </div>
                            
                            <div class="device-status warning">
                                <div class="me-3">
                                    <i class="bi bi-exclamation-triangle text-warning" style="font-size: 1.5rem;"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <strong>Sensores de Movimento</strong>
                                    <div class="small text-muted">5 dispositivos com falha</div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-semibold text-warning">92%</div>
                                    <div class="small text-muted">Operacional</div>
                                </div>
                            </div>
                            
                            <div class="device-status offline">
                                <div class="me-3">
                                    <i class="bi bi-wifi-off text-danger" style="font-size: 1.5rem;"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <strong>Sistemas de Alarme</strong>
                                    <div class="small text-muted">2 sistemas em manutenção</div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-semibold text-danger">95%</div>
                                    <div class="small text-muted">Online</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Estoque e Manutenção -->
                <div class="col-xl-6 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Estoque e Manutenção</h5>
                            <a href="estoque.html" class="btn btn-sm btn-outline-primary">Ver estoque</a>
                        </div>
                        <div class="card-body">
                            <div class="row text-center mb-4">
                                <div class="col-4">
                                    <div class="mb-3">
                                        <div class="metric-value text-success">85%</div>
                                        <div class="metric-label">Equipamentos</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="mb-3">
                                        <div class="metric-value text-warning">45%</div>
                                        <div class="metric-label">Peças de Reposição</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="mb-3">
                                        <div class="metric-value text-danger">20%</div>
                                        <div class="metric-label">Baterias</div>
                                    </div>
                                </div>
                            </div>
                            
                            <h6 class="mb-3">Status de Manutenção</h6>
                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
                                <div>
                                    <strong>Câmeras Dome</strong>
                                    <div class="small text-muted">12 unidades para manutenção</div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-semibold text-warning">Pendente</div>
                                    <div class="small text-muted">Agendamento em 3 dias</div>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
                                <div>
                                    <strong>Controladoras de Acesso</strong>
                                    <div class="small text-muted">2 unidades com falha</div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-semibold text-danger">Urgente</div>
                                    <div class="small text-muted">Aguardando peças</div>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
                                <div>
                                    <strong>Sensores de Portão</strong>
                                    <div class="small text-muted">5 sensores com baixa performance</div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-semibold text-warning">Monitorando</div>
                                    <div class="small text-muted">Troca programada</div>
                                </div>
                            </div>
                            
                            <div class="mt-3">
                                <h6 class="mb-3">Próximas Manutenções</h6>
                                <div class="row text-center">
                                    <div class="col-4">
                                        <div class="metric-value text-primary">3</div>
                                        <div class="metric-label">Dias</div>
                                    </div>
                                    <div class="col-4">
                                        <div class="metric-value text-success">R$ 8.5K</div>
                                        <div class="metric-label">Orçamento</div>
                                    </div>
                                    <div class="col-4">
                                        <div class="metric-value text-info">12</div>
                                        <div class="metric-label">Equipamentos</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Alertas de Segurança -->
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
                                        <i class="bi bi-camera-video-off me-3"></i>
                                        <div>
                                            <strong>5 câmeras offline</strong> no setor B
                                            <div class="mt-1">
                                                <a href="#" class="btn btn-sm btn-warning">Verificar conexão</a>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="alert alert-info d-flex align-items-center mb-3">
                                        <i class="bi bi-shield-exclamation me-3"></i>
                                        <div>
                                            <strong>Relatório mensal pendente</strong>
                                            <div class="mt-1">
                                                <small class="text-muted">Prazo: 2 dias</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="alert alert-success d-flex align-items-center mb-3">
                                        <i class="bi bi-check-circle me-3"></i>
                                        <div>
                                            <strong>Sistema de backup</strong> atualizado com sucesso
                                            <div class="mt-1">
                                                <small class="text-muted">Todos os dados protegidos</small>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="alert alert-danger d-flex align-items-center mb-0">
                                        <i class="bi bi-exclamation-triangle me-3"></i>
                                        <div>
                                            <strong>Tentativa de invasão</strong> detectada
                                            <div class="mt-1">
                                                <a href="incidentes.html" class="btn btn-sm btn-danger">Ver detalhes</a>
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

        // Gráficos específicos do segmento de segurança patrimonial
        document.addEventListener('DOMContentLoaded', function() {
            // Gráfico de Incidentes
            const incidentsCtx = document.getElementById('incidentsChart').getContext('2d');
            const incidentsChart = new Chart(incidentsCtx, {
                type: 'bar',
                data: {
                    labels: ['Tentativa Invasão', 'Acesso Não Autorizado', 'Furto/ Roubo', 'Danos ao Patrimônio', 'Falha Sistema', 'Outros'],
                    datasets: [{
                        label: 'Incidentes (últimos 30 dias)',
                        data: [5, 8, 3, 7, 12, 4],
                        backgroundColor: '#2C3E50',
                        borderColor: '#34495E',
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
                                text: 'Número de Incidentes'
                            }
                        }
                    }
                }
            });
            
            // Gráfico de Status dos Dispositivos
            const devicesCtx = document.getElementById('devicesChart').getContext('2d');
            const devicesChart = new Chart(devicesCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Online', 'Offline', 'Manutenção', 'Alerta'],
                    datasets: [{
                        data: [85, 5, 7, 3],
                        backgroundColor: [
                            '#27AE60',
                            '#E74C3C',
                            '#F39C12',
                            '#3498DB'
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
            nexusFlow.showNotification('Gerando relatório de segurança...', 'info');
            setTimeout(() => {
                nexusFlow.showNotification('Relatório exportado com sucesso!', 'success');
            }, 1500);
        }
        
        function refreshDashboard() {
            nexusFlow.showNotification('Atualizando dados de segurança...', 'info');
            setTimeout(() => {
                nexusFlow.showNotification('Dados atualizados!', 'success');
            }, 1000);
        }
        
        // Definir papel como supervisor de segurança
        localStorage.setItem('userRole', 'security_supervisor');
        
        // Simulação de atualização em tempo real do status
        setInterval(function() {
            // Simular atualização de status (apenas visual)
            const statusElement = document.querySelector('.incident-card .metric-value');
            if (statusElement) {
                const statuses = ['NORMAL', 'ALERTA', 'CRÍTICO'];
                const randomStatus = statuses[Math.floor(Math.random() * statuses.length)];
                statusElement.textContent = randomStatus;
                
                // Atualizar cor do nível de segurança
                const securityLevel = document.querySelector('.security-level');
                if (randomStatus === 'NORMAL') {
                    securityLevel.className = 'security-level level-low';
                    securityLevel.textContent = 'B';
                } else if (randomStatus === 'ALERTA') {
                    securityLevel.className = 'security-level level-medium';
                    securityLevel.textContent = 'M';
                } else {
                    securityLevel.className = 'security-level level-high';
                    securityLevel.textContent = 'A';
                }
            }
        }, 45000); // Atualiza a cada 45 segundos
    </script>
</body>
</html>