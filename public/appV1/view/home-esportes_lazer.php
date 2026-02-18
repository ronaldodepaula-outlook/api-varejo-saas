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
    <title>Dashboard Esportes & Lazer</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #E74C3C;
            --secondary-color: #27AE60;
            --warning-color: #F39C12;
            --danger-color: #C0392B;
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
        
        .metric-icon.primary { background: rgba(231, 76, 60, 0.1); color: var(--primary-color); }
        .metric-icon.success { background: rgba(39, 174, 96, 0.1); color: var(--secondary-color); }
        .metric-icon.warning { background: rgba(243, 156, 18, 0.1); color: var(--warning-color); }
        .metric-icon.danger { background: rgba(192, 57, 43, 0.1); color: var(--danger-color); }
        
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
        
        .status-active { background: rgba(39, 174, 96, 0.1); color: var(--secondary-color); }
        .status-pending { background: rgba(243, 156, 18, 0.1); color: var(--warning-color); }
        .status-warning { background: rgba(231, 76, 60, 0.1); color: var(--primary-color); }
        
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
        
        .activity-card {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 15px;
            transition: transform 0.2s;
        }
        
        .activity-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .activity-image {
            height: 100px;
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
        }
        
        .activity-info {
            padding: 15px;
        }
        
        .activity-title {
            font-size: 1rem;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .activity-meta {
            font-size: 0.85rem;
            color: #6c757d;
            margin-bottom: 10px;
        }
        
        .activity-stats {
            display: flex;
            justify-content: space-between;
            font-size: 0.8rem;
        }
        
        .schedule-item {
            padding: 10px;
            border-left: 4px solid transparent;
            margin-bottom: 8px;
            border-radius: 5px;
            background: #f8f9fa;
        }
        
        .schedule-item.ongoing {
            border-left-color: var(--danger-color);
            background: rgba(231, 76, 60, 0.05);
        }
        
        .schedule-item.upcoming {
            border-left-color: var(--warning-color);
            background: rgba(243, 156, 18, 0.05);
        }
        
        .schedule-item.completed {
            border-left-color: var(--secondary-color);
            background: rgba(39, 174, 96, 0.05);
        }
        
        .equipment-status {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 8px;
            background: #f8f9fa;
        }
        
        .equipment-status.good { border-left: 4px solid var(--secondary-color); }
        .equipment-status.maintenance { border-left: 4px solid var(--warning-color); }
        .equipment-status.broken { border-left: 4px solid var(--danger-color); }
        
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
                        <li class="breadcrumb-item active">Esportes & Lazer</li>
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
                        <li><h6 class="dropdown-header">Alertas Esportivos</h6></li>
                        <li><a class="dropdown-item" href="#">Aula de Yoga lotada</a></li>
                        <li><a class="dropdown-item" href="#">Manutenção necessária na piscina</a></li>
                        <li><a class="dropdown-item" href="#">Novo recorde de frequência</a></li>
                        <li><a class="dropdown-item" href="#">Equipamento precisa de reparo</a></li>
                        <li><a class="dropdown-item" href="#">Evento especial no sábado</a></li>
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
                        <li><h6 class="dropdown-header user-name">Gerente Esportivo</h6></li>
                        <li><small class="dropdown-header text-muted user-email">esportes@empresa.com</small></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="perfil.html"><i class="bi bi-person me-2"></i>Meu Perfil</a></li>
                        <li><a class="dropdown-item" href="#"><i class="bi bi-calendar-event me-2"></i>Eventos</a></li>
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
                    <h1 class="page-title">Dashboard Esportes & Lazer</h1>
                    <p class="page-subtitle">Gestão de academias, atividades e eventos esportivos</p>
                </div>
                <div>
                    <button class="btn btn-outline-primary me-2" onclick="exportReport()">
                        <i class="bi bi-file-earmark-spreadsheet me-2"></i>Relatório Semanal
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
                            <i class="bi bi-people"></i>
                        </div>
                        <div class="metric-value">1.248</div>
                        <div class="metric-label">Membros Ativos</div>
                        <div class="metric-change positive">
                            <i class="bi bi-arrow-up"></i> +42 este mês
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="metric-card success">
                        <div class="metric-icon success">
                            <i class="bi bi-calendar-check"></i>
                        </div>
                        <div class="metricValue">78%</div>
                        <div class="metric-label">Taxa de Frequência</div>
                        <div class="metric-change positive">
                            <i class="bi bi-arrow-up"></i> +5% vs semana passada
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="metric-card warning">
                        <div class="metric-icon warning">
                            <i class="bi bi-currency-dollar"></i>
                        </div>
                        <div class="metric-value">R$ 45.820</div>
                        <div class="metric-label">Receita Mensal</div>
                        <div class="metric-change positive">
                            <i class="bi bi-arrow-up"></i> +12% vs mês anterior
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="metric-card danger">
                        <div class="metric-icon danger">
                            <i class="bi bi-tools"></i>
                        </div>
                        <div class="metric-value">8</div>
                        <div class="metric-label">Equip. Manutenção</div>
                        <div class="metric-change negative">
                            <i class="bi bi-exclamation-triangle"></i> 2 urgentes
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Gráficos e Dados -->
            <div class="row mb-4">
                <!-- Frequência e Participação -->
                <div class="col-xl-8 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Frequência e Participação</h5>
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
                            <canvas id="attendanceChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
                
                <!-- Atividades Populares -->
                <div class="col-xl-4 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom">
                            <h5 class="mb-0">Atividades Mais Populares</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="activitiesChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Atividades e Agenda -->
            <div class="row mb-4">
                <!-- Próximas Atividades -->
                <div class="col-xl-6 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Próximas Atividades</h5>
                            <a href="agenda.html" class="btn btn-sm btn-outline-primary">Ver agenda completa</a>
                        </div>
                        <div class="card-body">
                            <div class="schedule-item ongoing">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>Musculação - Área Livre</strong>
                                        <div class="small">Instrutor: Carlos - 25 participantes</div>
                                        <div class="small text-muted">09:00 - 10:30</div>
                                    </div>
                                    <span class="badge bg-danger">Em andamento</span>
                                </div>
                            </div>
                            
                            <div class="schedule-item upcoming">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>Yoga Avançado</strong>
                                        <div class="small">Instrutor: Ana - 18 inscritos</div>
                                        <div class="small text-muted">10:30 - 11:45</div>
                                    </div>
                                    <span class="badge bg-warning">Próxima</span>
                                </div>
                            </div>
                            
                            <div class="schedule-item upcoming">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>Natação Adultos</strong>
                                        <div class="small">Instrutor: Pedro - 12 inscritos</div>
                                        <div class="small text-muted">14:00 - 15:30</div>
                                    </div>
                                    <span class="badge bg-primary">Às 14:00</span>
                                </div>
                            </div>
                            
                            <div class="schedule-item completed">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>Pilates Mat</strong>
                                        <div class="small">Instrutor: Maria - 15 participantes</div>
                                        <div class="small text-muted">07:30 - 08:45</div>
                                    </div>
                                    <span class="badge bg-success">Concluída</span>
                                </div>
                            </div>
                            
                            <div class="mt-3">
                                <h6 class="mb-3">Estatísticas do Dia</h6>
                                <div class="row text-center">
                                    <div class="col-4">
                                        <div class="metric-value text-primary">6</div>
                                        <div class="metric-label">Atividades</div>
                                    </div>
                                    <div class="col-4">
                                        <div class="metric-value text-success">142</div>
                                        <div class="metric-label">Participantes</div>
                                    </div>
                                    <div class="col-4">
                                        <div class="metric-value text-warning">92%</div>
                                        <div class="metric-label">Ocupação</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Status de Equipamentos -->
                <div class="col-xl-6 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Status dos Equipamentos</h5>
                            <span class="badge bg-primary">48 equipamentos</span>
                        </div>
                        <div class="card-body">
                            <div class="equipment-status good">
                                <div class="me-3">
                                    <i class="bi bi-check-circle-fill text-success" style="font-size: 1.5rem;"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <strong>Esteiras (12 unidades)</strong>
                                    <div class="small text-muted">Todas operacionais</div>
                                </div>
                            </div>
                            
                            <div class="equipment-status maintenance">
                                <div class="me-3">
                                    <i class="bi bi-tools text-warning" style="font-size: 1.5rem;"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <strong>Bicicletas Ergométricas (2 unidades)</strong>
                                    <div class="small text-muted">Manutenção preventiva</div>
                                </div>
                            </div>
                            
                            <div class="equipment-status broken">
                                <div class="me-3">
                                    <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size: 1.5rem;"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <strong>Leg Press</strong>
                                    <div class="small text-muted">Necessita reparo urgente</div>
                                </div>
                            </div>
                            
                            <div class="equipment-status good">
                                <div class="me-3">
                                    <i class="bi bi-check-circle-fill text-success" style="font-size: 1.5rem;"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <strong>Halteres e Anilhas</strong>
                                    <div class="small text-muted">Conjunto completo</div>
                                </div>
                            </div>
                            
                            <div class="mt-3">
                                <h6 class="mb-3">Resumo de Manutenção</h6>
                                <div class="row text-center">
                                    <div class="col-4">
                                        <div class="metric-value text-success">38</div>
                                        <div class="metric-label">Operacionais</div>
                                    </div>
                                    <div class="col-4">
                                        <div class="metric-value text-warning">6</div>
                                        <div class="metric-label">Manutenção</div>
                                    </div>
                                    <div class="col-4">
                                        <div class="metric-value text-danger">2</div>
                                        <div class="metric-label">Quebrados</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Modalidades em Destaque -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Modalidades em Destaque</h5>
                            <a href="modalidades.html" class="btn btn-sm btn-outline-primary">Todas modalidades</a>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <div class="activity-card">
                                        <div class="activity-image" style="background: linear-gradient(135deg, #3498DB 0%, #2980B9 100%);">
                                            <i class="bi bi-droplet"></i>
                                        </div>
                                        <div class="activity-info">
                                            <div class="activity-title">Natação</div>
                                            <div class="activity-meta">Piscina olímpica</div>
                                            <div class="activity-stats">
                                                <span><i class="bi bi-people"></i> 68 inscritos</span>
                                                <span class="text-success">92% freq.</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-3 mb-3">
                                    <div class="activity-card">
                                        <div class="activity-image" style="background: linear-gradient(135deg, #2ECC71 0%, #27AE60 100%);">
                                            <i class="bi bi-flower1"></i>
                                        </div>
                                        <div class="activity-info">
                                            <div class="activity-title">Yoga</div>
                                            <div class="activity-meta">Sala de meditação</div>
                                            <div class="activity-stats">
                                                <span><i class="bi bi-people"></i> 45 inscritos</span>
                                                <span class="text-success">88% freq.</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-3 mb-3">
                                    <div class="activity-card">
                                        <div class="activity-image" style="background: linear-gradient(135deg, #E74C3C 0%, #C0392B 100%);">
                                            <i class="bi bi-activity"></i>
                                        </div>
                                        <div class="activity-info">
                                            <div class="activity-title">Musculação</div>
                                            <div class="activity-meta">Academia completa</div>
                                            <div class="activity-stats">
                                                <span><i class="bi bi-people"></i> 124 inscritos</span>
                                                <span class="text-success">78% freq.</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-3 mb-3">
                                    <div class="activity-card">
                                        <div class="activity-image" style="background: linear-gradient(135deg, #9B59B6 0%, #8E44AD 100%);">
                                            <i class="bi bi-music-note-beamed"></i>
                                        </div>
                                        <div class="activity-info">
                                            <div class="activity-title">Dança</div>
                                            <div class="activity-meta">Sala espelhada</div>
                                            <div class="activity-stats">
                                                <span><i class="bi bi-people"></i> 32 inscritos</span>
                                                <span class="text-success">85% freq.</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Alertas do Setor Esportivo -->
            <div class="row">
                <div class="col-12">
                    <div class="card-custom">
                        <div class="card-header-custom">
                            <h5 class="mb-0">Alertas e Oportunidades</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="alert alert-warning d-flex align-items-center mb-3">
                                        <i class="bi bi-tools me-3"></i>
                                        <div>
                                            <strong>8 equipamentos</strong> necessitam de manutenção
                                            <div class="mt-1">
                                                <a href="manutencao.html" class="btn btn-sm btn-warning">Agendar reparos</a>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="alert alert-info d-flex align-items-center mb-3">
                                        <i class="bi bi-calendar-event me-3"></i>
                                        <div>
                                            <strong>Evento especial</strong> no próximo sábado
                                            <div class="mt-1">
                                                <small class="text-muted">Maratona de atividades - 150 inscritos</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="alert alert-success d-flex align-items-center mb-3">
                                        <i class="bi bi-graph-up-arrow me-3"></i>
                                        <div>
                                            <strong>Recorde de frequência</strong> batido
                                            <div class="mt-1">
                                                <small class="text-muted">78% de taxa média esta semana</small>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="alert alert-danger d-flex align-items-center mb-0">
                                        <i class="bi bi-droplet me-3"></i>
                                        <div>
                                            <strong>Manutenção da piscina</strong> necessária
                                            <div class="mt-1">
                                                <a href="piscina.html" class="btn btn-sm btn-danger">Ver detalhes</a>
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

        // Gráficos específicos do segmento de esportes e lazer
        document.addEventListener('DOMContentLoaded', function() {
            // Gráfico de Frequência
            const attendanceCtx = document.getElementById('attendanceChart').getContext('2d');
            const attendanceChart = new Chart(attendanceCtx, {
                type: 'line',
                data: {
                    labels: ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'],
                    datasets: [{
                        label: 'Frequência Diária',
                        data: [185, 210, 195, 235, 268, 312, 245],
                        borderColor: '#E74C3C',
                        backgroundColor: 'rgba(231, 76, 60, 0.1)',
                        tension: 0.4,
                        fill: true
                    }, {
                        label: 'Média Móvel (7 dias)',
                        data: [190, 195, 200, 210, 225, 240, 235],
                        borderColor: '#3498DB',
                        borderDash: [5, 5],
                        backgroundColor: 'transparent',
                        tension: 0.4,
                        fill: false
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
                                text: 'Número de Pessoas'
                            }
                        }
                    }
                }
            });
            
            // Gráfico de Atividades Populares
            const activitiesCtx = document.getElementById('activitiesChart').getContext('2d');
            const activitiesChart = new Chart(activitiesCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Musculação', 'Yoga', 'Natação', 'Pilates', 'Dança', 'Outros'],
                    datasets: [{
                        data: [35, 20, 15, 12, 10, 8],
                        backgroundColor: [
                            '#E74C3C',
                            '#2ECC71',
                            '#3498DB',
                            '#F39C12',
                            '#9B59B6',
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
            nexusFlow.showNotification('Gerando relatório esportivo...', 'info');
            setTimeout(() => {
                nexusFlow.showNotification('Relatório exportado com sucesso!', 'success');
            }, 1500);
        }
        
        function refreshDashboard() {
            nexusFlow.showNotification('Atualizando dados esportivos...', 'info');
            setTimeout(() => {
                nexusFlow.showNotification('Dados atualizados!', 'success');
            }, 1000);
        }
        
        // Definir papel como gerente esportivo
        localStorage.setItem('userRole', 'sports_manager');
        
        // Simulação de atualização em tempo real das atividades
        setInterval(function() {
            // Simular mudança de status de atividades (apenas visual)
            const ongoingActivities = document.querySelectorAll('.schedule-item.ongoing');
            if (ongoingActivities.length > 0) {
                const randomActivity = ongoingActivities[Math.floor(Math.random() * ongoingActivities.length)];
                if (randomActivity) {
                    randomActivity.classList.remove('ongoing');
                    randomActivity.classList.add('completed');
                    const badge = randomActivity.querySelector('.badge');
                    if (badge) {
                        badge.className = 'badge bg-success';
                        badge.textContent = 'Concluída';
                    }
                }
            }
        }, 30000); // Atualiza a cada 30 segundos
    </script>
</body>
</html>