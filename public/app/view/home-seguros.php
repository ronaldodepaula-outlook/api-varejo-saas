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
    <title>Dashboard Seguros - NexusFlow</title>
    
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
            --insurance-color: #2A7F62;
            --claims-color: #8B4513;
            --policy-color: #D4AF37;
            --renewal-color: #6A5ACD;
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
            color: var(--insurance-color);
            margin-right: 1rem;
        }
        
        .breadcrumb-custom {
            margin-bottom: 0;
        }
        
        .breadcrumb-custom .breadcrumb-item.active {
            color: var(--insurance-color);
            font-weight: 600;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--insurance-color);
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
            color: var(--insurance-color);
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
        
        .metric-card.insurance {
            border-left: 4px solid var(--insurance-color);
        }
        
        .metric-card.claims {
            border-left: 4px solid var(--claims-color);
        }
        
        .metric-card.policy {
            border-left: 4px solid var(--policy-color);
        }
        
        .metric-card.renewal {
            border-left: 4px solid var(--renewal-color);
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
        
        .metric-icon.insurance {
            background: rgba(42, 127, 98, 0.1);
            color: var(--insurance-color);
        }
        
        .metric-icon.claims {
            background: rgba(139, 69, 19, 0.1);
            color: var(--claims-color);
        }
        
        .metric-icon.policy {
            background: rgba(212, 175, 55, 0.1);
            color: var(--policy-color);
        }
        
        .metric-icon.renewal {
            background: rgba(106, 90, 205, 0.1);
            color: var(--renewal-color);
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
        
        .status-expired {
            background: rgba(108, 117, 125, 0.1);
            color: #6c757d;
        }
        
        .status-claim {
            background: rgba(196, 69, 54, 0.1);
            color: var(--danger-color);
        }
        
        .insurance-badge {
            padding: 0.35rem 0.65rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .insurance-auto {
            background: rgba(42, 127, 98, 0.1);
            color: var(--insurance-color);
        }
        
        .insurance-life {
            background: rgba(139, 69, 19, 0.1);
            color: var(--claims-color);
        }
        
        .insurance-home {
            background: rgba(212, 175, 55, 0.1);
            color: var(--policy-color);
        }
        
        .insurance-health {
            background: rgba(106, 90, 205, 0.1);
            color: var(--renewal-color);
        }
        
        .notification-toast {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1050;
            min-width: 300px;
        }
        
        .renewal-indicator {
            height: 6px;
            border-radius: 3px;
            background: #e9ecef;
            margin-top: 5px;
            overflow: hidden;
        }
        
        .renewal-progress {
            height: 100%;
            border-radius: 3px;
        }
        
        .renewal-urgent {
            background: var(--danger-color);
        }
        
        .renewal-warning {
            background: var(--warning-color);
        }
        
        .renewal-normal {
            background: var(--success-color);
        }
        
        .claim-priority-high {
            border-left: 4px solid var(--danger-color);
        }
        
        .claim-priority-medium {
            border-left: 4px solid var(--warning-color);
        }
        
        .claim-priority-low {
            border-left: 4px solid var(--success-color);
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
                        <li class="breadcrumb-item active">Seguros</li>
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
                        <li><h6 class="dropdown-header">Alertas de Seguros</h6></li>
                        <li><a class="dropdown-item" href="#">15 apólices vencem em 7 dias</a></li>
                        <li><a class="dropdown-item" href="#">3 sinistros requerem atenção</a></li>
                        <li><a class="dropdown-item" href="#">Pagamento em atraso - Cliente XPTO</a></li>
                        <li><a class="dropdown-item" href="#">Nova proposta recebida</a></li>
                        <li><a class="dropdown-item" href="#">Renovação automática falhou</a></li>
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
                        <li><h6 class="dropdown-header user-name">Seguros NexusFlow</h6></li>
                        <li><small class="dropdown-header text-muted user-email">seguros@nexusflow.com</small></li>
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
                    <h1 class="page-title">Dashboard de Seguros</h1>
                    <p class="page-subtitle">Gestão de apólices, sinistros e renovação de clientes</p>
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
                    <div class="metric-card insurance">
                        <div class="metric-icon insurance">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <div class="metric-value">2.847</div>
                        <div class="metric-label">Apólices Ativas</div>
                        <div class="metric-change positive">
                            <i class="bi bi-arrow-up"></i> +45 este mês
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="metric-card claims">
                        <div class="metric-icon claims">
                            <i class="bi bi-clipboard-x"></i>
                        </div>
                        <div class="metric-value">127</div>
                        <div class="metric-label">Sinistros em Aberto</div>
                        <div class="metric-change negative">
                            <i class="bi bi-arrow-up"></i> +12 esta semana
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="metric-card policy">
                        <div class="metric-icon policy">
                            <i class="bi bi-cash-coin"></i>
                        </div>
                        <div class="metric-value">R$ 3.2M</div>
                        <div class="metric-label">Prêmios Emitidos</div>
                        <div class="metric-change positive">
                            <i class="bi bi-arrow-up"></i> +8.5% vs mês anterior
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="metric-card renewal">
                        <div class="metric-icon renewal">
                            <i class="bi bi-calendar-event"></i>
                        </div>
                        <div class="metric-value">89</div>
                        <div class="metric-label">Renovações Pendentes</div>
                        <div class="metric-change negative">
                            <i class="bi bi-arrow-up"></i> Próximos 30 dias
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Gráficos e Dados -->
            <div class="row mb-4">
                <!-- Gráfico de Vendas e Sinistros -->
                <div class="col-xl-8 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Vendas vs Sinistros - Últimos 6 Meses</h5>
                            <div class="btn-group btn-group-sm" role="group">
                                <input type="radio" class="btn-check" name="period" id="period7" checked>
                                <label class="btn btn-outline-primary" for="period7">Automóvel</label>
                                
                                <input type="radio" class="btn-check" name="period" id="period30">
                                <label class="btn btn-outline-primary" for="period30">Vida</label>
                                
                                <input type="radio" class="btn-check" name="period" id="period90">
                                <label class="btn btn-outline-primary" for="period90">Residencial</label>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="salesClaimsChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
                
                <!-- Distribuição por Tipo de Seguro -->
                <div class="col-xl-4 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom">
                            <h5 class="mb-0">Distribuição por Tipo de Seguro</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="insuranceTypesChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tabelas de Dados -->
            <div class="row">
                <!-- Apólices Recentes -->
                <div class="col-xl-6 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Apólices Recentes</h5>
                            <a href="apolices.html" class="btn btn-sm btn-outline-primary">Ver todas</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-custom mb-0">
                                    <thead>
                                        <tr>
                                            <th>Cliente</th>
                                            <th>Tipo</th>
                                            <th>Status</th>
                                            <th>Vencimento</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                                            JS
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold">João Silva</div>
                                                        <small class="text-muted">CPF: 123.456.789-00</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="insurance-badge insurance-auto">Automóvel</span></td>
                                            <td><span class="status-badge status-active">Ativa</span></td>
                                            <td>
                                                <div>15/11/2023</div>
                                                <div class="renewal-indicator">
                                                    <div class="renewal-progress renewal-normal" style="width: 25%"></div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                                            MA
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold">Maria Andrade</div>
                                                        <small class="text-muted">CPF: 987.654.321-00</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="insurance-badge insurance-life">Vida</span></td>
                                            <td><span class="status-badge status-active">Ativa</span></td>
                                            <td>
                                                <div>22/10/2023</div>
                                                <div class="renewal-indicator">
                                                    <div class="renewal-progress renewal-warning" style="width: 75%"></div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                                            PC
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold">Pedro Costa</div>
                                                        <small class="text-muted">CPF: 456.789.123-00</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="insurance-badge insurance-home">Residencial</span></td>
                                            <td><span class="status-badge status-pending">Pendente</span></td>
                                            <td>
                                                <div>05/10/2023</div>
                                                <div class="renewal-indicator">
                                                    <div class="renewal-progress renewal-urgent" style="width: 95%"></div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Sinistros Recentes -->
                <div class="col-xl-6 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Sinistros Recentes</h5>
                            <a href="sinistros.html" class="btn btn-sm btn-outline-primary">Ver todos</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-custom mb-0">
                                    <thead>
                                        <tr>
                                            <th>Cliente</th>
                                            <th>Tipo</th>
                                            <th>Status</th>
                                            <th>Data</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="claim-priority-high">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                                            AC
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold">André Carvalho</div>
                                                        <small class="text-muted">Colisão traseira</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="insurance-badge insurance-auto">Automóvel</span></td>
                                            <td><span class="status-badge status-claim">Avaliação</span></td>
                                            <td>Hoje</td>
                                        </tr>
                                        <tr class="claim-priority-medium">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                                            FS
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold">Fernanda Santos</div>
                                                        <small class="text-muted">Roubo de pertences</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="insurance-badge insurance-home">Residencial</span></td>
                                            <td><span class="status-badge status-pending">Documentação</span></td>
                                            <td>Ontem</td>
                                        </tr>
                                        <tr class="claim-priority-low">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                                            RM
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold">Ricardo Mendes</div>
                                                        <small class="text-muted">Quebra de vidro</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="insurance-badge insurance-auto">Automóvel</span></td>
                                            <td><span class="status-badge status-active">Processando</span></td>
                                            <td>2 dias</td>
                                        </tr>
                                    </tbody>
                                </table>
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
                                    <strong>15 apólices</strong> vencem nos próximos 7 dias
                                    <div class="mt-1">
                                        <a href="renovacoes.html" class="btn btn-sm btn-warning">Ver detalhes</a>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="alert alert-info d-flex align-items-center mb-3">
                                <i class="bi bi-clipboard-x me-3"></i>
                                <div>
                                    <strong>3 sinistros</strong> requerem atenção imediata
                                    <div class="mt-1">
                                        <a href="sinistros.html" class="btn btn-sm btn-info">Avaliar agora</a>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="alert alert-success d-flex align-items-center mb-3">
                                <i class="bi bi-cash-coin me-3"></i>
                                <div>
                                    <strong>Taxa de renovação</strong> de 92% este mês
                                    <div class="mt-1">
                                        <small class="text-muted">Meta superada em 7%</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="alert alert-danger d-flex align-items-center mb-0">
                                <i class="bi bi-shield-exclamation me-3"></i>
                                <div>
                                    <strong>Pagamento em atraso</strong> detectado para 5 clientes
                                    <div class="mt-1">
                                        <a href="#" class="btn btn-sm btn-danger">Enviar notificações</a>
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
            // Gráfico de Vendas vs Sinistros
            const salesClaimsCtx = document.getElementById('salesClaimsChart').getContext('2d');
            const salesClaimsChart = new Chart(salesClaimsCtx, {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun'],
                    datasets: [{
                        label: 'Novas Apólices',
                        data: [245, 289, 312, 278, 345, 398],
                        backgroundColor: 'rgba(42, 127, 98, 0.7)',
                        borderColor: 'rgba(42, 127, 98, 1)',
                        borderWidth: 1
                    }, {
                        label: 'Sinistros Reportados',
                        data: [45, 52, 38, 67, 42, 55],
                        backgroundColor: 'rgba(139, 69, 19, 0.7)',
                        borderColor: 'rgba(139, 69, 19, 1)',
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
            
            // Gráfico de Distribuição por Tipo de Seguro
            const insuranceTypesCtx = document.getElementById('insuranceTypesChart').getContext('2d');
            const insuranceTypesChart = new Chart(insuranceTypesCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Automóvel', 'Residencial', 'Vida', 'Saúde', 'Empresarial', 'Outros'],
                    datasets: [{
                        data: [45, 22, 15, 10, 5, 3],
                        backgroundColor: [
                            '#2A7F62',
                            '#D4AF37',
                            '#8B4513',
                            '#6A5ACD',
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
            nexusFlow.showNotification('Exportando relatório de seguros...', 'info');
            setTimeout(() => {
                nexusFlow.showNotification('Relatório exportado com sucesso!', 'success');
            }, 2000);
        }
        
        function refreshDashboard() {
            nexusFlow.showNotification('Atualizando dashboard de seguros...', 'info');
            setTimeout(() => {
                nexusFlow.showNotification('Dashboard atualizado!', 'success');
                // Em uma aplicação real, aqui seria feita uma requisição para atualizar os dados
                // Por simplicidade, apenas recarregamos a página
                location.reload();
            }, 1000);
        }
        
        // Definir papel como seguros
        localStorage.setItem('userRole', 'seguros');
    </script>
</body>
</html>