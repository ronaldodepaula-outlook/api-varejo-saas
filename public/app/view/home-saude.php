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
    <title>Dashboard Saúde - NexusFlow</title>
    
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
            --health-color: #2A9D8F;
            --patient-color: #E76F51;
            --medical-color: #264653;
            --appointment-color: #E9C46A;
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
            color: var(--health-color);
            margin-right: 1rem;
        }
        
        .breadcrumb-custom {
            margin-bottom: 0;
        }
        
        .breadcrumb-custom .breadcrumb-item.active {
            color: var(--health-color);
            font-weight: 600;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--health-color);
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
            color: var(--health-color);
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
        }
        
        .metric-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px rgba(0,0,0,0.1);
        }
        
        .metric-card.health {
            border-left: 4px solid var(--health-color);
        }
        
        .metric-card.patient {
            border-left: 4px solid var(--patient-color);
        }
        
        .metric-card.medical {
            border-left: 4px solid var(--medical-color);
        }
        
        .metric-card.appointment {
            border-left: 4px solid var(--appointment-color);
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
        
        .metric-icon.health {
            background: rgba(42, 157, 143, 0.1);
            color: var(--health-color);
        }
        
        .metric-icon.patient {
            background: rgba(231, 111, 81, 0.1);
            color: var(--patient-color);
        }
        
        .metric-icon.medical {
            background: rgba(38, 70, 83, 0.1);
            color: var(--medical-color);
        }
        
        .metric-icon.appointment {
            background: rgba(233, 196, 106, 0.1);
            color: var(--appointment-color);
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
        
        .status-scheduled {
            background: rgba(233, 196, 106, 0.1);
            color: var(--appointment-color);
        }
        
        .status-completed {
            background: rgba(27, 153, 139, 0.1);
            color: var(--success-color);
        }
        
        .status-cancelled {
            background: rgba(196, 69, 54, 0.1);
            color: var(--danger-color);
        }
        
        .status-emergency {
            background: rgba(244, 96, 54, 0.1);
            color: var(--warning-color);
        }
        
        .specialty-badge {
            padding: 0.35rem 0.65rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .specialty-cardio {
            background: rgba(42, 157, 143, 0.1);
            color: var(--health-color);
        }
        
        .specialty-ortho {
            background: rgba(231, 111, 81, 0.1);
            color: var(--patient-color);
        }
        
        .specialty-pediatric {
            background: rgba(38, 70, 83, 0.1);
            color: var(--medical-color);
        }
        
        .specialty-dermatology {
            background: rgba(233, 196, 106, 0.1);
            color: var(--appointment-color);
        }
        
        .notification-toast {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1050;
            min-width: 300px;
        }
        
        .urgency-indicator {
            height: 6px;
            border-radius: 3px;
            background: #e9ecef;
            margin-top: 5px;
            overflow: hidden;
        }
        
        .urgency-progress {
            height: 100%;
            border-radius: 3px;
        }
        
        .urgency-high {
            background: var(--danger-color);
        }
        
        .urgency-medium {
            background: var(--warning-color);
        }
        
        .urgency-low {
            background: var(--success-color);
        }
        
        .patient-priority-high {
            border-left: 4px solid var(--danger-color);
        }
        
        .patient-priority-medium {
            border-left: 4px solid var(--warning-color);
        }
        
        .patient-priority-low {
            border-left: 4px solid var(--success-color);
        }
        
        .vital-signs {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }
        
        .vital-sign {
            text-align: center;
        }
        
        .vital-sign-value {
            font-weight: 700;
            font-size: 1.1rem;
        }
        
        .vital-sign-label {
            font-size: 0.75rem;
            color: #6c757d;
        }
        
        .vital-normal {
            color: var(--success-color);
        }
        
        .vital-warning {
            color: var(--warning-color);
        }
        
        .vital-critical {
            color: var(--danger-color);
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
                        <li class="breadcrumb-item active">Saúde</li>
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
                        <li><h6 class="dropdown-header">Alertas de Saúde</h6></li>
                        <li><a class="dropdown-item" href="#">Paciente com sinais vitais críticos</a></li>
                        <li><a class="dropdown-item" href="#">Medicamentos em falta no estoque</a></li>
                        <li><a class="dropdown-item" href="#">Exames pendentes de laudo</a></li>
                        <li><a class="dropdown-item" href="#">Consultas de emergência agendadas</a></li>
                        <li><a class="dropdown-item" href="#">Alerta de infecção hospitalar</a></li>
                        <li><a class="dropdown-item" href="#">Manutenção de equipamentos</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-center" href="#">Ver todas</a></li>
                    </ul>
                </div>
                
                <!-- Dropdown do Usuário -->
                <div class="dropdown user-dropdown">
                    <div class="user-avatar" data-bs-toggle="dropdown" aria-expanded="false">
                        S
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><h6 class="dropdown-header user-name">Saúde NexusFlow</h6></li>
                        <li><small class="dropdown-header text-muted user-email">saude@nexusflow.com</small></li>
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
                    <h1 class="page-title">Dashboard de Saúde</h1>
                    <p class="page-subtitle">Gestão de pacientes, consultas e indicadores de saúde</p>
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
                    <div class="metric-card health">
                        <div class="metric-icon health">
                            <i class="bi bi-people"></i>
                        </div>
                        <div class="metric-value">1,247</div>
                        <div class="metric-label">Pacientes Ativos</div>
                        <div class="metric-change positive">
                            <i class="bi bi-arrow-up"></i> +45 este mês
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="metric-card patient">
                        <div class="metric-icon patient">
                            <i class="bi bi-calendar-check"></i>
                        </div>
                        <div class="metric-value">89</div>
                        <div class="metric-label">Consultas Hoje</div>
                        <div class="metric-change negative">
                            <i class="bi bi-arrow-up"></i> +12 vs ontem
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="metric-card medical">
                        <div class="metric-icon medical">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <div class="metric-value">23</div>
                        <div class="metric-label">Emergências</div>
                        <div class="metric-change negative">
                            <i class="bi bi-arrow-up"></i> +5 esta semana
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="metric-card appointment">
                        <div class="metric-icon appointment">
                            <i class="bi bi-capsule"></i>
                        </div>
                        <div class="metric-value">94%</div>
                        <div class="metric-label">Taxa de Ocupação</div>
                        <div class="metric-change positive">
                            <i class="bi bi-arrow-up"></i> +3% vs mês anterior
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Gráficos e Dados -->
            <div class="row mb-4">
                <!-- Gráfico de Atendimentos -->
                <div class="col-xl-8 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Atendimentos por Especialidade - Últimos 6 Meses</h5>
                            <div class="btn-group btn-group-sm" role="group">
                                <input type="radio" class="btn-check" name="period" id="period7" checked>
                                <label class="btn btn-outline-primary" for="period7">Cardio</label>
                                
                                <input type="radio" class="btn-check" name="period" id="period30">
                                <label class="btn btn-outline-primary" for="period30">Ortopedia</label>
                                
                                <input type="radio" class="btn-check" name="period" id="period90">
                                <label class="btn btn-outline-primary" for="period90">Pediatria</label>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="specialtyChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
                
                <!-- Distribuição por Especialidade -->
                <div class="col-xl-4 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom">
                            <h5 class="mb-0">Distribuição por Especialidade</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="specialtyDistributionChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tabelas de Dados -->
            <div class="row">
                <!-- Próximas Consultas -->
                <div class="col-xl-6 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Próximas Consultas</h5>
                            <a href="consultas.html" class="btn btn-sm btn-outline-primary">Ver todas</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-custom mb-0">
                                    <thead>
                                        <tr>
                                            <th>Paciente</th>
                                            <th>Especialidade</th>
                                            <th>Status</th>
                                            <th>Horário</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="patient-priority-high">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                                            JS
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold">João Silva</div>
                                                        <small class="text-muted">Pré-consulta: Hipertensão</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="specialty-badge specialty-cardio">Cardiologia</span></td>
                                            <td><span class="status-badge status-emergency">Emergência</span></td>
                                            <td>
                                                <div>09:30</div>
                                                <div class="urgency-indicator">
                                                    <div class="urgency-progress urgency-high" style="width: 95%"></div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="patient-priority-medium">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                                            MA
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold">Maria Andrade</div>
                                                        <small class="text-muted">Acompanhamento: Pós-cirúrgico</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="specialty-badge specialty-ortho">Ortopedia</span></td>
                                            <td><span class="status-badge status-scheduled">Agendada</span></td>
                                            <td>
                                                <div>10:15</div>
                                                <div class="urgency-indicator">
                                                    <div class="urgency-progress urgency-medium" style="width: 60%"></div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="patient-priority-low">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                                            PC
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold">Pedro Costa</div>
                                                        <small class="text-muted">Rotina: Check-up anual</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="specialty-badge specialty-dermatology">Dermatologia</span></td>
                                            <td><span class="status-badge status-scheduled">Agendada</span></td>
                                            <td>
                                                <div>11:00</div>
                                                <div class="urgency-indicator">
                                                    <div class="urgency-progress urgency-low" style="width: 25%"></div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Monitoramento de Pacientes -->
                <div class="col-xl-6 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Monitoramento de Pacientes</h5>
                            <a href="pacientes.html" class="btn btn-sm btn-outline-primary">Ver todos</a>
                        </div>
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <i class="bi bi-heart-pulse text-danger" style="font-size: 1.5rem;"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold">João Silva</div>
                                            <div class="vital-signs">
                                                <div class="vital-sign">
                                                    <div class="vital-sign-value vital-critical">142/95</div>
                                                    <div class="vital-sign-label">Pressão</div>
                                                </div>
                                                <div class="vital-sign">
                                                    <div class="vital-sign-value vital-warning">98</div>
                                                    <div class="vital-sign-label">BPM</div>
                                                </div>
                                                <div class="vital-sign">
                                                    <div class="vital-sign-value vital-normal">37.8°</div>
                                                    <div class="vital-sign-label">Temp</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <i class="bi bi-activity text-warning" style="font-size: 1.5rem;"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold">Maria Andrade</div>
                                            <div class="vital-signs">
                                                <div class="vital-sign">
                                                    <div class="vital-sign-value vital-normal">120/80</div>
                                                    <div class="vital-sign-label">Pressão</div>
                                                </div>
                                                <div class="vital-sign">
                                                    <div class="vital-sign-value vital-normal">72</div>
                                                    <div class="vital-sign-label">BPM</div>
                                                </div>
                                                <div class="vital-sign">
                                                    <div class="vital-sign-value vital-normal">36.5°</div>
                                                    <div class="vital-sign-label">Temp</div>
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
                                            <i class="bi bi-droplet text-info" style="font-size: 1.5rem;"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold">Ana Rodrigues</div>
                                            <div class="vital-signs">
                                                <div class="vital-sign">
                                                    <div class="vital-sign-value vital-warning">135/88</div>
                                                    <div class="vital-sign-label">Pressão</div>
                                                </div>
                                                <div class="vital-sign">
                                                    <div class="vital-sign-value vital-normal">68</div>
                                                    <div class="vital-sign-label">BPM</div>
                                                </div>
                                                <div class="vital-sign">
                                                    <div class="vital-sign-value vital-normal">36.7°</div>
                                                    <div class="vital-sign-label">Temp</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <i class="bi bi-lungs text-success" style="font-size: 1.5rem;"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold">Carlos Santos</div>
                                            <div class="vital-signs">
                                                <div class="vital-sign">
                                                    <div class="vital-sign-value vital-normal">118/76</div>
                                                    <div class="vital-sign-label">Pressão</div>
                                                </div>
                                                <div class="vital-sign">
                                                    <div class="vital-sign-value vital-normal">75</div>
                                                    <div class="vital-sign-label">BPM</div>
                                                </div>
                                                <div class="vital-sign">
                                                    <div class="vital-sign-value vital-normal">36.9°</div>
                                                    <div class="vital-sign-label">Temp</div>
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
            
            <!-- Alertas do Sistema -->
            <div class="row">
                <div class="col-12">
                    <div class="card-custom">
                        <div class="card-header-custom">
                            <h5 class="mb-0">Alertas e Notificações</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-warning d-flex align-items-center mb-3">
                                <i class="bi bi-exclamation-triangle me-3"></i>
                                <div>
                                    <strong>Paciente João Silva</strong> com sinais vitais críticos
                                    <div class="mt-1">
                                        <a href="#" class="btn btn-sm btn-warning">Avaliar urgência</a>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="alert alert-info d-flex align-items-center mb-3">
                                <i class="bi bi-capsule me-3"></i>
                                <div>
                                    <strong>Medicamentos em falta</strong> - 12 itens com estoque crítico
                                    <div class="mt-1">
                                        <a href="estoque.html" class="btn btn-sm btn-info">Ver estoque</a>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="alert alert-success d-flex align-items-center mb-3">
                                <i class="bi bi-file-earmark-medical me-3"></i>
                                <div>
                                    <strong>23 exames</strong> realizados com sucesso hoje
                                    <div class="mt-1">
                                        <small class="text-muted">5 aguardando laudo médico</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="alert alert-danger d-flex align-items-center mb-0">
                                <i class="bi bi-shield-exclamation me-3"></i>
                                <div>
                                    <strong>Alerta de infecção</strong> detectado no setor de pediatria
                                    <div class="mt-1">
                                        <a href="#" class="btn btn-sm btn-danger">Protocolo de contenção</a>
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
            // Gráfico de Atendimentos por Especialidade
            const specialtyCtx = document.getElementById('specialtyChart').getContext('2d');
            const specialtyChart = new Chart(specialtyCtx, {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun'],
                    datasets: [{
                        label: 'Cardiologia',
                        data: [145, 189, 212, 245, 278, 312],
                        backgroundColor: 'rgba(42, 157, 143, 0.7)',
                        borderColor: 'rgba(42, 157, 143, 1)',
                        borderWidth: 1
                    }, {
                        label: 'Ortopedia',
                        data: [89, 112, 134, 156, 178, 195],
                        backgroundColor: 'rgba(231, 111, 81, 0.7)',
                        borderColor: 'rgba(231, 111, 81, 1)',
                        borderWidth: 1
                    }, {
                        label: 'Pediatria',
                        data: [245, 278, 312, 345, 378, 412],
                        backgroundColor: 'rgba(38, 70, 83, 0.7)',
                        borderColor: 'rgba(38, 70, 83, 1)',
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
            
            // Gráfico de Distribuição por Especialidade
            const specialtyDistributionCtx = document.getElementById('specialtyDistributionChart').getContext('2d');
            const specialtyDistributionChart = new Chart(specialtyDistributionCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Cardiologia', 'Ortopedia', 'Pediatria', 'Dermatologia', 'Clínica Geral', 'Outros'],
                    datasets: [{
                        data: [25, 18, 22, 12, 15, 8],
                        backgroundColor: [
                            '#2A9D8F',
                            '#E76F51',
                            '#264653',
                            '#E9C46A',
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
            nexusFlow.showNotification('Exportando relatório de saúde...', 'info');
            setTimeout(() => {
                nexusFlow.showNotification('Relatório exportado com sucesso!', 'success');
            }, 2000);
        }
        
        function refreshDashboard() {
            nexusFlow.showNotification('Atualizando dashboard de saúde...', 'info');
            setTimeout(() => {
                nexusFlow.showNotification('Dashboard atualizado!', 'success');
                // Em uma aplicação real, aqui seria feita uma requisição para atualizar os dados
                // Por simplicidade, apenas recarregamos a página
                location.reload();
            }, 1000);
        }
        
        // Definir papel como saúde
        localStorage.setItem('userRole', 'saude');
    </script>
</body>
</html>