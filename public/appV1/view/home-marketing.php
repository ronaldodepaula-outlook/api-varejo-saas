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
$segmento = $_SESSION['segmento'] ?? 'marketing';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Marketing</title>
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
        .campaign-card {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 15px;
            transition: transform 0.2s;
        }
        .campaign-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .campaign-image {
            height: 120px;
            background: linear-gradient(135deg, #3498DB 0%, #2980B9 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
        }
        .campaign-info {
            padding: 15px;
        }
        .campaign-title {
            font-size: 1rem;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .campaign-meta {
            font-size: 0.85rem;
            color: #6c757d;
            margin-bottom: 10px;
        }
        .campaign-progress {
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
        .budget-status {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 8px;
            background: #f8f9fa;
        }
        .budget-status.good { border-left: 4px solid var(--secondary-color); }
        .budget-status.warning { border-left: 4px solid var(--warning-color); }
        .budget-status.critical { border-left: 4px solid var(--danger-color); }
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
                        <li class="breadcrumb-item active">Marketing</li>
                    </ol>
                </nav>
            </div>
            <div class="header-right">
                <!-- Notificações -->
                <div class="dropdown">
                    <button class="btn btn-link text-dark position-relative" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-bell" style="font-size: 1.25rem;"></i>
                        <span class="badge bg-danger position-absolute translate-middle rounded-pill" style="font-size: 0.6rem; top: 8px; right: 8px;">3</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><h6 class="dropdown-header">Alertas de Campanha</h6></li>
                        <li><a class="dropdown-item" href="#">CPC acima do limite em Meta Ads</a></li>
                        <li><a class="dropdown-item" href="#">Taxa de conversão caiu 15%</a></li>
                        <li><a class="dropdown-item" href="#">Orçamento esgotado - Google Ads</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-center" href="#">Ver todos</a></li>
                    </ul>
                </div>
                <!-- Dropdown do Usuário -->
                <div class="dropdown user-dropdown">
                    <div class="user-avatar" data-bs-toggle="dropdown" aria-expanded="false">
                        M
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><h6 class="dropdown-header user-name">Gestor de Marketing</h6></li>
                        <li><small class="dropdown-header text-muted user-email">marketing@<?= htmlspecialchars($empresa) ?></small></li>
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
                    <h1 class="page-title">Dashboard Marketing</h1>
                    <p class="page-subtitle">Desempenho de campanhas, leads e ROI</p>
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
                            <i class="bi bi-graph-up"></i>
                        </div>
                        <div class="metric-value">4.2%</div>
                        <div class="metric-label">Taxa de Conversão</div>
                        <div class="metric-change positive">
                            <i class="bi bi-arrow-up"></i> +0.8% vs semana passada
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="metric-card success">
                        <div class="metric-icon success">
                            <i class="bi bi-currency-dollar"></i>
                        </div>
                        <div class="metric-value">R$ 182K</div>
                        <div class="metric-label">Receita Gerada</div>
                        <div class="metric-change positive">
                            <i class="bi bi-arrow-up"></i> +12% mês
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="metric-card warning">
                        <div class="metric-icon warning">
                            <i class="bi bi-people"></i>
                        </div>
                        <div class="metric-value">1.240</div>
                        <div class="metric-label">Novos Leads</div>
                        <div class="metric-change positive">
                            <i class="bi bi-arrow-up"></i> +210 vs meta
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="metric-card danger">
                        <div class="metric-icon danger">
                            <i class="bi bi-wallet2"></i>
                        </div>
                        <div class="metric-value">R$ 42K</div>
                        <div class="metric-label">Custo Total de Aquisição</div>
                        <div class="metric-change negative">
                            <i class="bi bi-arrow-up"></i> +8% vs orçado
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gráficos e Dados -->
            <div class="row mb-4">
                <!-- Desempenho de Campanhas -->
                <div class="col-xl-8 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Desempenho de Campanhas (Últimos 30 dias)</h5>
                            <div class="btn-group btn-group-sm" role="group">
                                <input type="radio" class="btn-check" name="period" id="period7" checked>
                                <label class="btn btn-outline-primary" for="period7">Semanal</label>
                                <input type="radio" class="btn-check" name="period" id="period30">
                                <label class="btn btn-outline-primary" for="period30">Mensal</label>
                                <input type="radio" class="btn-check" name="period" id="period90">
                                <label class="btn btn-outline-primary" for="period90">Trimestral</label>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="campaignChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
                <!-- Canais de Tráfego -->
                <div class="col-xl-4 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom">
                            <h5 class="mb-0">Canais de Tráfego</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="trafficChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Campanhas e Atividades -->
            <div class="row mb-4">
                <!-- Campanhas Ativas -->
                <div class="col-xl-6 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Campanhas Ativas</h5>
                            <a href="campanhas.html" class="btn btn-sm btn-outline-primary">Ver todas</a>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="campaign-card">
                                        <div class="campaign-image">
                                            <i class="bi bi-facebook"></i>
                                        </div>
                                        <div class="campaign-info">
                                            <div class="campaign-title">Lançamento Produto X</div>
                                            <div class="campaign-meta">Meta Ads • Conversão</div>
                                            <div class="campaign-progress">
                                                <div class="d-flex justify-content-between mb-1">
                                                    <small>ROI</small>
                                                    <small>3.2x</small>
                                                </div>
                                                <div class="progress progress-thin">
                                                    <div class="progress-bar bg-success" style="width: 85%"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="campaign-card">
                                        <div class="campaign-image">
                                            <i class="bi bi-google"></i>
                                        </div>
                                        <div class="campaign-info">
                                            <div class="campaign-title">Busca por Palavras-chave</div>
                                            <div class="campaign-meta">Google Ads • Tráfego</div>
                                            <div class="campaign-progress">
                                                <div class="d-flex justify-content-between mb-1">
                                                    <small>ROI</small>
                                                    <small>2.1x</small>
                                                </div>
                                                <div class="progress progress-thin">
                                                    <div class="progress-bar bg-primary" style="width: 60%"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="campaign-card">
                                        <div class="campaign-image">
                                            <i class="bi bi-envelope"></i>
                                        </div>
                                        <div class="campaign-info">
                                            <div class="campaign-title">E-mail Marketing - Promoção</div>
                                            <div class="campaign-meta">Black Friday • Engajamento</div>
                                            <div class="campaign-progress">
                                                <div class="d-flex justify-content-between mb-1">
                                                    <small>Abertura</small>
                                                    <small>28%</small>
                                                </div>
                                                <div class="progress progress-thin">
                                                    <div class="progress-bar bg-warning" style="width: 70%"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="campaign-card">
                                        <div class="campaign-image">
                                            <i class="bi bi-instagram"></i>
                                        </div>
                                        <div class="campaign-info">
                                            <div class="campaign-title">Influencers - Verão 2025</div>
                                            <div class="campaign-meta">Instagram • Alcance</div>
                                            <div class="campaign-progress">
                                                <div class="d-flex justify-content-between mb-1">
                                                    <small>Engajamento</small>
                                                    <small>5.4%</small>
                                                </div>
                                                <div class="progress progress-thin">
                                                    <div class="progress-bar bg-info" style="width: 90%"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tarefas do Dia -->
                <div class="col-xl-6 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Tarefas do Dia</h5>
                            <a href="cronograma.html" class="btn btn-sm btn-outline-primary">Planejamento</a>
                        </div>
                        <div class="card-body">
                            <div class="task-item ongoing">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>Revisão de Anúncios - Meta</strong>
                                        <div class="small">Campanha Lançamento X • A/B Test</div>
                                        <div class="small text-muted">Responsável: Ana Costa</div>
                                    </div>
                                    <span class="badge bg-primary">Em andamento</span>
                                </div>
                            </div>
                            <div class="task-item completed">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>Relatório Semanal</strong>
                                        <div class="small">Google Analytics + Ads</div>
                                        <div class="small text-muted">Enviado às 10:00</div>
                                    </div>
                                    <span class="badge bg-success">Concluído</span>
                                </div>
                            </div>
                            <div class="task-item delayed">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>Criação de Conteúdo - Blog</strong>
                                        <div class="small">Artigo: "Tendências 2025"</div>
                                        <div class="small text-muted">Atraso: revisão pendente</div>
                                    </div>
                                    <span class="badge bg-danger">Atrasado</span>
                                </div>
                            </div>
                            <div class="task-item ongoing">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>Reunião com Influencers</strong>
                                        <div class="small">Briefing Campanha Verão</div>
                                        <div class="small text-muted">15:00 • Sala Zoom</div>
                                    </div>
                                    <span class="badge bg-primary">Em andamento</span>
                                </div>
                            </div>
                            <div class="mt-3">
                                <h6 class="mb-3">Resumo do Dia</h6>
                                <div class="row text-center">
                                    <div class="col-4">
                                        <div class="metric-value text-primary">8</div>
                                        <div class="metric-label">Campanhas</div>
                                    </div>
                                    <div class="col-4">
                                        <div class="metric-value text-success">12</div>
                                        <div class="metric-label">Tarefas</div>
                                    </div>
                                    <div class="col-4">
                                        <div class="metric-value text-warning">1</div>
                                        <div class="metric-label">Atrasos</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Orçamento e Redes Sociais -->
            <div class="row mb-4">
                <!-- Status do Orçamento -->
                <div class="col-xl-6 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Status do Orçamento</h5>
                            <a href="orcamento.html" class="btn btn-sm btn-outline-primary">Detalhes</a>
                        </div>
                        <div class="card-body">
                            <div class="budget-status good">
                                <div class="me-3">
                                    <i class="bi bi-check-circle-fill text-success" style="font-size: 1.5rem;"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <strong>Meta Ads</strong>
                                    <div class="small text-muted">Gasto: R$ 12.5K / R$ 20K</div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-semibold">62%</div>
                                    <div class="small text-muted">Dentro do limite</div>
                                </div>
                            </div>
                            <div class="budget-status good">
                                <div class="me-3">
                                    <i class="bi bi-check-circle-fill text-success" style="font-size: 1.5rem;"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <strong>Google Ads</strong>
                                    <div class="small text-muted">Gasto: R$ 8.2K / R$ 15K</div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-semibold">55%</div>
                                    <div class="small text-muted">Dentro do limite</div>
                                </div>
                            </div>
                            <div class="budget-status warning">
                                <div class="me-3">
                                    <i class="bi bi-exclamation-triangle-fill text-warning" style="font-size: 1.5rem;"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <strong>Influencers</strong>
                                    <div class="small text-muted">Gasto: R$ 9.8K / R$ 10K</div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-semibold text-warning">98%</div>
                                    <div class="small text-muted">Quase esgotado</div>
                                </div>
                            </div>
                            <div class="budget-status critical">
                                <div class="me-3">
                                    <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size: 1.5rem;"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <strong>Produção de Vídeo</strong>
                                    <div class="small text-muted">Gasto: R$ 18K / R$ 15K</div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-semibold text-danger">120%</div>
                                    <div class="small text-muted">Excedido</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Redes Sociais e Métricas -->
                <div class="col-xl-6 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Redes Sociais</h5>
                            <a href="social.html" class="btn btn-sm btn-outline-primary">Analytics</a>
                        </div>
                        <div class="card-body">
                            <div class="row text-center mb-4">
                                <div class="col-4">
                                    <div class="mb-3">
                                        <div class="metric-value text-primary">24.5K</div>
                                        <div class="metric-label">Seguidores</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="mb-3">
                                        <div class="metric-value text-success">5.8%</div>
                                        <div class="metric-label">Engajamento</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="mb-3">
                                        <div class="metric-value text-info">182</div>
                                        <div class="metric-label">Menções</div>
                                    </div>
                                </div>
                            </div>
                            <h6 class="mb-3">Desempenho por Plataforma</h6>
                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
                                <div><strong>Instagram</strong><div class="small text-muted">+1.2K seguidores</div></div>
                                <div class="fw-semibold text-success">↑ 8%</div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
                                <div><strong>LinkedIn</strong><div class="small text-muted">+420 conexões</div></div>
                                <div class="fw-semibold text-primary">↑ 5%</div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
                                <div><strong>TikTok</strong><div class="small text-muted">Vídeo viral (250K views)</div></div>
                                <div class="fw-semibold text-warning">↑ 22%</div>
                            </div>
                            <div class="mt-3">
                                <h6 class="mb-3">Indicadores de Qualidade</h6>
                                <div class="row text-center">
                                    <div class="col-4">
                                        <div class="metric-value text-success">94%</div>
                                        <div class="metric-label">Feedback Positivo</div>
                                    </div>
                                    <div class="col-4">
                                        <div class="metric-value text-primary">87%</div>
                                        <div class="metric-label">CTR Médio</div>
                                    </div>
                                    <div class="col-4">
                                        <div class="metric-value text-danger">3</div>
                                        <div class="metric-label">Críticas Relevantes</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alertas de Marketing -->
            <div class="row">
                <div class="col-12">
                    <div class="card-custom">
                        <div class="card-header-custom">
                            <h5 class="mb-0">Alertas e Ações Recomendadas</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="alert alert-warning d-flex align-items-center mb-3">
                                        <i class="bi bi-graph-down me-3"></i>
                                        <div>
                                            <strong>Queda de 15% na conversão</strong> do funil
                                            <div class="mt-1">
                                                <a href="#" class="btn btn-sm btn-warning">Diagnosticar causa</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="alert alert-info d-flex align-items-center mb-3">
                                        <i class="bi bi-cash-coin me-3"></i>
                                        <div>
                                            <strong>CPC elevado</strong> em campanha do Google
                                            <div class="mt-1">
                                                <small class="text-muted">R$ 8,20 vs meta R$ 5,00</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="alert alert-success d-flex align-items-center mb-3">
                                        <i class="bi bi-trophy me-3"></i>
                                        <div>
                                            <strong>Campanha no TikTok</strong> superou meta de views
                                            <div class="mt-1">
                                                <small class="text-muted">+250K visualizações</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="alert alert-danger d-flex align-items-center mb-0">
                                        <i class="bi bi-exclamation-octagon me-3"></i>
                                        <div>
                                            <strong>Orçamento excedido</strong> em produção de vídeo
                                            <div class="mt-1">
                                                <a href="orcamento.html" class="btn btn-sm btn-danger">Revisar alocação</a>
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

        // Gráficos de Marketing
        document.addEventListener('DOMContentLoaded', function() {
            // Gráfico de Desempenho de Campanhas
            const campaignCtx = document.getElementById('campaignChart').getContext('2d');
            const campaignChart = new Chart(campaignCtx, {
                type: 'line',
                data: {
                    labels: ['1', '5', '10', '15', '20', '25', '30'],
                    datasets: [{
                        label: 'Receita (R$)',
                        data: [8000, 12000, 18000, 22000, 28000, 35000, 42000],
                        borderColor: '#27AE60',
                        backgroundColor: 'rgba(39, 174, 96, 0.1)',
                        tension: 0.4,
                        fill: true
                    }, {
                        label: 'Custo (R$)',
                        data: [3000, 5000, 7500, 9000, 11000, 13000, 15000],
                        borderColor: '#E74C3C',
                        backgroundColor: 'rgba(231, 76, 60, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'R$' + (value / 1000) + 'K';
                                }
                            }
                        }
                    }
                }
            });

            // Gráfico de Canais de Tráfego
            const trafficCtx = document.getElementById('trafficChart').getContext('2d');
            const trafficChart = new Chart(trafficCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Orgânico', 'Meta Ads', 'Google Ads', 'Email', 'Direto', 'Outros'],
                    datasets: [{
                        data: [35, 25, 20, 10, 7, 3],
                        backgroundColor: [
                            '#27AE60',
                            '#3b5998',
                            '#DB4437',
                            '#E67E22',
                            '#95A5A6',
                            '#9B59B6'
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
            nexusFlow.showNotification('Gerando relatório de marketing...', 'info');
            setTimeout(() => {
                nexusFlow.showNotification('Relatório exportado com sucesso!', 'success');
            }, 1500);
        }

        function refreshDashboard() {
            nexusFlow.showNotification('Atualizando dados de marketing...', 'info');
            setTimeout(() => {
                nexusFlow.showNotification('Dados atualizados!', 'success');
            }, 1000);
        }

        localStorage.setItem('userRole', 'marketing_manager');
    </script>
</body>
</html>