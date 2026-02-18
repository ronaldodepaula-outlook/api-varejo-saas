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
<main class="main-content">
    <!-- Header -->
    <header class="main-header">
        <div class="header-left">
            <button class="sidebar-toggle" type="button">
                <i class="bi bi-list"></i>
            </button>
            
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-custom">
                    <li class="breadcrumb-item active">Dashboard Varejo</li>
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
                    <li><h6 class="dropdown-header">Alertas do Varejo</h6></li>
                    <li><a class="dropdown-item" href="#">Estoque crítico em 5 produtos</a></li>
                    <li><a class="dropdown-item" href="#">Promoção ativa esta semana</a></li>
                    <li><a class="dropdown-item" href="#">Meta de vendas atingida</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-center" href="#">Ver todos</a></li>
                </ul>
            </div>
            
            <!-- Dropdown do Usuário -->
            <div class="dropdown user-dropdown">
                <div class="user-avatar" data-bs-toggle="dropdown" aria-expanded="false">
                    V
                </div>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><h6 class="dropdown-header user-name">Gerente Varejo</h6></li>
                    <li><small class="dropdown-header text-muted user-email">varejo@empresa.com</small></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="perfil.html"><i class="bi bi-person me-2"></i>Meu Perfil</a></li>
                    <li><a class="dropdown-item" href="#"><i class="bi bi-graph-up me-2"></i>Relatórios</a></li>
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
                <h1 class="page-title">Dashboard Varejo</h1>
                <p class="page-subtitle">Métricas de vendas, estoque e desempenho</p>
            </div>
            <div>
                <button class="btn btn-outline-primary me-2" onclick="exportReport()">
                    <i class="bi bi-receipt me-2"></i>Relatório Diário
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
                        <i class="bi bi-cart-check"></i>
                    </div>
                    <div class="metric-value">R$ 124.580</div>
                    <div class="metric-label">Vendas do Dia</div>
                    <div class="metric-change positive">
                        <i class="bi bi-arrow-up"></i> +8.2% vs ontem
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="metric-card success">
                    <div class="metric-icon success">
                        <i class="bi bi-basket"></i>
                    </div>
                    <div class="metric-value">1.247</div>
                    <div class="metric-label">Itens Vendidos</div>
                    <div class="metric-change positive">
                        <i class="bi bi-arrow-up"></i> +12% vs semana passada
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="metric-card warning">
                    <div class="metric-icon warning">
                        <i class="bi bi-box-seam"></i>
                    </div>
                    <div class="metric-value">48</div>
                    <div class="metric-label">Produtos em Estoque Baixo</div>
                    <div class="metric-change negative">
                        <i class="bi bi-exclamation-triangle"></i> Necessita reposição
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="metric-card danger">
                    <div class="metric-icon danger">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <div class="metric-value">5.2%</div>
                    <div class="metric-label">Taxa de Devolução</div>
                    <div class="metric-change positive">
                        <i class="bi bi-arrow-down"></i> -0.8% vs mês anterior
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Gráficos e Dados -->
        <div class="row mb-4">
            <!-- Gráfico de Vendas -->
            <div class="col-xl-8 mb-4">
                <div class="card-custom">
                    <div class="card-header-custom d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Desempenho de Vendas</h5>
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
            
            <!-- Categorias Mais Vendidas -->
            <div class="col-xl-4 mb-4">
                <div class="card-custom">
                    <div class="card-header-custom">
                        <h5 class="mb-0">Categorias em Destaque</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="categoryChart" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tabelas de Dados -->
        <div class="row">
            <!-- Produtos em Destaque -->
            <div class="col-xl-6 mb-4">
                <div class="card-custom">
                    <div class="card-header-custom d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Produtos Mais Vendidos</h5>
                        <a href="estoque.html" class="btn btn-sm btn-outline-primary">Ver estoque</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-custom mb-0">
                                <thead>
                                    <tr>
                                        <th>Produto</th>
                                        <th>Categoria</th>
                                        <th>Vendas</th>
                                        <th>Estoque</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="me-3">
                                                    <img src="assets/img/produto1.jpg" class="rounded" width="32" height="32" alt="">
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">Smartphone XYZ</div>
                                                    <small class="text-muted">Eletrônicos</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-info">Eletrônicos</span></td>
                                        <td>156 unidades</td>
                                        <td><span class="status-badge status-warning">12 unidades</span></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="me-3">
                                                    <img src="assets/img/produto2.jpg" class="rounded" width="32" height="32" alt="">
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">Tênis Esportivo</div>
                                                    <small class="text-muted">Calçados</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-success">Esportes</span></td>
                                        <td>89 unidades</td>
                                        <td><span class="status-badge status-active">45 unidades</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Alertas do Varejo -->
            <div class="col-xl-6 mb-4">
                <div class="card-custom">
                    <div class="card-header-custom">
                        <h5 class="mb-0">Alertas do Sistema</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning d-flex align-items-center mb-3">
                            <i class="bi bi-exclamation-triangle me-3"></i>
                            <div>
                                <strong>48 produtos</strong> com estoque abaixo do mínimo
                                <div class="mt-1">
                                    <a href="reposicao.html" class="btn btn-sm btn-warning">Repor Estoque</a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="alert alert-info d-flex align-items-center mb-3">
                            <i class="bi bi-megaphone me-3"></i>
                            <div>
                                <strong>Promoção ativa</strong> até sexta-feira
                                <div class="mt-1">
                                    <small class="text-muted">Desconto de 30% em eletrônicos</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="alert alert-success d-flex align-items-center mb-0">
                            <i class="bi bi-trophy me-3"></i>
                            <div>
                                <strong>Meta batida</strong> em vendas desta semana
                                <div class="mt-1">
                                    <a href="#" class="btn btn-sm btn-success">Ver desempenho</a>
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

    // Gráficos específicos do varejo
    document.addEventListener('DOMContentLoaded', function() {
        // Gráfico de Vendas
        const salesCtx = document.getElementById('salesChart').getContext('2d');
        const salesChart = new Chart(salesCtx, {
            type: 'bar',
            data: {
                labels: ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'],
                datasets: [{
                    label: 'Vendas (R$)',
                    data: [12000, 19000, 15000, 25000, 22000, 30000, 28000],
                    backgroundColor: '#3498DB',
                    borderColor: '#2980B9',
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
                                return 'R$ ' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
        
        // Gráfico de Categorias
        const categoryCtx = document.getElementById('categoryChart').getContext('2d');
        const categoryChart = new Chart(categoryCtx, {
            type: 'doughnut',
            data: {
                labels: ['Eletrônicos', 'Vestuário', 'Casa', 'Esportes', 'Beleza'],
                datasets: [{
                    data: [35, 25, 20, 15, 5],
                    backgroundColor: [
                        '#3498DB',
                        '#2ECC71',
                        '#F39C12',
                        '#E74C3C',
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
        nexusFlow.showNotification('Gerando relatório de vendas...', 'info');
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
</script>