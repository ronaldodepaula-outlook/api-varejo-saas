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
    <title>Dashboard Imobiliário</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #2980B9;
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
        
        .metric-icon.primary { background: rgba(41, 128, 185, 0.1); color: var(--primary-color); }
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
        
        .property-card {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 15px;
            transition: transform 0.2s;
        }
        
        .property-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .property-image {
            height: 120px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
        }
        
        .property-info {
            padding: 15px;
        }
        
        .property-price {
            font-size: 1.2rem;
            font-weight: bold;
            color: var(--primary-color);
        }
        
        .property-address {
            font-size: 0.85rem;
            color: #6c757d;
            margin-bottom: 10px;
        }
        
        .property-features {
            display: flex;
            gap: 10px;
            font-size: 0.8rem;
        }
        
        .feature {
            display: flex;
            align-items: center;
            gap: 4px;
        }
        
        .visit-schedule {
            padding: 10px;
            border-left: 4px solid transparent;
            margin-bottom: 8px;
            border-radius: 5px;
            background: #f8f9fa;
        }
        
        .visit-schedule.today {
            border-left-color: var(--danger-color);
            background: rgba(231, 76, 60, 0.05);
        }
        
        .visit-schedule.tomorrow {
            border-left-color: var(--warning-color);
            background: rgba(243, 156, 18, 0.05);
        }
        
        .visit-schedule.upcoming {
            border-left-color: var(--primary-color);
            background: rgba(41, 128, 185, 0.05);
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
                        <li class="breadcrumb-item active">Imobiliário</li>
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
                        <li><h6 class="dropdown-header">Alertas Imobiliários</h6></li>
                        <li><a class="dropdown-item" href="#">3 visitas agendadas para hoje</a></li>
                        <li><a class="dropdown-item" href="#">Contrato pendente de assinatura</a></li>
                        <li><a class="dropdown-item" href="#">Novo imóvel cadastrado</a></li>
                        <li><a class="dropdown-item" href="#">Proposta recebida - Casa Jardins</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-center" href="#">Ver todos</a></li>
                    </ul>
                </div>
                
                <!-- Dropdown do Usuário -->
                <div class="dropdown user-dropdown">
                    <div class="user-avatar" data-bs-toggle="dropdown" aria-expanded="false">
                        I
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><h6 class="dropdown-header user-name">Corretor Imobiliário</h6></li>
                        <li><small class="dropdown-header text-muted user-email">imoveis@empresa.com</small></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="perfil.html"><i class="bi bi-person me-2"></i>Meu Perfil</a></li>
                        <li><a class="dropdown-item" href="#"><i class="bi bi-house me-2"></i>Meus Imóveis</a></li>
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
                    <h1 class="page-title">Dashboard Imobiliário</h1>
                    <p class="page-subtitle">Gestão de imóveis, vendas e clientes</p>
                </div>
                <div>
                    <button class="btn btn-outline-primary me-2" onclick="exportReport()">
                        <i class="bi bi-file-earmark-spreadsheet me-2"></i>Relatório Mensal
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
                            <i class="bi bi-house"></i>
                        </div>
                        <div class="metric-value">42</div>
                        <div class="metric-label">Imóveis Ativos</div>
                        <div class="metric-change positive">
                            <i class="bi bi-arrow-up"></i> +5 este mês
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="metric-card success">
                        <div class="metric-icon success">
                            <i class="bi bi-currency-dollar"></i>
                        </div>
                        <div class="metric-value">R$ 2.8M</div>
                        <div class="metric-label">Volume de Vendas</div>
                        <div class="metric-change positive">
                            <i class="bi bi-arrow-up"></i> +15% vs mês anterior
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="metric-card warning">
                        <div class="metric-icon warning">
                            <i class="bi bi-calendar-check"></i>
                        </div>
                        <div class="metric-value">18</div>
                        <div class="metric-label">Visitas Agendadas</div>
                        <div class="metric-change positive">
                            <i class="bi bi-arrow-up"></i> +7 esta semana
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="metric-card danger">
                        <div class="metric-icon danger">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <div class="metric-value">12</div>
                        <div class="metric-label">Contratos Pendentes</div>
                        <div class="metric-change negative">
                            <i class="bi bi-exclamation-triangle"></i> 3 urgentes
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Gráficos e Dados -->
            <div class="row mb-4">
                <!-- Performance de Vendas -->
                <div class="col-xl-8 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Performance de Vendas e Aluguéis</h5>
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
                            <canvas id="salesChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
                
                <!-- Distribuição de Imóveis -->
                <div class="col-xl-4 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom">
                            <h5 class="mb-0">Distribuição por Tipo</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="propertyTypeChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Imóveis em Destaque e Visitas -->
            <div class="row mb-4">
                <!-- Imóveis em Destaque -->
                <div class="col-xl-6 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Imóveis em Destaque</h5>
                            <a href="imoveis.html" class="btn btn-sm btn-outline-primary">Ver todos</a>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="property-card">
                                        <div class="property-image">
                                            <i class="bi bi-house-door"></i>
                                        </div>
                                        <div class="property-info">
                                            <div class="property-price">R$ 850.000</div>
                                            <div class="property-address">Casa 4 quartos - Jardins</div>
                                            <div class="property-features">
                                                <div class="feature">
                                                    <i class="bi bi-door-closed"></i>
                                                    <span>4 quartos</span>
                                                </div>
                                                <div class="feature">
                                                    <i class="bi bi-car-front"></i>
                                                    <span>2 vagas</span>
                                                </div>
                                            </div>
                                            <div class="mt-2">
                                                <span class="status-badge status-active">Venda</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <div class="property-card">
                                        <div class="property-image">
                                            <i class="bi bi-building"></i>
                                        </div>
                                        <div class="property-info">
                                            <div class="property-price">R$ 3.200/mês</div>
                                            <div class="property-address">Apto 2 quartos - Centro</div>
                                            <div class="property-features">
                                                <div class="feature">
                                                    <i class="bi bi-door-closed"></i>
                                                    <span>2 quartos</span>
                                                </div>
                                                <div class="feature">
                                                    <i class="bi bi-square"></i>
                                                    <span>65m²</span>
                                                </div>
                                            </div>
                                            <div class="mt-2">
                                                <span class="status-badge status-pending">Aluguel</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <div class="property-card">
                                        <div class="property-image">
                                            <i class="bi bi-house"></i>
                                        </div>
                                        <div class="property-info">
                                            <div class="property-price">R$ 1.250.000</div>
                                            <div class="property-address">Sobrado - Alto de Pinheiros</div>
                                            <div class="property-features">
                                                <div class="feature">
                                                    <i class="bi bi-door-closed"></i>
                                                    <span>3 quartos</span>
                                                </div>
                                                <div class="feature">
                                                    <i class="bi bi-car-front"></i>
                                                    <span>3 vagas</span>
                                                </div>
                                            </div>
                                            <div class="mt-2">
                                                <span class="status-badge status-active">Venda</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <div class="property-card">
                                        <div class="property-image">
                                            <i class="bi bi-building"></i>
                                        </div>
                                        <div class="property-info">
                                            <div class="property-price">R$ 4.500/mês</div>
                                            <div class="property-address">Cobertura - Moema</div>
                                            <div class="property-features">
                                                <div class="feature">
                                                    <i class="bi bi-door-closed"></i>
                                                    <span>3 quartos</span>
                                                </div>
                                                <div class="feature">
                                                    <i class="bi bi-car-front"></i>
                                                    <span>2 vagas</span>
                                                </div>
                                            </div>
                                            <div class="mt-2">
                                                <span class="status-badge status-warning">Reservado</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Agenda de Visitas -->
                <div class="col-xl-6 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Agenda de Visitas</h5>
                            <a href="agenda.html" class="btn btn-sm btn-outline-primary">Nova visita</a>
                        </div>
                        <div class="card-body">
                            <div class="visit-schedule today">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>Casa Jardins - R$ 850.000</strong>
                                        <div class="small">Cliente: João Silva</div>
                                        <div class="small text-muted">14:30 - 15:30</div>
                                    </div>
                                    <span class="badge bg-danger">Hoje</span>
                                </div>
                            </div>
                            
                            <div class="visit-schedule today">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>Apto Centro - R$ 3.200/mês</strong>
                                        <div class="small">Cliente: Maria Santos</div>
                                        <div class="small text-muted">16:00 - 16:45</div>
                                    </div>
                                    <span class="badge bg-danger">Hoje</span>
                                </div>
                            </div>
                            
                            <div class="visit-schedule tomorrow">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>Sobrado Pinheiros - R$ 1.250.000</strong>
                                        <div class="small">Cliente: Carlos Oliveira</div>
                                        <div class="small text-muted">10:00 - 11:00</div>
                                    </div>
                                    <span class="badge bg-warning">Amanhã</span>
                                </div>
                            </div>
                            
                            <div class="visit-schedule upcoming">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>Cobertura Moema - R$ 4.500/mês</strong>
                                        <div class="small">Cliente: Ana Costa</div>
                                        <div class="small text-muted">Sexta, 15:00 - 16:00</div>
                                    </div>
                                    <span class="badge bg-primary">Sexta</span>
                                </div>
                            </div>
                            
                            <div class="mt-3">
                                <h6 class="mb-3">Estatísticas de Visitas</h6>
                                <div class="row text-center">
                                    <div class="col-4">
                                        <div class="metric-value text-primary">12</div>
                                        <div class="metric-label">Esta semana</div>
                                    </div>
                                    <div class="col-4">
                                        <div class="metric-value text-success">42%</div>
                                        <div class="metric-label">Taxa de conversão</div>
                                    </div>
                                    <div class="col-4">
                                        <div class="metric-value text-warning">8</div>
                                        <div class="metric-label">Agendadas</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tabelas de Dados -->
            <div class="row">
                <!-- Propostas Recentes -->
                <div class="col-xl-6 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Propostas Recentes</h5>
                            <a href="propostas.html" class="btn btn-sm btn-outline-primary">Ver todas</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-custom mb-0">
                                    <thead>
                                        <tr>
                                            <th>Imóvel</th>
                                            <th>Cliente</th>
                                            <th>Valor</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div class="fw-semibold">Casa Jardins</div>
                                                <small class="text-muted">4 quartos, 2 vagas</small>
                                            </td>
                                            <td>João Silva</td>
                                            <td>R$ 820.000</td>
                                            <td><span class="status-badge status-pending">Em análise</span></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="fw-semibold">Apto Centro</div>
                                                <small class="text-muted">2 quartos, 65m²</small>
                                            </td>
                                            <td>Maria Santos</td>
                                            <td>R$ 3.000/mês</td>
                                            <td><span class="status-badge status-active">Aceita</span></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="fw-semibold">Sobrado Pinheiros</div>
                                                <small class="text-muted">3 quartos, 3 vagas</small>
                                            </td>
                                            <td>Carlos Oliveira</td>
                                            <td>R$ 1.180.000</td>
                                            <td><span class="status-badge status-warning">Negociando</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Contratos Pendentes -->
                <div class="col-xl-6 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Contratos Pendentes</h5>
                            <a href="contratos.html" class="btn btn-sm btn-outline-primary">Ver todos</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-custom mb-0">
                                    <thead>
                                        <tr>
                                            <th>Contrato</th>
                                            <th>Imóvel</th>
                                            <th>Vencimento</th>
                                            <th>Ação</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div class="fw-semibold">#CT-2023-045</div>
                                                <small class="text-muted">Venda - Casa Jardins</small>
                                            </td>
                                            <td>R$ 850.000</td>
                                            <td>20/06/2023</td>
                                            <td><button class="btn btn-sm btn-outline-primary">Assinar</button></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="fw-semibold">#CT-2023-046</div>
                                                <small class="text-muted">Aluguel - Apto Centro</small>
                                            </td>
                                            <td>R$ 3.200/mês</td>
                                            <td>22/06/2023</td>
                                            <td><button class="btn btn-sm btn-outline-warning">Revisar</button></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="fw-semibold">#CT-2023-047</div>
                                                <small class="text-muted">Venda - Sobrado Pinheiros</small>
                                            </td>
                                            <td>R$ 1.250.000</td>
                                            <td>25/06/2023</td>
                                            <td><button class="btn btn-sm btn-outline-success">Finalizar</button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Alertas do Setor Imobiliário -->
            <div class="row">
                <div class="col-12">
                    <div class="card-custom">
                        <div class="card-header-custom">
                            <h5 class="mb-0">Alertas e Oportunidades</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="alert alert-warning d-flex align-items-center mb-3">
                                        <i class="bi bi-calendar me-3"></i>
                                        <div>
                                            <strong>3 visitas</strong> agendadas para hoje
                                            <div class="mt-1">
                                                <a href="agenda.html" class="btn btn-sm btn-warning">Ver agenda</a>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="alert alert-info d-flex align-items-center mb-3">
                                        <i class="bi bi-graph-up me-3"></i>
                                        <div>
                                            <strong>Mercado em alta</strong> na região dos Jardins
                                            <div class="mt-1">
                                                <small class="text-muted">Valor médio aumentou 8% este mês</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="alert alert-success d-flex align-items-center mb-3">
                                        <i class="bi bi-house-check me-3"></i>
                                        <div>
                                            <strong>2 imóveis</strong> vendidos esta semana
                                            <div class="mt-1">
                                                <small class="text-muted">Comissão total: R$ 42.000</small>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="alert alert-danger d-flex align-items-center mb-0">
                                        <i class="bi bi-clock me-3"></i>
                                        <div>
                                            <strong>1 contrato</strong> com assinatura pendente
                                            <div class="mt-1">
                                                <a href="contratos.html" class="btn btn-sm btn-danger">Resolver agora</a>
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

        // Gráficos específicos do segmento imobiliário
        document.addEventListener('DOMContentLoaded', function() {
            // Gráfico de Performance
            const salesCtx = document.getElementById('salesChart').getContext('2d');
            const salesChart = new Chart(salesCtx, {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun'],
                    datasets: [{
                        label: 'Vendas (R$)',
                        data: [1200000, 1850000, 1500000, 2100000, 2450000, 2800000],
                        backgroundColor: '#2980B9',
                        borderColor: '#2471A3',
                        borderWidth: 1
                    }, {
                        label: 'Aluguéis (R$)',
                        data: [85000, 92000, 78000, 105000, 128000, 145000],
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
                            ticks: {
                                callback: function(value) {
                                    if (value >= 1000000) {
                                        return 'R$ ' + (value / 1000000).toFixed(1) + 'M';
                                    } else if (value >= 1000) {
                                        return 'R$ ' + (value / 1000).toFixed(0) + 'K';
                                    }
                                    return 'R$ ' + value;
                                }
                            }
                        }
                    }
                }
            });
            
            // Gráfico de Tipos de Imóveis
            const propertyTypeCtx = document.getElementById('propertyTypeChart').getContext('2d');
            const propertyTypeChart = new Chart(propertyTypeCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Apartamentos', 'Casas', 'Sobrados', 'Comercial', 'Terrenos'],
                    datasets: [{
                        data: [35, 28, 15, 12, 10],
                        backgroundColor: [
                            '#2980B9',
                            '#27AE60',
                            '#F39C12',
                            '#E74C3C',
                            '#8E44AD'
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
            nexusFlow.showNotification('Gerando relatório imobiliário...', 'info');
            setTimeout(() => {
                nexusFlow.showNotification('Relatório exportado com sucesso!', 'success');
            }, 1500);
        }
        
        function refreshDashboard() {
            nexusFlow.showNotification('Atualizando dados imobiliários...', 'info');
            setTimeout(() => {
                nexusFlow.showNotification('Dados atualizados!', 'success');
            }, 1000);
        }
        
        // Definir papel como corretor imobiliário
        localStorage.setItem('userRole', 'real_estate_agent');
        
        // Simulação de atualização em tempo real das visitas
        setInterval(function() {
            // Simular mudança de status de visitas (apenas visual)
            const todayVisits = document.querySelectorAll('.visit-schedule.today');
            if (todayVisits.length > 0) {
                const randomVisit = todayVisits[Math.floor(Math.random() * todayVisits.length)];
                if (randomVisit) {
                    randomVisit.classList.remove('today');
                    randomVisit.classList.add('upcoming');
                    const badge = randomVisit.querySelector('.badge');
                    if (badge) {
                        badge.className = 'badge bg-primary';
                        badge.textContent = 'Concluída';
                    }
                }
            }
        }, 60000); // Atualiza a cada 60 segundos
    </script>
</body>
</html>