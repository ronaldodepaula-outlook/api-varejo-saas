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
    <title>Dashboard Segurança - NexusFlow</title>
    
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
            --security-color: #14213D;
            --monitoring-color: #FCA311;
            --incident-color: #E63946;
            --prevention-color: #2A9D8F;
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
            color: var(--security-color);
            margin-right: 1rem;
        }
        
        .breadcrumb-custom {
            margin-bottom: 0;
        }
        
        .breadcrumb-custom .breadcrumb-item.active {
            color: var(--security-color);
            font-weight: 600;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--security-color);
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
            color: var(--security-color);
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
        
        .metric-card.security {
            border-left: 4px solid var(--security-color);
        }
        
        .metric-card.monitoring {
            border-left: 4px solid var(--monitoring-color);
        }
        
        .metric-card.incident {
            border-left: 4px solid var(--incident-color);
        }
        
        .metric-card.prevention {
            border-left: 4px solid var(--prevention-color);
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
        
        .metric-icon.security {
            background: rgba(20, 33, 61, 0.1);
            color: var(--security-color);
        }
        
        .metric-icon.monitoring {
            background: rgba(252, 163, 17, 0.1);
            color: var(--monitoring-color);
        }
        
        .metric-icon.incident {
            background: rgba(230, 57, 70, 0.1);
            color: var(--incident-color);
        }
        
        .metric-icon.prevention {
            background: rgba(42, 157, 143, 0.1);
            color: var(--prevention-color);
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
        
        .status-secure {
            background: rgba(27, 153, 139, 0.1);
            color: var(--success-color);
        }
        
        .status-warning {
            background: rgba(244, 96, 54, 0.1);
            color: var(--warning-color);
        }
        
        .status-critical {
            background: rgba(196, 69, 54, 0.1);
            color: var(--danger-color);
        }
        
        .status-investigating {
            background: rgba(20, 33, 61, 0.1);
            color: var(--security-color);
        }
        
        .threat-badge {
            padding: 0.35rem 0.65rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .threat-malware {
            background: rgba(20, 33, 61, 0.1);
            color: var(--security-color);
        }
        
        .threat-phishing {
            background: rgba(252, 163, 17, 0.1);
            color: var(--monitoring-color);
        }
        
        .threat-breach {
            background: rgba(230, 57, 70, 0.1);
            color: var(--incident-color);
        }
        
        .threat-unauthorized {
            background: rgba(42, 157, 143, 0.1);
            color: var(--prevention-color);
        }
        
        .notification-toast {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1050;
            min-width: 300px;
        }
        
        .severity-indicator {
            height: 6px;
            border-radius: 3px;
            background: #e9ecef;
            margin-top: 5px;
            overflow: hidden;
        }
        
        .severity-bar {
            height: 100%;
            border-radius: 3px;
        }
        
        .severity-critical {
            background: var(--danger-color);
        }
        
        .severity-high {
            background: var(--warning-color);
        }
        
        .severity-medium {
            background: var(--monitoring-color);
        }
        
        .severity-low {
            background: var(--success-color);
        }
        
        .incident-priority-critical {
            border-left: 4px solid var(--danger-color);
        }
        
        .incident-priority-high {
            border-left: 4px solid var(--warning-color);
        }
        
        .incident-priority-medium {
            border-left: 4px solid var(--monitoring-color);
        }
        
        .security-stats {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }
        
        .security-stat {
            text-align: center;
        }
        
        .security-stat-value {
            font-weight: 700;
            font-size: 1.1rem;
        }
        
        .security-stat-label {
            font-size: 0.75rem;
            color: #6c757d;
        }
        
        .monitoring-event {
            padding: 0.5rem;
            border-radius: 5px;
            margin-bottom: 0.5rem;
            font-size: 0.85rem;
        }
        
        .event-alert {
            background: rgba(20, 33, 61, 0.1);
            border-left: 3px solid var(--security-color);
        }
        
        .event-incident {
            background: rgba(252, 163, 17, 0.1);
            border-left: 3px solid var(--monitoring-color);
        }
        
        .event-breach {
            background: rgba(230, 57, 70, 0.1);
            border-left: 3px solid var(--incident-color);
        }
        
        .event-prevention {
            background: rgba(42, 157, 143, 0.1);
            border-left: 3px solid var(--prevention-color);
        }
        
        .risk-indicator {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-right: 5px;
        }
        
        .risk-critical {
            background: var(--danger-color);
        }
        
        .risk-high {
            background: var(--warning-color);
        }
        
        .risk-medium {
            background: var(--monitoring-color);
        }
        
        .risk-low {
            background: var(--success-color);
        }
        
        .security-status-card {
            background: linear-gradient(135deg, #14213D, #FCA311);
            color: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1rem;
        }
        
        .threat-level {
            display: flex;
            justify-content: space-between;
            margin-top: 1rem;
        }
        
        .threat-item {
            text-align: center;
            flex: 1;
        }
        
        .threat-value {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 0.25rem;
        }
        
        .threat-label {
            font-size: 0.75rem;
            opacity: 0.9;
        }
        
        .system-badge {
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

        .security-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--security-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: bold;
        }

        .live-monitoring {
            background: #1a1a1a;
            color: #00ff00;
            font-family: 'Courier New', monospace;
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1rem;
            max-height: 120px;
            overflow-y: auto;
        }

        .monitoring-line {
            margin-bottom: 0.25rem;
            font-size: 0.85rem;
        }

        .timestamp {
            color: #888;
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
                        <li class="breadcrumb-item active">Segurança</li>
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
                        <li><a class="dropdown-item" href="#">3 tentativas de acesso não autorizado</a></li>
                        <li><a class="dropdown-item" href="#">Sistema de detecção de intrusos ativado</a></li>
                        <li><a class="dropdown-item" href="#">Vulnerabilidade crítica detectada</a></li>
                        <li><a class="dropdown-item" href="#">Backup de segurança concluído</a></li>
                        <li><a class="dropdown-item" href="#">Atualização de firewall pendente</a></li>
                        <li><a class="dropdown-item" href="#">Ameaça de phishing identificada</a></li>
                        <li><a class="dropdown-item" href="#">Monitoramento offline - Câmera 4</a></li>
                        <li><a class="dropdown-item" href="#">Relatório de auditoria pronto</a></li>
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
                        <li><h6 class="dropdown-header user-name">Segurança NexusFlow</h6></li>
                        <li><small class="dropdown-header text-muted user-email">seguranca@nexusflow.com</small></li>
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
                    <h1 class="page-title">Dashboard de Segurança</h1>
                    <p class="page-subtitle">Monitoramento em tempo real, prevenção de ameaças e gestão de incidentes</p>
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
            
            <!-- Card de Status de Segurança -->
            <div class="security-status-card mb-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h3 class="text-white">Status de Segurança Corporativa</h3>
                        <p class="text-white mb-0">Sistema de proteção integrado e monitoramento 24/7</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="gauge-container">
                            <div class="gauge-background"></div>
                            <div class="gauge-fill" id="securityGauge" style="background: var(--success-color); transform: rotate(0.76turn);"></div>
                            <div class="gauge-value text-white">88%</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Monitoramento em Tempo Real -->
            <div class="card-custom mb-4">
                <div class="card-header-custom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Monitoramento em Tempo Real</h5>
                    <span class="badge bg-success">Online</span>
                </div>
                <div class="card-body">
                    <div class="live-monitoring">
                        <div class="monitoring-line">
                            <span class="timestamp">[14:23:45]</span> Sistema: Todos os sensores operacionais
                        </div>
                        <div class="monitoring-line">
                            <span class="timestamp">[14:23:42]</span> Firewall: 128 conexões bloqueadas (última hora)
                        </div>
                        <div class="monitoring-line">
                            <span class="timestamp">[14:23:38]</span> Rede: Tráfego normal - 45MB/s
                        </div>
                        <div class="monitoring-line">
                            <span class="timestamp">[14:23:35]</span> IDS: Nenhuma intrusão detectada
                        </div>
                        <div class="monitoring-line">
                            <span class="timestamp">[14:23:30]</span> Câmeras: 12/14 online | 2 em manutenção
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Nível de Ameaça -->
            <div class="card-custom mb-4">
                <div class="card-header-custom">
                    <h5 class="mb-0">Nível de Ameaça por Categoria</h5>
                </div>
                <div class="card-body">
                    <div class="threat-level">
                        <div class="threat-item">
                            <div class="threat-value"><span class="risk-indicator risk-low"></span>12%</div>
                            <div class="threat-label">Malware</div>
                        </div>
                        <div class="threat-item">
                            <div class="threat-value"><span class="risk-indicator risk-medium"></span>28%</div>
                            <div class="threat-label">Phishing</div>
                        </div>
                        <div class="threat-item">
                            <div class="threat-value"><span class="risk-indicator risk-high"></span>45%</div>
                            <div class="threat-label">Acesso Não Autorizado</div>
                        </div>
                        <div class="threat-item">
                            <div class="threat-value"><span class="risk-indicator risk-critical"></span>62%</div>
                            <div class="threat-label">Vazamento de Dados</div>
                        </div>
                        <div class="threat-item">
                            <div class="threat-value"><span class="risk-indicator risk-medium"></span>34%</div>
                            <div class="threat-label">Ransomware</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Métricas Principais -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="metric-card security">
                        <div class="metric-icon security">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <div class="metric-value">98.7%</div>
                        <div class="metric-label">Sistemas Protegidos</div>
                        <div class="metric-change positive">
                            <i class="bi bi-arrow-up"></i> +2.3% este mês
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="metric-card monitoring">
                        <div class="metric-icon monitoring">
                            <i class="bi bi-eye"></i>
                        </div>
                        <div class="metric-value">247</div>
                        <div class="metric-label">Alertas Este Mês</div>
                        <div class="metric-change negative">
                            <i class="bi bi-arrow-up"></i> +18 esta semana
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="metric-card incident">
                        <div class="metric-icon incident">
                            <i class="bi bi-exclamation-triangle"></i>
                        </div>
                        <div class="metric-value">12</div>
                        <div class="metric-label">Incidentes Críticos</div>
                        <div class="metric-change negative">
                            <i class="bi bi-arrow-up"></i> +3 hoje
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="metric-card prevention">
                        <div class="metric-icon prevention">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <div class="metric-value">4.2min</div>
                        <div class="metric-label">Tempo Médio de Resposta</div>
                        <div class="metric-change positive">
                            <i class="bi bi-arrow-down"></i> -1.8min vs mês anterior
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Gráficos e Dados -->
            <div class="row mb-4">
                <!-- Gráfico de Ameaças -->
                <div class="col-xl-8 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Tendência de Ameaças - Últimos 6 Meses</h5>
                            <div class="btn-group btn-group-sm" role="group">
                                <input type="radio" class="btn-check" name="period" id="period7" checked>
                                <label class="btn btn-outline-primary" for="period7">Malware</label>
                                
                                <input type="radio" class="btn-check" name="period" id="period30">
                                <label class="btn btn-outline-primary" for="period30">Phishing</label>
                                
                                <input type="radio" class="btn-check" name="period" id="period90">
                                <label class="btn btn-outline-primary" for="period90">Acesso Não Autorizado</label>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="threatTrendChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
                
                <!-- Distribuição por Tipo -->
                <div class="col-xl-4 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom">
                            <h5 class="mb-0">Distribuição por Tipo de Ameaça</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="threatDistributionChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tabelas de Dados -->
            <div class="row">
                <!-- Incidentes Críticos -->
                <div class="col-xl-6 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Incidentes Críticos</h5>
                            <a href="incidentes.html" class="btn btn-sm btn-outline-primary">Ver todos</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-custom mb-0">
                                    <thead>
                                        <tr>
                                            <th>Incidente</th>
                                            <th>Tipo</th>
                                            <th>Status</th>
                                            <th>Severidade</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="incident-priority-critical">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <div class="security-avatar">
                                                            ID
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold">Tentativa de Acesso Não Autorizado</div>
                                                        <small class="text-muted">IP: 192.168.1.45 | Sistema: Servidor Principal</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="threat-badge threat-unauthorized">Acesso</span></td>
                                            <td><span class="status-badge status-critical">Crítico</span></td>
                                            <td>
                                                <div>Nível 9</div>
                                                <div class="severity-indicator">
                                                    <div class="severity-bar severity-critical" style="width: 90%"></div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="incident-priority-high">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <div class="security-avatar" style="background: var(--monitoring-color);">
                                                            PH
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold">Ataque de Phishing Detectado</div>
                                                        <small class="text-muted">Origem: phishing@fakecompany.com | 15 usuários afetados</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="threat-badge threat-phishing">Phishing</span></td>
                                            <td><span class="status-badge status-warning">Investigando</span></td>
                                            <td>
                                                <div>Nível 7</div>
                                                <div class="severity-indicator">
                                                    <div class="severity-bar severity-high" style="width: 70%"></div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="incident-priority-medium">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <div class="security-avatar" style="background: var(--prevention-color);">
                                                            MW
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold">Malware Detectado</div>
                                                        <small class="text-muted">Tipo: Ransomware | Sistema: Estação de Trabalho 12</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="threat-badge threat-malware">Malware</span></td>
                                            <td><span class="status-badge status-investigating">Contido</span></td>
                                            <td>
                                                <div>Nível 5</div>
                                                <div class="severity-indicator">
                                                    <div class="severity-bar severity-medium" style="width: 50%"></div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Monitoramento de Sistemas -->
                <div class="col-xl-6 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Monitoramento de Sistemas</h5>
                            <a href="monitoramento.html" class="btn btn-sm btn-outline-primary">Ver completo</a>
                        </div>
                        <div class="card-body">
                            <div class="monitoring-event event-alert">
                                <div class="fw-bold">Alerta: Tentativa de Acesso Não Autorizado</div>
                                <small>14:23:45 | IP: 192.168.1.45 | Sistema: Servidor Principal</small>
                            </div>
                            
                            <div class="monitoring-event event-incident">
                                <div class="fw-bold">Incidente: Firewall Sob Carga</div>
                                <small>14:15:30 | Tráfego: 245MB/s | Ações: 128 bloqueios</small>
                            </div>
                            
                            <div class="monitoring-event event-breach">
                                <div class="fw-bold">Violação: Tentativa de Vazamento de Dados</div>
                                <small>13:45:22 | Usuário: j.silva | Sistema: Banco de Dados</small>
                            </div>
                            
                            <div class="monitoring-event event-prevention">
                                <div class="fw-bold">Prevenção: Ataque DDoS Mitigado</div>
                                <small>13:30:15 | Origem: IPs Internacionais | Duração: 4min</small>
                            </div>
                            
                            <div class="monitoring-event event-alert">
                                <div class="fw-bold">Alerta: Atividade Suspeita na Rede</div>
                                <small>12:58:40 | Dispositivo: Estação 07 | Ação: Isolamento</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- KPIs de Segurança -->
            <div class="row">
                <!-- Indicadores de Proteção -->
                <div class="col-xl-6 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom">
                            <h5 class="mb-0">Indicadores de Proteção</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <i class="bi bi-shield-lock text-primary" style="font-size: 1.5rem;"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold">Firewall & IPS</div>
                                            <div class="security-stats">
                                                <div class="security-stat">
                                                    <div class="security-stat-value"><span class="risk-indicator risk-low"></span>99.2%</div>
                                                    <div class="security-stat-label">Eficiência</div>
                                                </div>
                                                <div class="security-stat">
                                                    <div class="security-stat-value"><span class="risk-indicator risk-medium"></span>42</div>
                                                    <div class="security-stat-label">Regras Ativas</div>
                                                </div>
                                                <div class="security-stat">
                                                    <div class="security-stat-value"><span class="risk-indicator risk-low"></span>0</div>
                                                    <div class="security-stat-label">Brechas</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <i class="bi bi-eye text-success" style="font-size: 1.5rem;"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold">Monitoramento</div>
                                            <div class="security-stats">
                                                <div class="security-stat">
                                                    <div class="security-stat-value"><span class="risk-indicator risk-low"></span>100%</div>
                                                    <div class="security-stat-label">Cobertura</div>
                                                </div>
                                                <div class="security-stat">
                                                    <div class="security-stat-value"><span class="risk-indicator risk-medium"></span>4.2min</div>
                                                    <div class="security-stat-label">Tempo Resposta</div>
                                                </div>
                                                <div class="security-stat">
                                                    <div class="security-stat-value"><span class="risk-indicator risk-high"></span>12</div>
                                                    <div class="security-stat-label">Alertas Ativos</div>
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
                                            <i class="bi bi-hdd-rack text-warning" style="font-size: 1.5rem;"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold">Sistemas</div>
                                            <div class="security-stats">
                                                <div class="security-stat">
                                                    <div class="security-stat-value"><span class="risk-indicator risk-low"></span>98.7%</div>
                                                    <div class="security-stat-label">Protegidos</div>
                                                </div>
                                                <div class="security-stat">
                                                    <div class="security-stat-value"><span class="risk-indicator risk-medium"></span>3</div>
                                                    <div class="security-stat-label">Atualizações Pendentes</div>
                                                </div>
                                                <div class="security-stat">
                                                    <div class="security-stat-value"><span class="risk-indicator risk-low"></span>0</div>
                                                    <div class="security-stat-label">Offline</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <i class="bi bi-person-check text-info" style="font-size: 1.5rem;"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold">Acesso</div>
                                            <div class="security-stats">
                                                <div class="security-stat">
                                                    <div class="security-stat-value"><span class="risk-indicator risk-low"></span>0</div>
                                                    <div class="security-stat-label">Violações</div>
                                                </div>
                                                <div class="security-stat">
                                                    <div class="security-stat-value"><span class="risk-indicator risk-medium"></span>247</div>
                                                    <div class="security-stat-label">Tentativas Bloqueadas</div>
                                                </div>
                                                <div class="security-stat">
                                                    <div class="security-stat-value"><span class="risk-indicator risk-high"></span>12</div>
                                                    <div class="security-stat-label">Credenciais Suspeitas</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Alertas de Segurança -->
                <div class="col-xl-6 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom">
                            <h5 class="mb-0">Alertas de Segurança</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-warning d-flex align-items-center mb-3">
                                <i class="bi bi-exclamation-triangle me-3"></i>
                                <div>
                                    <strong>3 tentativas de acesso não autorizado</strong> detectadas no servidor principal
                                    <div class="mt-1">
                                        <a href="#" class="btn btn-sm btn-warning">Investigar</a>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="alert alert-info d-flex align-items-center mb-3">
                                <i class="bi bi-shield-check me-3"></i>
                                <div>
                                    <strong>Sistema de detecção de intrusos</strong> ativado com sucesso
                                    <div class="mt-1">
                                        <a href="#" class="btn btn-sm btn-info">Ver logs</a>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="alert alert-success d-flex align-items-center mb-3">
                                <i class="bi bi-check-circle me-3"></i>
                                <div>
                                    <strong>Backup de segurança</strong> concluído com sucesso
                                    <div class="mt-1">
                                        <small class="text-muted">Todos os sistemas protegidos</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="alert alert-danger d-flex align-items-center mb-0">
                                <i class="bi bi-shield-exclamation me-3"></i>
                                <div>
                                    <strong>Vulnerabilidade crítica</strong> detectada no sistema de autenticação
                                    <div class="mt-1">
                                        <a href="#" class="btn btn-sm btn-danger">Corrigir agora</a>
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
            // Gráfico de Tendência de Ameaças
            const threatTrendCtx = document.getElementById('threatTrendChart').getContext('2d');
            const threatTrendChart = new Chart(threatTrendCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun'],
                    datasets: [{
                        label: 'Tentativas de Acesso Não Autorizado',
                        data: [45, 52, 48, 65, 72, 68],
                        borderColor: '#14213D',
                        backgroundColor: 'rgba(20, 33, 61, 0.1)',
                        tension: 0.4,
                        fill: true
                    }, {
                        label: 'Ataques de Phishing',
                        data: [28, 35, 42, 38, 45, 52],
                        borderColor: '#FCA311',
                        backgroundColor: 'rgba(252, 163, 17, 0.1)',
                        tension: 0.4,
                        fill: true
                    }, {
                        label: 'Detecções de Malware',
                        data: [15, 18, 22, 25, 28, 32],
                        borderColor: '#E63946',
                        backgroundColor: 'rgba(230, 57, 70, 0.1)',
                        tension: 0.4,
                        fill: true
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
            
            // Gráfico de Distribuição por Tipo de Ameaça
            const threatDistributionCtx = document.getElementById('threatDistributionChart').getContext('2d');
            const threatDistributionChart = new Chart(threatDistributionCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Acesso Não Autorizado', 'Phishing', 'Malware', 'Vazamento de Dados', 'DDoS', 'Outros'],
                    datasets: [{
                        data: [35, 25, 15, 12, 8, 5],
                        backgroundColor: [
                            '#14213D',
                            '#FCA311',
                            '#E63946',
                            '#2A9D8F',
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

            // Simular monitoramento em tempo real
            function updateMonitoring() {
                const monitoring = document.querySelector('.live-monitoring');
                if (monitoring) {
                    const lines = monitoring.querySelectorAll('.monitoring-line');
                    if (lines.length > 0) {
                        monitoring.scrollTop = monitoring.scrollHeight;
                    }
                }
            }

            // Inicializar monitoramento
            updateMonitoring();
        });
        
        // Funções específicas da página
        function exportReport() {
            nexusFlow.showNotification('Exportando relatório de segurança...', 'info');
            setTimeout(() => {
                nexusFlow.showNotification('Relatório exportado com sucesso!', 'success');
            }, 2000);
        }
        
        function refreshDashboard() {
            nexusFlow.showNotification('Atualizando dashboard de segurança...', 'info');
            setTimeout(() => {
                nexusFlow.showNotification('Dashboard atualizado!', 'success');
                // Em uma aplicação real, aqui seria feita uma requisição para atualizar os dados
                // Por simplicidade, apenas recarregamos a página
                location.reload();
            }, 1000);
        }
        
        // Definir papel como segurança
        localStorage.setItem('userRole', 'seguranca');
    </script>
