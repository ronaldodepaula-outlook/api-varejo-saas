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
    <title>Dashboard ONGs & Terceiro Setor - NexusFlow</title>
    
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
            --ngo-color: #2A9D8F;
            --impact-color: #E9C46A;
            --volunteer-color: #E76F51;
            --donation-color: #264653;
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
            color: var(--ngo-color);
            margin-right: 1rem;
        }
        
        .breadcrumb-custom {
            margin-bottom: 0;
        }
        
        .breadcrumb-custom .breadcrumb-item.active {
            color: var(--ngo-color);
            font-weight: 600;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--ngo-color);
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
            color: var(--ngo-color);
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
        
        .metric-card.ngo {
            border-left: 4px solid var(--ngo-color);
        }
        
        .metric-card.impact {
            border-left: 4px solid var(--impact-color);
        }
        
        .metric-card.volunteer {
            border-left: 4px solid var(--volunteer-color);
        }
        
        .metric-card.donation {
            border-left: 4px solid var(--donation-color);
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
        
        .metric-icon.ngo {
            background: rgba(42, 157, 143, 0.1);
            color: var(--ngo-color);
        }
        
        .metric-icon.impact {
            background: rgba(233, 196, 106, 0.1);
            color: var(--impact-color);
        }
        
        .metric-icon.volunteer {
            background: rgba(231, 111, 81, 0.1);
            color: var(--volunteer-color);
        }
        
        .metric-icon.donation {
            background: rgba(38, 70, 83, 0.1);
            color: var(--donation-color);
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
        
        .status-completed {
            background: rgba(108, 117, 125, 0.1);
            color: #6c757d;
        }
        
        .status-urgent {
            background: rgba(196, 69, 54, 0.1);
            color: var(--danger-color);
        }
        
        .program-badge {
            padding: 0.35rem 0.65rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .program-education {
            background: rgba(42, 157, 143, 0.1);
            color: var(--ngo-color);
        }
        
        .program-health {
            background: rgba(233, 196, 106, 0.1);
            color: var(--impact-color);
        }
        
        .program-environment {
            background: rgba(231, 111, 81, 0.1);
            color: var(--volunteer-color);
        }
        
        .program-social {
            background: rgba(38, 70, 83, 0.1);
            color: var(--donation-color);
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
            background: var(--ngo-color);
        }
        
        .progress-average {
            background: var(--warning-color);
        }
        
        .progress-poor {
            background: var(--danger-color);
        }
        
        .project-priority-high {
            border-left: 4px solid var(--danger-color);
        }
        
        .project-priority-medium {
            border-left: 4px solid var(--warning-color);
        }
        
        .project-priority-low {
            border-left: 4px solid var(--success-color);
        }
        
        .impact-stats {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }
        
        .impact-stat {
            text-align: center;
        }
        
        .impact-stat-value {
            font-weight: 700;
            font-size: 1.1rem;
        }
        
        .impact-stat-label {
            font-size: 0.75rem;
            color: #6c757d;
        }
        
        .campaign-event {
            padding: 0.5rem;
            border-radius: 5px;
            margin-bottom: 0.5rem;
            font-size: 0.85rem;
        }
        
        .event-fundraising {
            background: rgba(42, 157, 143, 0.1);
            border-left: 3px solid var(--ngo-color);
        }
        
        .event-volunteer {
            background: rgba(233, 196, 106, 0.1);
            border-left: 3px solid var(--impact-color);
        }
        
        .event-community {
            background: rgba(231, 111, 81, 0.1);
            border-left: 3px solid var(--volunteer-color);
        }
        
        .event-training {
            background: rgba(38, 70, 83, 0.1);
            border-left: 3px solid var(--donation-color);
        }
        
        .impact-indicator {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-right: 5px;
        }
        
        .impact-high {
            background: var(--success-color);
        }
        
        .impact-medium {
            background: var(--warning-color);
        }
        
        .impact-low {
            background: var(--danger-color);
        }
        
        .social-impact-card {
            background: linear-gradient(135deg, #2A9D8F, #E9C46A);
            color: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1rem;
        }
        
        .impact-meter {
            display: flex;
            justify-content: space-between;
            margin-top: 1rem;
        }
        
        .impact-item {
            text-align: center;
            flex: 1;
        }
        
        .impact-value {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 0.25rem;
        }
        
        .impact-label {
            font-size: 0.75rem;
            opacity: 0.9;
        }
        
        .donor-badge {
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

        .volunteer-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--volunteer-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: bold;
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
                        <li class="breadcrumb-item active">ONGs & Terceiro Setor</li>
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
                        <li><h6 class="dropdown-header">Alertas do Terceiro Setor</h6></li>
                        <li><a class="dropdown-item" href="#">Campanha de arrecadação atinge 75% da meta</a></li>
                        <li><a class="dropdown-item" href="#">3 projetos necessitam de voluntários</a></li>
                        <li><a class="dropdown-item" href="#">Relatório de impacto trimestral pronto</a></li>
                        <li><a class="dropdown-item" href="#">Doação recorrente cancelada</a></li>
                        <li><a class="dropdown-item" href="#">Evento comunitário confirmado</a></li>
                        <li><a class="dropdown-item" href="#">Parceria com nova empresa confirmada</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-center" href="#">Ver todas</a></li>
                    </ul>
                </div>
                
                <!-- Dropdown do Usuário -->
                <div class="dropdown user-dropdown">
                    <div class="user-avatar" data-bs-toggle="dropdown" aria-expanded="false">
                        O
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><h6 class="dropdown-header user-name">ONGs NexusFlow</h6></li>
                        <li><small class="dropdown-header text-muted user-email">ongs@nexusflow.com</small></li>
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
                    <h1 class="page-title">Dashboard de Impacto Social</h1>
                    <p class="page-subtitle">Gestão de projetos, voluntários e métricas de transformação social</p>
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
            
            <!-- Card de Impacto Social -->
            <div class="social-impact-card mb-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h3 class="text-white">Índice de Transformação Social</h3>
                        <p class="text-white mb-0">Avaliação do impacto positivo gerado na comunidade</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="gauge-container">
                            <div class="gauge-background"></div>
                            <div class="gauge-fill" id="impactGauge" style="background: var(--success-color); transform: rotate(0.68turn);"></div>
                            <div class="gauge-value text-white">84%</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Medidor de Impacto -->
            <div class="card-custom mb-4">
                <div class="card-header-custom">
                    <h5 class="mb-0">Indicadores de Impacto</h5>
                </div>
                <div class="card-body">
                    <div class="impact-meter">
                        <div class="impact-item">
                            <div class="impact-value">1.247</div>
                            <div class="impact-label">Pessoas Impactadas</div>
                        </div>
                        <div class="impact-item">
                            <div class="impact-value">342</div>
                            <div class="impact-label">Famílias Atendidas</div>
                        </div>
                        <div class="impact-item">
                            <div class="impact-value">78%</div>
                            <div class="impact-label">Meta de Impacto</div>
                        </div>
                        <div class="impact-item">
                            <div class="impact-value">12</div>
                            <div class="impact-label">Comunidades</div>
                        </div>
                        <div class="impact-item">
                            <div class="impact-value">94%</div>
                            <div class="impact-label">Satisfação</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Métricas Principais -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="metric-card ngo">
                        <div class="metric-icon ngo">
                            <i class="bi bi-people"></i>
                        </div>
                        <div class="metric-value">247</div>
                        <div class="metric-label">Voluntários Ativos</div>
                        <div class="metric-change positive">
                            <i class="bi bi-arrow-up"></i> +18 este mês
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="metric-card impact">
                        <div class="metric-icon impact">
                            <i class="bi bi-heart"></i>
                        </div>
                        <div class="metric-value">R$ 128K</div>
                        <div class="metric-label">Arrecadado Este Mês</div>
                        <div class="metric-change positive">
                            <i class="bi bi-arrow-up"></i> +12% vs mês anterior
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="metric-card volunteer">
                        <div class="metric-icon volunteer">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <div class="metric-value">1.850</div>
                        <div class="metric-label">Horas Voluntárias</div>
                        <div class="metric-change positive">
                            <i class="bi bi-arrow-up"></i> +320h este mês
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="metric-card donation">
                        <div class="metric-icon donation">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>
                        <div class="metric-value">78%</div>
                        <div class="metric-label">Meta de Arrecadação</div>
                        <div class="metric-change positive">
                            <i class="bi bi-arrow-up"></i> +15% vs trimestre anterior
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Gráficos e Dados -->
            <div class="row mb-4">
                <!-- Gráfico de Arrecadação vs Impacto -->
                <div class="col-xl-8 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Arrecadação vs Impacto - Últimos 6 Meses</h5>
                            <div class="btn-group btn-group-sm" role="group">
                                <input type="radio" class="btn-check" name="period" id="period7" checked>
                                <label class="btn btn-outline-primary" for="period7">Educação</label>
                                
                                <input type="radio" class="btn-check" name="period" id="period30">
                                <label class="btn btn-outline-primary" for="period30">Saúde</label>
                                
                                <input type="radio" class="btn-check" name="period" id="period90">
                                <label class="btn btn-outline-primary" for="period90">Meio Ambiente</label>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="impactChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
                
                <!-- Distribuição por Programa -->
                <div class="col-xl-4 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom">
                            <h5 class="mb-0">Distribuição por Programa</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="programDistributionChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tabelas de Dados -->
            <div class="row">
                <!-- Projetos em Andamento -->
                <div class="col-xl-6 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Projetos em Andamento</h5>
                            <a href="projetos.html" class="btn btn-sm btn-outline-primary">Ver todos</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-custom mb-0">
                                    <thead>
                                        <tr>
                                            <th>Projeto</th>
                                            <th>Programa</th>
                                            <th>Status</th>
                                            <th>Progresso</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="project-priority-high">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <div class="volunteer-avatar">
                                                            EP
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold">Educação para Todos</div>
                                                        <small class="text-muted">Meta: 500 crianças</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="program-badge program-education">Educação</span></td>
                                            <td><span class="status-badge status-urgent">Crítico</span></td>
                                            <td>
                                                <div>42% completo</div>
                                                <div class="progress-indicator">
                                                    <div class="progress-bar-custom progress-poor" style="width: 42%"></div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="project-priority-medium">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <div class="volunteer-avatar" style="background: var(--impact-color);">
                                                            SA
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold">Saúde Comunitária</div>
                                                        <small class="text-muted">Meta: 1.000 atendimentos</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="program-badge program-health">Saúde</span></td>
                                            <td><span class="status-badge status-pending">Urgente</span></td>
                                            <td>
                                                <div>68% completo</div>
                                                <div class="progress-indicator">
                                                    <div class="progress-bar-custom progress-average" style="width: 68%"></div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="project-priority-low">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <div class="volunteer-avatar" style="background: var(--donation-color);">
                                                            MA
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold">Meio Ambiente</div>
                                                        <small class="text-muted">Meta: 5.000 árvores</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="program-badge program-environment">Meio Ambiente</span></td>
                                            <td><span class="status-badge status-active">Ativo</span></td>
                                            <td>
                                                <div>85% completo</div>
                                                <div class="progress-indicator">
                                                    <div class="progress-bar-custom progress-good" style="width: 85%"></div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Agenda de Impacto -->
                <div class="col-xl-6 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Agenda de Impacto</h5>
                            <a href="calendario.html" class="btn btn-sm btn-outline-primary">Ver completa</a>
                        </div>
                        <div class="card-body">
                            <div class="campaign-event event-fundraising">
                                <div class="fw-bold">Campanha de Arrecadação - Educação para Todos</div>
                                <small>Hoje - 14:00 | Meta: R$ 50.000 | Arrecadado: R$ 37.500</small>
                            </div>
                            
                            <div class="campaign-event event-volunteer">
                                <div class="fw-bold">Treinamento de Voluntários - Saúde Comunitária</div>
                                <small>Amanhã - 09:00 | Local: Centro Comunitário</small>
                            </div>
                            
                            <div class="campaign-event event-community">
                                <div class="fw-bold">Ação Comunitária - Meio Ambiente</div>
                                <small>Sábado - 08:00 | Plantio de 500 mudas</small>
                            </div>
                            
                            <div class="campaign-event event-training">
                                <div class="fw-bold">Capacitação - Inclusão Digital</div>
                                <small>Segunda-feira - 14:00 | 25 participantes confirmados</small>
                            </div>
                            
                            <div class="campaign-event event-fundraising">
                                <div class="fw-bold">Evento Beneficente - Jantar Solidário</div>
                                <small>Próximo sábado - 19:00 | 120 convites vendidos</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- KPIs de Impacto -->
            <div class="row">
                <!-- Indicadores Sociais -->
                <div class="col-xl-6 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom">
                            <h5 class="mb-0">Indicadores Sociais</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <i class="bi bi-person-check text-primary" style="font-size: 1.5rem;"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold">Impacto Educacional</div>
                                            <div class="impact-stats">
                                                <div class="impact-stat">
                                                    <div class="impact-stat-value"><span class="impact-indicator impact-high"></span>87%</div>
                                                    <div class="impact-stat-label">Frequência</div>
                                                </div>
                                                <div class="impact-stat">
                                                    <div class="impact-stat-value"><span class="impact-indicator impact-medium"></span>4.2/5</div>
                                                    <div class="impact-stat-label">Aprendizado</div>
                                                </div>
                                                <div class="impact-stat">
                                                    <div class="impact-stat-value"><span class="impact-indicator impact-high"></span>92%</div>
                                                    <div class="impact-stat-label">Satisfação</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <i class="bi bi-heart-pulse text-success" style="font-size: 1.5rem;"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold">Saúde Comunitária</div>
                                            <div class="impact-stats">
                                                <div class="impact-stat">
                                                    <div class="impact-stat-value"><span class="impact-indicator impact-high"></span>1.247</div>
                                                    <div class="impact-stat-label">Atendimentos</div>
                                                </div>
                                                <div class="impact-stat">
                                                    <div class="impact-stat-value"><span class="impact-indicator impact-medium"></span>78%</div>
                                                    <div class="impact-stat-label">Acompanhamento</div>
                                                </div>
                                                <div class="impact-stat">
                                                    <div class="impact-stat-value"><span class="impact-indicator impact-high"></span>94%</div>
                                                    <div class="impact-stat-label">Melhoria</div>
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
                                            <i class="bi bi-tree text-warning" style="font-size: 1.5rem;"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold">Meio Ambiente</div>
                                            <div class="impact-stats">
                                                <div class="impact-stat">
                                                    <div class="impact-stat-value"><span class="impact-indicator impact-high"></span>3.8K</div>
                                                    <div class="impact-stat-label">Árvores Plantadas</div>
                                                </div>
                                                <div class="impact-stat">
                                                    <div class="impact-stat-value"><span class="impact-indicator impact-medium"></span>42</div>
                                                    <div class="impact-stat-label">Ton. Recicladas</div>
                                                </div>
                                                <div class="impact-stat">
                                                    <div class="impact-stat-value"><span class="impact-indicator impact-high"></span>85%</div>
                                                    <div class="impact-stat-label">Conscientização</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <i class="bi bi-people text-info" style="font-size: 1.5rem;"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold">Engajamento</div>
                                            <div class="impact-stats">
                                                <div class="impact-stat">
                                                    <div class="impact-stat-value"><span class="impact-indicator impact-high"></span>78%</div>
                                                    <div class="impact-stat-label">Retenção Voluntários</div>
                                                </div>
                                                <div class="impact-stat">
                                                    <div class="impact-stat-value"><span class="impact-indicator impact-medium"></span>4.5/5</div>
                                                    <div class="impact-stat-label">Satisfação</div>
                                                </div>
                                                <div class="impact-stat">
                                                    <div class="impact-stat-value"><span class="impact-indicator impact-high"></span>92%</div>
                                                    <div class="impact-stat-label">Recomendação</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Alertas de Impacto -->
                <div class="col-xl-6 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom">
                            <h5 class="mb-0">Alertas de Impacto</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-warning d-flex align-items-center mb-3">
                                <i class="bi bi-exclamation-triangle me-3"></i>
                                <div>
                                    <strong>Projeto Educação para Todos</strong> precisa de mais voluntários
                                    <div class="mt-1">
                                        <a href="#" class="btn btn-sm btn-warning">Recrutar voluntários</a>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="alert alert-info d-flex align-items-center mb-3">
                                <i class="bi bi-calendar-check me-3"></i>
                                <div>
                                    <strong>Campanha de arrecadação</strong> atinge 75% da meta
                                    <div class="mt-1">
                                        <a href="#" class="btn btn-sm btn-info">Acelerar campanha</a>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="alert alert-success d-flex align-items-center mb-3">
                                <i class="bi bi-award me-3"></i>
                                <div>
                                    <strong>Impacto social comprovado</strong> em 3 comunidades
                                    <div class="mt-1">
                                        <small class="text-muted">Relatório de impacto disponível</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="alert alert-danger d-flex align-items-center mb-0">
                                <i class="bi bi-shield-exclamation me-3"></i>
                                <div>
                                    <strong>Doações recorrentes</strong> em queda de 12%
                                    <div class="mt-1">
                                        <a href="#" class="btn btn-sm btn-danger">Rever estratégia</a>
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
            // Gráfico de Arrecadação vs Impacto
            const impactCtx = document.getElementById('impactChart').getContext('2d');
            const impactChart = new Chart(impactCtx, {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun'],
                    datasets: [{
                        label: 'Arrecadação (R$)',
                        data: [45000, 52000, 48000, 65000, 72000, 68000],
                        backgroundColor: 'rgba(42, 157, 143, 0.7)',
                        borderColor: 'rgba(42, 157, 143, 1)',
                        borderWidth: 1,
                        yAxisID: 'y'
                    }, {
                        label: 'Pessoas Impactadas',
                        data: [850, 920, 780, 1100, 1250, 980],
                        backgroundColor: 'rgba(233, 196, 106, 0.7)',
                        borderColor: 'rgba(233, 196, 106, 1)',
                        borderWidth: 1,
                        yAxisID: 'y1'
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
                            type: 'linear',
                            display: true,
                            position: 'left',
                            title: {
                                display: true,
                                text: 'Arrecadação (R$)'
                            }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            title: {
                                display: true,
                                text: 'Pessoas Impactadas'
                            },
                            grid: {
                                drawOnChartArea: false,
                            },
                        }
                    }
                }
            });
            
            // Gráfico de Distribuição por Programa
            const programDistributionCtx = document.getElementById('programDistributionChart').getContext('2d');
            const programDistributionChart = new Chart(programDistributionCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Educação', 'Saúde', 'Meio Ambiente', 'Inclusão Social', 'Emergência', 'Outros'],
                    datasets: [{
                        data: [35, 25, 15, 12, 8, 5],
                        backgroundColor: [
                            '#2A9D8F',
                            '#E9C46A',
                            '#E76F51',
                            '#264653',
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
            nexusFlow.showNotification('Exportando relatório de impacto...', 'info');
            setTimeout(() => {
                nexusFlow.showNotification('Relatório exportado com sucesso!', 'success');
            }, 2000);
        }
        
        function refreshDashboard() {
            nexusFlow.showNotification('Atualizando dashboard de impacto...', 'info');
            setTimeout(() => {
                nexusFlow.showNotification('Dashboard atualizado!', 'success');
                // Em uma aplicação real, aqui seria feita uma requisição para atualizar os dados
                // Por simplicidade, apenas recarregamos a página
                location.reload();
            }, 1000);
        }
        
        // Definir papel como ONG
        localStorage.setItem('userRole', 'ongs_terceiro_setor');
    </script>
</body>
</html>