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
    <title>Dashboard Industrial</title>
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
        
        .production-line {
            padding: 10px;
            border-left: 4px solid transparent;
            margin-bottom: 8px;
            border-radius: 5px;
            background: #f8f9fa;
        }
        
        .production-line.running { border-left-color: var(--secondary-color); }
        .production-line.idle { border-left-color: var(--warning-color); }
        .production-line.stopped { border-left-color: var(--danger-color); }
        
        .machine-status {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 8px;
            background: #f8f9fa;
        }
        
        .machine-status.operational { border-left: 4px solid var(--secondary-color); }
        .machine-status.maintenance { border-left: 4px solid var(--warning-color); }
        .machine-status.broken { border-left: 4px solid var(--danger-color); }
        
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
                        <li class="breadcrumb-item active">Industrial</li>
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
                        <li><h6 class="dropdown-header">Alertas da Fábrica</h6></li>
                        <li><a class="dropdown-item" href="#">Manutenção preventiva necessária</a></li>
                        <li><a class="dropdown-item" href="#">Estoque de matéria-prima crítico</a></li>
                        <li><a class="dropdown-item" href="#">Parada não programada - Linha B</a></li>
                        <li><a class="dropdown-item" href="#">Qualidade abaixo do padrão</a></li>
                        <li><a class="dropdown-item" href="#">Pedido urgente para produção</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-center" href="#">Ver todos</a></li>
                    </ul>
                </div>
                
                <!-- Dropdown do Usuário -->
                <div class="dropdown user-dropdown">
                    <div class="user-avatar" data-bs-toggle="dropdown" aria-expanded="false">
                        I
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><h6 class="dropdown-header user-name">Gerente Industrial</h6></li>
                        <li><small class="dropdown-header text-muted user-email">industria@empresa.com</small></li>
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
                    <h1 class="page-title">Dashboard Industrial</h1>
                    <p class="page-subtitle">Monitoramento de produção, eficiência e manutenção</p>
                </div>
                <div>
                    <button class="btn btn-outline-primary me-2" onclick="exportReport()">
                        <i class="bi bi-file-earmark-spreadsheet me-2"></i>Relatório de Turno
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
                            <i class="bi bi-speedometer2"></i>
                        </div>
                        <div class="metric-value">94.2%</div>
                        <div class="metric-label">Eficiência Geral</div>
                        <div class="metric-change positive">
                            <i class="bi bi-arrow-up"></i> +2.1% vs semana passada
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="metric-card success">
                        <div class="metric-icon success">
                            <i class="bi bi-box-seam"></i>
                        </div>
                        <div class="metric-value">12,458</div>
                        <div class="metric-label">Unidades Produzidas</div>
                        <div class="metric-change positive">
                            <i class="bi bi-arrow-up"></i> +8% vs meta
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="metric-card warning">
                        <div class="metric-icon warning">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <div class="metric-value">3.2%</div>
                        <div class="metric-label">Tempo de Inatividade</div>
                        <div class="metric-change negative">
                            <i class="bi bi-arrow-up"></i> +0.8% vs turno anterior
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="metric-card danger">
                        <div class="metric-icon danger">
                            <i class="bi bi-exclamation-triangle"></i>
                        </div>
                        <div class="metric-value">1.8%</div>
                        <div class="metric-label">Taxa de Refugo</div>
                        <div class="metric-change positive">
                            <i class="bi bi-arrow-down"></i> -0.3% vs mês anterior
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Gráficos e Dados -->
            <div class="row mb-4">
                <!-- Produção e Eficiência -->
                <div class="col-xl-8 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Produção e Eficiência por Turno</h5>
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
                            <canvas id="productionChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
                
                <!-- Status das Linhas de Produção -->
                <div class="col-xl-4 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Status das Linhas</h5>
                            <span class="badge bg-primary">6 linhas</span>
                        </div>
                        <div class="card-body">
                            <div class="production-line running">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>Linha A - Montagem</strong>
                                        <div class="small">Produto: Componente X</div>
                                        <div class="small text-muted">Meta: 2.100/dia</div>
                                    </div>
                                    <span class="badge bg-success">Operando</span>
                                </div>
                                <div class="progress progress-thin mt-2">
                                    <div class="progress-bar bg-success" style="width: 92%"></div>
                                </div>
                                <div class="small text-muted mt-1">92% da capacidade</div>
                            </div>
                            
                            <div class="production-line running">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>Linha B - Pintura</strong>
                                        <div class="small">Produto: Componente Y</div>
                                        <div class="small text-muted">Meta: 1.800/dia</div>
                                    </div>
                                    <span class="badge bg-success">Operando</span>
                                </div>
                                <div class="progress progress-thin mt-2">
                                    <div class="progress-bar bg-success" style="width: 88%"></div>
                                </div>
                                <div class="small text-muted mt-1">88% da capacidade</div>
                            </div>
                            
                            <div class="production-line idle">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>Linha C - Testes</strong>
                                        <div class="small">Produto: Componente Z</div>
                                        <div class="small text-muted">Meta: 2.400/dia</div>
                                    </div>
                                    <span class="badge bg-warning">Troca de Lote</span>
                                </div>
                                <div class="progress progress-thin mt-2">
                                    <div class="progress-bar bg-warning" style="width: 45%"></div>
                                </div>
                                <div class="small text-muted mt-1">45% da capacidade</div>
                            </div>
                            
                            <div class="production-line stopped">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>Linha D - Embalagem</strong>
                                        <div class="small">Produto: Final</div>
                                        <div class="small text-muted">Meta: 3.000/dia</div>
                                    </div>
                                    <span class="badge bg-danger">Manutenção</span>
                                </div>
                                <div class="progress progress-thin mt-2">
                                    <div class="progress-bar bg-danger" style="width: 0%"></div>
                                </div>
                                <div class="small text-muted mt-1">Parada para manutenção</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Máquinas e Manutenção -->
            <div class="row mb-4">
                <!-- Status das Máquinas -->
                <div class="col-xl-6 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Status das Máquinas</h5>
                            <a href="manutencao.html" class="btn btn-sm btn-outline-primary">Ver todas</a>
                        </div>
                        <div class="card-body">
                            <div class="machine-status operational">
                                <div class="me-3">
                                    <i class="bi bi-check-circle-fill text-success" style="font-size: 1.5rem;"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <strong>Prensa Hidráulica #01</strong>
                                    <div class="small text-muted">Linha A • 2.148h de operação</div>
                                </div>
                                <div class="text-end">
                                    <div class="gauge-container">
                                        <div class="gauge-value">98%</div>
                                    </div>
                                    <div class="small text-muted">Eficiência</div>
                                </div>
                            </div>
                            
                            <div class="machine-status operational">
                                <div class="me-3">
                                    <i class="bi bi-check-circle-fill text-success" style="font-size: 1.5rem;"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <strong>Robô Soldador #03</strong>
                                    <div class="small text-muted">Linha B • 1.845h de operação</div>
                                </div>
                                <div class="text-end">
                                    <div class="gauge-container">
                                        <div class="gauge-value">95%</div>
                                    </div>
                                    <div class="small text-muted">Eficiência</div>
                                </div>
                            </div>
                            
                            <div class="machine-status maintenance">
                                <div class="me-3">
                                    <i class="bi bi-tools text-warning" style="font-size: 1.5rem;"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <strong>Esteira Transportadora #02</strong>
                                    <div class="small text-muted">Linha D • Manutenção preventiva</div>
                                </div>
                                <div class="text-end">
                                    <div class="gauge-container">
                                        <div class="gauge-value">0%</div>
                                    </div>
                                    <div class="small text-muted">Parada</div>
                                </div>
                            </div>
                            
                            <div class="machine-status broken">
                                <div class="me-3">
                                    <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size: 1.5rem;"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <strong>Injetora #05</strong>
                                    <div class="small text-muted">Linha C • Falha no sistema hidráulico</div>
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
                
                <!-- Controle de Qualidade -->
                <div class="col-xl-6 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Controle de Qualidade</h5>
                            <a href="qualidade.html" class="btn btn-sm btn-outline-primary">Relatório</a>
                        </div>
                        <div class="card-body">
                            <div class="row text-center mb-4">
                                <div class="col-4">
                                    <div class="mb-3">
                                        <div class="metric-value text-success">98.2%</div>
                                        <div class="metric-label">Aprovação</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="mb-3">
                                        <div class="metric-value text-warning">1.8%</div>
                                        <div class="metric-label">Refugo</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="mb-3">
                                        <div class="metric-value text-danger">0.4%</div>
                                        <div class="metric-label">Retrabalho</div>
                                    </div>
                                </div>
                            </div>
                            
                            <h6 class="mb-3">Principais Não Conformidades</h6>
                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
                                <div>
                                    <strong>Dimensão Fora do Padrão</strong>
                                    <div class="small text-muted">Componente X - Linha A</div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-semibold text-danger">42 unidades</div>
                                    <div class="small text-muted">2.1% do lote</div>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
                                <div>
                                    <strong>Acabamento Irregular</strong>
                                    <div class="small text-muted">Componente Y - Linha B</div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-semibold text-warning">28 unidades</div>
                                    <div class="small text-muted">1.4% do lote</div>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
                                <div>
                                    <strong>Falha no Teste Final</strong>
                                    <div class="small text-muted">Produto Final - Linha C</div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-semibold text-danger">15 unidades</div>
                                    <div class="small text-muted">0.8% do lote</div>
                                </div>
                            </div>
                            
                            <div class="mt-3">
                                <h6 class="mb-3">Indicadores de Qualidade</h6>
                                <div class="row text-center">
                                    <div class="col-4">
                                        <div class="metric-value text-primary">6σ</div>
                                        <div class="metric-label">Sigma Level</div>
                                    </div>
                                    <div class="col-4">
                                        <div class="metric-value text-success">99.7%</div>
                                        <div class="metric-label">CPK</div>
                                    </div>
                                    <div class="col-4">
                                        <div class="metric-value text-info">0.8</div>
                                        <div class="metric-label">PPM</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Ordens de Produção -->
            <div class="row">
                <!-- Ordens em Andamento -->
                <div class="col-xl-6 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Ordens de Produção</h5>
                            <a href="ordens.html" class="btn btn-sm btn-outline-primary">Nova ordem</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-custom mb-0">
                                    <thead>
                                        <tr>
                                            <th>Ordem</th>
                                            <th>Produto</th>
                                            <th>Quantidade</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div class="fw-semibold">#OP-2023-045</div>
                                                <small class="text-muted">Prazo: 20/06</small>
                                            </td>
                                            <td>Componente X</td>
                                            <td>5.000 unidades</td>
                                            <td><span class="status-badge status-active">Em produção</span></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="fw-semibold">#OP-2023-046</div>
                                                <small class="text-muted">Prazo: 22/06</small>
                                            </td>
                                            <td>Componente Y</td>
                                            <td>3.500 unidades</td>
                                            <td><span class="status-badge status-active">Em produção</span></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="fw-semibold">#OP-2023-047</div>
                                                <small class="text-muted">Prazo: 25/06</small>
                                            </td>
                                            <td>Produto Final Z</td>
                                            <td>2.000 unidades</td>
                                            <td><span class="status-badge status-pending">Planejada</span></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="fw-semibold">#OP-2023-044</div>
                                                <small class="text-muted">Concluída: 15/06</small>
                                            </td>
                                            <td>Componente W</td>
                                            <td>4.200 unidades</td>
                                            <td><span class="status-badge status-active">Concluída</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Estoque e Matéria-Prima -->
                <div class="col-xl-6 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Estoque e Matéria-Prima</h5>
                            <a href="estoque.html" class="btn btn-sm btn-outline-primary">Ver estoque</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-custom mb-0">
                                    <thead>
                                        <tr>
                                            <th>Material</th>
                                            <th>Estoque Atual</th>
                                            <th>Estoque Mínimo</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div class="fw-semibold">Aço Inox</div>
                                                <small class="text-muted">Lote: #AC-4587</small>
                                            </td>
                                            <td>1.250 kg</td>
                                            <td>800 kg</td>
                                            <td><span class="status-badge status-active">Normal</span></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="fw-semibold">Plástico ABS</div>
                                                <small class="text-muted">Lote: #PL-3214</small>
                                            </td>
                                            <td>480 kg</td>
                                            <td>600 kg</td>
                                            <td><span class="status-badge status-warning">Baixo</span></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="fw-semibold">Componentes Eletrônicos</div>
                                                <small class="text-muted">Lote: #CE-7854</small>
                                            </td>
                                            <td>8.500 unidades</td>
                                            <td>5.000 unidades</td>
                                            <td><span class="status-badge status-active">Normal</span></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="fw-semibold">Embalagens</div>
                                                <small class="text-muted">Lote: #EM-9652</small>
                                            </td>
                                            <td>2.100 unidades</td>
                                            <td>3.000 unidades</td>
                                            <td><span class="status-badge status-warning">Baixo</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Alertas da Indústria -->
            <div class="row">
                <div class="col-12">
                    <div class="card-custom">
                        <div class="card-header-custom">
                            <h5 class="mb-0">Alertas e Manutenções</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="alert alert-warning d-flex align-items-center mb-3">
                                        <i class="bi bi-tools me-3"></i>
                                        <div>
                                            <strong>Manutenção preventiva</strong> necessária
                                            <div class="mt-1">
                                                <a href="manutencao.html" class="btn btn-sm btn-warning">Agendar</a>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="alert alert-info d-flex align-items-center mb-3">
                                        <i class="bi bi-box-seam me-3"></i>
                                        <div>
                                            <strong>2 materiais</strong> com estoque crítico
                                            <div class="mt-1">
                                                <small class="text-muted">Plástico ABS e Embalagens</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="alert alert-success d-flex align-items-center mb-3">
                                        <i class="bi bi-graph-up-arrow me-3"></i>
                                        <div>
                                            <strong>Meta de produção</strong> superada
                                            <div class="mt-1">
                                                <small class="text-muted">+8% acima da meta deste mês</small>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="alert alert-danger d-flex align-items-center mb-0">
                                        <i class="bi bi-exclamation-triangle me-3"></i>
                                        <div>
                                            <strong>2 máquinas</strong> paradas
                                            <div class="mt-1">
                                                <a href="maquinas.html" class="btn btn-sm btn-danger">Ver detalhes</a>
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

        // Gráficos específicos do segmento industrial
        document.addEventListener('DOMContentLoaded', function() {
            // Gráfico de Produção e Eficiência
            const productionCtx = document.getElementById('productionChart').getContext('2d');
            const productionChart = new Chart(productionCtx, {
                type: 'bar',
                data: {
                    labels: ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'],
                    datasets: [{
                        label: 'Produção (Unidades)',
                        data: [11500, 12400, 11800, 13200, 12800, 9800, 10500],
                        backgroundColor: '#3498DB',
                        borderColor: '#2980B9',
                        borderWidth: 1,
                        yAxisID: 'y'
                    }, {
                        label: 'Eficiência (%)',
                        data: [92, 94, 91, 95, 93, 88, 90],
                        type: 'line',
                        borderColor: '#2ECC71',
                        backgroundColor: 'rgba(46, 204, 113, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        yAxisID: 'y1'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    scales: {
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            title: {
                                display: true,
                                text: 'Produção (Unidades)'
                            }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            title: {
                                display: true,
                                text: 'Eficiência (%)'
                            },
                            grid: {
                                drawOnChartArea: false,
                            },
                            min: 80,
                            max: 100
                        }
                    }
                }
            });
        });
        
        function exportReport() {
            nexusFlow.showNotification('Gerando relatório industrial...', 'info');
            setTimeout(() => {
                nexusFlow.showNotification('Relatório exportado com sucesso!', 'success');
            }, 1500);
        }
        
        function refreshDashboard() {
            nexusFlow.showNotification('Atualizando dados da fábrica...', 'info');
            setTimeout(() => {
                nexusFlow.showNotification('Dados atualizados!', 'success');
            }, 1000);
        }
        
        // Definir papel como gerente industrial
        localStorage.setItem('userRole', 'industrial_manager');
        
        // Simulação de atualização em tempo real do status das linhas
        setInterval(function() {
            // Simular mudança de status das linhas (apenas visual)
            const stoppedLines = document.querySelectorAll('.production-line.stopped');
            if (stoppedLines.length > 0) {
                const randomLine = stoppedLines[Math.floor(Math.random() * stoppedLines.length)];
                if (randomLine) {
                    randomLine.classList.remove('stopped');
                    randomLine.classList.add('running');
                    const badge = randomLine.querySelector('.badge');
                    const progress = randomLine.querySelector('.progress-bar');
                    if (badge && progress) {
                        badge.className = 'badge bg-success';
                        badge.textContent = 'Operando';
                        progress.className = 'progress-bar bg-success';
                        progress.style.width = '85%';
                        randomLine.querySelector('.small.text-muted').textContent = '85% da capacidade';
                    }
                }
            }
        }, 45000); // Atualiza a cada 45 segundos
    </script>
</body>
</html>