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
    <title>Dashboard Logística & Transporte</title>
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
        
        .vehicle-card {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 15px;
            transition: transform 0.2s;
        }
        
        .vehicle-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .vehicle-image {
            height: 120px;
            background: linear-gradient(135deg, #3498DB 0%, #2980B9 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
        }
        
        .vehicle-info {
            padding: 15px;
        }
        
        .vehicle-title {
            font-size: 1rem;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .vehicle-meta {
            font-size: 0.85rem;
            color: #6c757d;
            margin-bottom: 10px;
        }
        
        .vehicle-stats {
            display: flex;
            justify-content: space-between;
            font-size: 0.8rem;
        }
        
        .delivery-item {
            padding: 10px;
            border-left: 4px solid transparent;
            margin-bottom: 8px;
            border-radius: 5px;
            background: #f8f9fa;
        }
        
        .delivery-item.ontime { border-left-color: var(--secondary-color); }
        .delivery-item.delayed { border-left-color: var(--danger-color); }
        .delivery-item.inprogress { border-left-color: var(--primary-color); }
        .delivery-item.completed { border-left-color: var(--warning-color); }
        
        .route-status {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 8px;
            background: #f8f9fa;
        }
        
        .route-status.optimal { border-left: 4px solid var(--secondary-color); }
        .route-status.congested { border-left: 4px solid var(--warning-color); }
        .route-status.blocked { border-left: 4px solid var(--danger-color); }
        
        .map-container {
            background: linear-gradient(135deg, #2C3E50 0%, #34495E 100%);
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
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
                        <li class="breadcrumb-item active">Logística & Transporte</li>
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
                        <li><h6 class="dropdown-header">Alertas de Logística</h6></li>
                        <li><a class="dropdown-item" href="#">Entrega atrasada - Carga #4587</a></li>
                        <li><a class="dropdown-item" href="#">Manutenção necessária - Caminhão ABC-1234</a></li>
                        <li><a class="dropdown-item" href="#">Trânsito intenso - Rota SP-RJ</a></li>
                        <li><a class="dropdown-item" href="#">Carga extraviada - Nota #7845</a></li>
                        <li><a class="dropdown-item" href="#">Novo pedido urgente</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-center" href="#">Ver todos</a></li>
                    </ul>
                </div>
                
                <!-- Dropdown do Usuário -->
                <div class="dropdown user-dropdown">
                    <div class="user-avatar" data-bs-toggle="dropdown" aria-expanded="false">
                        L
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><h6 class="dropdown-header user-name">Gerente de Logística</h6></li>
                        <li><small class="dropdown-header text-muted user-email">logistica@empresa.com</small></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="perfil.html"><i class="bi bi-person me-2"></i>Meu Perfil</a></li>
                        <li><a class="dropdown-item" href="#"><i class="bi bi-geo-alt me-2"></i>Rotas</a></li>
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
                    <h1 class="page-title">Dashboard Logística & Transporte</h1>
                    <p class="page-subtitle">Gestão de frota, rotas e entregas em tempo real</p>
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
                            <i class="bi bi-truck"></i>
                        </div>
                        <div class="metric-value">42</div>
                        <div class="metric-label">Entregas Hoje</div>
                        <div class="metric-change positive">
                            <i class="bi bi-arrow-up"></i> +8 vs ontem
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="metric-card success">
                        <div class="metric-icon success">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        <div class="metric-value">94.5%</div>
                        <div class="metric-label">Taxa de Entrega</div>
                        <div class="metric-change positive">
                            <i class="bi bi-arrow-up"></i> +2.1% este mês
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="metric-card warning">
                        <div class="metric-icon warning">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <div class="metric-value">18.2%</div>
                        <div class="metric-label">Entregas Atrasadas</div>
                        <div class="metric-change negative">
                            <i class="bi bi-arrow-up"></i> +3.5% vs semana passada
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="metric-card danger">
                        <div class="metric-icon danger">
                            <i class="bi bi-speedometer2"></i>
                        </div>
                        <div class="metric-value">R$ 8.245</div>
                        <div class="metric-label">Custo Combustível</div>
                        <div class="metric-change negative">
                            <i class="bi bi-arrow-up"></i> +12% vs mês anterior
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Gráficos e Dados -->
            <div class="row mb-4">
                <!-- Desempenho de Entregas -->
                <div class="col-xl-8 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Desempenho de Entregas - Semana Atual</h5>
                            <div class="btn-group btn-group-sm" role="group">
                                <input type="radio" class="btn-check" name="period" id="period7" checked>
                                <label class="btn btn-outline-primary" for="period7">7 dias</label>
                                
                                <input type="radio" class="btn-check" name="period" id="period30">
                                <label class="btn btn-outline-primary" for="period30">30 dias</label>
                                
                                <input type="radio" class="btn-check" name="period" id="period90">
                                <label class="btn btn-outline-primary" for="period90">Trimestre</label>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="deliveryChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
                
                <!-- Mapa de Rotas -->
                <div class="col-xl-4 mb-4">
                    <div class="map-container">
                        <div class="d-flex justify-content-between align-items-center mb-3 w-100">
                            <h5 class="mb-0">Mapa de Rotas Ativas</h5>
                            <i class="bi bi-geo-alt" style="font-size: 2rem;"></i>
                        </div>
                        <div class="text-center mb-3">
                            <div class="metric-value">8</div>
                            <div class="metric-label">Rotas em Andamento</div>
                        </div>
                        <div class="row text-center w-100">
                            <div class="col-4">
                                <div class="mb-2">
                                    <i class="bi bi-check-circle"></i>
                                    <div class="small">5</div>
                                    <div class="small">No prazo</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="mb-2">
                                    <i class="bi bi-clock"></i>
                                    <div class="small">2</div>
                                    <div class="small">Atrasadas</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="mb-2">
                                    <i class="bi bi-exclamation-triangle"></i>
                                    <div class="small">1</div>
                                    <div class="small">Críticas</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Distribuição de Cargas -->
                    <div class="card-custom mt-3">
                        <div class="card-header-custom">
                            <h5 class="mb-0">Tipo de Cargas</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="cargoChart" height="250"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Frota e Entregas -->
            <div class="row mb-4">
                <!-- Frota em Operação -->
                <div class="col-xl-6 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Frota em Operação</h5>
                            <a href="frota.html" class="btn btn-sm btn-outline-primary">Ver frota completa</a>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="vehicle-card">
                                        <div class="vehicle-image" style="background: linear-gradient(135deg, #3498DB 0%, #2980B9 100%);">
                                            <i class="bi bi-truck"></i>
                                        </div>
                                        <div class="vehicle-info">
                                            <div class="vehicle-title">ABC-1234</div>
                                            <div class="vehicle-meta">Volvo FH • 40T</div>
                                            <div class="vehicle-stats">
                                                <span><i class="bi bi-speedometer2"></i> 85%</span>
                                                <span class="text-success">Em rota</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <div class="vehicle-card">
                                        <div class="vehicle-image" style="background: linear-gradient(135deg, #2ECC71 0%, #27AE60 100%);">
                                            <i class="bi bi-truck"></i>
                                        </div>
                                        <div class="vehicle-info">
                                            <div class="vehicle-title">DEF-5678</div>
                                            <div class="vehicle-meta">Mercedes • 35T</div>
                                            <div class="vehicle-stats">
                                                <span><i class="bi bi-speedometer2"></i> 92%</span>
                                                <span class="text-success">Em rota</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <div class="vehicle-card">
                                        <div class="vehicle-image" style="background: linear-gradient(135deg, #F39C12 0%, #D68910 100%);">
                                            <i class="bi bi-truck"></i>
                                        </div>
                                        <div class="vehicle-info">
                                            <div class="vehicle-title">GHI-9012</div>
                                            <div class="vehicle-meta">Scania • 38T</div>
                                            <div class="vehicle-stats">
                                                <span><i class="bi bi-speedometer2"></i> 45%</span>
                                                <span class="text-warning">Carregando</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <div class="vehicle-card">
                                        <div class="vehicle-image" style="background: linear-gradient(135deg, #E74C3C 0%, #CB4335 100%);">
                                            <i class="bi bi-truck"></i>
                                        </div>
                                        <div class="vehicle-info">
                                            <div class="vehicle-title">JKL-3456</div>
                                            <div class="vehicle-meta">Volkswagen • 14T</div>
                                            <div class="vehicle-stats">
                                                <span><i class="bi bi-tools"></i> 0%</span>
                                                <span class="text-danger">Manutenção</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Entregas do Dia -->
                <div class="col-xl-6 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Entregas do Dia</h5>
                            <a href="entregas.html" class="btn btn-sm btn-outline-primary">Ver todas</a>
                        </div>
                        <div class="card-body">
                            <div class="delivery-item inprogress">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>#ENT-4587 - Eletrônicos</strong>
                                        <div class="small">Cliente: Tech Store • ABC-1234</div>
                                        <div class="small text-muted">Previsão: 14:30 • 45min restantes</div>
                                    </div>
                                    <span class="badge bg-primary">Em rota</span>
                                </div>
                            </div>
                            
                            <div class="delivery-item inprogress">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>#ENT-4588 - Móveis</strong>
                                        <div class="small">Cliente: Casa Nova • DEF-5678</div>
                                        <div class="small text-muted">Previsão: 15:45 • 2h restantes</div>
                                    </div>
                                    <span class="badge bg-primary">Em rota</span>
                                </div>
                            </div>
                            
                            <div class="delivery-item delayed">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>#ENT-4586 - Alimentos</strong>
                                        <div class="small">Cliente: Super Mercado • GHI-9012</div>
                                        <div class="small text-muted">Atraso: 35min • Trânsito intenso</div>
                                    </div>
                                    <span class="badge bg-danger">Atrasada</span>
                                </div>
                            </div>
                            
                            <div class="delivery-item completed">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>#ENT-4585 - Roupas</strong>
                                        <div class="small">Cliente: Fashion Mall • MNO-7890</div>
                                        <div class="small text-muted">Entregue às 11:20 • Assinatura OK</div>
                                    </div>
                                    <span class="badge bg-success">Concluída</span>
                                </div>
                            </div>
                            
                            <div class="mt-3">
                                <h6 class="mb-3">Resumo do Dia</h6>
                                <div class="row text-center">
                                    <div class="col-4">
                                        <div class="metric-value text-primary">28</div>
                                        <div class="metric-label">Concluídas</div>
                                    </div>
                                    <div class="col-4">
                                        <div class="metric-value text-success">14</div>
                                        <div class="metric-label">Em andamento</div>
                                    </div>
                                    <div class="col-4">
                                        <div class="metric-value text-warning">3</div>
                                        <div class="metric-label">Atrasadas</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Rotas e Monitoramento -->
            <div class="row mb-4">
                <!-- Status das Rotas -->
                <div class="col-xl-6 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Status das Rotas</h5>
                            <a href="rotas.html" class="btn btn-sm btn-outline-primary">Otimizar rotas</a>
                        </div>
                        <div class="card-body">
                            <div class="route-status optimal">
                                <div class="me-3">
                                    <i class="bi bi-check-circle-fill text-success" style="font-size: 1.5rem;"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <strong>SP → RJ - Rota 01</strong>
                                    <div class="small text-muted">ABC-1234 • 420km • 5 entregas</div>
                                </div>
                                <div class="text-end">
                                    <div class="gauge-container">
                                        <div class="gauge-value">95%</div>
                                    </div>
                                    <div class="small text-muted">Eficiência</div>
                                </div>
                            </div>
                            
                            <div class="route-status optimal">
                                <div class="me-3">
                                    <i class="bi bi-check-circle-fill text-success" style="font-size: 1.5rem;"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <strong>SP → MG - Rota 02</strong>
                                    <div class="small text-muted">DEF-5678 • 580km • 8 entregas</div>
                                </div>
                                <div class="text-end">
                                    <div class="gauge-container">
                                        <div class="gauge-value">88%</div>
                                    </div>
                                    <div class="small text-muted">Eficiência</div>
                                </div>
                            </div>
                            
                            <div class="route-status congested">
                                <div class="me-3">
                                    <i class="bi bi-exclamation-triangle-fill text-warning" style="font-size: 1.5rem;"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <strong>SP → PR - Rota 03</strong>
                                    <div class="small text-muted">GHI-9012 • 320km • Trânsito intenso</div>
                                </div>
                                <div class="text-end">
                                    <div class="gauge-container">
                                        <div class="gauge-value">65%</div>
                                    </div>
                                    <div class="small text-muted">Eficiência</div>
                                </div>
                            </div>
                            
                            <div class="route-status blocked">
                                <div class="me-3">
                                    <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size: 1.5rem;"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <strong>SP → BA - Rota 04</strong>
                                    <div class="small text-muted">JKL-3456 • Interdição na BR-116</div>
                                </div>
                                <div class="text-end">
                                    <div class="gauge-container">
                                        <div class="gauge-value">0%</div>
                                    </div>
                                    <div class="small text-muted">Parada</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Custos e Indicadores -->
                <div class="col-xl-6 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Custos e Indicadores</h5>
                            <a href="custos.html" class="btn btn-sm btn-outline-primary">Relatório</a>
                        </div>
                        <div class="card-body">
                            <div class="row text-center mb-4">
                                <div class="col-4">
                                    <div class="mb-3">
                                        <div class="metric-value text-primary">R$ 42.8K</div>
                                        <div class="metric-label">Custo Mensal</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="mb-3">
                                        <div class="metric-value text-success">8.2 km/L</div>
                                        <div class="metric-label">Consumo Médio</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="mb-3">
                                        <div class="metric-value text-warning">R$ 0.38</div>
                                        <div class="metric-label">Custo/km</div>
                                    </div>
                                </div>
                            </div>
                            
                            <h6 class="mb-3">Distribuição de Custos</h6>
                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
                                <div>
                                    <strong>Combustível</strong>
                                    <div class="small text-muted">45% do custo total</div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-semibold">R$ 19.3K</div>
                                    <div class="small text-muted">+8% vs mês anterior</div>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
                                <div>
                                    <strong>Manutenção</strong>
                                    <div class="small text-muted">25% do custo total</div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-semibold">R$ 10.7K</div>
                                    <div class="small text-muted">+12% vs mês anterior</div>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
                                <div>
                                    <strong>Pedágios</strong>
                                    <div class="small text-muted">15% do custo total</div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-semibold">R$ 6.4K</div>
                                    <div class="small text-muted">+5% vs mês anterior</div>
                                </div>
                            </div>
                            
                            <div class="mt-3">
                                <h6 class="mb-3">Indicadores de Eficiência</h6>
                                <div class="row text-center">
                                    <div class="col-4">
                                        <div class="metric-value text-primary">94.5%</div>
                                        <div class="metric-label">OTIF</div>
                                    </div>
                                    <div class="col-4">
                                        <div class="metric-value text-success">78%</div>
                                        <div class="metric-label">Utilização</div>
                                    </div>
                                    <div class="col-4">
                                        <div class="metric-value text-info">2.1</div>
                                        <div class="metric-label">Rota/Veículo</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Alertas da Logística -->
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
                                        <i class="bi bi-clock me-3"></i>
                                        <div>
                                            <strong>3 entregas</strong> com atraso
                                            <div class="mt-1">
                                                <a href="#" class="btn btn-sm btn-warning">Reagendar</a>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="alert alert-info d-flex align-items-center mb-3">
                                        <i class="bi bi-tools me-3"></i>
                                        <div>
                                            <strong>Manutenção preventiva</strong> necessária
                                            <div class="mt-1">
                                                <small class="text-muted">2 veículos com 45.000km</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="alert alert-success d-flex align-items-center mb-3">
                                        <i class="bi bi-graph-up-arrow me-3"></i>
                                        <div>
                                            <strong>Eficiência de rotas</strong> acima da meta
                                            <div class="mt-1">
                                                <small class="text-muted">+8% vs mês anterior</small>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="alert alert-danger d-flex align-items-center mb-0">
                                        <i class="bi bi-exclamation-triangle me-3"></i>
                                        <div>
                                            <strong>Rota interrompida</strong> na BR-116
                                            <div class="mt-1">
                                                <a href="rotas.html" class="btn btn-sm btn-danger">Redirecionar</a>
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

        // Gráficos específicos do segmento de logística e transporte
        document.addEventListener('DOMContentLoaded', function() {
            // Gráfico de Desempenho de Entregas
            const deliveryCtx = document.getElementById('deliveryChart').getContext('2d');
            const deliveryChart = new Chart(deliveryCtx, {
                type: 'bar',
                data: {
                    labels: ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'],
                    datasets: [{
                        label: 'Entregas Concluídas',
                        data: [45, 52, 48, 65, 72, 58, 42],
                        backgroundColor: '#3498DB',
                        borderColor: '#2980B9',
                        borderWidth: 1
                    }, {
                        label: 'Entregas Atrasadas',
                        data: [8, 5, 12, 7, 15, 9, 11],
                        backgroundColor: '#E74C3C',
                        borderColor: '#CB4335',
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
                                text: 'Número de Entregas'
                            }
                        }
                    }
                }
            });
            
            // Gráfico de Tipo de Cargas
            const cargoCtx = document.getElementById('cargoChart').getContext('2d');
            const cargoChart = new Chart(cargoCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Eletrônicos', 'Alimentos', 'Móveis', 'Roupas', 'Outros'],
                    datasets: [{
                        data: [35, 25, 20, 15, 5],
                        backgroundColor: [
                            '#3498DB',
                            '#2ECC71',
                            '#F39C12',
                            '#E74C3C',
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
            nexusFlow.showNotification('Gerando relatório logístico...', 'info');
            setTimeout(() => {
                nexusFlow.showNotification('Relatório exportado com sucesso!', 'success');
            }, 1500);
        }
        
        function refreshDashboard() {
            nexusFlow.showNotification('Atualizando dados de logística...', 'info');
            setTimeout(() => {
                nexusFlow.showNotification('Dados atualizados!', 'success');
            }, 1000);
        }
        
        // Definir papel como gerente de logística
        localStorage.setItem('userRole', 'logistics_manager');
        
        // Simulação de atualização em tempo real das entregas
        setInterval(function() {
            // Simular atualização de status de entregas (apenas visual)
            const inProgressItems = document.querySelectorAll('.delivery-item.inprogress');
            if (inProgressItems.length > 0) {
                const randomItem = inProgressItems[Math.floor(Math.random() * inProgressItems.length)];
                if (randomItem) {
                    randomItem.classList.remove('inprogress');
                    randomItem.classList.add('completed');
                    const badge = randomItem.querySelector('.badge');
                    if (badge) {
                        badge.className = 'badge bg-success';
                        badge.textContent = 'Concluída';
                        const timeText = randomItem.querySelector('.small.text-muted');
                        if (timeText) {
                            timeText.textContent = 'Entregue agora • Assinatura OK';
                        }
                    }
                }
            }
        }, 45000); // Atualiza a cada 45 segundos
    </script>
</body>
</html>