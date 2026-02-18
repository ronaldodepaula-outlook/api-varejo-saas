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
    <title>Dashboard Marketing - NexusFlow</title>
    
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
            --marketing-color: #9B59B6;
            --social-color: #3498DB;
            --seo-color: #2ECC71;
            --content-color: #F39C12;
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
            color: var(--marketing-color);
            margin-right: 1rem;
        }
        
        .breadcrumb-custom {
            margin-bottom: 0;
        }
        
        .breadcrumb-custom .breadcrumb-item.active {
            color: var(--marketing-color);
            font-weight: 600;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--marketing-color);
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
            color: var(--marketing-color);
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
        
        .metric-card.marketing {
            border-left: 4px solid var(--marketing-color);
        }
        
        .metric-card.social {
            border-left: 4px solid var(--social-color);
        }
        
        .metric-card.seo {
            border-left: 4px solid var(--seo-color);
        }
        
        .metric-card.content {
            border-left: 4px solid var(--content-color);
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
        
        .metric-icon.marketing {
            background: rgba(155, 89, 182, 0.1);
            color: var(--marketing-color);
        }
        
        .metric-icon.social {
            background: rgba(52, 152, 219, 0.1);
            color: var(--social-color);
        }
        
        .metric-icon.seo {
            background: rgba(46, 204, 113, 0.1);
            color: var(--seo-color);
        }
        
        .metric-icon.content {
            background: rgba(243, 156, 18, 0.1);
            color: var(--content-color);
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
        
        .status-completed {
            background: rgba(108, 117, 125, 0.1);
            color: #6c757d;
        }
        
        .status-draft {
            background: rgba(155, 89, 182, 0.1);
            color: var(--marketing-color);
        }
        
        .channel-badge {
            padding: 0.35rem 0.65rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .channel-social {
            background: rgba(52, 152, 219, 0.1);
            color: var(--social-color);
        }
        
        .channel-email {
            background: rgba(46, 204, 113, 0.1);
            color: var(--seo-color);
        }
        
        .channel-seo {
            background: rgba(243, 156, 18, 0.1);
            color: var(--content-color);
        }
        
        .channel-ads {
            background: rgba(155, 89, 182, 0.1);
            color: var(--marketing-color);
        }
        
        .notification-toast {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1050;
            min-width: 300px;
        }
        
        .performance-indicator {
            height: 6px;
            border-radius: 3px;
            background: #e9ecef;
            margin-top: 5px;
            overflow: hidden;
        }
        
        .performance-progress {
            height: 100%;
            border-radius: 3px;
        }
        
        .performance-excellent {
            background: var(--success-color);
        }
        
        .performance-good {
            background: var(--seo-color);
        }
        
        .performance-average {
            background: var(--warning-color);
        }
        
        .performance-poor {
            background: var(--danger-color);
        }
        
        .campaign-priority-high {
            border-left: 4px solid var(--danger-color);
        }
        
        .campaign-priority-medium {
            border-left: 4px solid var(--warning-color);
        }
        
        .campaign-priority-low {
            border-left: 4px solid var(--success-color);
        }
        
        .social-stats {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }
        
        .social-stat {
            text-align: center;
        }
        
        .social-stat-value {
            font-weight: 700;
            font-size: 1.1rem;
        }
        
        .social-stat-label {
            font-size: 0.75rem;
            color: #6c757d;
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
                        <li class="breadcrumb-item active">Marketing</li>
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
                        <li><h6 class="dropdown-header">Alertas de Marketing</h6></li>
                        <li><a class="dropdown-item" href="#">Campanha com CTR baixo</a></li>
                        <li><a class="dropdown-item" href="#">Orçamento de Google Ads próximo do limite</a></li>
                        <li><a class="dropdown-item" href="#">Novos leads qualificados</a></li>
                        <li><a class="dropdown-item" href="#">Post viral no Instagram</a></li>
                        <li><a class="dropdown-item" href="#">Meta de conversão atingida</a></li>
                        <li><a class="dropdown-item" href="#">Relatório mensal pronto</a></li>
                        <li><a class="dropdown-item" href="#">Site com taxa de rejeição alta</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-center" href="#">Ver todas</a></li>
                    </ul>
                </div>
                
                <!-- Dropdown do Usuário -->
                <div class="dropdown user-dropdown">
                    <div class="user-avatar" data-bs-toggle="dropdown" aria-expanded="false">
                        M
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><h6 class="dropdown-header user-name">Marketing NexusFlow</h6></li>
                        <li><small class="dropdown-header text-muted user-email">marketing@nexusflow.com</small></li>
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
                    <h1 class="page-title">Dashboard de Marketing</h1>
                    <p class="page-subtitle">Performance de campanhas, engajamento e métricas de conversão</p>
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
                    <div class="metric-card marketing">
                        <div class="metric-icon marketing">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>
                        <div class="metric-value">12.4%</div>
                        <div class="metric-label">Taxa de Conversão</div>
                        <div class="metric-change positive">
                            <i class="bi bi-arrow-up"></i> +2.1% vs mês anterior
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="metric-card social">
                        <div class="metric-icon social">
                            <i class="bi bi-people"></i>
                        </div>
                        <div class="metric-value">45.7K</div>
                        <div class="metric-label">Engajamento Social</div>
                        <div class="metric-change positive">
                            <i class="bi bi-arrow-up"></i> +18% esta semana
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="metric-card seo">
                        <div class="metric-icon seo">
                            <i class="bi bi-search"></i>
                        </div>
                        <div class="metric-value">8.2K</div>
                        <div class="metric-label">Tráfego Orgânico</div>
                        <div class="metric-change positive">
                            <i class="bi bi-arrow-up"></i> +12.3% vs mês anterior
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="metric-card content">
                        <div class="metric-icon content">
                            <i class="bi bi-envelope-open"></i>
                        </div>
                        <div class="metric-value">24.8%</div>
                        <div class="metric-label">Taxa de Abertura</div>
                        <div class="metric-change negative">
                            <i class="bi bi-arrow-down"></i> -1.2% vs mês anterior
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Gráficos e Dados -->
            <div class="row mb-4">
                <!-- Gráfico de Performance de Campanhas -->
                <div class="col-xl-8 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Performance de Campanhas - Últimos 6 Meses</h5>
                            <div class="btn-group btn-group-sm" role="group">
                                <input type="radio" class="btn-check" name="period" id="period7" checked>
                                <label class="btn btn-outline-primary" for="period7">ROI</label>
                                
                                <input type="radio" class="btn-check" name="period" id="period30">
                                <label class="btn btn-outline-primary" for="period30">Conversões</label>
                                
                                <input type="radio" class="btn-check" name="period" id="period90">
                                <label class="btn btn-outline-primary" for="period90">Custo</label>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="campaignPerformanceChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
                
                <!-- Distribuição por Canal -->
                <div class="col-xl-4 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom">
                            <h5 class="mb-0">Distribuição por Canal</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="channelDistributionChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tabelas de Dados -->
            <div class="row">
                <!-- Campanhas Ativas -->
                <div class="col-xl-6 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Campanhas Ativas</h5>
                            <a href="campanhas.html" class="btn btn-sm btn-outline-primary">Ver todas</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-custom mb-0">
                                    <thead>
                                        <tr>
                                            <th>Campanha</th>
                                            <th>Canal</th>
                                            <th>Status</th>
                                            <th>Performance</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="campaign-priority-high">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                                            LA
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold">Lançamento App</div>
                                                        <small class="text-muted">Meta: 5.000 downloads</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="channel-badge channel-social">Social Media</span></td>
                                            <td><span class="status-badge status-active">Ativa</span></td>
                                            <td>
                                                <div>72% da meta</div>
                                                <div class="performance-indicator">
                                                    <div class="performance-progress performance-excellent" style="width: 72%"></div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="campaign-priority-medium">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                                            NV
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold">Newsletter Verão</div>
                                                        <small class="text-muted">Meta: 15% conversão</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="channel-badge channel-email">Email Marketing</span></td>
                                            <td><span class="status-badge status-active">Ativa</span></td>
                                            <td>
                                                <div>58% da meta</div>
                                                <div class="performance-indicator">
                                                    <div class="performance-progress performance-good" style="width: 58%"></div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="campaign-priority-low">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                                            BS
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold">Black Friday</div>
                                                        <small class="text-muted">Meta: R$ 500K em vendas</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="channel-badge channel-ads">Google Ads</span></td>
                                            <td><span class="status-badge status-draft">Planejamento</span></td>
                                            <td>
                                                <div>25% da meta</div>
                                                <div class="performance-indicator">
                                                    <div class="performance-progress performance-average" style="width: 25%"></div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Métricas de Redes Sociais -->
                <div class="col-xl-6 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Métricas de Redes Sociais</h5>
                            <a href="redes-sociais.html" class="btn btn-sm btn-outline-primary">Ver detalhes</a>
                        </div>
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <i class="bi bi-instagram text-danger" style="font-size: 1.5rem;"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold">Instagram</div>
                                            <div class="social-stats">
                                                <div class="social-stat">
                                                    <div class="social-stat-value">12.5K</div>
                                                    <div class="social-stat-label">Seguidores</div>
                                                </div>
                                                <div class="social-stat">
                                                    <div class="social-stat-value">4.2%</div>
                                                    <div class="social-stat-label">Engajamento</div>
                                                </div>
                                                <div class="social-stat">
                                                    <div class="social-stat-value">+325</div>
                                                    <div class="social-stat-label">Novos</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <i class="bi bi-facebook text-primary" style="font-size: 1.5rem;"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold">Facebook</div>
                                            <div class="social-stats">
                                                <div class="social-stat">
                                                    <div class="social-stat-value">8.7K</div>
                                                    <div class="social-stat-label">Curtidas</div>
                                                </div>
                                                <div class="social-stat">
                                                    <div class="social-stat-value">2.8%</div>
                                                    <div class="social-stat-label">Engajamento</div>
                                                </div>
                                                <div class="social-stat">
                                                    <div class="social-stat-value">+142</div>
                                                    <div class="social-stat-label">Novos</div>
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
                                            <i class="bi bi-linkedin text-info" style="font-size: 1.5rem;"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold">LinkedIn</div>
                                            <div class="social-stats">
                                                <div class="social-stat">
                                                    <div class="social-stat-value">5.3K</div>
                                                    <div class="social-stat-label">Seguidores</div>
                                                </div>
                                                <div class="social-stat">
                                                    <div class="social-stat-value">1.5%</div>
                                                    <div class="social-stat-label">Engajamento</div>
                                                </div>
                                                <div class="social-stat">
                                                    <div class="social-stat-value">+87</div>
                                                    <div class="social-stat-label">Novos</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <i class="bi bi-twitter text-info" style="font-size: 1.5rem;"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold">Twitter</div>
                                            <div class="social-stats">
                                                <div class="social-stat">
                                                    <div class="social-stat-value">7.2K</div>
                                                    <div class="social-stat-label">Seguidores</div>
                                                </div>
                                                <div class="social-stat">
                                                    <div class="social-stat-value">3.1%</div>
                                                    <div class="social-stat-label">Engajamento</div>
                                                </div>
                                                <div class="social-stat">
                                                    <div class="social-stat-value">+203</div>
                                                    <div class="social-stat-label">Novos</div>
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
                            <h5 class="mb-0">Alertas e Oportunidades</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-warning d-flex align-items-center mb-3">
                                <i class="bi bi-graph-down me-3"></i>
                                <div>
                                    <strong>Campanha de Email</strong> com CTR abaixo da média (2.1% vs 3.5%)
                                    <div class="mt-1">
                                        <a href="#" class="btn btn-sm btn-warning">Otimizar</a>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="alert alert-info d-flex align-items-center mb-3">
                                <i class="bi bi-cash-coin me-3"></i>
                                <div>
                                    <strong>Orçamento do Google Ads</strong> 85% utilizado - Restam R$ 1.245
                                    <div class="mt-1">
                                        <a href="orcamento.html" class="btn btn-sm btn-info">Ajustar orçamento</a>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="alert alert-success d-flex align-items-center mb-3">
                                <i class="bi bi-arrow-up-right me-3"></i>
                                <div>
                                    <strong>Post no Instagram</strong> viralizando +325% engajamento
                                    <div class="mt-1">
                                        <a href="#" class="btn btn-sm btn-success">Aproveitar tendência</a>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="alert alert-danger d-flex align-items-center mb-0">
                                <i class="bi bi-shield-exclamation me-3"></i>
                                <div>
                                    <strong>Site com taxa de rejeição</strong> de 68% - Acima do ideal
                                    <div class="mt-1">
                                        <a href="#" class="btn btn-sm btn-danger">Investigar causas</a>
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
            // Gráfico de Performance de Campanhas
            const campaignPerformanceCtx = document.getElementById('campaignPerformanceChart').getContext('2d');
            const campaignPerformanceChart = new Chart(campaignPerformanceCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun'],
                    datasets: [{
                        label: 'ROI (%)',
                        data: [125, 142, 138, 165, 158, 172],
                        borderColor: '#9B59B6',
                        backgroundColor: 'rgba(155, 89, 182, 0.1)',
                        tension: 0.4,
                        fill: true
                    }, {
                        label: 'Conversões',
                        data: [245, 289, 312, 345, 378, 412],
                        borderColor: '#3498DB',
                        backgroundColor: 'rgba(52, 152, 219, 0.1)',
                        tension: 0.4,
                        fill: true
                    }, {
                        label: 'Custo por Aquisição (R$)',
                        data: [45, 42, 38, 35, 32, 28],
                        borderColor: '#2ECC71',
                        backgroundColor: 'rgba(46, 204, 113, 0.1)',
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
            
            // Gráfico de Distribuição por Canal
            const channelDistributionCtx = document.getElementById('channelDistributionChart').getContext('2d');
            const channelDistributionChart = new Chart(channelDistributionCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Social Media', 'Email Marketing', 'SEO', 'Google Ads', 'Conteúdo', 'Outros'],
                    datasets: [{
                        data: [35, 25, 15, 12, 8, 5],
                        backgroundColor: [
                            '#3498DB',
                            '#2ECC71',
                            '#F39C12',
                            '#9B59B6',
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
            nexusFlow.showNotification('Exportando relatório de marketing...', 'info');
            setTimeout(() => {
                nexusFlow.showNotification('Relatório exportado com sucesso!', 'success');
            }, 2000);
        }
        
        function refreshDashboard() {
            nexusFlow.showNotification('Atualizando dashboard de marketing...', 'info');
            setTimeout(() => {
                nexusFlow.showNotification('Dashboard atualizado!', 'success');
                // Em uma aplicação real, aqui seria feita uma requisição para atualizar os dados
                // Por simplicidade, apenas recarregamos a página
                location.reload();
            }, 1000);
        }
        
        // Definir papel como marketing
        localStorage.setItem('userRole', 'marketing');
    </script>
</body>
</html>