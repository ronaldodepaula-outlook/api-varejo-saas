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
    <title>Dashboard E-commerce</title>
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
                        <li class="breadcrumb-item active">E-commerce</li>
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
                        <li><h6 class="dropdown-header">Alertas do E-commerce</h6></li>
                        <li><a class="dropdown-item" href="#">15 pedidos pendentes</a></li>
                        <li><a class="dropdown-item" href="#">3 produtos com estoque crítico</a></li>
                        <li><a class="dropdown-item" href="#">Taxa de conversão em alta</a></li>
                        <li><a class="dropdown-item" href="#">Novo cupom criado</a></li>
                        <li><a class="dropdown-item" href="#">Problema no gateway de pagamento</a></li>
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
                        <li><h6 class="dropdown-header user-name">Gerente E-commerce</h6></li>
                        <li><small class="dropdown-header text-muted user-email">ecommerce@empresa.com</small></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="perfil.html"><i class="bi bi-person me-2"></i>Meu Perfil</a></li>
                        <li><a class="dropdown-item" href="#"><i class="bi bi-graph-up me-2"></i>Relatórios Avançados</a></li>
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
                    <h1 class="page-title">Dashboard E-commerce</h1>
                    <p class="page-subtitle">Métricas de vendas online, tráfego e conversão</p>
                </div>
                <div>
                    <button class="btn btn-outline-primary me-2" onclick="exportReport()">
                        <i class="bi bi-file-earmark-spreadsheet me-2"></i>Exportar Dados
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
                        <div class="metric-value">R$ 84.250</div>
                        <div class="metric-label">Receita Online</div>
                        <div class="metric-change positive">
                            <i class="bi bi-arrow-up"></i> +15.3% vs mês anterior
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="metric-card success">
                        <div class="metric-icon success">
                            <i class="bi bi-cart-check"></i>
                        </div>
                        <div class="metric-value">3.8%</div>
                        <div class="metric-label">Taxa de Conversão</div>
                        <div class="metric-change positive">
                            <i class="bi bi-arrow-up"></i> +0.7% vs semana passada
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="metric-card warning">
                        <div class="metric-icon warning">
                            <i class="bi bi-people"></i>
                        </div>
                        <div class="metric-value">24.5K</div>
                        <div class="metric-label">Visitantes Únicos</div>
                        <div class="metric-change positive">
                            <i class="bi bi-arrow-up"></i> +2.1K este mês
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="metric-card danger">
                        <div class="metric-icon danger">
                            <i class="bi bi-cart-x"></i>
                        </div>
                        <div class="metric-value">18.7%</div>
                        <div class="metric-label">Carrinhos Abandonados</div>
                        <div class="metric-change negative">
                            <i class="bi bi-arrow-up"></i> +2.3% vs mês anterior
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Gráficos e Dados -->
            <div class="row mb-4">
                <!-- Gráfico de Vendas e Tráfego -->
                <div class="col-xl-8 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Desempenho de Vendas e Tráfego</h5>
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
                            <canvas id="salesTrafficChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
                
                <!-- Fontes de Tráfego -->
                <div class="col-xl-4 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom">
                            <h5 class="mb-0">Fontes de Tráfego</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="trafficSourcesChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tabelas de Dados -->
            <div class="row">
                <!-- Pedidos Recentes -->
                <div class="col-xl-6 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Pedidos Recentes</h5>
                            <a href="pedidos.html" class="btn btn-sm btn-outline-primary">Ver todos</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-custom mb-0">
                                    <thead>
                                        <tr>
                                            <th>Pedido</th>
                                            <th>Cliente</th>
                                            <th>Valor</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div class="fw-semibold">#EC-7845</div>
                                                <small class="text-muted">Hoje, 14:32</small>
                                            </td>
                                            <td>Maria Silva</td>
                                            <td>R$ 248,90</td>
                                            <td><span class="status-badge status-active">Processando</span></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="fw-semibold">#EC-7844</div>
                                                <small class="text-muted">Hoje, 12:15</small>
                                            </td>
                                            <td>João Santos</td>
                                            <td>R$ 189,50</td>
                                            <td><span class="status-badge status-pending">Pagamento</span></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="fw-semibold">#EC-7843</div>
                                                <small class="text-muted">Ontem, 18:45</small>
                                            </td>
                                            <td>Ana Oliveira</td>
                                            <td>R$ 542,30</td>
                                            <td><span class="status-badge status-active">Enviado</span></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="fw-semibold">#EC-7842</div>
                                                <small class="text-muted">Ontem, 16:20</small>
                                            </td>
                                            <td>Carlos Lima</td>
                                            <td>R$ 127,80</td>
                                            <td><span class="status-badge status-warning">Cancelado</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Produtos Populares -->
                <div class="col-xl-6 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Produtos Mais Visualizados</h5>
                            <a href="produtos.html" class="btn btn-sm btn-outline-primary">Ver catálogo</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-custom mb-0">
                                    <thead>
                                        <tr>
                                            <th>Produto</th>
                                            <th>Visualizações</th>
                                            <th>Conversão</th>
                                            <th>Estoque</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <img src="assets/img/produto-e1.jpg" class="rounded" width="32" height="32" alt="Smartphone">
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold">Smartphone Pro Max</div>
                                                        <small class="text-muted">Eletrônicos</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>2.458</td>
                                            <td>4.2%</td>
                                            <td><span class="status-badge status-active">Em estoque</span></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <img src="assets/img/produto-e2.jpg" class="rounded" width="32" height="32" alt="Fone">
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold">Fone Bluetooth</div>
                                                        <small class="text-muted">Áudio</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>1.874</td>
                                            <td>3.8%</td>
                                            <td><span class="status-badge status-warning">Baixo estoque</span></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <img src="assets/img/produto-e3.jpg" class="rounded" width="32" height="32" alt="Relógio">
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold">Smartwatch Fitness</div>
                                                        <small class="text-muted">Wearables</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>1.542</td>
                                            <td>2.9%</td>
                                            <td><span class="status-badge status-active">Em estoque</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Alertas do E-commerce -->
            <div class="row">
                <div class="col-12">
                    <div class="card-custom">
                        <div class="card-header-custom">
                            <h5 class="mb-0">Alertas e Insights</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="alert alert-warning d-flex align-items-center mb-3">
                                        <i class="bi bi-cart-x me-3"></i>
                                        <div>
                                            <strong>42 carrinhos</strong> abandonados hoje
                                            <div class="mt-1">
                                                <a href="#" class="btn btn-sm btn-warning">Enviar lembrete</a>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="alert alert-info d-flex align-items-center mb-3">
                                        <i class="bi bi-tags me-3"></i>
                                        <div>
                                            <strong>Cupom ativo:</strong> FRETEGRATIS
                                            <div class="mt-1">
                                                <small class="text-muted">Válido até 30/06 - 15% de desconto</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="alert alert-success d-flex align-items-center mb-3">
                                        <i class="bi bi-graph-up-arrow me-3"></i>
                                        <div>
                                            <strong>Taxa de conversão</strong> acima da média
                                            <div class="mt-1">
                                                <small class="text-muted">3.8% vs média do setor de 2.5%</small>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="alert alert-danger d-flex align-items-center mb-0">
                                        <i class="bi bi-exclamation-triangle me-3"></i>
                                        <div>
                                            <strong>3 produtos</strong> com estoque crítico
                                            <div class="mt-1">
                                                <a href="estoque.html" class="btn btn-sm btn-danger">Repor agora</a>
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

        // Gráficos específicos do e-commerce
        document.addEventListener('DOMContentLoaded', function() {
            // Gráfico de Vendas e Tráfego
            const salesTrafficCtx = document.getElementById('salesTrafficChart').getContext('2d');
            const salesTrafficChart = new Chart(salesTrafficCtx, {
                type: 'line',
                data: {
                    labels: ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'],
                    datasets: [{
                        label: 'Vendas (R$)',
                        data: [12500, 14200, 11800, 16800, 19200, 21500, 18500],
                        borderColor: '#3498DB',
                        backgroundColor: 'rgba(52, 152, 219, 0.1)',
                        tension: 0.4,
                        fill: true,
                        yAxisID: 'y'
                    }, {
                        label: 'Visitantes',
                        data: [3200, 3850, 2950, 4200, 5100, 5800, 4500],
                        borderColor: '#2ECC71',
                        backgroundColor: 'rgba(46, 204, 113, 0.1)',
                        tension: 0.4,
                        fill: true,
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
                                text: 'Vendas (R$)'
                            },
                            ticks: {
                                callback: function(value) {
                                    return 'R$ ' + value.toLocaleString();
                                }
                            }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            title: {
                                display: true,
                                text: 'Visitantes'
                            },
                            grid: {
                                drawOnChartArea: false,
                            },
                        }
                    }
                }
            });
            
            // Gráfico de Fontes de Tráfego
            const trafficSourcesCtx = document.getElementById('trafficSourcesChart').getContext('2d');
            const trafficSourcesChart = new Chart(trafficSourcesCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Orgânico', 'Direto', 'Social', 'Email', 'Referência', 'Pago'],
                    datasets: [{
                        data: [35, 22, 18, 12, 8, 5],
                        backgroundColor: [
                            '#3498DB',
                            '#2ECC71',
                            '#F39C12',
                            '#E74C3C',
                            '#9B59B6',
                            '#34495E'
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
            nexusFlow.showNotification('Exportando dados do e-commerce...', 'info');
            setTimeout(() => {
                nexusFlow.showNotification('Relatório exportado com sucesso!', 'success');
            }, 1500);
        }
        
        function refreshDashboard() {
            nexusFlow.showNotification('Atualizando métricas...', 'info');
            setTimeout(() => {
                nexusFlow.showNotification('Dados atualizados!', 'success');
            }, 1000);
        }
        
        // Definir papel como ecommerce
        localStorage.setItem('userRole', 'ecommerce_manager');
    </script>
</body>
</html>