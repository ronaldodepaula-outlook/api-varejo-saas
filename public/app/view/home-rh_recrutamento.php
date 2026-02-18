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
    <title>Dashboard RH & Recrutamento - NexusFlow</title>
    
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
            --hr-color: #4361EE;
            --recruitment-color: #7209B7;
            --talent-color: #F72585;
            --performance-color: #4CC9F0;
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
            color: var(--hr-color);
            margin-right: 1rem;
        }
        
        .breadcrumb-custom {
            margin-bottom: 0;
        }
        
        .breadcrumb-custom .breadcrumb-item.active {
            color: var(--hr-color);
            font-weight: 600;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--hr-color);
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
            color: var(--hr-color);
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
        
        .metric-card.hr {
            border-left: 4px solid var(--hr-color);
        }
        
        .metric-card.recruitment {
            border-left: 4px solid var(--recruitment-color);
        }
        
        .metric-card.talent {
            border-left: 4px solid var(--talent-color);
        }
        
        .metric-card.performance {
            border-left: 4px solid var(--performance-color);
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
        
        .metric-icon.hr {
            background: rgba(67, 97, 238, 0.1);
            color: var(--hr-color);
        }
        
        .metric-icon.recruitment {
            background: rgba(114, 9, 183, 0.1);
            color: var(--recruitment-color);
        }
        
        .metric-icon.talent {
            background: rgba(247, 37, 133, 0.1);
            color: var(--talent-color);
        }
        
        .metric-icon.performance {
            background: rgba(76, 201, 240, 0.1);
            color: var(--performance-color);
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
        
        .department-badge {
            padding: 0.35rem 0.65rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .department-tech {
            background: rgba(67, 97, 238, 0.1);
            color: var(--hr-color);
        }
        
        .department-sales {
            background: rgba(114, 9, 183, 0.1);
            color: var(--recruitment-color);
        }
        
        .department-marketing {
            background: rgba(247, 37, 133, 0.1);
            color: var(--talent-color);
        }
        
        .department-finance {
            background: rgba(76, 201, 240, 0.1);
            color: var(--performance-color);
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
            background: var(--hr-color);
        }
        
        .progress-average {
            background: var(--warning-color);
        }
        
        .progress-poor {
            background: var(--danger-color);
        }
        
        .candidate-priority-high {
            border-left: 4px solid var(--danger-color);
        }
        
        .candidate-priority-medium {
            border-left: 4px solid var(--warning-color);
        }
        
        .candidate-priority-low {
            border-left: 4px solid var(--success-color);
        }
        
        .recruitment-stats {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }
        
        .recruitment-stat {
            text-align: center;
        }
        
        .recruitment-stat-value {
            font-weight: 700;
            font-size: 1.1rem;
        }
        
        .recruitment-stat-label {
            font-size: 0.75rem;
            color: #6c757d;
        }
        
        .process-event {
            padding: 0.5rem;
            border-radius: 5px;
            margin-bottom: 0.5rem;
            font-size: 0.85rem;
        }
        
        .event-interview {
            background: rgba(67, 97, 238, 0.1);
            border-left: 3px solid var(--hr-color);
        }
        
        .event-assessment {
            background: rgba(114, 9, 183, 0.1);
            border-left: 3px solid var(--recruitment-color);
        }
        
        .event-offer {
            background: rgba(247, 37, 133, 0.1);
            border-left: 3px solid var(--talent-color);
        }
        
        .event-onboarding {
            background: rgba(76, 201, 240, 0.1);
            border-left: 3px solid var(--performance-color);
        }
        
        .kpi-indicator {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-right: 5px;
        }
        
        .kpi-excellent {
            background: var(--success-color);
        }
        
        .kpi-good {
            background: var(--hr-color);
        }
        
        .kpi-warning {
            background: var(--warning-color);
        }
        
        .kpi-critical {
            background: var(--danger-color);
        }
        
        .talent-score-card {
            background: linear-gradient(135deg, #4361EE, #7209B7);
            color: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1rem;
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
        
        .talent-pipeline {
            display: flex;
            justify-content: space-between;
            margin-top: 1rem;
        }
        
        .pipeline-stage {
            text-align: center;
            flex: 1;
        }
        
        .pipeline-count {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 0.25rem;
        }
        
        .pipeline-label {
            font-size: 0.75rem;
            color: #6c757d;
        }
        
        @media (max-width: 768px) {
            .content-area {
                padding: 1rem;
            }
            
            .main-header {
                padding: 1rem;
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
                        <li class="breadcrumb-item active">RH & Recrutamento</li>
                    </ol>
                </nav>
            </div>
            
            <div class="header-right">
                <!-- Notificações -->
                <div class="dropdown">
                    <button class="btn btn-link text-dark position-relative" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-bell" style="font-size: 1.25rem;"></i>
                        <span class="badge bg-danger position-absolute translate-middle rounded-pill" style="font-size: 0.6rem; top: 8px; right: 8px;">9</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><h6 class="dropdown-header">Alertas de RH</h6></li>
                        <li><a class="dropdown-item" href="#">5 vagas críticas sem candidatos</a></li>
                        <li><a class="dropdown-item" href="#">3 candidatos aguardando feedback</a></li>
                        <li><a class="dropdown-item" href="#">Onboarding de 2 novos colaboradores</a></li>
                        <li><a class="dropdown-item" href="#">Avaliação de desempenho pendente</a></li>
                        <li><a class="dropdown-item" href="#">Benefícios com vencimento próximo</a></li>
                        <li><a class="dropdown-item" href="#">Folha de pagamento em processamento</a></li>
                        <li><a class="dropdown-item" href="#">Treinamento obrigatório agendado</a></li>
                        <li><a class="dropdown-item" href="#">Indicadores de turnover em alerta</a></li>
                        <li><a class="dropdown-item" href="#">Relatório mensal pronto</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-center" href="#">Ver todas</a></li>
                    </ul>
                </div>
                
                <!-- Dropdown do Usuário -->
                <div class="dropdown user-dropdown">
                    <div class="user-avatar" data-bs-toggle="dropdown" aria-expanded="false">
                        R
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><h6 class="dropdown-header user-name">RH NexusFlow</h6></li>
                        <li><small class="dropdown-header text-muted user-email">rh@nexusflow.com</small></li>
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
                    <h1 class="page-title">Dashboard Estratégico de RH</h1>
                    <p class="page-subtitle">Gestão de talentos, recrutamento e indicadores de desempenho organizacional</p>
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
            
            <!-- Scorecard de Talentos -->
            <div class="talent-score-card mb-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h3 class="text-white">Índice de Saúde Organizacional</h3>
                        <p class="text-white mb-0">Avaliação estratégica do capital humano e performance</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="gauge-container">
                            <div class="gauge-background"></div>
                            <div class="gauge-fill" id="talentGauge" style="background: var(--success-color); transform: rotate(0.6turn);"></div>
                            <div class="gauge-value">82%</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Pipeline de Talentos -->
            <div class="card-custom mb-4">
                <div class="card-header-custom">
                    <h5 class="mb-0">Pipeline de Talentos</h5>
                </div>
                <div class="card-body">
                    <div class="talent-pipeline">
                        <div class="pipeline-stage">
                            <div class="pipeline-count">147</div>
                            <div class="pipeline-label">Candidatos</div>
                        </div>
                        <div class="pipeline-stage">
                            <div class="pipeline-count">42</div>
                            <div class="pipeline-label">Entrevistas</div>
                        </div>
                        <div class="pipeline-stage">
                            <div class="pipeline-count">18</div>
                            <div class="pipeline-label">Avaliações</div>
                        </div>
                        <div class="pipeline-stage">
                            <div class="pipeline-count">12</div>
                            <div class="pipeline-label">Ofertas</div>
                        </div>
                        <div class="pipeline-stage">
                            <div class="pipeline-count">8</div>
                            <div class="pipeline-label">Contratações</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Métricas Principais -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="metric-card hr">
                        <div class="metric-icon hr">
                            <i class="bi bi-people"></i>
                        </div>
                        <div class="metric-value">847</div>
                        <div class="metric-label">Colaboradores Ativos</div>
                        <div class="metric-change positive">
                            <i class="bi bi-arrow-up"></i> +12 este trimestre
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="metric-card recruitment">
                        <div class="metric-icon recruitment">
                            <i class="bi bi-briefcase"></i>
                        </div>
                        <div class="metric-value">23</div>
                        <div class="metric-label">Vagas Abertas</div>
                        <div class="metric-change negative">
                            <i class="bi bi-arrow-up"></i> +5 esta semana
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="metric-card talent">
                        <div class="metric-icon talent">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>
                        <div class="metric-value">42 dias</div>
                        <div class="metric-label">Tempo Médio de Contratação</div>
                        <div class="metric-change positive">
                            <i class="bi bi-arrow-down"></i> -8 dias vs trimestre anterior
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="metric-card performance">
                        <div class="metric-icon performance">
                            <i class="bi bi-cash-coin"></i>
                        </div>
                        <div class="metric-value">R$ 4.2K</div>
                        <div class="metric-label">Custo por Contratação</div>
                        <div class="metric-change negative">
                            <i class="bi bi-arrow-up"></i> +12% vs meta
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Gráficos e Dados -->
            <div class="row mb-4">
                <!-- Gráfico de Indicadores de RH -->
                <div class="col-xl-8 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Indicadores de Performance - Últimos 6 Meses</h5>
                            <div class="btn-group btn-group-sm" role="group">
                                <input type="radio" class="btn-check" name="period" id="period7" checked>
                                <label class="btn btn-outline-primary" for="period7">Turnover</label>
                                
                                <input type="radio" class="btn-check" name="period" id="period30">
                                <label class="btn btn-outline-primary" for="period30">Engajamento</label>
                                
                                <input type="radio" class="btn-check" name="period" id="period90">
                                <label class="btn btn-outline-primary" for="period90">Produtividade</label>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="hrMetricsChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
                
                <!-- Distribuição por Departamento -->
                <div class="col-xl-4 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom">
                            <h5 class="mb-0">Distribuição por Departamento</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="departmentDistributionChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tabelas de Dados -->
            <div class="row">
                <!-- Vagas Críticas -->
                <div class="col-xl-6 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Vagas Críticas</h5>
                            <a href="vagas.html" class="btn btn-sm btn-outline-primary">Ver todas</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-custom mb-0">
                                    <thead>
                                        <tr>
                                            <th>Vaga</th>
                                            <th>Departamento</th>
                                            <th>Status</th>
                                            <th>Tempo Aberta</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="candidate-priority-high">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                                            DS
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold">Desenvolvedor Sênior</div>
                                                        <small class="text-muted">5 candidatos ativos</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="department-badge department-tech">Tecnologia</span></td>
                                            <td><span class="status-badge status-urgent">Crítica</span></td>
                                            <td>
                                                <div>67 dias</div>
                                                <div class="progress-indicator">
                                                    <div class="progress-bar-custom progress-poor" style="width: 95%"></div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="candidate-priority-medium">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                                            GA
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold">Gerente de Vendas</div>
                                                        <small class="text-muted">12 candidatos ativos</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="department-badge department-sales">Vendas</span></td>
                                            <td><span class="status-badge status-pending">Urgente</span></td>
                                            <td>
                                                <div>42 dias</div>
                                                <div class="progress-indicator">
                                                    <div class="progress-bar-custom progress-average" style="width: 75%"></div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="candidate-priority-low">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                                            AM
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold">Analista de Marketing</div>
                                                        <small class="text-muted">8 candidatos ativos</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="department-badge department-marketing">Marketing</span></td>
                                            <td><span class="status-badge status-active">Ativa</span></td>
                                            <td>
                                                <div>28 dias</div>
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
                
                <!-- Processos em Andamento -->
                <div class="col-xl-6 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Processos em Andamento</h5>
                            <a href="processos.html" class="btn btn-sm btn-outline-primary">Ver todos</a>
                        </div>
                        <div class="card-body">
                            <div class="process-event event-interview">
                                <div class="fw-bold">Entrevista Técnica - Desenvolvedor Sênior</div>
                                <small>Hoje - 14:00 | Candidato: João Silva</small>
                            </div>
                            
                            <div class="process-event event-assessment">
                                <div class="fw-bold">Avaliação Psicológica - Gerente de Vendas</div>
                                <small>Amanhã - 10:00 | Candidato: Maria Santos</small>
                            </div>
                            
                            <div class="process-event event-offer">
                                <div class="fw-bold">Proposta de Contratação - Analista de Marketing</div>
                                <small>Prazo: 2 dias | Candidato: Pedro Costa</small>
                            </div>
                            
                            <div class="process-event event-onboarding">
                                <div class="fw-bold">Onboarding - Novo Desenvolvedor</div>
                                <small>Segunda-feira - 09:00 | Colaborador: Ana Rodrigues</small>
                            </div>
                            
                            <div class="process-event event-interview">
                                <div class="fw-bold">Entrevista Final - Diretor Financeiro</div>
                                <small>Quarta-feira - 16:00 | Candidato: Carlos Mendes</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- KPIs de Performance -->
            <div class="row">
                <!-- Indicadores de Qualidade -->
                <div class="col-xl-6 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom">
                            <h5 class="mb-0">Indicadores de Qualidade</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <i class="bi bi-person-check text-primary" style="font-size: 1.5rem;"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold">Qualidade de Contratação</div>
                                            <div class="recruitment-stats">
                                                <div class="recruitment-stat">
                                                    <div class="recruitment-stat-value"><span class="kpi-indicator kpi-excellent"></span>87%</div>
                                                    <div class="recruitment-stat-label">Retenção 6m</div>
                                                </div>
                                                <div class="recruitment-stat">
                                                    <div class="recruitment-stat-value"><span class="kpi-indicator kpi-good"></span>4.2/5</div>
                                                    <div class="recruitment-stat-label">Satisfação</div>
                                                </div>
                                                <div class="recruitment-stat">
                                                    <div class="recruitment-stat-value"><span class="kpi-indicator kpi-excellent"></span>92%</div>
                                                    <div class="recruitment-stat-label">Performance</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <i class="bi bi-arrow-repeat text-success" style="font-size: 1.5rem;"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold">Taxa de Turnover</div>
                                            <div class="recruitment-stats">
                                                <div class="recruitment-stat">
                                                    <div class="recruitment-stat-value"><span class="kpi-indicator kpi-warning"></span>12.3%</div>
                                                    <div class="recruitment-stat-label">Geral</div>
                                                </div>
                                                <div class="recruitment-stat">
                                                    <div class="recruitment-stat-value"><span class="kpi-indicator kpi-critical"></span>18.7%</div>
                                                    <div class="recruitment-stat-label">Voluntário</div>
                                                </div>
                                                <div class="recruitment-stat">
                                                    <div class="recruitment-stat-value"><span class="kpi-indicator kpi-good"></span>5.2%</div>
                                                    <div class="recruitment-stat-label">Involuntário</div>
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
                                            <div class="fw-bold">Eficiência do Recrutamento</div>
                                            <div class="recruitment-stats">
                                                <div class="recruitment-stat">
                                                    <div class="recruitment-stat-value"><span class="kpi-indicator kpi-good"></span>3.8%</div>
                                                    <div class="recruitment-stat-label">Taxa Conversão</div>
                                                </div>
                                                <div class="recruitment-stat">
                                                    <div class="recruitment-stat-value"><span class="kpi-indicator kpi-excellent"></span>42 dias</div>
                                                    <div class="recruitment-stat-label">Tempo Médio</div>
                                                </div>
                                                <div class="recruitment-stat">
                                                    <div class="recruitment-stat-value"><span class="kpi-indicator kpi-warning"></span>R$ 4.2K</div>
                                                    <div class="recruitment-stat-label">Custo Médio</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <i class="bi bi-heart text-info" style="font-size: 1.5rem;"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold">Engajamento</div>
                                            <div class="recruitment-stats">
                                                <div class="recruitment-stat">
                                                    <div class="recruitment-stat-value"><span class="kpi-indicator kpi-excellent"></span>78%</div>
                                                    <div class="recruitment-stat-label">NPS Interno</div>
                                                </div>
                                                <div class="recruitment-stat">
                                                    <div class="recruitment-stat-value"><span class="kpi-indicator kpi-good"></span>4.1/5</div>
                                                    <div class="recruitment-stat-label">Satisfação</div>
                                                </div>
                                                <div class="recruitment-stat">
                                                    <div class="recruitment-stat-value"><span class="kpi-indicator kpi-warning"></span>8.7%</div>
                                                    <div class="recruitment-stat-label">Absenteísmo</div>
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
                                    <strong>Turnover alto em TI</strong> - 18.7% no último trimestre
                                    <div class="mt-1">
                                        <a href="#" class="btn btn-sm btn-warning">Analisar causas</a>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="alert alert-info d-flex align-items-center mb-3">
                                <i class="bi bi-calendar-check me-3"></i>
                                <div>
                                    <strong>Avaliações de desempenho</strong> pendentes para 45 colaboradores
                                    <div class="mt-1">
                                        <a href="avaliacoes.html" class="btn btn-sm btn-info">Ver pendências</a>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="alert alert-success d-flex align-items-center mb-3">
                                <i class="bi bi-award me-3"></i>
                                <div>
                                    <strong>Programa de desenvolvimento</strong> com 92% de satisfação
                                    <div class="mt-1">
                                        <small class="text-muted">87 colaboradores participantes</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="alert alert-danger d-flex align-items-center mb-0">
                                <i class="bi bi-shield-exclamation me-3"></i>
                                <div>
                                    <strong>5 vagas críticas</strong> sem candidatos há mais de 60 dias
                                    <div class="mt-1">
                                        <a href="#" class="btn btn-sm btn-danger">Revisar estratégia</a>
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
            // Gráfico de Indicadores de RH
            const hrMetricsCtx = document.getElementById('hrMetricsChart').getContext('2d');
            const hrMetricsChart = new Chart(hrMetricsCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun'],
                    datasets: [{
                        label: 'Taxa de Turnover (%)',
                        data: [14.2, 12.8, 11.5, 13.2, 12.7, 12.3],
                        borderColor: '#4361EE',
                        backgroundColor: 'rgba(67, 97, 238, 0.1)',
                        tension: 0.4,
                        fill: true,
                        yAxisID: 'y'
                    }, {
                        label: 'Engajamento (NPS)',
                        data: [72, 75, 78, 76, 79, 78],
                        borderColor: '#7209B7',
                        backgroundColor: 'rgba(114, 9, 183, 0.1)',
                        tension: 0.4,
                        fill: true,
                        yAxisID: 'y1'
                    }, {
                        label: 'Produtividade (Índice)',
                        data: [82, 85, 87, 84, 88, 86],
                        borderColor: '#F72585',
                        backgroundColor: 'rgba(247, 37, 133, 0.1)',
                        tension: 0.4,
                        fill: true,
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
                                text: 'Turnover (%)'
                            },
                            min: 10,
                            max: 20
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            title: {
                                display: true,
                                text: 'Engajamento/Produtividade'
                            },
                            min: 60,
                            max: 100,
                            grid: {
                                drawOnChartArea: false,
                            },
                        }
                    }
                }
            });
            
            // Gráfico de Distribuição por Departamento
            const departmentDistributionCtx = document.getElementById('departmentDistributionChart').getContext('2d');
            const departmentDistributionChart = new Chart(departmentDistributionCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Tecnologia', 'Vendas', 'Marketing', 'Financeiro', 'RH', 'Operações'],
                    datasets: [{
                        data: [35, 22, 15, 12, 8, 8],
                        backgroundColor: [
                            '#4361EE',
                            '#7209B7',
                            '#F72585',
                            '#4CC9F0',
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
            nexusFlow.showNotification('Exportando relatório de RH...', 'info');
            setTimeout(() => {
                nexusFlow.showNotification('Relatório exportado com sucesso!', 'success');
            }, 2000);
        }
        
        function refreshDashboard() {
            nexusFlow.showNotification('Atualizando dashboard de RH...', 'info');
            setTimeout(() => {
                nexusFlow.showNotification('Dashboard atualizado!', 'success');
                // Em uma aplicação real, aqui seria feita uma requisição para atualizar os dados
                // Por simplicidade, apenas recarregamos a página
                location.reload();
            }, 1000);
        }
        
        // Definir papel como RH
        localStorage.setItem('userRole', 'rh_recrutamento');
    </script>
</body>
</html>