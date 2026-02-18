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
    <title>Dashboard Educação - NexusFlow</title>
    
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
            --education-color: #4361EE;
            --student-color: #7209B7;
            --teacher-color: #F72585;
            --course-color: #4CC9F0;
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
            color: var(--education-color);
            margin-right: 1rem;
        }
        
        .breadcrumb-custom {
            margin-bottom: 0;
        }
        
        .breadcrumb-custom .breadcrumb-item.active {
            color: var(--education-color);
            font-weight: 600;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--education-color);
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
            color: var(--education-color);
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
        
        .metric-card.education {
            border-left: 4px solid var(--education-color);
        }
        
        .metric-card.student {
            border-left: 4px solid var(--student-color);
        }
        
        .metric-card.teacher {
            border-left: 4px solid var(--teacher-color);
        }
        
        .metric-card.course {
            border-left: 4px solid var(--course-color);
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
        
        .metric-icon.education {
            background: rgba(67, 97, 238, 0.1);
            color: var(--education-color);
        }
        
        .metric-icon.student {
            background: rgba(114, 9, 183, 0.1);
            color: var(--student-color);
        }
        
        .metric-icon.teacher {
            background: rgba(247, 37, 133, 0.1);
            color: var(--teacher-color);
        }
        
        .metric-icon.course {
            background: rgba(76, 201, 240, 0.1);
            color: var(--course-color);
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
        
        .status-inactive {
            background: rgba(108, 117, 125, 0.1);
            color: #6c757d;
        }
        
        .status-warning {
            background: rgba(247, 37, 133, 0.1);
            color: var(--teacher-color);
        }
        
        .level-badge {
            padding: 0.35rem 0.65rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .level-basic {
            background: rgba(67, 97, 238, 0.1);
            color: var(--education-color);
        }
        
        .level-intermediate {
            background: rgba(114, 9, 183, 0.1);
            color: var(--student-color);
        }
        
        .level-advanced {
            background: rgba(247, 37, 133, 0.1);
            color: var(--teacher-color);
        }
        
        .level-expert {
            background: rgba(76, 201, 240, 0.1);
            color: var(--course-color);
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
            background: var(--course-color);
        }
        
        .progress-average {
            background: var(--warning-color);
        }
        
        .progress-poor {
            background: var(--danger-color);
        }
        
        .student-priority-high {
            border-left: 4px solid var(--danger-color);
        }
        
        .student-priority-medium {
            border-left: 4px solid var(--warning-color);
        }
        
        .student-priority-low {
            border-left: 4px solid var(--success-color);
        }
        
        .course-stats {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }
        
        .course-stat {
            text-align: center;
        }
        
        .course-stat-value {
            font-weight: 700;
            font-size: 1.1rem;
        }
        
        .course-stat-label {
            font-size: 0.75rem;
            color: #6c757d;
        }
        
        .calendar-event {
            padding: 0.5rem;
            border-radius: 5px;
            margin-bottom: 0.5rem;
            font-size: 0.85rem;
        }
        
        .event-exam {
            background: rgba(247, 37, 133, 0.1);
            border-left: 3px solid var(--teacher-color);
        }
        
        .event-holiday {
            background: rgba(67, 97, 238, 0.1);
            border-left: 3px solid var(--education-color);
        }
        
        .event-meeting {
            background: rgba(114, 9, 183, 0.1);
            border-left: 3px solid var(--student-color);
        }
        
        .event-deadline {
            background: rgba(244, 96, 54, 0.1);
            border-left: 3px solid var(--warning-color);
        }
        
        .attendance-indicator {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-right: 5px;
        }
        
        .attendance-present {
            background: var(--success-color);
        }
        
        .attendance-absent {
            background: var(--danger-color);
        }
        
        .attendance-late {
            background: var(--warning-color);
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
                        <li class="breadcrumb-item active">Educação</li>
                    </ol>
                </nav>
            </div>
            
            <div class="header-right">
                <!-- Notificações -->
                <div class="dropdown">
                    <button class="btn btn-link text-dark position-relative" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-bell" style="font-size: 1.25rem;"></i>
                        <span class="badge bg-danger position-absolute translate-middle rounded-pill" style="font-size: 0.6rem; top: 8px; right: 8px;">8</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><h6 class="dropdown-header">Alertas Educacionais</h6></li>
                        <li><a class="dropdown-item" href="#">5 alunos com baixo rendimento</a></li>
                        <li><a class="dropdown-item" href="#">Prova de Matemática agendada para amanhã</a></li>
                        <li><a class="dropdown-item" href="#">Taxa de evasão aumentou 3%</a></li>
                        <li><a class="dropdown-item" href="#">Novos materiais disponíveis</a></li>
                        <li><a class="dropdown-item" href="#">Reunião de pais às 18h</a></li>
                        <li><a class="dropdown-item" href="#">Inscrições abertas para cursos extracurriculares</a></li>
                        <li><a class="dropdown-item" href="#">Manutenção do laboratório de informática</a></li>
                        <li><a class="dropdown-item" href="#">Relatório de frequência mensal pronto</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-center" href="#">Ver todas</a></li>
                    </ul>
                </div>
                
                <!-- Dropdown do Usuário -->
                <div class="dropdown user-dropdown">
                    <div class="user-avatar" data-bs-toggle="dropdown" aria-expanded="false">
                        E
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><h6 class="dropdown-header user-name">Educação NexusFlow</h6></li>
                        <li><small class="dropdown-header text-muted user-email">educacao@nexusflow.com</small></li>
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
                    <h1 class="page-title">Dashboard Educacional</h1>
                    <p class="page-subtitle">Gestão de alunos, desempenho acadêmico e indicadores educacionais</p>
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
                    <div class="metric-card education">
                        <div class="metric-icon education">
                            <i class="bi bi-people"></i>
                        </div>
                        <div class="metric-value">1,847</div>
                        <div class="metric-label">Alunos Matriculados</div>
                        <div class="metric-change positive">
                            <i class="bi bi-arrow-up"></i> +127 este semestre
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="metric-card student">
                        <div class="metric-icon student">
                            <i class="bi bi-mortarboard"></i>
                        </div>
                        <div class="metric-value">87.3%</div>
                        <div class="metric-label">Taxa de Aprovação</div>
                        <div class="metric-change positive">
                            <i class="bi bi-arrow-up"></i> +2.1% vs semestre anterior
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="metric-card teacher">
                        <div class="metric-icon teacher">
                            <i class="bi bi-person-badge"></i>
                        </div>
                        <div class="metric-value">94</div>
                        <div class="metric-label">Professores Ativos</div>
                        <div class="metric-change positive">
                            <i class="bi bi-arrow-up"></i> +8 este ano
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="metric-card course">
                        <div class="metric-icon course">
                            <i class="bi bi-book"></i>
                        </div>
                        <div class="metric-value">42</div>
                        <div class="metric-label">Cursos Oferecidos</div>
                        <div class="metric-change positive">
                            <i class="bi bi-arrow-up"></i> +5 novos cursos
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Gráficos e Dados -->
            <div class="row mb-4">
                <!-- Gráfico de Desempenho Acadêmico -->
                <div class="col-xl-8 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Desempenho Acadêmico - Últimos 4 Bimestres</h5>
                            <div class="btn-group btn-group-sm" role="group">
                                <input type="radio" class="btn-check" name="period" id="period7" checked>
                                <label class="btn btn-outline-primary" for="period7">Matemática</label>
                                
                                <input type="radio" class="btn-check" name="period" id="period30">
                                <label class="btn btn-outline-primary" for="period30">Português</label>
                                
                                <input type="radio" class="btn-check" name="period" id="period90">
                                <label class="btn btn-outline-primary" for="period90">Ciências</label>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="academicPerformanceChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
                
                <!-- Distribuição por Nível -->
                <div class="col-xl-4 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom">
                            <h5 class="mb-0">Distribuição por Nível de Ensino</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="educationLevelChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tabelas de Dados -->
            <div class="row">
                <!-- Alunos com Baixo Rendimento -->
                <div class="col-xl-6 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Alunos com Baixo Rendimento</h5>
                            <a href="alunos.html" class="btn btn-sm btn-outline-primary">Ver todos</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-custom mb-0">
                                    <thead>
                                        <tr>
                                            <th>Aluno</th>
                                            <th>Turma</th>
                                            <th>Status</th>
                                            <th>Desempenho</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="student-priority-high">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                                            JS
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold">João Silva</div>
                                                        <small class="text-muted">Matrícula: 2023001</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="level-badge level-basic">8º Ano A</span></td>
                                            <td><span class="status-badge status-warning">Atenção</span></td>
                                            <td>
                                                <div>42% de aproveitamento</div>
                                                <div class="progress-indicator">
                                                    <div class="progress-bar-custom progress-poor" style="width: 42%"></div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="student-priority-medium">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                                            MA
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold">Maria Andrade</div>
                                                        <small class="text-muted">Matrícula: 2023002</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="level-badge level-intermediate">1º Ano EM</span></td>
                                            <td><span class="status-badge status-pending">Recuperação</span></td>
                                            <td>
                                                <div>58% de aproveitamento</div>
                                                <div class="progress-indicator">
                                                    <div class="progress-bar-custom progress-average" style="width: 58%"></div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="student-priority-low">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                                            PC
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold">Pedro Costa</div>
                                                        <small class="text-muted">Matrícula: 2023003</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="level-badge level-advanced">3º Ano EM</span></td>
                                            <td><span class="status-badge status-active">Monitoria</span></td>
                                            <td>
                                                <div>65% de aproveitamento</div>
                                                <div class="progress-indicator">
                                                    <div class="progress-bar-custom progress-good" style="width: 65%"></div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Calendário Acadêmico -->
                <div class="col-xl-6 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Calendário Acadêmico</h5>
                            <a href="calendario.html" class="btn btn-sm btn-outline-primary">Ver completo</a>
                        </div>
                        <div class="card-body">
                            <div class="calendar-event event-exam">
                                <div class="fw-bold">Prova de Matemática - 8º Ano</div>
                                <small>15/10/2023 - 10:00 às 12:00</small>
                            </div>
                            
                            <div class="calendar-event event-holiday">
                                <div class="fw-bold">Feriado - Dia do Professor</div>
                                <small>15/10/2023 - Dia inteiro</small>
                            </div>
                            
                            <div class="calendar-event event-meeting">
                                <div class="fw-bold">Reunião de Pais - Ensino Fundamental</div>
                                <small>18/10/2023 - 19:00 às 21:00</small>
                            </div>
                            
                            <div class="calendar-event event-deadline">
                                <div class="fw-bold">Prazo para Entrega de Trabalhos</div>
                                <small>20/10/2023 - Até 23:59</small>
                            </div>
                            
                            <div class="calendar-event event-exam">
                                <div class="fw-bold">Simulado ENEM</div>
                                <small>25/10/2023 - 08:00 às 13:00</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Informações Adicionais -->
            <div class="row">
                <!-- Frequência por Turma -->
                <div class="col-xl-6 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom">
                            <h5 class="mb-0">Frequência por Turma</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <i class="bi bi-people-fill text-primary" style="font-size: 1.5rem;"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold">8º Ano A</div>
                                            <div class="course-stats">
                                                <div class="course-stat">
                                                    <div class="course-stat-value"><span class="attendance-indicator attendance-present"></span>89%</div>
                                                    <div class="course-stat-label">Presentes</div>
                                                </div>
                                                <div class="course-stat">
                                                    <div class="course-stat-value"><span class="attendance-indicator attendance-late"></span>7%</div>
                                                    <div class="course-stat-label">Atrasos</div>
                                                </div>
                                                <div class="course-stat">
                                                    <div class="course-stat-value"><span class="attendance-indicator attendance-absent"></span>4%</div>
                                                    <div class="course-stat-label">Faltas</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <i class="bi bi-people-fill text-success" style="font-size: 1.5rem;"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold">1º Ano EM</div>
                                            <div class="course-stats">
                                                <div class="course-stat">
                                                    <div class="course-stat-value"><span class="attendance-indicator attendance-present"></span>92%</div>
                                                    <div class="course-stat-label">Presentes</div>
                                                </div>
                                                <div class="course-stat">
                                                    <div class="course-stat-value"><span class="attendance-indicator attendance-late"></span>5%</div>
                                                    <div class="course-stat-label">Atrasos</div>
                                                </div>
                                                <div class="course-stat">
                                                    <div class="course-stat-value"><span class="attendance-indicator attendance-absent"></span>3%</div>
                                                    <div class="course-stat-label">Faltas</div>
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
                                            <i class="bi bi-people-fill text-warning" style="font-size: 1.5rem;"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold">3º Ano EM</div>
                                            <div class="course-stats">
                                                <div class="course-stat">
                                                    <div class="course-stat-value"><span class="attendance-indicator attendance-present"></span>85%</div>
                                                    <div class="course-stat-label">Presentes</div>
                                                </div>
                                                <div class="course-stat">
                                                    <div class="course-stat-value"><span class="attendance-indicator attendance-late"></span>8%</div>
                                                    <div class="course-stat-label">Atrasos</div>
                                                </div>
                                                <div class="course-stat">
                                                    <div class="course-stat-value"><span class="attendance-indicator attendance-absent"></span>7%</div>
                                                    <div class="course-stat-label">Faltas</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <i class="bi bi-people-fill text-info" style="font-size: 1.5rem;"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold">Curso Técnico</div>
                                            <div class="course-stats">
                                                <div class="course-stat">
                                                    <div class="course-stat-value"><span class="attendance-indicator attendance-present"></span>94%</div>
                                                    <div class="course-stat-label">Presentes</div>
                                                </div>
                                                <div class="course-stat">
                                                    <div class="course-stat-value"><span class="attendance-indicator attendance-late"></span>4%</div>
                                                    <div class="course-stat-label">Atrasos</div>
                                                </div>
                                                <div class="course-stat">
                                                    <div class="course-stat-value"><span class="attendance-indicator attendance-absent"></span>2%</div>
                                                    <div class="course-stat-label">Faltas</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Alertas Educacionais -->
                <div class="col-xl-6 mb-4">
                    <div class="card-custom">
                        <div class="card-header-custom">
                            <h5 class="mb-0">Alertas e Oportunidades</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-warning d-flex align-items-center mb-3">
                                <i class="bi bi-graph-down me-3"></i>
                                <div>
                                    <strong>Taxa de evasão</strong> aumentou 3% no ensino médio
                                    <div class="mt-1">
                                        <a href="#" class="btn btn-sm btn-warning">Analisar causas</a>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="alert alert-info d-flex align-items-center mb-3">
                                <i class="bi bi-calendar-check me-3"></i>
                                <div>
                                    <strong>Prova de Matemática</strong> agendada para amanhã
                                    <div class="mt-1">
                                        <a href="calendario.html" class="btn btn-sm btn-info">Ver detalhes</a>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="alert alert-success d-flex align-items-center mb-3">
                                <i class="bi bi-award me-3"></i>
                                <div>
                                    <strong>Desempenho em Ciências</strong> melhorou 12% este bimestre
                                    <div class="mt-1">
                                        <small class="text-muted">Parabéns aos professores e alunos!</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="alert alert-danger d-flex align-items-center mb-0">
                                <i class="bi bi-exclamation-octagon me-3"></i>
                                <div>
                                    <strong>5 alunos</strong> com risco de reprovação
                                    <div class="mt-1">
                                        <a href="#" class="btn btn-sm btn-danger">Plano de recuperação</a>
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
            // Gráfico de Desempenho Acadêmico
            const academicPerformanceCtx = document.getElementById('academicPerformanceChart').getContext('2d');
            const academicPerformanceChart = new Chart(academicPerformanceCtx, {
                type: 'line',
                data: {
                    labels: ['1º Bimestre', '2º Bimestre', '3º Bimestre', '4º Bimestre'],
                    datasets: [{
                        label: 'Matemática',
                        data: [6.8, 7.2, 7.5, 7.8],
                        borderColor: '#4361EE',
                        backgroundColor: 'rgba(67, 97, 238, 0.1)',
                        tension: 0.4,
                        fill: true
                    }, {
                        label: 'Português',
                        data: [7.2, 7.4, 7.1, 7.6],
                        borderColor: '#7209B7',
                        backgroundColor: 'rgba(114, 9, 183, 0.1)',
                        tension: 0.4,
                        fill: true
                    }, {
                        label: 'Ciências',
                        data: [6.5, 7.0, 7.8, 8.2],
                        borderColor: '#F72585',
                        backgroundColor: 'rgba(247, 37, 133, 0.1)',
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
                            beginAtZero: false,
                            min: 5,
                            max: 10,
                            ticks: {
                                callback: function(value) {
                                    return value.toFixed(1);
                                }
                            }
                        }
                    }
                }
            });
            
            // Gráfico de Distribuição por Nível de Ensino
            const educationLevelCtx = document.getElementById('educationLevelChart').getContext('2d');
            const educationLevelChart = new Chart(educationLevelCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Ensino Fundamental I', 'Ensino Fundamental II', 'Ensino Médio', 'Cursos Técnicos', 'EJA'],
                    datasets: [{
                        data: [25, 30, 28, 12, 5],
                        backgroundColor: [
                            '#4361EE',
                            '#7209B7',
                            '#F72585',
                            '#4CC9F0',
                            '#2E86AB'
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
            nexusFlow.showNotification('Exportando relatório educacional...', 'info');
            setTimeout(() => {
                nexusFlow.showNotification('Relatório exportado com sucesso!', 'success');
            }, 2000);
        }
        
        function refreshDashboard() {
            nexusFlow.showNotification('Atualizando dashboard educacional...', 'info');
            setTimeout(() => {
                nexusFlow.showNotification('Dashboard atualizado!', 'success');
                // Em uma aplicação real, aqui seria feita uma requisição para atualizar os dados
                // Por simplicidade, apenas recarregamos a página
                location.reload();
            }, 1000);
        }
        
        // Definir papel como educação
        localStorage.setItem('userRole', 'educacao');
    </script>
</body>
</html>