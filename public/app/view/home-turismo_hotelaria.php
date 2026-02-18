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
    <title>Dashboard Turismo & Hotelaria</title>
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
        
        .room-status {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 8px;
            background: #f8f9fa;
        }
        
        .room-status.occupied { border-left: 4px solid var(--danger-color); }
        .room-status.available { border-left: 4px solid var(--secondary-color); }
        .room-status.maintenance { border-left: 4px solid var(--warning-color); }
        .room-status.cleaning { border-left: 4px solid var(--primary-color); }
        
        .guest-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 15px;
        }
        
        .occupancy-chart {
            height: 200px;
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
                        <li class="breadcrumb-item active">Turismo & Hotelaria</li>
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
                        <li><h6 class="dropdown-header">Alertas do Hotel</h6></li>
                        <li><a class="dropdown-item" href="#">3 check-ins pendentes</a></li>
                        <li><a class="dropdown-item" href="#">Quarto 203 solicita limpeza</a></li>
                        <li><a class="dropdown-item" href="#">Reserva especial para VIP</a></li>
                        <li><a class="dropdown-item" href="#">Manutenção agendada para piscina</a></li>
                        <li><a class="dropdown-item" href="#">Nova avaliação 5 estrelas</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-center" href="#">Ver todos</a></li>
                    </ul>
                </div>
                
                <!-- Dropdown do Usuário -->
                <div class="dropdown user-dropdown">
                    <div class="user-avatar" data-bs-toggle="dropdown" aria-expanded="false">
                        T
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><h6 class="dropdown-header user-name">Gerente Hotel</h6></li>
                        <li><small class="dropdown-header text-muted user-email">hotel@empresa.com</small></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="perfil.html"><i class="bi bi-person me-2"></i>Meu Perfil</a></li>
                        <li><a class="dropdown-item" href="#"><i class="bi bi-calendar-check me-2"></i>Reservas</a></li>
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
                    <h1 class="page-title">Dashboard Hotel & Turismo</h1>
                    <p class="page-subtitle">Gestão de ocupação, reservas e operações hoteleiras</p>
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
                            <i class="bi bi-house-door"></i>
                        </div>
                        <div class="metric-value">78%</div>
                        <div class="metric-label">Taxa de Ocupação</div>
                        <div class="metric-change positive">
                            <i class="bi bi-arrow-up"></i> +12% vs semana passada
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="metric-card success">
                        <div class="metric-icon success">
                            <i class="bi bi-currency-dollar"></i>
                        </div>
                        <div class="metric-value">R$ 24.850</div>
                        <div class="metric-label">Receita do Mês</div>
                        <div class="metric-change positive">
                            <i class="bi bi-arrow-up"></i> +8.5% vs mês anterior
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="metric-card warning">
                        <div class="metric-icon warning">
                            <i class="bi bi-people"></i>
                        </div>
                        <div class="metric-value">42</div>
                        <div class="metric-label">Hóspedes Atuais</div>
                        <div class="metric-change positive">
                            <i class="bi bi-arrow-up"></i> +8 hóspedes vs ontem
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="metric-card danger">
                        <div class="metric-icon danger">
                            <i class="bi bi-calendar-x"></i>
                        </div>
                        <div class="metricValue">5</div>
                        <div class="metric-label">Check-outs Hoje</div>
                        <div class="metric-change negative">
                            <i class="bi bi-clock"></i> 2 pendentes
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Gráficos e Dados -->
            <div class="row mb-4">
                <!-- Ocupação e Reservas -->
                <div class="col-xl-8 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Ocupação e Previsão de Reservas</h5>
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
                            <canvas id="occupancyChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
                
                <!-- Status dos Quartos -->
                <div class="col-xl-4 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Status dos Quartos</h5>
                            <span class="badge bg-primary">48 quartos</span>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-3">
                                <div class="text-center">
                                    <div class="metric-value text-success">28</div>
                                    <div class="metric-label">Disponíveis</div>
                                </div>
                                <div class="text-center">
                                    <div class="metric-value text-danger">15</div>
                                    <div class="metric-label">Ocupados</div>
                                </div>
                                <div class="text-center">
                                    <div class="metric-value text-warning">5</div>
                                    <div class="metric-label">Manutenção</div>
                                </div>
                            </div>
                            
                            <div class="occupancy-chart">
                                <canvas id="roomStatusChart"></canvas>
                            </div>
                            
                            <div class="mt-3">
                                <h6 class="mb-3">Quartos para Limpeza</h6>
                                <div class="room-status cleaning">
                                    <div class="guest-avatar">203</div>
                                    <div>
                                        <strong>Quarto 203</strong>
                                        <div class="small text-muted">Check-out 11:00</div>
                                    </div>
                                </div>
                                <div class="room-status cleaning">
                                    <div class="guest-avatar">215</div>
                                    <div>
                                        <strong>Quarto 215</strong>
                                        <div class="small text-muted">Solicitada limpeza</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tabelas de Dados -->
            <div class="row">
                <!-- Check-ins do Dia -->
                <div class="col-xl-6 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Check-ins de Hoje</h5>
                            <a href="reservas.html" class="btn btn-sm btn-outline-primary">Ver todas</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-custom mb-0">
                                    <thead>
                                        <tr>
                                            <th>Hóspede</th>
                                            <th>Quarto</th>
                                            <th>Período</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <div class="guest-avatar" style="background: #3498DB;">JS</div>
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold">João Silva</div>
                                                        <small class="text-muted">2 adultos</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <strong>204</strong>
                                                <div class="small text-muted">Standard</div>
                                            </td>
                                            <td>
                                                <div>15/06 - 18/06</div>
                                                <small class="text-muted">3 noites</small>
                                            </td>
                                            <td><span class="status-badge status-active">Confirmado</span></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <div class="guest-avatar" style="background: #2ECC71;">MA</div>
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold">Maria Andrade</div>
                                                        <small class="text-muted">Família (4)</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <strong>301</strong>
                                                <div class="small text-muted">Família</div>
                                            </td>
                                            <td>
                                                <div>15/06 - 20/06</div>
                                                <small class="text-muted">5 noites</small>
                                            </td>
                                            <td><span class="status-badge status-pending">Pendente</span></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <div class="guest-avatar" style="background: #9B59B6;">CP</div>
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold">Carlos Pereira</div>
                                                        <small class="text-muted">Executivo</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <strong>108</strong>
                                                <div class="small text-muted">Luxo</div>
                                            </td>
                                            <td>
                                                <div>15/06 - 17/06</div>
                                                <small class="text-muted">2 noites</small>
                                            </td>
                                            <td><span class="status-badge status-active">Confirmado</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Atividades e Serviços -->
                <div class="col-xl-6 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Atividades e Serviços</h5>
                            <a href="servicos.html" class="btn btn-sm btn-outline-primary">Agendar</a>
                        </div>
                        <div class="card-body">
                            <div class="row text-center mb-4">
                                <div class="col-4">
                                    <div class="mb-3">
                                        <div class="metric-value text-primary">8</div>
                                        <div class="metric-label">Passeios</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="mb-3">
                                        <div class="metric-value text-success">12</div>
                                        <div class="metric-label">Spa</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="mb-3">
                                        <div class="metric-value text-warning">5</div>
                                        <div class="metric-label">Restaurante</div>
                                    </div>
                                </div>
                            </div>
                            
                            <h6 class="mb-3">Próximas Atividades</h6>
                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
                                <div>
                                    <strong>Tour Histórico</strong>
                                    <div class="small text-muted">Centro da cidade - 15 pessoas</div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-semibold">14:00</div>
                                    <div class="small text-muted">em 2 horas</div>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
                                <div>
                                    <strong>Massagem Relaxante</strong>
                                    <div class="small text-muted">Spa - Quarto 204</div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-semibold">16:30</div>
                                    <div class="small text-muted">em 4h30min</div>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
                                <div>
                                    <strong>Jantar Romântico</strong>
                                    <div class="small text-muted">Restaurante - Varanda</div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-semibold">20:00</div>
                                    <div class="small text-muted">em 8 horas</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Alertas do Hotel -->
            <div class="row">
                <div class="col-12">
                    <div class="card-custom">
                        <div class="card-header-custom">
                            <h5 class="mb-0">Alertas e Operações</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="alert alert-warning d-flex align-items-center mb-3">
                                        <i class="bi bi-house-gear me-3"></i>
                                        <div>
                                            <strong>5 quartos</strong> em manutenção
                                            <div class="mt-1">
                                                <a href="manutencao.html" class="btn btn-sm btn-warning">Ver detalhes</a>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="alert alert-info d-flex align-items-center mb-3">
                                        <i class="bi bi-star me-3"></i>
                                        <div>
                                            <strong>Avaliação 4.7</strong> no Booking.com
                                            <div class="mt-1">
                                                <small class="text-muted">12 novas avaliações esta semana</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="alert alert-success d-flex align-items-center mb-3">
                                        <i class="bi bi-calendar-check me-3"></i>
                                        <div>
                                            <strong>Alta temporada</strong> começa em 2 semanas
                                            <div class="mt-1">
                                                <small class="text-muted">92% de ocupação prevista</small>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="alert alert-danger d-flex align-items-center mb-0">
                                        <i class="bi bi-clock me-3"></i>
                                        <div>
                                            <strong>2 check-ins</strong> pendentes
                                            <div class="mt-1">
                                                <a href="recepcao.html" class="btn btn-sm btn-danger">Atender agora</a>
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

        // Gráficos específicos do segmento de turismo e hotelaria
        document.addEventListener('DOMContentLoaded', function() {
            // Gráfico de Ocupação
            const occupancyCtx = document.getElementById('occupancyChart').getContext('2d');
            const occupancyChart = new Chart(occupancyCtx, {
                type: 'line',
                data: {
                    labels: ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'],
                    datasets: [{
                        label: 'Ocupação Real (%)',
                        data: [65, 72, 68, 78, 85, 92, 88],
                        borderColor: '#3498DB',
                        backgroundColor: 'rgba(52, 152, 219, 0.1)',
                        tension: 0.4,
                        fill: true
                    }, {
                        label: 'Reservas Previstas (%)',
                        data: [70, 75, 72, 80, 88, 95, 90],
                        borderColor: '#2ECC71',
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
                            max: 100,
                            ticks: {
                                callback: function(value) {
                                    return value + '%';
                                }
                            }
                        }
                    }
                }
            });
            
            // Gráfico de Status dos Quartos
            const roomStatusCtx = document.getElementById('roomStatusChart').getContext('2d');
            const roomStatusChart = new Chart(roomStatusCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Disponíveis', 'Ocupados', 'Manutenção', 'Limpeza'],
                    datasets: [{
                        data: [28, 15, 3, 2],
                        backgroundColor: [
                            '#2ECC71',
                            '#E74C3C',
                            '#F39C12',
                            '#3498DB'
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
            nexusFlow.showNotification('Gerando relatório hoteleiro...', 'info');
            setTimeout(() => {
                nexusFlow.showNotification('Relatório exportado com sucesso!', 'success');
            }, 1500);
        }
        
        function refreshDashboard() {
            nexusFlow.showNotification('Atualizando dados do hotel...', 'info');
            setTimeout(() => {
                nexusFlow.showNotification('Dados atualizados!', 'success');
            }, 1000);
        }
        
        // Definir papel como gerente de hotel
        localStorage.setItem('userRole', 'hotel_manager');
        
        // Simulação de atualização em tempo real dos status dos quartos
        setInterval(function() {
            // Simular mudança de status de quartos (apenas visual)
            const cleaningRooms = document.querySelectorAll('.room-status.cleaning');
            if (cleaningRooms.length > 0) {
                const randomRoom = cleaningRooms[Math.floor(Math.random() * cleaningRooms.length)];
                if (randomRoom) {
                    randomRoom.classList.remove('cleaning');
                    randomRoom.classList.add('available');
                    randomRoom.querySelector('.small').textContent = 'Pronto para ocupação';
                }
            }
        }, 45000); // Atualiza a cada 45 segundos
    </script>
</body>
</html>