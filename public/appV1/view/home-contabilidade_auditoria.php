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
    <title>Dashboard Contabilidade - NexusFlow</title>
    
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
            --accounting-color: #2A7F62;
            --audit-color: #8B4513;
            --tax-color: #D4AF37;
            --consulting-color: #6A5ACD;
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
            color: var(--accounting-color);
            margin-right: 1rem;
        }
        
        .breadcrumb-custom {
            margin-bottom: 0;
        }
        
        .breadcrumb-custom .breadcrumb-item.active {
            color: var(--accounting-color);
            font-weight: 600;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--accounting-color);
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
            color: var(--accounting-color);
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
        
        .metric-card.accounting {
            border-left: 4px solid var(--accounting-color);
        }
        
        .metric-card.audit {
            border-left: 4px solid var(--audit-color);
        }
        
        .metric-card.tax {
            border-left: 4px solid var(--tax-color);
        }
        
        .metric-card.consulting {
            border-left: 4px solid var(--consulting-color);
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
        
        .metric-icon.accounting {
            background: rgba(42, 127, 98, 0.1);
            color: var(--accounting-color);
        }
        
        .metric-icon.audit {
            background: rgba(139, 69, 19, 0.1);
            color: var(--audit-color);
        }
        
        .metric-icon.tax {
            background: rgba(212, 175, 55, 0.1);
            color: var(--tax-color);
        }
        
        .metric-icon.consulting {
            background: rgba(106, 90, 205, 0.1);
            color: var(--consulting-color);
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
        
        .status-completed {
            background: rgba(27, 153, 139, 0.1);
            color: var(--success-color);
        }
        
        .status-in-progress {
            background: rgba(244, 96, 54, 0.1);
            color: var(--warning-color);
        }
        
        .status-pending {
            background: rgba(108, 117, 125, 0.1);
            color: #6c757d;
        }
        
        .status-urgent {
            background: rgba(196, 69, 54, 0.1);
            color: var(--danger-color);
        }
        
        .service-badge {
            padding: 0.35rem 0.65rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .service-accounting {
            background: rgba(42, 127, 98, 0.1);
            color: var(--accounting-color);
        }
        
        .service-audit {
            background: rgba(139, 69, 19, 0.1);
            color: var(--audit-color);
        }
        
        .service-tax {
            background: rgba(212, 175, 55, 0.1);
            color: var(--tax-color);
        }
        
        .service-consulting {
            background: rgba(106, 90, 205, 0.1);
            color: var(--consulting-color);
        }
        
        .notification-toast {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1050;
            min-width: 300px;
        }
        
        .deadline-indicator {
            height: 6px;
            border-radius: 3px;
            background: #e9ecef;
            margin-top: 5px;
            overflow: hidden;
        }
        
        .deadline-progress {
            height: 100%;
            border-radius: 3px;
        }
        
        .deadline-urgent {
            background: var(--danger-color);
        }
        
        .deadline-warning {
            background: var(--warning-color);
        }
        
        .deadline-normal {
            background: var(--success-color);
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
                        <li class="breadcrumb-item active">Contabilidade & Auditoria</li>
                    </ol>
                </nav>
            </div>
            
            <div class="header-right">
                <!-- Notificações -->
                <div class="dropdown">
                    <button class="btn btn-link text-dark position-relative" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-bell" style="font-size: 1.25rem;"></i>
                        <span class="badge bg-danger position-absolute translate-middle rounded-pill" style="font-size: 0.6rem; top: 8px; right: 8px;">4</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><h6 class="dropdown-header">Alertas Contábeis</h6></li>
                        <li><a class="dropdown-item" href="#">SPED Fiscal vence em 5 dias</a></li>
                        <li><a class="dropdown-item" href="#">Auditoria pendente para 3 clientes</a></li>
                        <li><a class="dropdown-item" href="#">Declaração de IR atrasada</a></li>
                        <li><a class="dropdown-item" href="#">Alteração legislativa detectada</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-center" href="#">Ver todas</a></li>
                    </ul>
                </div>
                
                <!-- Dropdown do Usuário -->
                <div class="dropdown user-dropdown">
                    <div class="user-avatar" data-bs-toggle="dropdown" aria-expanded="false">
                        C
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><h6 class="dropdown-header user-name">Contabilidade NexusFlow</h6></li>
                        <li><small class="dropdown-header text-muted user-email">contabilidade@nexusflow.com</small></li>
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
                    <h1 class="page-title">Dashboard Contábil</h1>
                    <p class="page-subtitle">Gestão de clientes, obrigações fiscais e processos de auditoria</p>
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
                    <div class="metric-card accounting">
                        <div class="metric-icon accounting">
                            <i class="bi bi-building"></i>
                        </div>
                        <div class="metric-value">147</div>
                        <div class="metric-label">Clientes Ativos</div>
                        <div class="metric-change positive">
                            <i class="bi bi-arrow-up"></i> +8 este mês
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="metric-card audit">
                        <div class="metric-icon audit">
                            <i class="bi bi-clipboard-check"></i>
                        </div>
                        <div class="metric-value">23</div>
                        <div class="metric-label">Auditorias em Andamento</div>
                        <div class="metric-change negative">
                            <i class="bi bi-arrow-up"></i> +3 esta semana
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="metric-card tax">
                        <div class="metric-icon tax">
                            <i class="bi bi-calendar-check"></i>
                        </div>
                        <div class="metric-value">89%</div>
                        <div class="metric-label">Obrigações em Dia</div>
                        <div class="metric-change positive">
                            <i class="bi bi-arrow-up"></i> +5% vs mês anterior
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="metric-card consulting">
                        <div class="metric-icon consulting">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>
                        <div class="metric-value">R$ 245K</div>
                        <div class="metric-label">Faturamento Mensal</div>
                        <div class="metric-change positive">
                            <i class="bi bi-arrow-up"></i> +12.3% vs mês anterior
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Gráficos e Dados -->
            <div class="row mb-4">
                <!-- Gráfico de Obrigações Fiscais -->
                <div class="col-xl-8 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Cumprimento de Obrigações Fiscais - Últimos 6 Meses</h5>
                            <div class="btn-group btn-group-sm" role="group">
                                <input type="radio" class="btn-check" name="period" id="period7" checked>
                                <label class="btn btn-outline-primary" for="period7">Mensais</label>
                                
                                <input type="radio" class="btn-check" name="period" id="period30">
                                <label class="btn btn-outline-primary" for="period30">Trimestrais</label>
                                
                                <input type="radio" class="btn-check" name="period" id="period90">
                                <label class="btn btn-outline-primary" for="period90">Anuais</label>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="taxComplianceChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
                
                <!-- Distribuição de Serviços -->
                <div class="col-xl-4 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom">
                            <h5 class="mb-0">Distribuição de Serviços</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="servicesChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tabelas de Dados -->
            <div class="row">
                <!-- Clientes Recentes -->
                <div class="col-xl-6 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Clientes Recentes</h5>
                            <a href="clientes.html" class="btn btn-sm btn-outline-primary">Ver todos</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-custom mb-0">
                                    <thead>
                                        <tr>
                                            <th>Cliente</th>
                                            <th>Serviço</th>
                                            <th>Status</th>
                                            <th>Próxima Obrigação</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                                            TI
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold">Tech Inovação Ltda</div>
                                                        <small class="text-muted">CNPJ: 12.345.678/0001-90</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="service-badge service-accounting">Contábil</span></td>
                                            <td><span class="status-badge status-completed">Em dia</span></td>
                                            <td>
                                                <div>10/10/2023</div>
                                                <div class="deadline-indicator">
                                                    <div class="deadline-progress deadline-normal" style="width: 25%"></div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                                            CV
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold">Construtora Vanguarda</div>
                                                        <small class="text-muted">CNPJ: 98.765.432/0001-10</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="service-badge service-audit">Auditoria</span></td>
                                            <td><span class="status-badge status-in-progress">Andamento</span></td>
                                            <td>
                                                <div>15/10/2023</div>
                                                <div class="deadline-indicator">
                                                    <div class="deadline-progress deadline-warning" style="width: 60%"></div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                                            MF
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold">Moda & Fashion</div>
                                                        <small class="text-muted">CNPJ: 45.678.123/0001-55</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="service-badge service-tax">Fiscal</span></td>
                                            <td><span class="status-badge status-urgent">Atrasado</span></td>
                                            <td>
                                                <div>05/10/2023</div>
                                                <div class="deadline-indicator">
                                                    <div class="deadline-progress deadline-urgent" style="width: 95%"></div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Alertas Contábeis -->
                <div class="col-xl-6 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom">
                            <h5 class="mb-0">Alertas e Prazos</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-warning d-flex align-items-center mb-3">
                                <i class="bi bi-exclamation-triangle me-3"></i>
                                <div>
                                    <strong>SPED Fiscal</strong> vence em 5 dias para 12 clientes
                                    <div class="mt-1">
                                        <a href="obrigacoes.html" class="btn btn-sm btn-warning">Ver detalhes</a>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="alert alert-info d-flex align-items-center mb-3">
                                <i class="bi bi-clipboard-check me-3"></i>
                                <div>
                                    <strong>3 auditorias</strong> precisam ser iniciadas esta semana
                                    <div class="mt-1">
                                        <a href="auditorias.html" class="btn btn-sm btn-info">Ver agenda</a>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="alert alert-success d-flex align-items-center mb-3">
                                <i class="bi bi-check-circle me-3"></i>
                                <div>
                                    <strong>Declaração de IR</strong> enviada para 98% dos clientes
                                    <div class="mt-1">
                                        <small class="text-muted">Restam apenas 3 declarações pendentes</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="alert alert-danger d-flex align-items-center mb-0">
                                <i class="bi bi-shield-exclamation me-3"></i>
                                <div>
                                    <strong>Alteração legislativa</strong> detectada - Impacta 45 clientes
                                    <div class="mt-1">
                                        <a href="#" class="btn btn-sm btn-danger">Analisar impacto</a>
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
            // Gráfico de Cumprimento de Obrigações Fiscais
            const taxComplianceCtx = document.getElementById('taxComplianceChart').getContext('2d');
            const taxComplianceChart = new Chart(taxComplianceCtx, {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun'],
                    datasets: [{
                        label: 'Obrigações Entregues',
                        data: [95, 92, 98, 96, 94, 97],
                        backgroundColor: 'rgba(42, 127, 98, 0.7)',
                        borderColor: 'rgba(42, 127, 98, 1)',
                        borderWidth: 1
                    }, {
                        label: 'Obrigações Pendentes',
                        data: [5, 8, 2, 4, 6, 3],
                        backgroundColor: 'rgba(244, 96, 54, 0.7)',
                        borderColor: 'rgba(244, 96, 54, 1)',
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
            
            // Gráfico de Distribuição de Serviços
            const servicesCtx = document.getElementById('servicesChart').getContext('2d');
            const servicesChart = new Chart(servicesCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Contábil', 'Fiscal', 'Auditoria', 'Consultoria', 'DP', 'Outros'],
                    datasets: [{
                        data: [35, 25, 15, 12, 8, 5],
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
            nexusFlow.showNotification('Exportando relatório contábil...', 'info');
            setTimeout(() => {
                nexusFlow.showNotification('Relatório exportado com sucesso!', 'success');
            }, 2000);
        }
        
        function refreshDashboard() {
            nexusFlow.showNotification('Atualizando dashboard contábil...', 'info');
            setTimeout(() => {
                nexusFlow.showNotification('Dashboard atualizado!', 'success');
                // Em uma aplicação real, aqui seria feita uma requisição para atualizar os dados
                // Por simplicidade, apenas recarregamos a página
                location.reload();
            }, 1000);
        }
        
        // Definir papel como contabilidade
        localStorage.setItem('userRole', 'contabilidade');
    </script>
</body>
</html>