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
    <title>Dashboard Agropecuária</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #27AE60;
            --secondary-color: #F39C12;
            --warning-color: #E67E22;
            --danger-color: #E74C3C;
            --dark-color: #2C3E50;
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
        .metric-card.success::before { background: var(--secondary-color); }
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
        
        .metric-icon.primary { background: rgba(39, 174, 96, 0.1); color: var(--primary-color); }
        .metric-icon.success { background: rgba(243, 156, 18, 0.1); color: var(--secondary-color); }
        .metric-icon.warning { background: rgba(230, 126, 34, 0.1); color: var(--warning-color); }
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
        
        .metric-change.positive { color: var(--primary-color); }
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
        
        .status-active { background: rgba(39, 174, 96, 0.1); color: var(--primary-color); }
        .status-pending { background: rgba(243, 156, 18, 0.1); color: var(--secondary-color); }
        .status-warning { background: rgba(231, 76, 60, 0.1); color: var(--danger-color); }
        
        .page-title {
            font-weight: 700;
            color: var(--dark-color);
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
        
        .farm-card {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 15px;
            transition: transform 0.2s;
        }
        
        .farm-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .farm-image {
            height: 120px;
            background: linear-gradient(135deg, #27AE60 0%, #229954 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
        }
        
        .farm-info {
            padding: 15px;
        }
        
        .farm-title {
            font-size: 1rem;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .farm-meta {
            font-size: 0.85rem;
            color: #6c757d;
            margin-bottom: 10px;
        }
        
        .farm-progress {
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
        .task-item.completed { border-left-color: var(--secondary-color); }
        
        .animal-status {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 8px;
            background: #f8f9fa;
        }
        
        .animal-status.healthy { border-left: 4px solid var(--primary-color); }
        .animal-status.treatment { border-left: 4px solid var(--warning-color); }
        .animal-status.critical { border-left: 4px solid var(--danger-color); }
        
        .weather-card {
            background: linear-gradient(135deg, #3498DB 0%, #2980B9 100%);
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
        }
        
        .progress-thin {
            height: 8px;
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
                        <li class="breadcrumb-item active">Agropecuária</li>
                    </ol>
                </nav>
            </div>
            
            <div class="header-right">
                <!-- Notificações -->
                <div class="dropdown">
                    <button class="btn btn-link text-dark position-relative" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-bell" style="font-size: 1.25rem;"></i>
                        <span class="badge bg-danger position-absolute translate-middle rounded-pill" style="font-size: 0.6rem; top: 8px; right: 8px;">6</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><h6 class="dropdown-header">Alertas da Fazenda</h6></li>
                        <li><a class="dropdown-item" href="#">Vacinação pendente - Lote B</a></li>
                        <li><a class="dropdown-item" href="#">Irrigação necessária - Soja</a></li>
                        <li><a class="dropdown-item" href="#">Previsão de chuva forte</a></li>
                        <li><a class="dropdown-item" href="#">Estoque de ração crítico</a></li>
                        <li><a class="dropdown-item" href="#">Animal doente identificado</a></li>
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
                        <li><h6 class="dropdown-header user-name">Gerente Agropecuário</h6></li>
                        <li><small class="dropdown-header text-muted user-email">agro@empresa.com</small></li>
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
                    <h1 class="page-title">Dashboard Agropecuária</h1>
                    <p class="page-subtitle">Gestão de produção agrícola e pecuária</p>
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
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="metric-card primary">
                        <div class="metric-icon primary">
                            <i class="bi bi-tree"></i>
                        </div>
                        <div class="metric-value">2.458</div>
                        <div class="metric-label">Hectares Plantados</div>
                        <div class="metric-change positive">
                            <i class="bi bi-arrow-up"></i> +120 ha este ano
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="metric-card success">
                        <div class="metric-icon success">
                            <i class="bi bi-arrow-through-heart"></i>
                        </div>
                        <div class="metric-value">1.248</div>
                        <div class="metric-label">Cabeças de Gado</div>
                        <div class="metric-change positive">
                            <i class="bi bi-arrow-up"></i> +42 este mês
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="metric-card warning">
                        <div class="metric-icon warning">
                            <i class="bi bi-droplet"></i>
                        </div>
                        <div class="metric-value">78%</div>
                        <div class="metric-label">Umidade do Solo</div>
                        <div class="metric-change negative">
                            <i class="bi bi-arrow-down"></i> -5% vs semana passada
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="metric-card danger">
                        <div class="metric-icon danger">
                            <i class="bi bi-thermometer-high"></i>
                        </div>
                        <div class="metric-value">3</div>
                        <div class="metric-label">Animais Doentes</div>
                        <div class="metric-change positive">
                            <i class="bi bi-arrow-down"></i> -2 esta semana
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Gráficos e Dados -->
            <div class="row mb-4">
                <!-- Produção Agrícola -->
                <div class="col-xl-8 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Produção Agrícola - Safra 2023/2024</h5>
                            <div class="btn-group btn-group-sm" role="group">
                                <input type="radio" class="btn-check" name="period" id="period7" checked>
                                <label class="btn btn-outline-primary" for="period7">Safra Atual</label>
                                
                                <input type="radio" class="btn-check" name="period" id="period30">
                                <label class="btn btn-outline-primary" for="period30">Comparativo</label>
                                
                                <input type="radio" class="btn-check" name="period" id="period90">
                                <label class="btn btn-outline-primary" for="period90">Histórico</label>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="productionChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
                
                <!-- Previsão do Tempo -->
                <div class="col-xl-4 mb-4">
                    <div class="weather-card">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Previsão do Tempo</h5>
                            <i class="bi bi-cloud-sun" style="font-size: 2rem;"></i>
                        </div>
                        <div class="text-center mb-3">
                            <div class="metric-value">28°C</div>
                            <div class="metric-label">Parcialmente Nublado</div>
                        </div>
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="mb-2">
                                    <i class="bi bi-droplet"></i>
                                    <div class="small">65%</div>
                                    <div class="small">Umidade</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="mb-2">
                                    <i class="bi bi-wind"></i>
                                    <div class="small">12km/h</div>
                                    <div class="small">Vento</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="mb-2">
                                    <i class="bi bi-cloud-rain"></i>
                                    <div class="small">30%</div>
                                    <div class="small">Chuva</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Distribuição de Culturas -->
                    <div class="card-custom mt-3">
                        <div class="card-header-custom">
                            <h5 class="mb-0">Distribuição de Culturas</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="cropsChart" height="250"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Fazendas e Atividades -->
            <div class="row mb-4">
                <!-- Fazendas em Produção -->
                <div class="col-xl-6 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Fazendas em Produção</h5>
                            <a href="fazendas.html" class="btn btn-sm btn-outline-primary">Ver todas</a>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="farm-card">
                                        <div class="farm-image">
                                            <i class="bi bi-tree-fill"></i>
                                        </div>
                                        <div class="farm-info">
                                            <div class="farm-title">Fazenda São João</div>
                                            <div class="farm-meta">Soja • 850 hectares</div>
                                            <div class="farm-progress">
                                                <div class="d-flex justify-content-between mb-1">
                                                    <small>Ciclo</small>
                                                    <small>65%</small>
                                                </div>
                                                <div class="progress progress-thin">
                                                    <div class="progress-bar bg-primary" style="width: 65%"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <div class="farm-card">
                                        <div class="farm-image" style="background: linear-gradient(135deg, #F39C12 0%, #D68910 100%);">
                                            <i class="bi bi-flower1"></i>
                                        </div>
                                        <div class="farm-info">
                                            <div class="farm-title">Fazenda Esperança</div>
                                            <div class="farm-meta">Milho • 620 hectares</div>
                                            <div class="farm-progress">
                                                <div class="d-flex justify-content-between mb-1">
                                                    <small>Ciclo</small>
                                                    <small>42%</small>
                                                </div>
                                                <div class="progress progress-thin">
                                                    <div class="progress-bar bg-warning" style="width: 42%"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <div class="farm-card">
                                        <div class="farm-image" style="background: linear-gradient(135deg, #E74C3C 0%, #CB4335 100%);">
                                            <i class="bi bi-cup-straw"></i>
                                        </div>
                                        <div class="farm-info">
                                            <div class="farm-title">Fazenda Boa Vista</div>
                                            <div class="farm-meta">Café • 320 hectares</div>
                                            <div class="farm-progress">
                                                <div class="d-flex justify-content-between mb-1">
                                                    <small>Ciclo</small>
                                                    <small>78%</small>
                                                </div>
                                                <div class="progress progress-thin">
                                                    <div class="progress-bar bg-success" style="width: 78%"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <div class="farm-card">
                                        <div class="farm-image" style="background: linear-gradient(135deg, #3498DB 0%, #2980B9 100%);">
                                            <i class="bi bi-droplet-fill"></i>
                                        </div>
                                        <div class="farm-info">
                                            <div class="farm-title">Fazenda Rio Verde</div>
                                            <div class="farm-meta">Arroz • 540 hectares</div>
                                            <div class="farm-progress">
                                                <div class="d-flex justify-content-between mb-1">
                                                    <small>Ciclo</small>
                                                    <small>25%</small>
                                                </div>
                                                <div class="progress progress-thin">
                                                    <div class="progress-bar bg-info" style="width: 25%"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Atividades do Dia -->
                <div class="col-xl-6 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Atividades do Dia</h5>
                            <a href="cronograma.html" class="btn btn-sm btn-outline-primary">Cronograma</a>
                        </div>
                        <div class="card-body">
                            <div class="task-item ongoing">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>Irrigação - Soja</strong>
                                        <div class="small">Fazenda São João • Setor 4B</div>
                                        <div class="small text-muted">8:00 - 12:00 • 3 tratores</div>
                                    </div>
                                    <span class="badge bg-primary">Em andamento</span>
                                </div>
                            </div>
                            
                            <div class="task-item ongoing">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>Aplicação de Fertilizante</strong>
                                        <div class="small">Fazenda Esperança • Milho</div>
                                        <div class="small text-muted">2 aviões agrícolas</div>
                                    </div>
                                    <span class="badge bg-primary">Em andamento</span>
                                </div>
                            </div>
                            
                            <div class="task-item delayed">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>Vacinação do Rebanho</strong>
                                        <div class="small">Lote B • 120 cabeças</div>
                                        <div class="small text-muted">Aguardando veterinário</div>
                                    </div>
                                    <span class="badge bg-danger">Atrasado</span>
                                </div>
                            </div>
                            
                            <div class="task-item completed">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>Colheita de Café</strong>
                                        <div class="small">Fazenda Boa Vista • Talhão 3</div>
                                        <div class="small text-muted">Concluído às 14:30</div>
                                    </div>
                                    <span class="badge bg-success">Concluído</span>
                                </div>
                            </div>
                            
                            <div class="mt-3">
                                <h6 class="mb-3">Resumo do Dia</h6>
                                <div class="row text-center">
                                    <div class="col-4">
                                        <div class="metric-value text-primary">28</div>
                                        <div class="metric-label">Funcionários</div>
                                    </div>
                                    <div class="col-4">
                                        <div class="metric-value text-success">6</div>
                                        <div class="metric-label">Atividades</div>
                                    </div>
                                    <div class="col-4">
                                        <div class="metric-value text-warning">1</div>
                                        <div class="metric-label">Atrasos</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Pecuária e Saúde Animal -->
            <div class="row mb-4">
                <!-- Saúde do Rebanho -->
                <div class="col-xl-6 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Saúde do Rebanho</h5>
                            <a href="pecuaria.html" class="btn btn-sm btn-outline-primary">Ver rebanho</a>
                        </div>
                        <div class="card-body">
                            <div class="animal-status healthy">
                                <div class="me-3">
                                    <i class="bi bi-heart-fill text-success" style="font-size: 1.5rem;"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <strong>Lote A - Nelore</strong>
                                    <div class="small text-muted">245 cabeças • Peso médio: 420kg</div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-semibold">98%</div>
                                    <div class="small text-muted">Saudável</div>
                                </div>
                            </div>
                            
                            <div class="animal-status healthy">
                                <div class="me-3">
                                    <i class="bi bi-heart-fill text-success" style="font-size: 1.5rem;"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <strong>Lote B - Angus</strong>
                                    <div class="small text-muted">180 cabeças • Peso médio: 480kg</div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-semibold">95%</div>
                                    <div class="small text-muted">Saudável</div>
                                </div>
                            </div>
                            
                            <div class="animal-status treatment">
                                <div class="me-3">
                                    <i class="bi bi-clipboard-pulse text-warning" style="font-size: 1.5rem;"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <strong>Lote C - Novilhas</strong>
                                    <div class="small text-muted">85 cabeças • Em tratamento</div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-semibold text-warning">88%</div>
                                    <div class="small text-muted">Em recuperação</div>
                                </div>
                            </div>
                            
                            <div class="animal-status critical">
                                <div class="me-3">
                                    <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size: 1.5rem;"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <strong>Lote D - Bezerros</strong>
                                    <div class="small text-muted">3 animais isolados</div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-semibold text-danger">Crítico</div>
                                    <div class="small text-muted">Monitoramento 24h</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Estoque e Insumos -->
                <div class="col-xl-6 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Estoque e Insumos</h5>
                            <a href="estoque.html" class="btn btn-sm btn-outline-primary">Ver estoque</a>
                        </div>
                        <div class="card-body">
                            <div class="row text-center mb-4">
                                <div class="col-4">
                                    <div class="mb-3">
                                        <div class="metric-value text-success">85%</div>
                                        <div class="metric-label">Ração</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="mb-3">
                                        <div class="metric-value text-warning">45%</div>
                                        <div class="metric-label">Fertilizante</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="mb-3">
                                        <div class="metric-value text-danger">20%</div>
                                        <div class="metric-label">Defensivos</div>
                                    </div>
                                </div>
                            </div>
                            
                            <h6 class="mb-3">Status de Insumos</h6>
                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
                                <div>
                                    <strong>Ração Bovinos</strong>
                                    <div class="small text-muted">Estoque: 8.5 ton • Mínimo: 10 ton</div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-semibold text-warning">85%</div>
                                    <div class="small text-muted">Reposição em 5 dias</div>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
                                <div>
                                    <strong>Fertilizante NPK</strong>
                                    <div class="small text-muted">Estoque: 4.5 ton • Mínimo: 10 ton</div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-semibold text-danger">45%</div>
                                    <div class="small text-muted">Reposição urgente</div>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
                                <div>
                                    <strong>Herbicida</strong>
                                    <div class="small text-muted">Estoque: 200L • Mínimo: 1.000L</div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-semibold text-danger">20%</div>
                                    <div class="small text-muted">Estoque crítico</div>
                                </div>
                            </div>
                            
                            <div class="mt-3">
                                <h6 class="mb-3">Próximas Compras</h6>
                                <div class="row text-center">
                                    <div class="col-4">
                                        <div class="metric-value text-primary">3</div>
                                        <div class="metric-label">Dias</div>
                                    </div>
                                    <div class="col-4">
                                        <div class="metric-value text-success">R$ 42K</div>
                                        <div class="metric-label">Valor</div>
                                    </div>
                                    <div class="col-4">
                                        <div class="metric-value text-info">5</div>
                                        <div class="metric-label">Itens</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Alertas do Agronegócio -->
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
                                        <i class="bi bi-droplet me-3"></i>
                                        <div>
                                            <strong>Irrigação necessária</strong> na soja
                                            <div class="mt-1">
                                                <a href="#" class="btn btn-sm btn-warning">Programar irrigação</a>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="alert alert-info d-flex align-items-center mb-3">
                                        <i class="bi bi-cloud-rain me-3"></i>
                                        <div>
                                            <strong>Chuva forte prevista</strong> para amanhã
                                            <div class="mt-1">
                                                <small class="text-muted">Ajustar cronograma de aplicação</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="alert alert-success d-flex align-items-center mb-3">
                                        <i class="bi bi-check-circle me-3"></i>
                                        <div>
                                            <strong>Colheita de café</strong> acima da expectativa
                                            <div class="mt-1">
                                                <small class="text-muted">+15% vs safra anterior</small>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="alert alert-danger d-flex align-items-center mb-0">
                                        <i class="bi bi-exclamation-triangle me-3"></i>
                                        <div>
                                            <strong>3 animais</strong> em estado crítico
                                            <div class="mt-1">
                                                <a href="pecuaria.html" class="btn btn-sm btn-danger">Ver detalhes</a>
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

        // Gráficos específicos do segmento agropecuário
        document.addEventListener('DOMContentLoaded', function() {
            // Gráfico de Produção Agrícola
            const productionCtx = document.getElementById('productionChart').getContext('2d');
            const productionChart = new Chart(productionCtx, {
                type: 'bar',
                data: {
                    labels: ['Soja', 'Milho', 'Café', 'Arroz', 'Trigo', 'Algodão'],
                    datasets: [{
                        label: 'Produção (ton/ha)',
                        data: [65, 45, 28, 42, 38, 22],
                        backgroundColor: '#27AE60',
                        borderColor: '#229954',
                        borderWidth: 1
                    }, {
                        label: 'Meta (ton/ha)',
                        data: [60, 50, 30, 45, 40, 25],
                        backgroundColor: '#F39C12',
                        borderColor: '#D68910',
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
                                text: 'Toneladas por Hectare'
                            }
                        }
                    }
                }
            });
            
            // Gráfico de Distribuição de Culturas
            const cropsCtx = document.getElementById('cropsChart').getContext('2d');
            const cropsChart = new Chart(cropsCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Soja', 'Milho', 'Café', 'Arroz', 'Outros'],
                    datasets: [{
                        data: [35, 25, 15, 12, 13],
                        backgroundColor: [
                            '#27AE60',
                            '#F39C12',
                            '#E74C3C',
                            '#3498DB',
                            '#95A5A6'
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
            nexusFlow.showNotification('Gerando relatório agropecuário...', 'info');
            setTimeout(() => {
                nexusFlow.showNotification('Relatório exportado com sucesso!', 'success');
            }, 1500);
        }
        
        function refreshDashboard() {
            nexusFlow.showNotification('Atualizando dados da fazenda...', 'info');
            setTimeout(() => {
                nexusFlow.showNotification('Dados atualizados!', 'success');
            }, 1000);
        }
        
        // Definir papel como gerente agropecuário
        localStorage.setItem('userRole', 'agribusiness_manager');
        
        // Simulação de atualização em tempo real do clima
        setInterval(function() {
            // Simular atualização de temperatura (apenas visual)
            const tempElement = document.querySelector('.weather-card .metric-value');
            if (tempElement) {
                const currentTemp = parseInt(tempElement.textContent);
                const newTemp = currentTemp + (Math.random() > 0.5 ? 1 : -1);
                if (newTemp >= 25 && newTemp <= 32) {
                    tempElement.textContent = newTemp + '°C';
                }
            }
        }, 30000); // Atualiza a cada 30 segundos
    </script>
</body>
</html>