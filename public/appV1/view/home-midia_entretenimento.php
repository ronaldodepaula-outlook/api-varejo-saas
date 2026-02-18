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
    <title>Dashboard Mídia & Entretenimento</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #9B59B6;
            --secondary-color: #E67E22;
            --warning-color: #F1C40F;
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
        
        .metric-icon.primary { background: rgba(155, 89, 182, 0.1); color: var(--primary-color); }
        .metric-icon.success { background: rgba(230, 126, 34, 0.1); color: var(--secondary-color); }
        .metric-icon.warning { background: rgba(241, 196, 15, 0.1); color: var(--warning-color); }
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
        
        .status-active { background: rgba(230, 126, 34, 0.1); color: var(--secondary-color); }
        .status-pending { background: rgba(241, 196, 15, 0.1); color: var(--warning-color); }
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
        
        .content-card {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 15px;
            transition: transform 0.2s;
        }
        
        .content-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .content-image {
            height: 120px;
            background: linear-gradient(135deg, #9B59B6 0%, #8E44AD 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
        }
        
        .content-info {
            padding: 15px;
        }
        
        .content-title {
            font-size: 1rem;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .content-meta {
            font-size: 0.85rem;
            color: #6c757d;
            margin-bottom: 10px;
        }
        
        .content-stats {
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
        
        .schedule-item.live {
            border-left-color: var(--danger-color);
            background: rgba(231, 76, 60, 0.05);
        }
        
        .schedule-item.upcoming {
            border-left-color: var(--warning-color);
            background: rgba(241, 196, 15, 0.05);
        }
        
        .schedule-item.completed {
            border-left-color: var(--secondary-color);
            background: rgba(230, 126, 34, 0.05);
        }
        
        .audience-metric {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 8px;
            background: #f8f9fa;
        }
        
        .audience-metric.primary { border-left: 4px solid var(--primary-color); }
        .audience-metric.success { border-left: 4px solid var(--secondary-color); }
        .audience-metric.warning { border-left: 4px solid var(--warning-color); }
        
        .trend-indicator {
            font-size: 0.75rem;
            padding: 2px 6px;
            border-radius: 12px;
            margin-left: auto;
        }
        
        .trend-up { background: rgba(39, 174, 96, 0.1); color: #27AE60; }
        .trend-down { background: rgba(231, 76, 60, 0.1); color: #E74C3C; }
        
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
                        <li class="breadcrumb-item active">Mídia & Entretenimento</li>
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
                        <li><h6 class="dropdown-header">Alertas de Mídia</h6></li>
                        <li><a class="dropdown-item" href="#">Novo recorde de audiência</a></li>
                        <li><a class="dropdown-item" href="#">Conteúdo viralizando nas redes</a></li>
                        <li><a class="dropdown-item" href="#">Transmissão ao vivo em 30min</a></li>
                        <li><a class="dropdown-item" href="#">Problema técnico no servidor</a></li>
                        <li><a class="dropdown-item" href="#">Novo patrocínio confirmado</a></li>
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
                        <li><h6 class="dropdown-header user-name">Gerente de Mídia</h6></li>
                        <li><small class="dropdown-header text-muted user-email">midia@empresa.com</small></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="perfil.html"><i class="bi bi-person me-2"></i>Meu Perfil</a></li>
                        <li><a class="dropdown-item" href="#"><i class="bi bi-camera-reels me-2"></i>Conteúdo</a></li>
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
                    <h1 class="page-title">Dashboard Mídia & Entretenimento</h1>
                    <p class="page-subtitle">Gestão de audiência, conteúdo e performance digital</p>
                </div>
                <div>
                    <button class="btn btn-outline-primary me-2" onclick="exportReport()">
                        <i class="bi bi-file-earmark-spreadsheet me-2"></i>Relatório de Performance
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
                            <i class="bi bi-eye"></i>
                        </div>
                        <div class="metric-value">2.4M</div>
                        <div class="metric-label">Audiência Total</div>
                        <div class="metric-change positive">
                            <i class="bi bi-arrow-up"></i> +15% este mês
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="metric-card success">
                        <div class="metric-icon success">
                            <i class="bi bi-arrow-through-heart"></i>
                        </div>
                        <div class="metric-value">4.8%</div>
                        <div class="metric-label">Taxa de Engajamento</div>
                        <div class="metric-change positive">
                            <i class="bi bi-arrow-up"></i> +0.7% vs semana passada
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="metric-card warning">
                        <div class="metric-icon warning">
                            <i class="bi bi-currency-dollar"></i>
                        </div>
                        <div class="metric-value">R$ 124.5K</div>
                        <div class="metric-label">Receita Publicitária</div>
                        <div class="metric-change positive">
                            <i class="bi bi-arrow-up"></i> +18% vs mês anterior
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="metric-card danger">
                        <div class="metric-icon danger">
                            <i class="bi bi-clock"></i>
                        </div>
                        <div class="metric-value">12:45</div>
                        <div class="metric-label">Tempo Médio Sessão</div>
                        <div class="metric-change negative">
                            <i class="bi bi-arrow-down"></i> -1.2min vs mês anterior
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Gráficos e Dados -->
            <div class="row mb-4">
                <!-- Performance de Audiência -->
                <div class="col-xl-8 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Performance de Audiência e Engajamento</h5>
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
                            <canvas id="audienceChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
                
                <!-- Distribuição de Plataformas -->
                <div class="col-xl-4 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom">
                            <h5 class="mb-0">Audiência por Plataforma</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="platformChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Conteúdo e Programação -->
            <div class="row mb-4">
                <!-- Conteúdo em Destaque -->
                <div class="col-xl-6 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Conteúdo em Alta</h5>
                            <a href="conteudo.html" class="btn btn-sm btn-outline-primary">Ver todos</a>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="content-card">
                                        <div class="content-image" style="background: linear-gradient(135deg, #3498DB 0%, #2980B9 100%);">
                                            <i class="bi bi-play-btn"></i>
                                        </div>
                                        <div class="content-info">
                                            <div class="content-title">Documentário: Vida Selvagem</div>
                                            <div class="content-meta">Natureza • 45min</div>
                                            <div class="content-stats">
                                                <span><i class="bi bi-eye"></i> 245K</span>
                                                <span class="text-success">92% retenção</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <div class="content-card">
                                        <div class="content-image" style="background: linear-gradient(135deg, #E74C3C 0%, #C0392B 100%);">
                                            <i class="bi bi-mic"></i>
                                        </div>
                                        <div class="content-info">
                                            <div class="content-title">Podcast: Tecnologia Hoje</div>
                                            <div class="content-meta">Tech • Ep. 45</div>
                                            <div class="content-stats">
                                                <span><i class="bi bi-headphones"></i> 189K</span>
                                                <span class="text-success">4.8⭐</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <div class="content-card">
                                        <div class="content-image" style="background: linear-gradient(135deg, #2ECC71 0%, #27AE60 100%);">
                                            <i class="bi bi-camera-reels"></i>
                                        </div>
                                        <div class="content-info">
                                            <div class="content-title">Série: Cidade Noturna</div>
                                            <div class="content-meta">Drama • Ep. 8</div>
                                            <div class="content-stats">
                                                <span><i class="bi bi-eye"></i> 312K</span>
                                                <span class="text-success">87% retenção</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <div class="content-card">
                                        <div class="content-image" style="background: linear-gradient(135deg, #F39C12 0%, #D35400 100%);">
                                            <i class="bi bi-music-note-beamed"></i>
                                        </div>
                                        <div class="content-info">
                                            <div class="content-title">Show Ao Vivo: Rock Festival</div>
                                            <div class="content-meta">Música • 2h15min</div>
                                            <div class="content-stats">
                                                <span><i class="bi bi-eye"></i> 458K</span>
                                                <span class="text-success">94% retenção</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Programação e Eventos -->
                <div class="col-xl-6 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Programação ao Vivo</h5>
                            <a href="programacao.html" class="btn btn-sm btn-outline-primary">Grade completa</a>
                        </div>
                        <div class="card-body">
                            <div class="schedule-item live">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>Notícias da Manhã - Edição Especial</strong>
                                        <div class="small">Jornalismo • 45.2K espectadores</div>
                                        <div class="small text-muted">08:00 - 09:00</div>
                                    </div>
                                    <span class="badge bg-danger">AO VIVO</span>
                                </div>
                            </div>
                            
                            <div class="schedule-item upcoming">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>Entrevista Exclusiva: Presidente</strong>
                                        <div class="small">Política • 28.7K aguardando</div>
                                        <div class="small text-muted">11:00 - 12:00</div>
                                    </div>
                                    <span class="badge bg-warning">EM BREVE</span>
                                </div>
                            </div>
                            
                            <div class="schedule-item upcoming">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>Debate: Eleições 2024</strong>
                                        <div class="small">Política • 15.3K aguardando</div>
                                        <div class="small text-muted">14:00 - 16:00</div>
                                    </div>
                                    <span class="badge bg-primary">ÀS 14:00</span>
                                </div>
                            </div>
                            
                            <div class="schedule-item completed">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>Programa de Culinária</strong>
                                        <div class="small">Entretenimento • 38.9K espectadores</div>
                                        <div class="small text-muted">07:00 - 08:00</div>
                                    </div>
                                    <span class="badge bg-success">CONCLUÍDO</span>
                                </div>
                            </div>
                            
                            <div class="mt-3">
                                <h6 class="mb-3">Métricas de Engajamento</h6>
                                <div class="audience-metric primary">
                                    <div class="me-3">
                                        <i class="bi bi-chat-heart" style="font-size: 1.5rem; color: #9B59B6;"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <strong>Curtidas</strong>
                                        <div class="small text-muted">24.8K hoje</div>
                                    </div>
                                    <span class="trend-indicator trend-up">+12%</span>
                                </div>
                                
                                <div class="audience-metric success">
                                    <div class="me-3">
                                        <i class="bi bi-share" style="font-size: 1.5rem; color: #E67E22;"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <strong>Compartilhamentos</strong>
                                        <div class="small text-muted">8.4K hoje</div>
                                    </div>
                                    <span class="trend-indicator trend-up">+18%</span>
                                </div>
                                
                                <div class="audience-metric warning">
                                    <div class="me-3">
                                        <i class="bi bi-chat-dots" style="font-size: 1.5rem; color: #F1C40F;"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <strong>Comentários</strong>
                                        <div class="small text-muted">5.7K hoje</div>
                                    </div>
                                    <span class="trend-indicator trend-up">+7%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Análise de Audiência -->
            <div class="row mb-4">
                <div class="col-xl-8 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Análise Demográfica da Audiência</h5>
                            <div class="btn-group btn-group-sm" role="group">
                                <input type="radio" class="btn-check" name="demographic" id="age" checked>
                                <label class="btn btn-outline-primary" for="age">Idade</label>
                                
                                <input type="radio" class="btn-check" name="demographic" id="gender">
                                <label class="btn btn-outline-primary" for="gender">Gênero</label>
                                
                                <input type="radio" class="btn-check" name="demographic" id="location">
                                <label class="btn btn-outline-primary" for="location">Localização</label>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="demographicChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
                
                <!-- Campanhas Publicitárias -->
                <div class="col-xl-4 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Campanhas Ativas</h5>
                            <span class="badge bg-primary">5 campanhas</span>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <strong>Nova Série - Lançamento</strong>
                                    <span class="badge bg-success">Ativa</span>
                                </div>
                                <div class="progress mb-3" style="height: 8px;">
                                    <div class="progress-bar bg-primary" style="width: 75%"></div>
                                </div>
                                <div class="small text-muted">R$ 45K / R$ 60K • 15 dias restantes</div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <strong>Patrocínio Esportivo</strong>
                                    <span class="badge bg-warning">Pendente</span>
                                </div>
                                <div class="progress mb-3" style="height: 8px;">
                                    <div class="progress-bar bg-warning" style="width: 40%"></div>
                                </div>
                                <div class="small text-muted">R$ 20K / R$ 50K • Aprovação pendente</div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <strong>Evento de Verão</strong>
                                    <span class="badge bg-success">Ativa</span>
                                </div>
                                <div class="progress mb-3" style="height: 8px;">
                                    <div class="progress-bar bg-success" style="width: 90%"></div>
                                </div>
                                <div class="small text-muted">R$ 90K / R$ 100K • 5 dias restantes</div>
                            </div>
                            
                            <div class="mt-4">
                                <h6 class="mb-3">Performance de Campanhas</h6>
                                <div class="row text-center">
                                    <div class="col-4">
                                        <div class="metric-value text-primary">3.2%</div>
                                        <div class="metric-label">CTR</div>
                                    </div>
                                    <div class="col-4">
                                        <div class="metric-value text-success">R$ 1.45</div>
                                        <div class="metric-label">CPC</div>
                                    </div>
                                    <div class="col-4">
                                        <div class="metric-value text-warning">245K</div>
                                        <div class="metric-label">Impressões</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Alertas do Setor de Mídia -->
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
                                        <i class="bi bi-graph-up-arrow me-3"></i>
                                        <div>
                                            <strong>Conteúdo viralizando</strong> nas redes sociais
                                            <div class="mt-1">
                                                <a href="#" class="btn btn-sm btn-warning">Analisar performance</a>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="alert alert-info d-flex align-items-center mb-3">
                                        <i class="bi bi-camera-video me-3"></i>
                                        <div>
                                            <strong>Transmissão ao vivo</strong> em 30 minutos
                                            <div class="mt-1">
                                                <small class="text-muted">28.7K pessoas aguardando</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="alert alert-success d-flex align-items-center mb-3">
                                        <i class="bi bi-currency-dollar me-3"></i>
                                        <div>
                                            <strong>Nova campanha</strong> superando expectativas
                                            <div class="mt-1">
                                                <small class="text-muted">ROI de 285% na primeira semana</small>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="alert alert-danger d-flex align-items-center mb-0">
                                        <i class="bi bi-exclamation-triangle me-3"></i>
                                        <div>
                                            <strong>Problema técnico</strong> no servidor de streaming
                                            <div class="mt-1">
                                                <a href="#" class="btn btn-sm btn-danger">Resolver agora</a>
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

        // Gráficos específicos do segmento de mídia e entretenimento
        document.addEventListener('DOMContentLoaded', function() {
            // Gráfico de Performance de Audiência
            const audienceCtx = document.getElementById('audienceChart').getContext('2d');
            const audienceChart = new Chart(audienceCtx, {
                type: 'line',
                data: {
                    labels: ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'],
                    datasets: [{
                        label: 'Visualizações (K)',
                        data: [185, 210, 245, 268, 312, 458, 385],
                        borderColor: '#9B59B6',
                        backgroundColor: 'rgba(155, 89, 182, 0.1)',
                        tension: 0.4,
                        fill: true,
                        yAxisID: 'y'
                    }, {
                        label: 'Taxa de Engajamento (%)',
                        data: [4.2, 4.5, 4.8, 4.6, 5.1, 5.8, 5.2],
                        borderColor: '#E67E22',
                        backgroundColor: 'rgba(230, 126, 34, 0.1)',
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
                                text: 'Visualizações (Mil)'
                            }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            title: {
                                display: true,
                                text: 'Engajamento (%)'
                            },
                            grid: {
                                drawOnChartArea: false,
                            },
                            min: 0,
                            max: 10
                        }
                    }
                }
            });
            
            // Gráfico de Plataformas
            const platformCtx = document.getElementById('platformChart').getContext('2d');
            const platformChart = new Chart(platformCtx, {
                type: 'doughnut',
                data: {
                    labels: ['YouTube', 'Instagram', 'Facebook', 'TikTok', 'Site', 'Outros'],
                    datasets: [{
                        data: [35, 25, 15, 12, 8, 5],
                        backgroundColor: [
                            '#FF0000',
                            '#E4405F',
                            '#1877F2',
                            '#000000',
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
            
            // Gráfico Demográfico
            const demographicCtx = document.getElementById('demographicChart').getContext('2d');
            const demographicChart = new Chart(demographicCtx, {
                type: 'bar',
                data: {
                    labels: ['18-24', '25-34', '35-44', '45-54', '55-64', '65+'],
                    datasets: [{
                        label: 'Distribuição por Idade',
                        data: [25, 35, 20, 12, 5, 3],
                        backgroundColor: '#9B59B6',
                        borderColor: '#8E44AD',
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
                                text: 'Percentual (%)'
                            },
                            ticks: {
                                callback: function(value) {
                                    return value + '%';
                                }
                            }
                        }
                    }
                }
            });
        });
        
        function exportReport() {
            nexusFlow.showNotification('Gerando relatório de mídia...', 'info');
            setTimeout(() => {
                nexusFlow.showNotification('Relatório exportado com sucesso!', 'success');
            }, 1500);
        }
        
        function refreshDashboard() {
            nexusFlow.showNotification('Atualizando dados de mídia...', 'info');
            setTimeout(() => {
                nexusFlow.showNotification('Dados atualizados!', 'success');
            }, 1000);
        }
        
        // Definir papel como gerente de mídia
        localStorage.setItem('userRole', 'media_manager');
        
        // Simulação de atualização em tempo real da programação
        setInterval(function() {
            // Simular mudança de status de programação (apenas visual)
            const liveItems = document.querySelectorAll('.schedule-item.live');
            if (liveItems.length > 0) {
                const randomItem = liveItems[Math.floor(Math.random() * liveItems.length)];
                if (randomItem) {
                    randomItem.classList.remove('live');
                    randomItem.classList.add('completed');
                    const badge = randomItem.querySelector('.badge');
                    if (badge) {
                        badge.className = 'badge bg-success';
                        badge.textContent = 'CONCLUÍDO';
                    }
                }
            }
        }, 60000); // Atualiza a cada 60 segundos
    </script>
</body>
</html>