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
    <title>Dashboard Alimentação</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #E67E22;
            --secondary-color: #27AE60;
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
        
        .metric-icon.primary { background: rgba(230, 126, 34, 0.1); color: var(--primary-color); }
        .metric-icon.success { background: rgba(39, 174, 96, 0.1); color: var(--secondary-color); }
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
        
        .status-active { background: rgba(39, 174, 96, 0.1); color: var(--secondary-color); }
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
        
        .kitchen-item {
            padding: 10px 15px;
            border-left: 4px solid transparent;
            margin-bottom: 8px;
            border-radius: 5px;
            background: #f8f9fa;
        }
        
        .kitchen-item.urgent {
            border-left-color: var(--danger-color);
            background: rgba(231, 76, 60, 0.05);
        }
        
        .kitchen-item.preparing {
            border-left-color: var(--warning-color);
            background: rgba(243, 156, 18, 0.05);
        }
        
        .kitchen-item.ready {
            border-left-color: var(--secondary-color);
            background: rgba(39, 174, 96, 0.05);
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
                        <li class="breadcrumb-item active">Alimentação</li>
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
                        <li><h6 class="dropdown-header">Alertas da Cozinha</h6></li>
                        <li><a class="dropdown-item" href="#">8 pedidos pendentes</a></li>
                        <li><a class="dropdown-item" href="#">3 ingredientes em falta</a></li>
                        <li><a class="dropdown-item" href="#">Mesa 12 solicitando conta</a></li>
                        <li><a class="dropdown-item" href="#">Avaliação 5 estrelas recebida</a></li>
                        <li><a class="dropdown-item" href="#">Entrega atrasada - Pedido #45</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-center" href="#">Ver todos</a></li>
                    </ul>
                </div>
                
                <!-- Dropdown do Usuário -->
                <div class="dropdown user-dropdown">
                    <div class="user-avatar" data-bs-toggle="dropdown" aria-expanded="false">
                        A
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><h6 class="dropdown-header user-name">Gerente Alimentação</h6></li>
                        <li><small class="dropdown-header text-muted user-email">restaurante@empresa.com</small></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="perfil.html"><i class="bi bi-person me-2"></i>Meu Perfil</a></li>
                        <li><a class="dropdown-item" href="#"><i class="bi bi-clipboard-data me-2"></i>Relatórios</a></li>
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
                    <h1 class="page-title">Dashboard Alimentação</h1>
                    <p class="page-subtitle">Controle de pedidos, estoque e operação do restaurante</p>
                </div>
                <div>
                    <button class="btn btn-outline-primary me-2" onclick="exportReport()">
                        <i class="bi bi-printer me-2"></i>Relatório Diário
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
                            <i class="bi bi-currency-dollar"></i>
                        </div>
                        <div class="metric-value">R$ 8.425</div>
                        <div class="metric-label">Faturamento Hoje</div>
                        <div class="metric-change positive">
                            <i class="bi bi-arrow-up"></i> +12.5% vs ontem
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="metric-card success">
                        <div class="metric-icon success">
                            <i class="bi bi-cup-straw"></i>
                        </div>
                        <div class="metric-value">148</div>
                        <div class="metric-label">Pedidos do Dia</div>
                        <div class="metric-change positive">
                            <i class="bi bi-arrow-up"></i> +18 pedidos vs ontem
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="metric-card warning">
                        <div class="metric-icon warning">
                            <i class="bi bi-clock"></i>
                        </div>
                        <div class="metric-value">14min</div>
                        <div class="metric-label">Tempo Médio Espera</div>
                        <div class="metric-change negative">
                            <i class="bi bi-arrow-up"></i> +2min vs semana passada
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="metric-card danger">
                        <div class="metric-icon danger">
                            <i class="bi bi-exclamation-triangle"></i>
                        </div>
                        <div class="metric-value">7</div>
                        <div class="metric-label">Ingredientes em Falta</div>
                        <div class="metric-change negative">
                            <i class="bi bi-arrow-up"></i> +2 vs ontem
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Gráficos e Dados -->
            <div class="row mb-4">
                <!-- Gráfico de Movimentação -->
                <div class="col-xl-8 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Movimentação do Restaurante</h5>
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
                            <canvas id="restaurantChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
                
                <!-- Pedidos na Cozinha -->
                <div class="col-xl-4 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Controle da Cozinha</h5>
                            <span class="badge bg-primary">8 pedidos</span>
                        </div>
                        <div class="card-body">
                            <div class="kitchen-item urgent">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>#P-1245</strong>
                                        <div class="small">Mesa 08 - 12min</div>
                                        <div class="small text-muted">2x Pizza Calabresa</div>
                                    </div>
                                    <span class="badge bg-danger">Urgente</span>
                                </div>
                            </div>
                            
                            <div class="kitchen-item preparing">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>#P-1244</strong>
                                        <div class="small">Delivery - 18min</div>
                                        <div class="small text-muted">1x Lasanha, 1x Refri</div>
                                    </div>
                                    <span class="badge bg-warning">Preparando</span>
                                </div>
                            </div>
                            
                            <div class="kitchen-item preparing">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>#P-1243</strong>
                                        <div class="small">Mesa 15 - 10min</div>
                                        <div class="small text-muted">3x Hamburguer Artesanal</div>
                                    </div>
                                    <span class="badge bg-warning">Preparando</span>
                                </div>
                            </div>
                            
                            <div class="kitchen-item ready">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>#P-1242</strong>
                                        <div class="small">Balcão - 5min</div>
                                        <div class="small text-muted">2x Café Expresso</div>
                                    </div>
                                    <span class="badge bg-success">Pronto</span>
                                </div>
                            </div>
                            
                            <button class="btn btn-outline-primary w-100 mt-3">
                                <i class="bi bi-plus-circle me-2"></i>Novo Pedido
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tabelas de Dados -->
            <div class="row">
                <!-- Pratos Mais Vendidos -->
                <div class="col-xl-6 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Pratos Mais Vendidos</h5>
                            <a href="cardapio.html" class="btn btn-sm btn-outline-primary">Ver Cardápio</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-custom mb-0">
                                    <thead>
                                        <tr>
                                            <th>Prato</th>
                                            <th>Categoria</th>
                                            <th>Vendas</th>
                                            <th>Lucro</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <img src="assets/img/prato1.jpg" class="rounded" width="32" height="32" alt="Pizza">
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold">Pizza Calabresa</div>
                                                        <small class="text-muted">R$ 42,90</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="badge bg-info">Pizzas</span></td>
                                            <td>48 unidades</td>
                                            <td><span class="status-badge status-active">R$ 1.258</span></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <img src="assets/img/prato2.jpg" class="rounded" width="32" height="32" alt="Hamburguer">
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold">Hamburguer Artesanal</div>
                                                        <small class="text-muted">R$ 28,50</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="badge bg-success">Lanches</span></td>
                                            <td>35 unidades</td>
                                            <td><span class="status-badge status-active">R$ 798</span></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <img src="assets/img/prato3.jpg" class="rounded" width="32" height="32" alt="Lasanha">
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold">Lasanha à Bolonhesa</div>
                                                        <small class="text-muted">R$ 36,80</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="badge bg-warning">Massas</span></td>
                                            <td>27 unidades</td>
                                            <td><span class="status-badge status-active">R$ 685</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Mesas e Reservas -->
                <div class="col-xl-6 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Status das Mesas</h5>
                            <a href="reservas.html" class="btn btn-sm btn-outline-primary">Ver Reservas</a>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-4">
                                    <div class="mb-3">
                                        <div class="metric-value text-primary">8</div>
                                        <div class="metric-label">Ocupadas</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="mb-3">
                                        <div class="metric-value text-success">12</div>
                                        <div class="metric-label">Livres</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="mb-3">
                                        <div class="metric-value text-warning">5</div>
                                        <div class="metric-label">Reservadas</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-3">
                                <h6 class="mb-3">Próximas Reservas</h6>
                                <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
                                    <div>
                                        <strong>Mesa 04</strong>
                                        <div class="small text-muted">Silva Family - 4 pessoas</div>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-semibold">19:30</div>
                                        <div class="small text-muted">em 45min</div>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
                                    <div>
                                        <strong>Mesa 08</strong>
                                        <div class="small text-muted">Aniversário Maria - 8 pessoas</div>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-semibold">20:15</div>
                                        <div class="small text-muted">em 1h30min</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Alertas do Restaurante -->
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
                                        <i class="bi bi-basket me-3"></i>
                                        <div>
                                            <strong>7 ingredientes</strong> com estoque crítico
                                            <div class="mt-1">
                                                <a href="estoque.html" class="btn btn-sm btn-warning">Repor agora</a>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="alert alert-info d-flex align-items-center mb-3">
                                        <i class="bi bi-star me-3"></i>
                                        <div>
                                            <strong>Avaliação 4.8</strong> no Google Reviews
                                            <div class="mt-1">
                                                <small class="text-muted">15 novas avaliações esta semana</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="alert alert-success d-flex align-items-center mb-3">
                                        <i class="bi bi-people me-3"></i>
                                        <div>
                                            <strong>Pico de clientes</strong> às 19:30
                                            <div class="mt-1">
                                                <small class="text-muted">Prepare equipe para horário de pico</small>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="alert alert-danger d-flex align-items-center mb-0">
                                        <i class="bi bi-clock me-3"></i>
                                        <div>
                                            <strong>3 entregas</strong> com atraso
                                            <div class="mt-1">
                                                <a href="entregas.html" class="btn btn-sm btn-danger">Ver detalhes</a>
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

        // Gráficos específicos do segmento de alimentação
        document.addEventListener('DOMContentLoaded', function() {
            // Gráfico de Movimentação do Restaurante
            const restaurantCtx = document.getElementById('restaurantChart').getContext('2d');
            const restaurantChart = new Chart(restaurantCtx, {
                type: 'bar',
                data: {
                    labels: ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'],
                    datasets: [{
                        label: 'Pedidos Presencial',
                        data: [85, 92, 78, 105, 128, 145, 120],
                        backgroundColor: '#E67E22',
                        borderColor: '#D35400',
                        borderWidth: 1
                    }, {
                        label: 'Pedidos Delivery',
                        data: [45, 52, 48, 65, 82, 95, 88],
                        backgroundColor: '#27AE60',
                        borderColor: '#229954',
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
                                text: 'Número de Pedidos'
                            }
                        }
                    }
                }
            });
        });
        
        function exportReport() {
            nexusFlow.showNotification('Gerando relatório do restaurante...', 'info');
            setTimeout(() => {
                nexusFlow.showNotification('Relatório exportado com sucesso!', 'success');
            }, 1500);
        }
        
        function refreshDashboard() {
            nexusFlow.showNotification('Atualizando dados do restaurante...', 'info');
            setTimeout(() => {
                nexusFlow.showNotification('Dados atualizados!', 'success');
            }, 1000);
        }
        
        // Definir papel como gerente de alimentação
        localStorage.setItem('userRole', 'food_manager');
        
        // Simulação de atualização em tempo real dos pedidos
        setInterval(function() {
            // Atualizar contadores de pedidos (simulação)
            const kitchenItems = document.querySelectorAll('.kitchen-item');
            if (kitchenItems.length > 0) {
                const randomIndex = Math.floor(Math.random() * kitchenItems.length);
                const item = kitchenItems[randomIndex];
                
                // Simular mudança de status
                if (item.classList.contains('preparing')) {
                    item.classList.remove('preparing');
                    item.classList.add('ready');
                    const badge = item.querySelector('.badge');
                    if (badge) {
                        badge.className = 'badge bg-success';
                        badge.textContent = 'Pronto';
                    }
                }
            }
        }, 30000); // Atualiza a cada 30 segundos
    </script>
</body>
</html>