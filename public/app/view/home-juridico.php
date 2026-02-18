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
    <title>Dashboard Jurídico - NexusFlow</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <style>
        :root {
            --primary-color: #2E86AB;
            --secondary-color: #A23B72;
            --success-color: #1B998B;
            --warning-color: #F46036;
            --danger-color: #C44536;
            --legal-color: #14213D;
            --case-color: #FCA311;
            --client-color: #E5E5E5;
            --deadline-color: #3A86FF;
            --light-bg: #F8F9FA;
            --dark-bg: #212529;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            color: #333;
        }
        
        
        .main-header {
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            padding: 1rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .header-left, .header-right {
            display: flex;
            align-items: center;
        }
        
        .sidebar-toggle {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--legal-color);
            margin-right: 1rem;
        }
        
        .breadcrumb-custom {
            margin-bottom: 0;
        }
        
        .breadcrumb-custom .breadcrumb-item.active {
            color: var(--legal-color);
            font-weight: 600;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--legal-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            cursor: pointer;
        }
        
        .content-area {
            padding: 1.5rem;
        }
        
        .page-title {
            font-weight: 700;
            color: var(--legal-color);
            margin-bottom: 0.25rem;
        }
        
        .page-subtitle {
            color: #6c757d;
            margin-bottom: 0;
        }
        
        .metric-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            transition: transform 0.3s, box-shadow 0.3s;
            height: 100%;
            position: relative;
            overflow: hidden;
        }
        
        .metric-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px rgba(0,0,0,0.1);
        }
        
        .metric-card.legal {
            border-left: 4px solid var(--legal-color);
        }
        
        .metric-card.case {
            border-left: 4px solid var(--case-color);
        }
        
        .metric-card.client {
            border-left: 4px solid var(--client-color);
        }
        
        .metric-card.deadline {
            border-left: 4px solid var(--deadline-color);
        }
        
        .metric-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }
        
        .metric-icon.legal {
            background: rgba(20, 33, 61, 0.1);
            color: var(--legal-color);
        }
        
        .metric-icon.case {
            background: rgba(252, 163, 17, 0.1);
            color: var(--case-color);
        }
        
        .metric-icon.client {
            background: rgba(229, 229, 229, 0.3);
            color: #6c757d;
        }
        
        .metric-icon.deadline {
            background: rgba(58, 134, 255, 0.1);
            color: var(--deadline-color);
        }
        
        .metric-value {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }
        
        .metric-label {
            color: #6c757d;
            margin-bottom: 0.5rem;
        }
        
        .metric-change {
            font-size: 0.85rem;
            font-weight: 500;
        }
        
        .metric-change.positive {
            color: var(--success-color);
        }
        
        .metric-change.negative {
            color: var(--danger-color);
        }
        
        .card-custom {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            overflow: hidden;
            height: 100%;
        }
        
        .card-header-custom {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #e9ecef;
            background: white;
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        .table-custom {
            margin-bottom: 0;
        }
        
        .table-custom th {
            border-top: none;
            font-weight: 600;
            color: #495057;
            background-color: #f8f9fa;
        }
        
        .status-badge {
            padding: 0.35rem 0.65rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .status-active {
            background: rgba(27, 153, 139, 0.1);
            color: var(--success-color);
        }
        
        .status-pending {
            background: rgba(244, 96, 54, 0.1);
            color: var(--warning-color);
        }
        
        .status-closed {
            background: rgba(108, 117, 125, 0.1);
            color: #6c757d;
        }
        
        .status-urgent {
            background: rgba(196, 69, 54, 0.1);
            color: var(--danger-color);
        }
        
        .area-badge {
            padding: 0.35rem 0.65rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .area-civil {
            background: rgba(20, 33, 61, 0.1);
            color: var(--legal-color);
        }
        
        .area-labor {
            background: rgba(252, 163, 17, 0.1);
            color: var(--case-color);
        }
        
        .area-tributary {
            background: rgba(58, 134, 255, 0.1);
            color: var(--deadline-color);
        }
        
        .area-corporate {
            background: rgba(229, 229, 229, 0.3);
            color: #6c757d;
        }
        
        .notification-toast {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1050;
            min-width: 300px;
        }
        
        .progress-indicator {
            height: 6px;
            border-radius: 3px;
            background: #e9ecef;
            margin-top: 5px;
            overflow: hidden;
        }
        
        .progress-bar-custom {
            height: 100%;
            border-radius: 3px;
        }
        
        .progress-excellent {
            background: var(--success-color);
        }
        
        .progress-good {
            background: var(--legal-color);
        }
        
        .progress-average {
            background: var(--warning-color);
        }
        
        .progress-poor {
            background: var(--danger-color);
        }
        
        .case-priority-high {
            border-left: 4px solid var(--danger-color);
        }
        
        .case-priority-medium {
            border-left: 4px solid var(--warning-color);
        }
        
        .case-priority-low {
            border-left: 4px solid var(--success-color);
        }
        
        .legal-stats {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }
        
        .legal-stat {
            text-align: center;
        }
        
        .legal-stat-value {
            font-weight: 700;
            font-size: 1.1rem;
        }
        
        .legal-stat-label {
            font-size: 0.75rem;
            color: #6c757d;
        }
        
        .deadline-event {
            padding: 0.5rem;
            border-radius: 5px;
            margin-bottom: 0.5rem;
            font-size: 0.85rem;
        }
        
        .event-hearing {
            background: rgba(20, 33, 61, 0.1);
            border-left: 3px solid var(--legal-color);
        }
        
        .event-deadline {
            background: rgba(252, 163, 17, 0.1);
            border-left: 3px solid var(--case-color);
        }
        
        .event-trial {
            background: rgba(58, 134, 255, 0.1);
            border-left: 3px solid var(--deadline-color);
        }
        
        .event-meeting {
            background: rgba(229, 229, 229, 0.3);
            border-left: 3px solid #6c757d;
        }
        
        .risk-indicator {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-right: 5px;
        }
        
        .risk-high {
            background: var(--danger-color);
        }
        
        .risk-medium {
            background: var(--warning-color);
        }
        
        .risk-low {
            background: var(--success-color);
        }
        
        .performance-card {
            background: linear-gradient(135deg, #14213D, #FCA311);
            color: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1rem;
        }
        
        .success-rate {
            display: flex;
            justify-content: space-between;
            margin-top: 1rem;
        }
        
        .rate-item {
            text-align: center;
            flex: 1;
        }
        
        .rate-value {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 0.25rem;
        }
        
        .rate-label {
            font-size: 0.75rem;
            opacity: 0.9;
        }
        
        .court-badge {
            font-size: 0.7rem;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
        }
        
        @media (max-width: 768px) {
            .content-area {
                padding: 1rem;
            }
            
            .main-header {
                padding: 1rem;
            }
        }

        .gauge-container {
            position: relative;
            width: 120px;
            height: 60px;
            margin: 0 auto;
        }
        
        .gauge-background {
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 120px 120px 0 0;
            background: #e9ecef;
            overflow: hidden;
        }
        
        .gauge-fill {
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 120px 120px 0 0;
            transform-origin: center bottom;
        }
        
        .gauge-value {
            position: absolute;
            bottom: 5px;
            left: 0;
            right: 0;
            text-align: center;
            font-weight: bold;
            font-size: 1.2rem;
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
                        <li class="breadcrumb-item active">Jurídico</li>
                    </ol>
                </nav>
            </div>
            
            <div class="header-right">
                <!-- Notificações -->
                <div class="dropdown">
                    <button class="btn btn-link text-dark position-relative" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-bell" style="font-size: 1.25rem;"></i>
                        <span class="badge bg-danger position-absolute translate-middle rounded-pill" style="font-size: 0.6rem; top: 8px; right: 8px;">7</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><h6 class="dropdown-header">Alertas Jurídicos</h6></li>
                        <li><a class="dropdown-item" href="#">5 prazos processuais vencem hoje</a></li>
                        <li><a class="dropdown-item" href="#">Nova petição requer análise urgente</a></li>
                        <li><a class="dropdown-item" href="#">Audiência marcada para amanhã</a></li>
                        <li><a class="dropdown-item" href="#">Cliente aguarda retorno sobre contrato</a></li>
                        <li><a class="dropdown-item" href="#">Decisão judicial publicada</a></li>
                        <li><a class="dropdown-item" href="#">Honorários em atraso - 3 clientes</a></li>
                        <li><a class="dropdown-item" href="#">Alteração legislativa impacta casos</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-center" href="#">Ver todas</a></li>
                    </ul>
                </div>
                
                <!-- Dropdown do Usuário -->
                <div class="dropdown user-dropdown">
                    <div class="user-avatar" data-bs-toggle="dropdown" aria-expanded="false">
                        J
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><h6 class="dropdown-header user-name">Jurídico NexusFlow</h6></li>
                        <li><small class="dropdown-header text-muted user-email">juridico@nexusflow.com</small></li>
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
                    <h1 class="page-title">Dashboard Jurídico Estratégico</h1>
                    <p class="page-subtitle">Gestão de processos, prazos e performance do escritório</p>
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
            
            <!-- Card de Performance -->
            <div class="performance-card mb-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h3 class="text-white">Índice de Performance Jurídica</h3>
                        <p class="text-white mb-0">Avaliação estratégica dos resultados e eficiência do escritório</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="gauge-container">
                            <div class="gauge-background"></div>
                            <div class="gauge-fill" id="performanceGauge" style="background: var(--success-color); transform: rotate(0.72turn);"></div>
                            <div class="gauge-value text-white">86%</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Taxas de Sucesso -->
            <div class="card-custom mb-4">
                <div class="card-header-custom">
                    <h5 class="mb-0">Taxas de Sucesso por Área</h5>
                </div>
                <div class="card-body">
                    <div class="success-rate">
                        <div class="rate-item">
                            <div class="rate-value">78%</div>
                            <div class="rate-label">Cível</div>
                        </div>
                        <div class="rate-item">
                            <div class="rate-value">82%</div>
                            <div class="rate-label">Trabalhista</div>
                        </div>
                        <div class="rate-item">
                            <div class="rate-value">65%</div>
                            <div class="rate-label">Tributário</div>
                        </div>
                        <div class="rate-item">
                            <div class="rate-value">91%</div>
                            <div class="rate-label">Empresarial</div>
                        </div>
                        <div class="rate-item">
                            <div class="rate-value">74%</div>
                            <div class="rate-label">Consumerista</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Métricas Principais -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="metric-card legal">
                        <div class="metric-icon legal">
                            <i class="bi bi-folder"></i>
                        </div>
                        <div class="metric-value">347</div>
                        <div class="metric-label">Processos Ativos</div>
                        <div class="metric-change positive">
                            <i class="bi bi-arrow-up"></i> +18 este mês
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="metric-card case">
                        <div class="metric-icon case">
                            <i class="bi bi-calendar-event"></i>
                        </div>
                        <div class="metric-value">23</div>
                        <div class="metric-label">Prazos Críticos</div>
                        <div class="metric-change negative">
                            <i class="bi bi-arrow-up"></i> +5 esta semana
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="metric-card client">
                        <div class="metric-icon client">
                            <i class="bi bi-people"></i>
                        </div>
                        <div class="metric-value">128</div>
                        <div class="metric-label">Clientes Ativos</div>
                        <div class="metric-change positive">
                            <i class="bi bi-arrow-up"></i> +12 este trimestre
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="metric-card deadline">
                        <div class="metric-icon deadline">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>
                        <div class="metric-value">74%</div>
                        <div class="metric-label">Taxa de Sucesso</div>
                        <div class="metric-change positive">
                            <i class="bi bi-arrow-up"></i> +8% vs ano anterior
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Gráficos e Dados -->
            <div class="row mb-4">
                <!-- Gráfico de Distribuição de Casos -->
                <div class="col-xl-8 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Distribuição de Casos por Área - Últimos 6 Meses</h5>
                            <div class="btn-group btn-group-sm" role="group">
                                <input type="radio" class="btn-check" name="period" id="period7" checked>
                                <label class="btn btn-outline-primary" for="period7">Volume</label>
                                
                                <input type="radio" class="btn-check" name="period" id="period30">
                                <label class="btn btn-outline-primary" for="period30">Valor</label>
                                
                                <input type="radio" class="btn-check" name="period" id="period90">
                                <label class="btn btn-outline-primary" for="period90">Sucesso</label>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="caseDistributionChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
                
                <!-- Distribuição por Tribunal -->
                <div class="col-xl-4 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom">
                            <h5 class="mb-0">Distribuição por Tribunal</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="courtDistributionChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tabelas de Dados -->
            <div class="row">
                <!-- Casos Críticos -->
                <div class="col-xl-6 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Casos com Prazos Críticos</h5>
                            <a href="processos.html" class="btn btn-sm btn-outline-primary">Ver todos</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-custom mb-0">
                                    <thead>
                                        <tr>
                                            <th>Processo</th>
                                            <th>Área</th>
                                            <th>Status</th>
                                            <th>Prazo</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="case-priority-high">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                                            001
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold">0001234-56.2023.8.26.0100</div>
                                                        <small class="text-muted">Cliente: Construtora Vanguarda</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="area-badge area-civil">Cível</span></td>
                                            <td><span class="status-badge status-urgent">Crítico</span></td>
                                            <td>
                                                <div>Hoje - 14:00</div>
                                                <div class="progress-indicator">
                                                    <div class="progress-bar-custom progress-poor" style="width: 95%"></div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="case-priority-medium">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                                            002
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold">0005678-90.2023.5.12.0001</div>
                                                        <small class="text-muted">Cliente: Tech Inovação Ltda</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="area-badge area-labor">Trabalhista</span></td>
                                            <td><span class="status-badge status-pending">Urgente</span></td>
                                            <td>
                                                <div>Amanhã - 10:00</div>
                                                <div class="progress-indicator">
                                                    <div class="progress-bar-custom progress-average" style="width: 75%"></div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="case-priority-low">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                                            003
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold">0009012-34.2023.8.26.0050</div>
                                                        <small class="text-muted">Cliente: Indústria Nacional</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="area-badge area-tributary">Tributário</span></td>
                                            <td><span class="status-badge status-active">Atenção</span></td>
                                            <td>
                                                <div>2 dias - 16:00</div>
                                                <div class="progress-indicator">
                                                    <div class="progress-bar-custom progress-good" style="width: 50%"></div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Agenda Jurídica -->
                <div class="col-xl-6 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Agenda Jurídica</h5>
                            <a href="calendario.html" class="btn btn-sm btn-outline-primary">Ver completa</a>
                        </div>
                        <div class="card-body">
                            <div class="deadline-event event-hearing">
                                <div class="fw-bold">Audiência - Processo 0001234-56.2023.8.26.0100</div>
                                <small>Hoje - 14:00 | Tribunal: TJSP | Vara: 12ª Cível</small>
                            </div>
                            
                            <div class="deadline-event event-deadline">
                                <div class="fw-bold">Prazo para Contestação - Processo 0005678-90.2023.5.12.0001</div>
                                <small>Amanhã - 10:00 | Tribunal: TRT2 | Vara: 3ª Trabalhista</small>
                            </div>
                            
                            <div class="deadline-event event-trial">
                                <div class="fw-bold">Sessão de Julgamento - Processo 0009012-34.2023.8.26.0050</div>
                                <small>Quarta-feira - 09:00 | Tribunal: TST | Turma: 4ª</small>
                            </div>
                            
                            <div class="deadline-event event-meeting">
                                <div class="fw-bold">Reunião com Cliente - Contrato de Fusão</div>
                                <small>Quinta-feira - 15:00 | Cliente: Grupo Empresarial</small>
                            </div>
                            
                            <div class="deadline-event event-hearing">
                                <div class="fw-bold">Audiência de Conciliação - Processo 0003456-78.2023.8.26.0075</div>
                                <small>Sexta-feira - 11:00 | Tribunal: TJSP | Vara: 5ª Cível</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- KPIs de Performance -->
            <div class="row">
                <!-- Indicadores de Eficiência -->
                <div class="col-xl-6 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom">
                            <h5 class="mb-0">Indicadores de Eficiência</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <i class="bi bi-clock-history text-primary" style="font-size: 1.5rem;"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold">Tempo Médio de Resolução</div>
                                            <div class="legal-stats">
                                                <div class="legal-stat">
                                                    <div class="legal-stat-value"><span class="risk-indicator risk-medium"></span>8.2</div>
                                                    <div class="legal-stat-label">Meses</div>
                                                </div>
                                                <div class="legal-stat">
                                                    <div class="legal-stat-value"><span class="risk-indicator risk-low"></span>-12%</div>
                                                    <div class="legal-stat-label">vs Ano Anterior</div>
                                                </div>
                                                <div class="legal-stat">
                                                    <div class="legal-stat-value"><span class="risk-indicator risk-high"></span>14.5</div>
                                                    <div class="legal-stat-label">Máximo</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <i class="bi bi-cash-coin text-success" style="font-size: 1.5rem;"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold">Honorários</div>
                                            <div class="legal-stats">
                                                <div class="legal-stat">
                                                    <div class="legal-stat-value"><span class="risk-indicator risk-low"></span>R$ 2.8M</div>
                                                    <div class="legal-stat-label">Arrecadado</div>
                                                </div>
                                                <div class="legal-stat">
                                                    <div class="legal-stat-value"><span class="risk-indicator risk-medium"></span>R$ 450K</div>
                                                    <div class="legal-stat-label">Pendente</div>
                                                </div>
                                                <div class="legal-stat">
                                                    <div class="legal-stat-value"><span class="risk-indicator risk-high"></span>R$ 120K</div>
                                                    <div class="legal-stat-label">Inadimplente</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <i class="bi bi-graph-up text-warning" style="font-size: 1.5rem;"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold">Eficiência Processual</div>
                                            <div class="legal-stats">
                                                <div class="legal-stat">
                                                    <div class="legal-stat-value"><span class="risk-indicator risk-low"></span>92%</div>
                                                    <div class="legal-stat-label">Prazos Cumpridos</div>
                                                </div>
                                                <div class="legal-stat">
                                                    <div class="legal-stat-value"><span class="risk-indicator risk-medium"></span>78%</div>
                                                    <div class="legal-stat-label">Recursos Aceitos</div>
                                                </div>
                                                <div class="legal-stat">
                                                    <div class="legal-stat-value"><span class="risk-indicator risk-low"></span>86%</div>
                                                    <div class="legal-stat-label">Satisfação</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <i class="bi bi-shield-check text-info" style="font-size: 1.5rem;"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold">Risco Jurídico</div>
                                            <div class="legal-stats">
                                                <div class="legal-stat">
                                                    <div class="legal-stat-value"><span class="risk-indicator risk-low"></span>12%</div>
                                                    <div class="legal-stat-label">Baixo</div>
                                                </div>
                                                <div class="legal-stat">
                                                    <div class="legal-stat-value"><span class="risk-indicator risk-medium"></span>23%</div>
                                                    <div class="legal-stat-label">Médio</div>
                                                </div>
                                                <div class="legal-stat">
                                                    <div class="legal-stat-value"><span class="risk-indicator risk-high"></span>8%</div>
                                                    <div class="legal-stat-label">Alto</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Alertas Estratégicos -->
                <div class="col-xl-6 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom">
                            <h5 class="mb-0">Alertas Estratégicos</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-warning d-flex align-items-center mb-3">
                                <i class="bi bi-exclamation-triangle me-3"></i>
                                <div>
                                    <strong>5 prazos processuais</strong> vencem nas próximas 24 horas
                                    <div class="mt-1">
                                        <a href="#" class="btn btn-sm btn-warning">Ver detalhes</a>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="alert alert-info d-flex align-items-center mb-3">
                                <i class="bi bi-journal-text me-3"></i>
                                <div>
                                    <strong>Nova Súmula do STJ</strong> impacta 18 casos ativos
                                    <div class="mt-1">
                                        <a href="#" class="btn btn-sm btn-info">Analisar impacto</a>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="alert alert-success d-flex align-items-center mb-3">
                                <i class="bi bi-award me-3"></i>
                                <div>
                                    <strong>Taxa de sucesso</strong> aumentou 8% este trimestre
                                    <div class="mt-1">
                                        <small class="text-muted">Parabéns à equipe jurídica!</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="alert alert-danger d-flex align-items-center mb-0">
                                <i class="bi bi-shield-exclamation me-3"></i>
                                <div>
                                    <strong>Risco alto identificado</strong> em 3 processos estratégicos
                                    <div class="mt-1">
                                        <a href="#" class="btn btn-sm btn-danger">Mitigar riscos</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
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
            
            // Configurar gráficos
            // Gráfico de Distribuição de Casos
            const caseDistributionCtx = document.getElementById('caseDistributionChart').getContext('2d');
            const caseDistributionChart = new Chart(caseDistributionCtx, {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun'],
                    datasets: [{
                        label: 'Cível',
                        data: [18, 22, 25, 28, 24, 30],
                        backgroundColor: 'rgba(20, 33, 61, 0.7)',
                        borderColor: 'rgba(20, 33, 61, 1)',
                        borderWidth: 1
                    }, {
                        label: 'Trabalhista',
                        data: [12, 15, 18, 14, 16, 20],
                        backgroundColor: 'rgba(252, 163, 17, 0.7)',
                        borderColor: 'rgba(252, 163, 17, 1)',
                        borderWidth: 1
                    }, {
                        label: 'Tributário',
                        data: [8, 10, 12, 15, 14, 16],
                        backgroundColor: 'rgba(58, 134, 255, 0.7)',
                        borderColor: 'rgba(58, 134, 255, 1)',
                        borderWidth: 1
                    }, {
                        label: 'Empresarial',
                        data: [5, 8, 10, 12, 15, 18],
                        backgroundColor: 'rgba(229, 229, 229, 0.7)',
                        borderColor: 'rgba(229, 229, 229, 1)',
                        borderWidth: 1
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
            
            // Gráfico de Distribuição por Tribunal
            const courtDistributionCtx = document.getElementById('courtDistributionChart').getContext('2d');
            const courtDistributionChart = new Chart(courtDistributionCtx, {
                type: 'doughnut',
                data: {
                    labels: ['TJSP', 'TRT2', 'TJMG', 'STJ', 'TST', 'Outros'],
                    datasets: [{
                        data: [35, 22, 15, 12, 8, 8],
                        backgroundColor: [
                            '#14213D',
                            '#FCA311',
                            '#3A86FF',
                            '#1B998B',
                            '#2E86AB',
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
            
            // Função para mostrar notificações
            window.nexusFlow = {
                showNotification: function(message, type = 'info') {
                    // Criar elemento de toast
                    const toast = document.createElement('div');
                    toast.className = `toast align-items-center text-bg-${type} border-0`;
                    toast.setAttribute('role', 'alert');
                    toast.setAttribute('aria-live', 'assertive');
                    toast.setAttribute('aria-atomic', 'true');
                    
                    toast.innerHTML = `
                        <div class="d-flex">
                            <div class="toast-body">
                                ${message}
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                    `;
                    
                    // Adicionar ao container de notificações
                    const container = document.getElementById('notificationContainer') || createNotificationContainer();
                    container.appendChild(toast);
                    
                    // Inicializar e mostrar o toast
                    const bsToast = new bootstrap.Toast(toast);
                    bsToast.show();
                    
                    // Remover o toast após ser escondido
                    toast.addEventListener('hidden.bs.toast', function() {
                        toast.remove();
                    });
                }
            };
            
            function createNotificationContainer() {
                const container = document.createElement('div');
                container.id = 'notificationContainer';
                container.className = 'notification-toast';
                document.body.appendChild(container);
                return container;
            }
        });
        
        // Funções específicas da página
        function exportReport() {
            nexusFlow.showNotification('Exportando relatório jurídico...', 'info');
            setTimeout(() => {
                nexusFlow.showNotification('Relatório exportado com sucesso!', 'success');
            }, 2000);
        }
        
        function refreshDashboard() {
            nexusFlow.showNotification('Atualizando dashboard jurídico...', 'info');
            setTimeout(() => {
                nexusFlow.showNotification('Dashboard atualizado!', 'success');
                // Em uma aplicação real, aqui seria feita uma requisição para atualizar os dados
                // Por simplicidade, apenas recarregamos a página
                location.reload();
            }, 1000);
        }
        
        // Definir papel como jurídico
        localStorage.setItem('userRole', 'juridico');
    </script>
</body>
</html>