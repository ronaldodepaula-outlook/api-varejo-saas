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
    <title>Dashboard Construção Civil</title>
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
        
        .project-card {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 15px;
            transition: transform 0.2s;
        }
        
        .project-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .project-image {
            height: 120px;
            background: linear-gradient(135deg, #E67E22 0%, #D35400 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
        }
        
        .project-info {
            padding: 15px;
        }
        
        .project-title {
            font-size: 1rem;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .project-meta {
            font-size: 0.85rem;
            color: #6c757d;
            margin-bottom: 10px;
        }
        
        .project-progress {
            margin-top: 10px;
        }
        
        .task-item {
            padding: 10px;
            border-left: 4px solid transparent;
            margin-bottom: 8px;
            border-radius: 5px;
            background: #f8f9fa;
        }
        
        .task-item.ongoing { border-left-color: var(--primary-color); }
        .task-item.delayed { border-left-color: var(--danger-color); }
        .task-item.completed { border-left-color: var(--secondary-color); }
        
        .supply-status {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 8px;
            background: #f8f9fa;
        }
        
        .supply-status.good { border-left: 4px solid var(--secondary-color); }
        .supply-status.warning { border-left: 4px solid var(--warning-color); }
        .supply-status.critical { border-left: 4px solid var(--danger-color); }
        
        .progress-thin {
            height: 8px;
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
                        <li class="breadcrumb-item active">Construção Civil</li>
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
                        <li><h6 class="dropdown-header">Alertas da Obra</h6></li>
                        <li><a class="dropdown-item" href="#">Atraso na entrega de concreto</a></li>
                        <li><a class="dropdown-item" href="#">Inspeção de segurança pendente</a></li>
                        <li><a class="dropdown-item" href="#">Estoque de ferragens crítico</a></li>
                        <li><a class="dropdown-item" href="#">Clima adverso previsto</a></li>
                        <li><a class="dropdown-item" href="#">Novo pedido de alteração</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-center" href="#">Ver todos</a></li>
                    </ul>
                </div>
                
                <!-- Dropdown do Usuário -->
                <div class="dropdown user-dropdown">
                    <div class="user-avatar" data-bs-toggle="dropdown" aria-expanded="false">
                        C
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><h6 class="dropdown-header user-name">Engenheiro Responsável</h6></li>
                        <li><small class="dropdown-header text-muted user-email">construcao@empresa.com</small></li>
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
                    <h1 class="page-title">Dashboard Construção Civil</h1>
                    <p class="page-subtitle">Gestão de obras, cronogramas e recursos</p>
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
                            <i class="bi bi-building"></i>
                        </div>
                        <div class="metric-value">78%</div>
                        <div class="metric-label">Progresso Geral</div>
                        <div class="metric-change positive">
                            <i class="bi bi-arrow-up"></i> +5% este mês
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="metric-card success">
                        <div class="metric-icon success">
                            <i class="bi bi-currency-dollar"></i>
                        </div>
                        <div class="metric-value">R$ 4.2M</div>
                        <div class="metric-label">Orçamento Executado</div>
                        <div class="metric-change positive">
                            <i class="bi bi-arrow-up"></i> Dentro do previsto
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="metric-card warning">
                        <div class="metric-icon warning">
                            <i class="bi bi-calendar-x"></i>
                        </div>
                        <div class="metric-value">12</div>
                        <div class="metric-label">Dias de Atraso</div>
                        <div class="metric-change negative">
                            <i class="bi bi-arrow-up"></i> +2 dias esta semana
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="metric-card danger">
                        <div class="metric-icon danger">
                            <i class="bi bi-exclamation-triangle"></i>
                        </div>
                        <div class="metric-value">3</div>
                        <div class="metric-label">Incidentes SST</div>
                        <div class="metric-change positive">
                            <i class="bi bi-arrow-down"></i> -1 este mês
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Gráficos e Dados -->
            <div class="row mb-4">
                <!-- Progresso e Cronograma -->
                <div class="col-xl-8 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Progresso da Obra vs Cronograma</h5>
                            <div class="btn-group btn-group-sm" role="group">
                                <input type="radio" class="btn-check" name="period" id="period7" checked>
                                <label class="btn btn-outline-primary" for="period7">Mensal</label>
                                
                                <input type="radio" class="btn-check" name="period" id="period30">
                                <label class="btn btn-outline-primary" for="period30">Trimestral</label>
                                
                                <input type="radio" class="btn-check" name="period" id="period90">
                                <label class="btn btn-outline-primary" for="period90">Anual</label>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="progressChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
                
                <!-- Distribuição de Recursos -->
                <div class="col-xl-4 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom">
                            <h5 class="mb-0">Alocação de Equipes</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="teamsChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Projetos e Atividades -->
            <div class="row mb-4">
                <!-- Projetos em Andamento -->
                <div class="col-xl-6 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Projetos em Andamento</h5>
                            <a href="projetos.html" class="btn btn-sm btn-outline-primary">Ver todos</a>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="project-card">
                                        <div class="project-image">
                                            <i class="bi bi-building"></i>
                                        </div>
                                        <div class="project-info">
                                            <div class="project-title">Residencial Solar</div>
                                            <div class="project-meta">Torre A • 24 andares</div>
                                            <div class="project-progress">
                                                <div class="d-flex justify-content-between mb-1">
                                                    <small>Progresso</small>
                                                    <small>65%</small>
                                                </div>
                                                <div class="progress progress-thin">
                                                    <div class="progress-bar bg-primary" style="width: 65%"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <div class="project-card">
                                        <div class="project-image">
                                            <i class="bi bi-shop"></i>
                                        </div>
                                        <div class="project-info">
                                            <div class="project-title">Shopping Center</div>
                                            <div class="project-meta">Estrutura metálica</div>
                                            <div class="project-progress">
                                                <div class="d-flex justify-content-between mb-1">
                                                    <small>Progresso</small>
                                                    <small>42%</small>
                                                </div>
                                                <div class="progress progress-thin">
                                                    <div class="progress-bar bg-warning" style="width: 42%"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <div class="project-card">
                                        <div class="project-image">
                                            <i class="bi bi-hospital"></i>
                                        </div>
                                        <div class="project-info">
                                            <div class="project-title">Hospital Municipal</div>
                                            <div class="project-meta">Reforma • 12 pavilhões</div>
                                            <div class="project-progress">
                                                <div class="d-flex justify-content-between mb-1">
                                                    <small>Progresso</small>
                                                    <small>78%</small>
                                                </div>
                                                <div class="progress progress-thin">
                                                    <div class="progress-bar bg-success" style="width: 78%"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <div class="project-card">
                                        <div class="project-image">
                                            <i class="bi bi-bank"></i>
                                        </div>
                                        <div class="project-info">
                                            <div class="project-title">Edifício Comercial</div>
                                            <div class="project-meta">18 andares • Garagem</div>
                                            <div class="project-progress">
                                                <div class="d-flex justify-content-between mb-1">
                                                    <small>Progresso</small>
                                                    <small>25%</small>
                                                </div>
                                                <div class="progress progress-thin">
                                                    <div class="progress-bar bg-info" style="width: 25%"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Atividades do Dia -->
                <div class="col-xl-6 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Atividades do Dia</h5>
                            <a href="cronograma.html" class="btn btn-sm btn-outline-primary">Cronograma</a>
                        </div>
                        <div class="card-body">
                            <div class="task-item ongoing">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>Lançamento de Laje - Torre A</strong>
                                        <div class="small">Residencial Solar • 12º andar</div>
                                        <div class="small text-muted">Equipe: 8 pedreiros + 6 serventes</div>
                                    </div>
                                    <span class="badge bg-primary">Em andamento</span>
                                </div>
                            </div>
                            
                            <div class="task-item ongoing">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>Instalação Elétrica - Área Comum</strong>
                                        <div class="small">Shopping Center • 2º pavimento</div>
                                        <div class="small text-muted">Equipe: 4 eletricistas</div>
                                    </div>
                                    <span class="badge bg-primary">Em andamento</span>
                                </div>
                            </div>
                            
                            <div class="task-item delayed">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>Revestimento Fachada</strong>
                                        <div class="small">Hospital Municipal • Ala Norte</div>
                                        <div class="small text-muted">Aguardando material</div>
                                    </div>
                                    <span class="badge bg-danger">Atrasado</span>
                                </div>
                            </div>
                            
                            <div class="task-item completed">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>Fundação - Estacas</strong>
                                        <div class="small">Edifício Comercial</div>
                                        <div class="small text-muted">Concluído às 14:30</div>
                                    </div>
                                    <span class="badge bg-success">Concluído</span>
                                </div>
                            </div>
                            
                            <div class="mt-3">
                                <h6 class="mb-3">Resumo do Dia</h6>
                                <div class="row text-center">
                                    <div class="col-4">
                                        <div class="metric-value text-primary">42</div>
                                        <div class="metric-label">Pessoas</div>
                                    </div>
                                    <div class="col-4">
                                        <div class="metric-value text-success">8</div>
                                        <div class="metric-label">Atividades</div>
                                    </div>
                                    <div class="col-4">
                                        <div class="metric-value text-warning">2</div>
                                        <div class="metric-label">Atrasos</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Materiais e Equipamentos -->
            <div class="row mb-4">
                <!-- Status de Materiais -->
                <div class="col-xl-6 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Status de Materiais</h5>
                            <a href="estoque.html" class="btn btn-sm btn-outline-primary">Ver estoque</a>
                        </div>
                        <div class="card-body">
                            <div class="supply-status good">
                                <div class="me-3">
                                    <i class="bi bi-check-circle-fill text-success" style="font-size: 1.5rem;"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <strong>Cimento</strong>
                                    <div class="small text-muted">Estoque: 450 sacos • Mínimo: 200</div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-semibold">225%</div>
                                    <div class="small text-muted">Acima do mínimo</div>
                                </div>
                            </div>
                            
                            <div class="supply-status good">
                                <div class="me-3">
                                    <i class="bi bi-check-circle-fill text-success" style="font-size: 1.5rem;"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <strong>Ferragens CA-50</strong>
                                    <div class="small text-muted">Estoque: 3.2 ton • Mínimo: 2.0 ton</div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-semibold">160%</div>
                                    <div class="small text-muted">Acima do mínimo</div>
                                </div>
                            </div>
                            
                            <div class="supply-status warning">
                                <div class="me-3">
                                    <i class="bi bi-exclamation-triangle-fill text-warning" style="font-size: 1.5rem;"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <strong>Tubos PVC 100mm</strong>
                                    <div class="small text-muted">Estoque: 85 unidades • Mínimo: 100</div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-semibold text-warning">85%</div>
                                    <div class="small text-muted">Abaixo do mínimo</div>
                                </div>
                            </div>
                            
                            <div class="supply-status critical">
                                <div class="me-3">
                                    <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size: 1.5rem;"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <strong>Cerâmica Piso</strong>
                                    <div class="small text-muted">Estoque: 120m² • Mínimo: 300m²</div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-semibold text-danger">40%</div>
                                    <div class="small text-muted">Estoque crítico</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Equipamentos e SST -->
                <div class="col-xl-6 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Equipamentos e Segurança</h5>
                            <a href="sst.html" class="btn btn-sm btn-outline-primary">Relatório SST</a>
                        </div>
                        <div class="card-body">
                            <div class="row text-center mb-4">
                                <div class="col-4">
                                    <div class="mb-3">
                                        <div class="metric-value text-success">186</div>
                                        <div class="metric-label">Dias sem acidentes</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="mb-3">
                                        <div class="metric-value text-primary">42</div>
                                        <div class="metric-label">EPIs em uso</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="mb-3">
                                        <div class="metric-value text-warning">3</div>
                                        <div class="metric-label">Inspeções pendentes</div>
                                    </div>
                                </div>
                            </div>
                            
                            <h6 class="mb-3">Status dos Equipamentos</h6>
                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
                                <div>
                                    <strong>Betoneira 400L</strong>
                                    <div class="small text-muted">Operacional • 2.148h de uso</div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-semibold text-success">Disponível</div>
                                    <div class="small text-muted">Próxima manutenção: 30 dias</div>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
                                <div>
                                    <strong>Guindaste 15T</strong>
                                    <div class="small text-muted">Em operação • Torre A</div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-semibold text-primary">Em uso</div>
                                    <div class="small text-muted">Operador: João Silva</div>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
                                <div>
                                    <strong>Compactador de Solo</strong>
                                    <div class="small text-muted">Manutenção preventiva</div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-semibold text-warning">Indisponível</div>
                                    <div class="small text-muted">Retorno: amanhã</div>
                                </div>
                            </div>
                            
                            <div class="mt-3">
                                <h6 class="mb-3">Indicadores de Segurança</h6>
                                <div class="row text-center">
                                    <div class="col-4">
                                        <div class="metric-value text-success">100%</div>
                                        <div class="metric-label">EPI</div>
                                    </div>
                                    <div class="col-4">
                                        <div class="metric-value text-primary">92%</div>
                                        <div class="metric-label">Treinamentos</div>
                                    </div>
                                    <div class="col-4">
                                        <div class="metric-value text-info">0</div>
                                        <div class="metric-label">Acidentes</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Alertas da Construção -->
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
                                        <i class="bi bi-cloud-rain me-3"></i>
                                        <div>
                                            <strong>Chuva prevista</strong> para esta tarde
                                            <div class="mt-1">
                                                <a href="#" class="btn btn-sm btn-warning">Ajustar cronograma</a>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="alert alert-info d-flex align-items-center mb-3">
                                        <i class="bi bi-truck me-3"></i>
                                        <div>
                                            <strong>Entrega atrasada</strong> de cerâmica
                                            <div class="mt-1">
                                                <small class="text-muted">Previsão: amanhã às 10:00</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="alert alert-success d-flex align-items-center mb-3">
                                        <i class="bi bi-check-circle me-3"></i>
                                        <div>
                                            <strong>Inspeção de qualidade</strong> aprovada
                                            <div class="mt-1">
                                                <small class="text-muted">Estrutura da Torre A - OK</small>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="alert alert-danger d-flex align-items-center mb-0">
                                        <i class="bi bi-clipboard-x me-3"></i>
                                        <div>
                                            <strong>2 atividades</strong> com atraso
                                            <div class="mt-1">
                                                <a href="cronograma.html" class="btn btn-sm btn-danger">Revisar cronograma</a>
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

        // Gráficos específicos do segmento de construção civil
        document.addEventListener('DOMContentLoaded', function() {
            // Gráfico de Progresso vs Cronograma
            const progressCtx = document.getElementById('progressChart').getContext('2d');
            const progressChart = new Chart(progressCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
                    datasets: [{
                        label: 'Progresso Real (%)',
                        data: [5, 12, 18, 25, 35, 45, 55, 65, 78, 85, 90, 95],
                        borderColor: '#E67E22',
                        backgroundColor: 'rgba(230, 126, 34, 0.1)',
                        tension: 0.4,
                        fill: true
                    }, {
                        label: 'Cronograma Planejado (%)',
                        data: [8, 15, 22, 30, 40, 50, 60, 70, 80, 88, 94, 100],
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
            
            // Gráfico de Alocação de Equipes
            const teamsCtx = document.getElementById('teamsChart').getContext('2d');
            const teamsChart = new Chart(teamsCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Pedreiros', 'Serventes', 'Eletricistas', 'Encanadores', 'Carpinteiros', 'Outros'],
                    datasets: [{
                        data: [35, 25, 15, 12, 8, 5],
                        backgroundColor: [
                            '#E67E22',
                            '#3498DB',
                            '#F39C12',
                            '#27AE60',
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
            nexusFlow.showNotification('Gerando relatório da obra...', 'info');
            setTimeout(() => {
                nexusFlow.showNotification('Relatório exportado com sucesso!', 'success');
            }, 1500);
        }
        
        function refreshDashboard() {
            nexusFlow.showNotification('Atualizando dados da construção...', 'info');
            setTimeout(() => {
                nexusFlow.showNotification('Dados atualizados!', 'success');
            }, 1000);
        }
        
        // Definir papel como engenheiro responsável
        localStorage.setItem('userRole', 'construction_engineer');
        
        // Simulação de atualização em tempo real do progresso
        setInterval(function() {
            // Simular atualização de progresso (apenas visual)
            const progressBars = document.querySelectorAll('.project-progress .progress-bar');
            if (progressBars.length > 0) {
                const randomBar = progressBars[Math.floor(Math.random() * progressBars.length)];
                if (randomBar) {
                    const currentWidth = parseInt(randomBar.style.width);
                    if (currentWidth < 95) {
                        const newWidth = currentWidth + 1;
                        randomBar.style.width = newWidth + '%';
                        randomBar.parentElement.querySelector('.small:last-child').textContent = newWidth + '%';
                    }
                }
            }
        }, 60000); // Atualiza a cada 60 segundos
    </script>
</body>
</html>