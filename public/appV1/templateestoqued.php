<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Estoque - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            border-radius: 8px;
            margin: 2px 0;
            transition: all 0.3s ease;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: rgba(255,255,255,0.2);
            color: white;
            transform: translateX(5px);
        }
        .card-stats {
            border-left: 4px solid;
            transition: transform 0.3s ease;
        }
        .card-stats:hover {
            transform: translateY(-5px);
        }
        .card-stats.primary { border-left-color: #007bff; }
        .card-stats.success { border-left-color: #28a745; }
        .card-stats.warning { border-left-color: #ffc107; }
        .card-stats.danger { border-left-color: #dc3545; }
        .product-row:hover {
            background-color: #f8f9fa;
        }
        .stock-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }
        .low-stock { background-color: #dc3545; }
        .medium-stock { background-color: #ffc107; }
        .high-stock { background-color: #28a745; }
        .action-btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar p-0">
                <div class="p-3">
                    <h4 class="text-white mb-4">
                        <i class="bi bi-boxes me-2"></i>
                        Estoque Pro
                    </h4>
                    <nav class="nav flex-column">
                        <a class="nav-link active" href="#dashboard">
                            <i class="bi bi-speedometer2 me-2"></i>
                            Dashboard
                        </a>
                        <a class="nav-link" href="#products">
                            <i class="bi bi-box-seam me-2"></i>
                            Produtos
                        </a>
                        <a class="nav-link" href="#inventory">
                            <i class="bi bi-clipboard-check me-2"></i>
                            Inventários
                        </a>
                        <a class="nav-link" href="#transfers">
                            <i class="bi bi-arrow-left-right me-2"></i>
                            Transferências
                        </a>
                        <a class="nav-link" href="#adjustments">
                            <i class="bi bi-pencil-square me-2"></i>
                            Ajustes
                        </a>
                        <a class="nav-link" href="#reports">
                            <i class="bi bi-graph-up me-2"></i>
                            Relatórios
                        </a>
                        <a class="nav-link" href="#settings">
                            <i class="bi bi-gear me-2"></i>
                            Configurações
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 p-4">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="mb-0">Dashboard de Estoque</h2>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#quickActionModal">
                            <i class="bi bi-lightning me-1"></i>
                            Ação Rápida
                        </button>
                        <div class="dropdown">
                            <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-plus-lg me-1"></i>
                                Novo
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="openNewProduct()">Produto</a></li>
                                <li><a class="dropdown-item" href="#" onclick="openNewInventory()">Inventário</a></li>
                                <li><a class="dropdown-item" href="#" onclick="openNewTransfer()">Transferência</a></li>
                                <li><a class="dropdown-item" href="#" onclick="openNewAdjustment()">Ajuste</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="card card-stats primary h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="card-title text-muted">Total de Produtos</h6>
                                        <h3 class="mb-0 text-primary">2,847</h3>
                                        <small class="text-success">
                                            <i class="bi bi-arrow-up"></i> +12% este mês
                                        </small>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="bi bi-box-seam fs-1 text-primary opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card card-stats success h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="card-title text-muted">Valor Total</h6>
                                        <h3 class="mb-0 text-success">R$ 1.2M</h3>
                                        <small class="text-success">
                                            <i class="bi bi-arrow-up"></i> +8% este mês
                                        </small>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="bi bi-currency-dollar fs-1 text-success opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card card-stats warning h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="card-title text-muted">Estoque Baixo</h6>
                                        <h3 class="mb-0 text-warning">47</h3>
                                        <small class="text-danger">
                                            <i class="bi bi-exclamation-triangle"></i> Atenção
                                        </small>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="bi bi-exclamation-triangle fs-1 text-warning opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card card-stats danger h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="card-title text-muted">Sem Estoque</h6>
                                        <h3 class="mb-0 text-danger">12</h3>
                                        <small class="text-danger">
                                            <i class="bi bi-arrow-down"></i> Crítico
                                        </small>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="bi bi-x-circle fs-1 text-danger opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Buscar Produto</label>
                                <input type="text" class="form-control" placeholder="Nome ou código">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Categoria</label>
                                <select class="form-select">
                                    <option value="">Todas</option>
                                    <option value="eletronicos">Eletrônicos</option>
                                    <option value="roupas">Roupas</option>
                                    <option value="casa">Casa & Jardim</option>
                                    <option value="esportes">Esportes</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Filial</label>
                                <select class="form-select">
                                    <option value="">Todas</option>
                                    <option value="matriz">Matriz</option>
                                    <option value="filial1">Filial 1</option>
                                    <option value="filial2">Filial 2</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Status Estoque</label>
                                <select class="form-select">
                                    <option value="">Todos</option>
                                    <option value="baixo">Estoque Baixo</option>
                                    <option value="zerado">Sem Estoque</option>
                                    <option value="normal">Normal</option>
                                </select>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button class="btn btn-primary me-2">
                                    <i class="bi bi-search me-1"></i>
                                    Filtrar
                                </button>
                                <button class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Products Table -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Lista de Produtos</h5>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-success btn-sm">
                                <i class="bi bi-download me-1"></i>
                                Exportar
                            </button>
                            <button class="btn btn-primary btn-sm" onclick="openNewProduct()">
                                <i class="bi bi-plus-lg me-1"></i>
                                Novo Produto
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Código</th>
                                        <th>Produto</th>
                                        <th>Categoria</th>
                                        <th>Estoque Total</th>
                                        <th>Status</th>
                                        <th>Valor Unit.</th>
                                        <th>Última Mov.</th>
                                        <th>Gerenciar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="product-row">
                                        <td><strong>PRD001</strong></td>
                                        <td>
                                            <div>
                                                <strong>Smartphone Galaxy S23</strong>
                                                <br>
                                                <small class="text-muted">128GB, Preto</small>
                                            </div>
                                        </td>
                                        <td>Eletrônicos</td>
                                        <td>
                                            <span class="badge high-stock stock-badge text-white">245 UN</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-success">Normal</span>
                                        </td>
                                        <td>R$ 2.499,00</td>
                                        <td>
                                            <small>15/01/2024</small>
                                            <br>
                                            <small class="text-muted">Entrada</small>
                                        </td>
                                        <td>
                                            <button class="btn btn-primary action-btn me-1" onclick="openStockDetail('PRD001')">
                                                <i class="bi bi-boxes"></i> Estoque
                                            </button>
                                            <div class="btn-group">
                                                <button class="btn btn-outline-secondary action-btn dropdown-toggle" data-bs-toggle="dropdown">
                                                    <i class="bi bi-three-dots"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="#"><i class="bi bi-pencil me-2"></i>Editar</a></li>
                                                    <li><a class="dropdown-item" href="#"><i class="bi bi-graph-up me-2"></i>Relatório</a></li>
                                                    <li><a class="dropdown-item" href="#"><i class="bi bi-arrow-repeat me-2"></i>Movimentar</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr class="product-row">
                                        <td><strong>PRD002</strong></td>
                                        <td>
                                            <div>
                                                <strong>Notebook Dell Inspiron</strong>
                                                <br>
                                                <small class="text-muted">i5, 8GB RAM, 256GB SSD</small>
                                            </div>
                                        </td>
                                        <td>Eletrônicos</td>
                                        <td>
                                            <span class="badge medium-stock stock-badge text-dark">15 UN</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-warning">Baixo</span>
                                        </td>
                                        <td>R$ 3.299,00</td>
                                        <td>
                                            <small>12/01/2024</small>
                                            <br>
                                            <small class="text-muted">Saída</small>
                                        </td>
                                        <td>
                                            <button class="btn btn-primary action-btn me-1" onclick="openStockDetail('PRD002')">
                                                <i class="bi bi-boxes"></i> Estoque
                                            </button>
                                            <div class="btn-group">
                                                <button class="btn btn-outline-secondary action-btn dropdown-toggle" data-bs-toggle="dropdown">
                                                    <i class="bi bi-three-dots"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="#"><i class="bi bi-pencil me-2"></i>Editar</a></li>
                                                    <li><a class="dropdown-item" href="#"><i class="bi bi-graph-up me-2"></i>Relatório</a></li>
                                                    <li><a class="dropdown-item" href="#"><i class="bi bi-arrow-repeat me-2"></i>Movimentar</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr class="product-row">
                                        <td><strong>PRD003</strong></td>
                                        <td>
                                            <div>
                                                <strong>Camiseta Polo Masculina</strong>
                                                <br>
                                                <small class="text-muted">Tamanho M, Azul</small>
                                            </div>
                                        </td>
                                        <td>Roupas</td>
                                        <td>
                                            <span class="badge low-stock stock-badge text-white">0 UN</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-danger">Zerado</span>
                                        </td>
                                        <td>R$ 89,90</td>
                                        <td>
                                            <small>10/01/2024</small>
                                            <br>
                                            <small class="text-muted">Saída</small>
                                        </td>
                                        <td>
                                            <button class="btn btn-primary action-btn me-1" onclick="openStockDetail('PRD003')">
                                                <i class="bi bi-boxes"></i> Estoque
                                            </button>
                                            <div class="btn-group">
                                                <button class="btn btn-outline-secondary action-btn dropdown-toggle" data-bs-toggle="dropdown">
                                                    <i class="bi bi-three-dots"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="#"><i class="bi bi-pencil me-2"></i>Editar</a></li>
                                                    <li><a class="dropdown-item" href="#"><i class="bi bi-graph-up me-2"></i>Relatório</a></li>
                                                    <li><a class="dropdown-item" href="#"><i class="bi bi-arrow-repeat me-2"></i>Movimentar</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <nav>
                            <ul class="pagination pagination-sm justify-content-center mb-0">
                                <li class="page-item disabled">
                                    <a class="page-link" href="#">Anterior</a>
                                </li>
                                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                <li class="page-item"><a class="page-link" href="#">2</a></li>
                                <li class="page-item"><a class="page-link" href="#">3</a></li>
                                <li class="page-item">
                                    <a class="page-link" href="#">Próximo</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Action Modal -->
    <div class="modal fade" id="quickActionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ação Rápida</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <button class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center p-4" onclick="openNewInventory()">
                                <i class="bi bi-clipboard-check fs-1 mb-2"></i>
                                <span>Novo Inventário</span>
                            </button>
                        </div>
                        <div class="col-6">
                            <button class="btn btn-outline-success w-100 h-100 d-flex flex-column align-items-center justify-content-center p-4" onclick="openNewTransfer()">
                                <i class="bi bi-arrow-left-right fs-1 mb-2"></i>
                                <span>Transferência</span>
                            </button>
                        </div>
                        <div class="col-6">
                            <button class="btn btn-outline-warning w-100 h-100 d-flex flex-column align-items-center justify-content-center p-4" onclick="openNewAdjustment()">
                                <i class="bi bi-pencil-square fs-1 mb-2"></i>
                                <span>Ajuste Manual</span>
                            </button>
                        </div>
                        <div class="col-6">
                            <button class="btn btn-outline-info w-100 h-100 d-flex flex-column align-items-center justify-content-center p-4">
                                <i class="bi bi-graph-up fs-1 mb-2"></i>
                                <span>Relatórios</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function openStockDetail(productCode) {
            window.location.href = `stock-detail.html?product=${productCode}`;
        }

        function openNewProduct() {
            // Implementar modal ou página de novo produto
            alert('Abrir formulário de novo produto');
        }

        function openNewInventory() {
            window.location.href = 'inventory-management.html';
        }

        function openNewTransfer() {
            window.location.href = 'transfer-management.html';
        }

        function openNewAdjustment() {
            window.location.href = 'adjustment-management.html';
        }

        // Sidebar navigation
        document.querySelectorAll('.sidebar .nav-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelectorAll('.sidebar .nav-link').forEach(l => l.classList.remove('active'));
                this.classList.add('active');
            });
        });
    </script>
</body>
</html>
