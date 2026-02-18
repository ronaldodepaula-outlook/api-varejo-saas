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
    <title>Dashboard Ciência & Pesquisa - NexusFlow</title>
    
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
            --science-color: #3A86FF;
            --research-color: #8338EC;
            --innovation-color: #FF006E;
            --data-color: #FB5607;
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
            color: var(--science-color);
            margin-right: 1rem;
        }
        
        .breadcrumb-custom {
            margin-bottom: 0;
        }
        
        .breadcrumb-custom .breadcrumb-item.active {
            color: var(--science-color);
            font-weight: 600;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--science-color);
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
            color: var(--science-color);
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
        
        .metric-card.science {
            border-left: 4px solid var(--science-color);
        }
        
        .metric-card.research {
            border-left: 4px solid var(--research-color);
        }
        
        .metric-card.innovation {
            border-left: 4px solid var(--innovation-color);
        }
        
        .metric-card.data {
            border-left: 4px solid var(--data-color);
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
        
        .metric-icon.science {
            background: rgba(58, 134, 255, 0.1);
            color: var(--science-color);
        }
        
        .metric-icon.research {
            background: rgba(131, 56, 236, 0.1);
            color: var(--research-color);
        }
        
        .metric-icon.innovation {
            background: rgba(255, 0, 110, 0.1);
            color: var(--innovation-color);
        }
        
        .metric-icon.data {
            background: rgba(251, 86, 7, 0.1);
            color: var(--data-color);
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
        
        .status-critical {
            background: rgba(255, 0, 110, 0.1);
            color: var(--innovation-color);
        }
        
        .discipline-badge {
            padding: 0.35rem 0.65rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .discipline-biology {
            background: rgba(58, 134, 255, 0.1);
            color: var(--science-color);
        }
        
        .discipline-physics {
            background: rgba(131, 56, 236, 0.1);
            color: var(--research-color);
        }
        
        .discipline-chemistry {
            background: rgba(255, 0, 110, 0.1);
            color: var(--innovation-color);
        }
        
        .discipline-computer {
            background: rgba(251, 86, 7, 0.1);
            color: var(--data-color);
        }
        
        .notification-toast {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1050;
            min-width: 300px;
        }
        
        .progress-indicator {
            height: 6px;
            border-radius: 3px;
            background: #e9ecef;
            margin-top: 5px;
            overflow: hidden;
        }
        
        .progress-bar-custom {
            height: 100%;
            border-radius: 3px;
        }
        
        .progress-excellent {
            background: var(--success-color);
        }
        
        .progress-good {
            background: var(--science-color);
        }
        
        .progress-average {
            background: var(--warning-color);
        }
        
        .progress-poor {
            background: var(--danger-color);
        }
        
        .project-priority-high {
            border-left: 4px solid var(--danger-color);
        }
        
        .project-priority-medium {
            border-left: 4px solid var(--warning-color);
        }
        
        .project-priority-low {
            border-left: 4px solid var(--success-color);
        }
        
        .publication-stats {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }
        
        .publication-stat {
            text-align: center;
        }
        
        .publication-stat-value {
            font-weight: 700;
            font-size: 1.1rem;
        }
        
        .publication-stat-label {
            font-size: 0.75rem;
            color: #6c757d;
        }
        
        .experiment-event {
            padding: 0.5rem;
            border-radius: 5px;
            margin-bottom: 0.5rem;
            font-size: 0.85rem;
        }
        
        .event-experiment {
            background: rgba(58, 134, 255, 0.1);
            border-left: 3px solid var(--science-color);
        }
        
        .event-analysis {
            background: rgba(131, 56, 236, 0.1);
            border-left: 3px solid var(--research-color);
        }
        
        .event-publication {
            background: rgba(255, 0, 110, 0.1);
            border-left: 3px solid var(--innovation-color);
        }
        
        .event-deadline {
            background: rgba(244, 96, 54, 0.1);
            border-left: 3px solid var(--warning-color);
        }
        
        .citation-indicator {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-right: 5px;
        }
        
        .citation-high {
            background: var(--success-color);
        }
        
        .citation-medium {
            background: var(--warning-color);
        }
        
        .citation-low {
            background: var(--danger-color);
        }
        
        .data-visualization {
            background: linear-gradient(135deg, #3A86FF, #8338EC);
            color: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1rem;
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
                        <li class="breadcrumb-item active">Ciência & Pesquisa</li>
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
                        <li><h6 class="dropdown-header">Alertas Científicos</h6></li>
                        <li><a class="dropdown-item" href="#">Novos dados disponíveis para análise</a></li>
                        <li><a class="dropdown-item" href="#">Prazo para submissão de artigo em 3 dias</a></li>
                        <li><a class="dropdown-item" href="#">Equipamento requer calibração</a></li>
                        <li><a class="dropdown-item" href="#">Resultados anômalos detectados</a></li>
                        <li><a class="dropdown-item" href="#">Novo financiamento aprovado</a></li>
                        <li><a class="dropdown-item" href="#">Colaboração internacional confirmada</a></li>
                        <li><a class="dropdown-item" href="#">Artigo aceito para publicação</a></li>
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
                        <li><h6 class="dropdown-header user-name">Ciência NexusFlow</h6></li>
                        <li><small class="dropdown-header text-muted user-email">ciencia@nexusflow.com</small></li>
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
                    <h1 class="page-title">Dashboard Científico</h1>
                    <p class="page-subtitle">Gestão de projetos de pesquisa, publicações e inovação científica</p>
                </div>
                <div>
                    <button class="btn btn-outline-primary me-2" onclick="exportReport()">
                        <i class="bi bi-download me-2"></i>Exportar Dados
                    </button>
                    <button class="btn btn-primary" onclick="refreshDashboard()">
                        <i class="bi bi-arrow-clockwise me-2"></i>Atualizar
                    </button>
                </div>
            </div>
            
            <!-- Visualização de Dados em Destaque -->
            <div class="data-visualization mb-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h3 class="text-white">Análise de Dados em Tempo Real</h3>
                        <p class="text-white mb-0">Processando 2.4TB de dados científicos - 87% completado</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="progress-indicator bg-white bg-opacity-25">
                            <div class="progress-bar-custom progress-excellent" style="width: 87%"></div>
                        </div>
                        <button class="btn btn-light btn-sm mt-2">
                            <i class="bi bi-play-circle me-1"></i>Visualizar Resultados
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Métricas Principais -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="metric-card science">
                        <div class="metric-icon science">
                            <i class="bi bi-clipboard-data"></i>
                        </div>
                        <div class="metric-value">47</div>
                        <div class="metric-label">Projetos Ativos</div>
                        <div class="metric-change positive">
                            <i class="bi bi-arrow-up"></i> +5 este trimestre
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="metric-card research">
                        <div class="metric-icon research">
                            <i class="bi bi-file-earmark-text"></i>
                        </div>
                        <div class="metric-value">128</div>
                        <div class="metric-label">Publicações</div>
                        <div class="metric-change positive">
                            <i class="bi bi-arrow-up"></i> +12% vs ano anterior
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="metric-card innovation">
                        <div class="metric-icon innovation">
                            <i class="bi bi-lightbulb"></i>
                        </div>
                        <div class="metric-value">23</div>
                        <div class="metric-label">Patentes Registradas</div>
                        <div class="metric-change positive">
                            <i class="bi bi-arrow-up"></i> +4 este ano
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="metric-card data">
                        <div class="metric-icon data">
                            <i class="bi bi-graph-up"></i>
                        </div>
                        <div class="metric-value">2.4M</div>
                        <div class="metric-label">Citações Recebidas</div>
                        <div class="metric-change positive">
                            <i class="bi bi-arrow-up"></i> +18% este ano
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Gráficos e Dados -->
            <div class="row mb-4">
                <!-- Gráfico de Produção Científica -->
                <div class="col-xl-8 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Produção Científica - Últimos 5 Anos</h5>
                            <div class="btn-group btn-group-sm" role="group">
                                <input type="radio" class="btn-check" name="period" id="period7" checked>
                                <label class="btn btn-outline-primary" for="period7">Artigos</label>
                                
                                <input type="radio" class="btn-check" name="period" id="period30">
                                <label class="btn btn-outline-primary" for="period30">Citações</label>
                                
                                <input type="radio" class="btn-check" name="period" id="period90">
                                <label class="btn btn-outline-primary" for="period90">Projetos</label>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="scientificProductionChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
                
                <!-- Distribuição por Área -->
                <div class="col-xl-4 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom">
                            <h5 class="mb-0">Distribuição por Área de Pesquisa</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="researchAreaChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tabelas de Dados -->
            <div class="row">
                <!-- Projetos em Andamento -->
                <div class="col-xl-6 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Projetos em Andamento</h5>
                            <a href="projetos.html" class="btn btn-sm btn-outline-primary">Ver todos</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-custom mb-0">
                                    <thead>
                                        <tr>
                                            <th>Projeto</th>
                                            <th>Área</th>
                                            <th>Status</th>
                                            <th>Progresso</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="project-priority-high">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                                            QG
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold">Quantum Computing</div>
                                                        <small class="text-muted">Financiamento: FAPESP</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="discipline-badge discipline-physics">Física</span></td>
                                            <td><span class="status-badge status-critical">Crítico</span></td>
                                            <td>
                                                <div>42% completo</div>
                                                <div class="progress-indicator">
                                                    <div class="progress-bar-custom progress-poor" style="width: 42%"></div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="project-priority-medium">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                                            GN
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold">Genoma Brasileiro</div>
                                                        <small class="text-muted">Financiamento: CNPq</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="discipline-badge discipline-biology">Biologia</span></td>
                                            <td><span class="status-badge status-active">Ativo</span></td>
                                            <td>
                                                <div>78% completo</div>
                                                <div class="progress-indicator">
                                                    <div class="progress-bar-custom progress-good" style="width: 78%"></div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="project-priority-low">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                                            ML
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold">Machine Learning</div>
                                                        <small class="text-muted">Financiamento: CAPES</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="discipline-badge discipline-computer">Computação</span></td>
                                            <td><span class="status-badge status-active">Ativo</span></td>
                                            <td>
                                                <div>65% completo</div>
                                                <div class="progress-indicator">
                                                    <div class="progress-bar-custom progress-average" style="width: 65%"></div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Agenda Científica -->
                <div class="col-xl-6 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Agenda Científica</h5>
                            <a href="calendario.html" class="btn btn-sm btn-outline-primary">Ver completo</a>
                        </div>
                        <div class="card-body">
                            <div class="experiment-event event-experiment">
                                <div class="fw-bold">Experimento: Acelerador de Partículas</div>
                                <small>15/10/2023 - 09:00 às 17:00 | Laboratório 4B</small>
                            </div>
                            
                            <div class="experiment-event event-analysis">
                                <div class="fw-bold">Análise de Dados: Projeto Genoma</div>
                                <small>16/10/2023 - 14:00 às 18:00 | Sala de Análise</small>
                            </div>
                            
                            <div class="experiment-event event-publication">
                                <div class="fw-bold">Submissão de Artigo: Nature</div>
                                <small>18/10/2023 - Prazo final</small>
                            </div>
                            
                            <div class="experiment-event event-deadline">
                                <div class="fw-bold">Relatório Trimestral: FAPESP</div>
                                <small>20/10/2023 - Prazo de entrega</small>
                            </div>
                            
                            <div class="experiment-event event-experiment">
                                <div class="fw-bold">Conferência: Inovação Científica</div>
                                <small>25/10/2023 - 08:00 às 18:00 | Auditório Principal</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Informações Adicionais -->
            <div class="row">
                <!-- Publicações Recentes -->
                <div class="col-xl-6 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom">
                            <h5 class="mb-0">Publicações Recentes</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <i class="bi bi-journal-text text-primary" style="font-size: 1.5rem;"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold">Nature</div>
                                            <div class="publication-stats">
                                                <div class="publication-stat">
                                                    <div class="publication-stat-value"><span class="citation-indicator citation-high"></span>42</div>
                                                    <div class="publication-stat-label">Citações</div>
                                                </div>
                                                <div class="publication-stat">
                                                    <div class="publication-stat-value">8.5</div>
                                                    <div class="publication-stat-label">Impacto</div>
                                                </div>
                                                <div class="publication-stat">
                                                    <div class="publication-stat-value">3</div>
                                                    <div class="publication-stat-label">Artigos</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <i class="bi bi-journal-text text-success" style="font-size: 1.5rem;"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold">Science</div>
                                            <div class="publication-stats">
                                                <div class="publication-stat">
                                                    <div class="publication-stat-value"><span class="citation-indicator citation-high"></span>38</div>
                                                    <div class="publication-stat-label">Citações</div>
                                                </div>
                                                <div class="publication-stat">
                                                    <div class="publication-stat-value">7.8</div>
                                                    <div class="publication-stat-label">Impacto</div>
                                                </div>
                                                <div class="publication-stat">
                                                    <div class="publication-stat-value">2</div>
                                                    <div class="publication-stat-label">Artigos</div>
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
                                            <i class="bi bi-journal-text text-warning" style="font-size: 1.5rem;"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold">Cell</div>
                                            <div class="publication-stats">
                                                <div class="publication-stat">
                                                    <div class="publication-stat-value"><span class="citation-indicator citation-medium"></span>24</div>
                                                    <div class="publication-stat-label">Citações</div>
                                                </div>
                                                <div class="publication-stat">
                                                    <div class="publication-stat-value">6.2</div>
                                                    <div class="publication-stat-label">Impacto</div>
                                                </div>
                                                <div class="publication-stat">
                                                    <div class="publication-stat-value">1</div>
                                                    <div class="publication-stat-label">Artigos</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <i class="bi bi-journal-text text-info" style="font-size: 1.5rem;"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold">IEEE</div>
                                            <div class="publication-stats">
                                                <div class="publication-stat">
                                                    <div class="publication-stat-value"><span class="citation-indicator citation-low"></span>15</div>
                                                    <div class="publication-stat-label">Citações</div>
                                                </div>
                                                <div class="publication-stat">
                                                    <div class="publication-stat-value">4.8</div>
                                                    <div class="publication-stat-label">Impacto</div>
                                                </div>
                                                <div class="publication-stat">
                                                    <div class="publication-stat-value">4</div>
                                                    <div class="publication-stat-label">Artigos</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Alertas Científicos -->
                <div class="col-xl-6 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom">
                            <h5 class="mb-0">Alertas e Oportunidades</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-warning d-flex align-items-center mb-3">
                                <i class="bi bi-exclamation-triangle me-3"></i>
                                <div>
                                    <strong>Equipamento crítico</strong> requer calibração urgente
                                    <div class="mt-1">
                                        <a href="#" class="btn btn-sm btn-warning">Agendar manutenção</a>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="alert alert-info d-flex align-items-center mb-3">
                                <i class="bi bi-calendar-check me-3"></i>
                                <div>
                                    <strong>Prazo para submissão</strong> de artigo em 3 dias
                                    <div class="mt-1">
                                        <a href="#" class="btn btn-sm btn-info">Ver detalhes</a>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="alert alert-success d-flex align-items-center mb-3">
                                <i class="bi bi-award me-3"></i>
                                <div>
                                    <strong>Novo financiamento</strong> aprovado - R$ 2.5 milhões
                                    <div class="mt-1">
                                        <small class="text-muted">Projeto: Nanotecnologia Aplicada</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="alert alert-danger d-flex align-items-center mb-0">
                                <i class="bi bi-shield-exclamation me-3"></i>
                                <div>
                                    <strong>Resultados anômalos</strong> detectados no experimento
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
            // Gráfico de Produção Científica
            const scientificProductionCtx = document.getElementById('scientificProductionChart').getContext('2d');
            const scientificProductionChart = new Chart(scientificProductionCtx, {
                type: 'bar',
                data: {
                    labels: ['2019', '2020', '2021', '2022', '2023'],
                    datasets: [{
                        label: 'Artigos Publicados',
                        data: [18, 22, 28, 35, 42],
                        backgroundColor: 'rgba(58, 134, 255, 0.7)',
                        borderColor: 'rgba(58, 134, 255, 1)',
                        borderWidth: 1
                    }, {
                        label: 'Citações Recebidas',
                        data: [245, 389, 512, 678, 845],
                        backgroundColor: 'rgba(131, 56, 236, 0.7)',
                        borderColor: 'rgba(131, 56, 236, 1)',
                        borderWidth: 1
                    }, {
                        label: 'Projetos Concluídos',
                        data: [8, 12, 15, 18, 23],
                        backgroundColor: 'rgba(255, 0, 110, 0.7)',
                        borderColor: 'rgba(255, 0, 110, 1)',
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
            
            // Gráfico de Distribuição por Área de Pesquisa
            const researchAreaCtx = document.getElementById('researchAreaChart').getContext('2d');
            const researchAreaChart = new Chart(researchAreaCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Ciências Biológicas', 'Física', 'Química', 'Computação', 'Engenharia', 'Outros'],
                    datasets: [{
                        data: [28, 22, 18, 15, 12, 5],
                        backgroundColor: [
                            '#3A86FF',
                            '#8338EC',
                            '#FF006E',
                            '#FB5607',
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
            nexusFlow.showNotification('Exportando dados científicos...', 'info');
            setTimeout(() => {
                nexusFlow.showNotification('Dados exportados com sucesso!', 'success');
            }, 2000);
        }
        
        function refreshDashboard() {
            nexusFlow.showNotification('Atualizando dashboard científico...', 'info');
            setTimeout(() => {
                nexusFlow.showNotification('Dashboard atualizado!', 'success');
                // Em uma aplicação real, aqui seria feita uma requisição para atualizar os dados
                // Por simplicidade, apenas recarregamos a página
                location.reload();
            }, 1000);
        }
        
        // Definir papel como ciência
        localStorage.setItem('userRole', 'ciencia_pesquisa');
    </script>
</body>
</html>