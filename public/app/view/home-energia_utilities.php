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
    <title>Dashboard Energia & Utilities</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #3498DB;
            --secondary-color: #2ECC71;
            --warning-color: #F39C12;
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
        
        .metric-icon.primary { background: rgba(52, 152, 219, 0.1); color: var(--primary-color); }
        .metric-icon.success { background: rgba(46, 204, 113, 0.1); color: var(--secondary-color); }
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
        
        .metric-change.positive { color: var(--secondary-color); }
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
        
        .status-active { background: rgba(46, 204, 113, 0.1); color: var(--secondary-color); }
        .status-pending { background: rgba(243, 156, 18, 0.1); color: var(--warning-color); }
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
        
        .plant-card {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 15px;
            transition: transform 0.2s;
        }
        
        .plant-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .plant-image {
            height: 120px;
            background: linear-gradient(135deg, #3498DB 0%, #2980B9 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
        }
        
        .plant-info {
            padding: 15px;
        }
        
        .plant-title {
            font-size: 1rem;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .plant-meta {
            font-size: 0.85rem;
            color: #6c757d;
            margin-bottom: 10px;
        }
        
        .plant-stats {
            display: flex;
            justify-content: space-between;
            font-size: 0.8rem;
        }
        
        .outage-item {
            padding: 10px;
            border-left: 4px solid transparent;
            margin-bottom: 8px;
            border-radius: 5px;
            background: #f8f9fa;
        }
        
        .outage-item.critical { border-left-color: var(--danger-color); background: rgba(231, 76, 60, 0.05); }
        .outage-item.warning { border-left-color: var(--warning-color); background: rgba(243, 156, 18, 0.05); }
        .outage-item.resolved { border-left-color: var(--secondary-color); background: rgba(46, 204, 113, 0.05); }
        
        .grid-status {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 8px;
            background: #f8f9fa;
        }
        
        .grid-status.stable { border-left: 4px solid var(--secondary-color); }
        .grid-status.unstable { border-left: 4px solid var(--warning-color); }
        .grid-status.critical { border-left: 4px solid var(--danger-color); }
        
        .consumption-card {
            background: linear-gradient(135deg, #2C3E50 0%, #34495E 100%);
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
        }
        
        .gauge-container {
            position: relative;
            width: 120px;
            height: 60px;
            margin: 0 auto;
        }
        
        .gauge-value {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-weight: bold;
            font-size: 1.2rem;
        }
        
        .progress-thin {
            height: 6px;
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
                        <li class="breadcrumb-item active">Energia & Utilities</li>
                    </ol>
                </nav>
            </div>
            
            <div class="header-right">
                <!-- Notificações -->
                <div class="dropdown">
                    <button class="btn btn-link text-dark position-relative" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-bell" style="font-size: 1.25rem;"></i>
                        <span class="badge bg-danger position-absolute translate-middle rounded-pill" style="font-size: 0.6rem; top: 8px; right: 8px;">4</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><h6 class="dropdown-header">Alertas do Sistema</h6></li>
                        <li><a class="dropdown-item" href="#">Queda de tensão - Subestação Norte</a></li>
                        <li><a class="dropdown-item" href="#">Manutenção programada - Linha 4B</a></li>
                        <li><a class="dropdown-item" href="#">Pico de consumo - Região Metropolitana</a></li>
                        <li><a class="dropdown-item" href="#">Falha no transformador - Setor Industrial</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-center" href="#">Ver todos</a></li>
                    </ul>
                </div>
                
                <!-- Dropdown do Usuário -->
                <div class="dropdown user-dropdown">
                    <div class="user-avatar" data-bs-toggle="dropdown" aria-expanded="false">
                        E
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><h6 class="dropdown-header user-name">Gerente de Operações</h6></li>
                        <li><small class="dropdown-header text-muted user-email">energia@empresa.com</small></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="perfil.html"><i class="bi bi-person me-2"></i>Meu Perfil</a></li>
                        <li><a class="dropdown-item" href="#"><i class="bi bi-lightning-charge me-2"></i>Operações</a></li>
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
                    <h1 class="page-title">Dashboard Energia & Utilities</h1>
                    <p class="page-subtitle">Monitoramento de geração, distribuição e qualidade de energia</p>
                </div>
                <div>
                    <button class="btn btn-outline-primary me-2" onclick="exportReport()">
                        <i class="bi bi-file-earmark-spreadsheet me-2"></i>Relatório Operacional
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
                            <i class="bi bi-lightning-charge"></i>
                        </div>
                        <div class="metric-value">2.458</div>
                        <div class="metric-label">MW Gerados</div>
                        <div class="metric-change positive">
                            <i class="bi bi-arrow-up"></i> +125 MW vs ontem
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="metric-card success">
                        <div class="metric-icon success">
                            <i class="bi bi-house-check"></i>
                        </div>
                        <div class="metric-value">99.87%</div>
                        <div class="metric-label">Confiabilidade</div>
                        <div class="metric-change positive">
                            <i class="bi bi-arrow-up"></i> +0.05% este mês
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="metric-card warning">
                        <div class="metric-icon warning">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <div class="metric-value">12.4</div>
                        <div class="metric-label">Min. Interrupção/Cliente</div>
                        <div class="metric-change negative">
                            <i class="bi bi-arrow-up"></i> +1.2min vs mês anterior
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="metric-card danger">
                        <div class="metric-icon danger">
                            <i class="bi bi-exclamation-triangle"></i>
                        </div>
                        <div class="metric-value">8</div>
                        <div class="metric-label">Interrupções Ativas</div>
                        <div class="metric-change negative">
                            <i class="bi bi-arrow-up"></i> +2 esta semana
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Gráficos e Dados -->
            <div class="row mb-4">
                <!-- Geração e Demanda -->
                <div class="col-xl-8 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Geração vs Demanda - Tempo Real</h5>
                            <div class="btn-group btn-group-sm" role="group">
                                <input type="radio" class="btn-check" name="period" id="period24" checked>
                                <label class="btn btn-outline-primary" for="period24">24h</label>
                                
                                <input type="radio" class="btn-check" name="period" id="period7">
                                <label class="btn btn-outline-primary" for="period7">7 dias</label>
                                
                                <input type="radio" class="btn-check" name="period" id="period30">
                                <label class="btn btn-outline-primary" for="period30">30 dias</label>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="generationChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
                
                <!-- Mix de Geração -->
                <div class="col-xl-4 mb-4">
                    <div class="consumption-card">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Consumo em Tempo Real</h5>
                            <i class="bi bi-lightning" style="font-size: 2rem;"></i>
                        </div>
                        <div class="text-center mb-3">
                            <div class="metric-value">1.845 MW</div>
                            <div class="metric-label">Demanda Atual</div>
                        </div>
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="mb-2">
                                    <i class="bi bi-building"></i>
                                    <div class="small">42%</div>
                                    <div class="small">Industrial</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="mb-2">
                                    <i class="bi bi-house"></i>
                                    <div class="small">35%</div>
                                    <div class="small">Residencial</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="mb-2">
                                    <i class="bi bi-shop"></i>
                                    <div class="small">23%</div>
                                    <div class="small">Comercial</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Mix de Geração -->
                    <div class="card-custom mt-3">
                        <div class="card-header-custom">
                            <h5 class="mb-0">Mix de Geração</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="generationMixChart" height="250"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Usinas e Subestações -->
            <div class="row mb-4">
                <!-- Usinas em Operação -->
                <div class="col-xl-6 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Usinas em Operação</h5>
                            <a href="usinas.html" class="btn btn-sm btn-outline-primary">Ver todas</a>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="plant-card">
                                        <div class="plant-image" style="background: linear-gradient(135deg, #F39C12 0%, #D68910 100%);">
                                            <i class="bi bi-sun"></i>
                                        </div>
                                        <div class="plant-info">
                                            <div class="plant-title">Usina Solar Norte</div>
                                            <div class="plant-meta">Fotovoltaica • 120 MW</div>
                                            <div class="plant-stats">
                                                <span><i class="bi bi-speedometer2"></i> 85 MW</span>
                                                <span class="text-success">71% cap.</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <div class="plant-card">
                                        <div class="plant-image" style="background: linear-gradient(135deg, #3498DB 0%, #2980B9 100%);">
                                            <i class="bi bi-droplet"></i>
                                        </div>
                                        <div class="plant-info">
                                            <div class="plant-title">Hidrelétrica Rio Verde</div>
                                            <div class="plant-meta">Hídrica • 450 MW</div>
                                            <div class="plant-stats">
                                                <span><i class="bi bi-speedometer2"></i> 420 MW</span>
                                                <span class="text-success">93% cap.</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <div class="plant-card">
                                        <div class="plant-image" style="background: linear-gradient(135deg, #E74C3C 0%, #CB4335 100%);">
                                            <i class="bi bi-thermometer-sun"></i>
                                        </div>
                                        <div class="plant-info">
                                            <div class="plant-title">Termelétrica Centro</div>
                                            <div class="plant-meta">Gás Natural • 320 MW</div>
                                            <div class="plant-stats">
                                                <span><i class="bi bi-speedometer2"></i> 280 MW</span>
                                                <span class="text-success">88% cap.</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <div class="plant-card">
                                        <div class="plant-image" style="background: linear-gradient(135deg, #27AE60 0%, #229954 100%);">
                                            <i class="bi bi-fan"></i>
                                        </div>
                                        <div class="plant-info">
                                            <div class="plant-title">Eólica Costa Sul</div>
                                            <div class="plant-meta">Eólica • 180 MW</div>
                                            <div class="plant-stats">
                                                <span><i class="bi bi-speedometer2"></i> 125 MW</span>
                                                <span class="text-warning">69% cap.</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Interrupções e Manutenções -->
                <div class="col-xl-6 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Interrupções e Manutenções</h5>
                            <a href="manutencao.html" class="btn btn-sm btn-outline-primary">Programar</a>
                        </div>
                        <div class="card-body">
                            <div class="outage-item critical">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>Queda de Linha - Setor Industrial</strong>
                                        <div class="small">Subestação Norte • 2.150 clientes</div>
                                        <div class="small text-muted">Início: 14:30 • Equipe no local</div>
                                    </div>
                                    <span class="badge bg-danger">Crítico</span>
                                </div>
                            </div>
                            
                            <div class="outage-item warning">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>Manutenção Programada</strong>
                                        <div class="small">Linha 4B • 850 clientes</div>
                                        <div class="small text-muted">08:00 - 12:00 • Amanhã</div>
                                    </div>
                                    <span class="badge bg-warning">Programada</span>
                                </div>
                            </div>
                            
                            <div class="outage-item warning">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>Queda de Tensão</strong>
                                        <div class="small">Região Central • 420 clientes</div>
                                        <div class="small text-muted">Equipe a caminho • ETA: 25min</div>
                                    </div>
                                    <span class="badge bg-warning">Em atendimento</span>
                                </div>
                            </div>
                            
                            <div class="outage-item resolved">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>Falha no Transformador</strong>
                                        <div class="small">Setor Sul • 1.200 clientes</div>
                                        <div class="small text-muted">Resolvido às 11:45</div>
                                    </div>
                                    <span class="badge bg-success">Resolvido</span>
                                </div>
                            </div>
                            
                            <div class="mt-3">
                                <h6 class="mb-3">Indicadores de Serviço</h6>
                                <div class="row text-center">
                                    <div class="col-4">
                                        <div class="metric-value text-primary">45min</div>
                                        <div class="metric-label">TMA</div>
                                    </div>
                                    <div class="col-4">
                                        <div class="metric-value text-success">98.7%</div>
                                        <div class="metric-label">DEC</div>
                                    </div>
                                    <div class="col-4">
                                        <div class="metric-value text-info">1.2</div>
                                        <div class="metric-label">FEC</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Rede e Qualidade -->
            <div class="row mb-4">
                <!-- Status da Rede -->
                <div class="col-xl-6 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Status da Rede Elétrica</h5>
                            <a href="rede.html" class="btn btn-sm btn-outline-primary">Mapa da rede</a>
                        </div>
                        <div class="card-body">
                            <div class="grid-status stable">
                                <div class="me-3">
                                    <i class="bi bi-check-circle-fill text-success" style="font-size: 1.5rem;"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <strong>Subestação Norte</strong>
                                    <div class="small text-muted">Tensão: 138kV • Carga: 78%</div>
                                </div>
                                <div class="text-end">
                                    <div class="gauge-container">
                                        <div class="gauge-value">98%</div>
                                    </div>
                                    <div class="small text-muted">Estabilidade</div>
                                </div>
                            </div>
                            
                            <div class="grid-status stable">
                                <div class="me-3">
                                    <i class="bi bi-check-circle-fill text-success" style="font-size: 1.5rem;"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <strong>Subestação Sul</strong>
                                    <div class="small text-muted">Tensão: 138kV • Carga: 65%</div>
                                </div>
                                <div class="text-end">
                                    <div class="gauge-container">
                                        <div class="gauge-value">99%</div>
                                    </div>
                                    <div class="small text-muted">Estabilidade</div>
                                </div>
                            </div>
                            
                            <div class="grid-status unstable">
                                <div class="me-3">
                                    <i class="bi bi-exclamation-triangle-fill text-warning" style="font-size: 1.5rem;"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <strong>Subestação Leste</strong>
                                    <div class="small text-muted">Tensão: 132kV • Carga: 92%</div>
                                </div>
                                <div class="text-end">
                                    <div class="gauge-container">
                                        <div class="gauge-value">85%</div>
                                    </div>
                                    <div class="small text-muted">Sobrecarga</div>
                                </div>
                            </div>
                            
                            <div class="grid-status critical">
                                <div class="me-3">
                                    <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size: 1.5rem;"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <strong>Subestação Oeste</strong>
                                    <div class="small text-muted">Tensão: 125kV • Em manutenção</div>
                                </div>
                                <div class="text-end">
                                    <div class="gauge-container">
                                        <div class="gauge-value">0%</div>
                                    </div>
                                    <div class="small text-muted">Fora de operação</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Qualidade de Energia -->
                <div class="col-xl-6 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Qualidade de Energia</h5>
                            <a href="qualidade.html" class="btn btn-sm btn-outline-primary">Relatório</a>
                        </div>
                        <div class="card-body">
                            <div class="row text-center mb-4">
                                <div class="col-4">
                                    <div class="mb-3">
                                        <div class="metric-value text-success">99.2%</div>
                                        <div class="metric-label">Tensão Nominal</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="mb-3">
                                        <div class="metric-value text-warning">2.8%</div>
                                        <div class="metric-label">THD</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="mb-3">
                                        <div class="metric-value text-primary">0.95</div>
                                        <div class="metric-label">Fator de Potência</div>
                                    </div>
                                </div>
                            </div>
                            
                            <h6 class="mb-3">Eventos de Qualidade</h6>
                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
                                <div>
                                    <strong>Afundamento de Tensão</strong>
                                    <div class="small text-muted">Subestação Norte • 14:25</div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-semibold text-warning">85%</div>
                                    <div class="small text-muted">15% de queda</div>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
                                <div>
                                    <strong>Harmônica 5ª Ordem</strong>
                                    <div class="small text-muted">Setor Industrial • 4.2%</div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-semibold text-success">Dentro do limite</div>
                                    <div class="small text-muted">Limite: 5%</div>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
                                <div>
                                    <strong>Flicker</strong>
                                    <div class="small text-muted">Região Central • Pst: 0.8</div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-semibold text-success">Aceitável</div>
                                    <div class="small text-muted">Limite: 1.0</div>
                                </div>
                            </div>
                            
                            <div class="mt-3">
                                <h6 class="mb-3">Indicadores ANEEL</h6>
                                <div class="row text-center">
                                    <div class="col-4">
                                        <div class="metric-value text-primary">12.4</div>
                                        <div class="metric-label">DEC (h/cliente)</div>
                                    </div>
                                    <div class="col-4">
                                        <div class="metric-value text-success">1.8</div>
                                        <div class="metric-label">FEC (ocorrências)</div>
                                    </div>
                                    <div class="col-4">
                                        <div class="metric-value text-info">99.87%</div>
                                        <div class="metric-label">Nível de Serviço</div>
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
                            <h5 class="mb-0">Alertas e Ações</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="alert alert-warning d-flex align-items-center mb-3">
                                        <i class="bi bi-lightning me-3"></i>
                                        <div>
                                            <strong>Pico de consumo</strong> previsto para hoje
                                            <div class="mt-1">
                                                <a href="#" class="btn btn-sm btn-warning">Ajustar geração</a>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="alert alert-info d-flex align-items-center mb-3">
                                        <i class="bi bi-tools me-3"></i>
                                        <div>
                                            <strong>Manutenção preventiva</strong> programada
                                            <div class="mt-1">
                                                <small class="text-muted">Linha 4B - 08:00 às 12:00</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="alert alert-success d-flex align-items-center mb-3">
                                        <i class="bi bi-graph-up-arrow me-3"></i>
                                        <div>
                                            <strong>Geração solar</strong> acima da média
                                            <div class="mt-1">
                                                <small class="text-muted">+15% vs previsão</small>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="alert alert-danger d-flex align-items-center mb-0">
                                        <i class="bi bi-exclamation-triangle me-3"></i>
                                        <div>
                                            <strong>1 interrupção crítica</strong> em andamento
                                            <div class="mt-1">
                                                <a href="operacoes.html" class="btn btn-sm btn-danger">Ver detalhes</a>
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

        // Gráficos específicos do segmento de energia e utilities
        document.addEventListener('DOMContentLoaded', function() {
            // Gráfico de Geração vs Demanda
            const generationCtx = document.getElementById('generationChart').getContext('2d');
            const generationChart = new Chart(generationCtx, {
                type: 'line',
                data: {
                    labels: ['00:00', '04:00', '08:00', '12:00', '16:00', '20:00', '24:00'],
                    datasets: [{
                        label: 'Geração (MW)',
                        data: [1850, 1750, 2100, 2350, 2450, 2300, 1950],
                        borderColor: '#3498DB',
                        backgroundColor: 'rgba(52, 152, 219, 0.1)',
                        tension: 0.4,
                        fill: true
                    }, {
                        label: 'Demanda (MW)',
                        data: [1650, 1550, 1950, 2250, 2400, 2350, 1850],
                        borderColor: '#2ECC71',
                        backgroundColor: 'rgba(46, 204, 113, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: false,
                            title: {
                                display: true,
                                text: 'Megawatts (MW)'
                            }
                        }
                    }
                }
            });
            
            // Gráfico de Mix de Geração
            const generationMixCtx = document.getElementById('generationMixChart').getContext('2d');
            const generationMixChart = new Chart(generationMixCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Hidrelétrica', 'Termelétrica', 'Solar', 'Eólica', 'Outros'],
                    datasets: [{
                        data: [45, 25, 15, 12, 3],
                        backgroundColor: [
                            '#3498DB',
                            '#E74C3C',
                            '#F39C12',
                            '#27AE60',
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
            nexusFlow.showNotification('Gerando relatório operacional...', 'info');
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
        
        // Definir papel como gerente de operações
        localStorage.setItem('userRole', 'energy_operations_manager');
        
        // Simulação de atualização em tempo real da demanda
        setInterval(function() {
            // Simular atualização de demanda (apenas visual)
            const demandElement = document.querySelector('.consumption-card .metric-value');
            if (demandElement) {
                const currentDemand = parseInt(demandElement.textContent);
                const variation = Math.floor(Math.random() * 21) - 10; // -10 a +10
                const newDemand = Math.max(1700, Math.min(2500, currentDemand + variation));
                demandElement.textContent = newDemand + ' MW';
            }
        }, 30000); // Atualiza a cada 30 segundos
    </script>
</body>
</html>