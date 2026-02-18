<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes do Estoque - Smartphone Galaxy S23</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .branch-card {
            transition: all 0.3s ease;
            cursor: pointer;
            border-left: 4px solid #dee2e6;
        }
        .branch-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .branch-card.selected {
            border-left-color: #007bff;
            background-color: #f8f9fa;
        }
        .stock-level-good { color: #28a745; }
        .stock-level-warning { color: #ffc107; }
        .stock-level-danger { color: #dc3545; }
        .movement-entry { border-left: 3px solid #28a745; }
        .movement-exit { border-left: 3px solid #dc3545; }
        .movement-transfer { border-left: 3px solid #007bff; }
        .movement-adjustment { border-left: 3px solid #ffc107; }
    </style>
</head>
<body>
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <button class="btn btn-outline-secondary me-3" onclick="history.back()">
                            <i class="bi bi-arrow-left"></i>
                        </button>
                        <div>
                            <h2 class="mb-0">Detalhes do Estoque</h2>
                            <p class="text-muted mb-0">PRD001 - Smartphone Galaxy S23</p>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#movementModal">
                            <i class="bi bi-arrow-repeat me-1"></i>
                            Nova Movimentação
                        </button>
                        <button class="btn btn-primary" onclick="openStockReport()">
                            <i class="bi bi-file-earmark-text me-1"></i>
                            Relatório Completo
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Summary -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h5 class="card-title">Smartphone Galaxy S23</h5>
                                <p class="card-text text-muted">128GB, Preto - Categoria: Eletrônicos</p>
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <small class="text-muted">Código:</small>
                                        <div><strong>PRD001</strong></div>
                                    </div>
                                    <div class="col-md-3">
                                        <small class="text-muted">Código de Barras:</small>
                                        <div><strong>7891234567890</strong></div>
                                    </div>
                                    <div class="col-md-3">
                                        <small class="text-muted">Unidade:</small>
                                        <div><strong>UN</strong></div>
                                    </div>
                                    <div class="col-md-3">
                                        <small class="text-muted">NCM:</small>
                                        <div><strong>85171231</strong></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <div class="card bg-primary text-white text-center">
                                            <div class="card-body py-2">
                                                <h6 class="card-title mb-1">Estoque Total</h6>
                                                <h4 class="mb-0">245</h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="card bg-success text-white text-center">
                                            <div class="card-body py-2">
                                                <h6 class="card-title mb-1">Valor Total</h6>
                                                <h4 class="mb-0">R$ 612K</h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="card bg-info text-white text-center">
                                            <div class="card-body py-2">
                                                <h6 class="card-title mb-1">Custo Médio</h6>
                                                <h4 class="mb-0">R$ 2.100</h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="card bg-warning text-white text-center">
                                            <div class="card-body py-2">
                                                <h6 class="card-title mb-1">Reservado</h6>
                                                <h4 class="mb-0">12</h4>
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

        <div class="row">
            <!-- Branch Stock List -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Estoque por Filial</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            <div class="list-group-item branch-card selected" onclick="selectBranch('matriz')">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">Matriz - São Paulo</h6>
                                        <small class="text-muted">Rua das Flores, 123</small>
                                    </div>
                                    <div class="text-end">
                                        <h5 class="mb-0 stock-level-good">150 UN</h5>
                                        <small class="text-muted">Min: 50 | Max: 200</small>
                                    </div>
                                </div>
                                <div class="progress mt-2" style="height: 4px;">
                                    <div class="progress-bar bg-success" style="width: 75%"></div>
                                </div>
                            </div>
                            
                            <div class="list-group-item branch-card" onclick="selectBranch('filial1')">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">Filial 1 - Rio de Janeiro</h6>
                                        <small class="text-muted">Av. Copacabana, 456</small>
                                    </div>
                                    <div class="text-end">
                                        <h5 class="mb-0 stock-level-warning">25 UN</h5>
                                        <small class="text-muted">Min: 30 | Max: 100</small>
                                    </div>
                                </div>
                                <div class="progress mt-2" style="height: 4px;">
                                    <div class="progress-bar bg-warning" style="width: 25%"></div>
                                </div>
                            </div>
                            
                            <div class="list-group-item branch-card" onclick="selectBranch('filial2')">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">Filial 2 - Belo Horizonte</h6>
                                        <small class="text-muted">Rua da Liberdade, 789</small>
                                    </div>
                                    <div class="text-end">
                                        <h5 class="mb-0 stock-level-good">70 UN</h5>
                                        <small class="text-muted">Min: 20 | Max: 80</small>
                                    </div>
                                </div>
                                <div class="progress mt-2" style="height: 4px;">
                                    <div class="progress-bar bg-success" style="width: 87%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="mb-0">Ações Rápidas</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button class="btn btn-outline-primary btn-sm" onclick="openTransferModal()">
                                <i class="bi bi-arrow-left-right me-1"></i>
                                Transferir Entre Filiais
                            </button>
                            <button class="btn btn-outline-success btn-sm" onclick="openAdjustmentModal()">
                                <i class="bi bi-pencil-square me-1"></i>
                                Ajuste de Estoque
                            </button>
                            <button class="btn btn-outline-info btn-sm" onclick="openInventoryModal()">
                                <i class="bi bi-clipboard-check me-1"></i>
                                Incluir em Inventário
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stock Movement History -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Ficha de Estoque - Matriz São Paulo</h5>
                        <div class="d-flex gap-2">
                            <select class="form-select form-select-sm" style="width: auto;">
                                <option>Últimos 30 dias</option>
                                <option>Últimos 60 dias</option>
                                <option>Últimos 90 dias</option>
                                <option>Este ano</option>
                            </select>
                            <button class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-download"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Current Stock Info -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="text-center p-3 bg-light rounded">
                                    <h6 class="text-muted mb-1">Estoque Atual</h6>
                                    <h4 class="mb-0 text-primary">150 UN</h4>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center p-3 bg-light rounded">
                                    <h6 class="text-muted mb-1">Custo Médio</h6>
                                    <h4 class="mb-0 text-success">R$ 2.100,00</h4>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center p-3 bg-light rounded">
                                    <h6 class="text-muted mb-1">Valor Total</h6>
                                    <h4 class="mb-0 text-info">R$ 315.000</h4>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center p-3 bg-light rounded">
                                    <h6 class="text-muted mb-1">Última Mov.</h6>
                                    <h4 class="mb-0 text-warning">15/01</h4>
                                </div>
                            </div>
                        </div>

                        <!-- Movement History -->
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>Data</th>
                                        <th>Tipo</th>
                                        <th>Documento</th>
                                        <th>Qtd</th>
                                        <th>Custo Unit.</th>
                                        <th>Saldo</th>
                                        <th>Custo Médio</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="movement-entry">
                                        <td>15/01/2024</td>
                                        <td>
                                            <span class="badge bg-success">Entrada</span>
                                        </td>
                                        <td>NF-e 123456</td>
                                        <td>+50</td>
                                        <td>R$ 2.050,00</td>
                                        <td><strong>150</strong></td>
                                        <td>R$ 2.100,00</td>
                                    </tr>
                                    <tr class="movement-exit">
                                        <td>12/01/2024</td>
                                        <td>
                                            <span class="badge bg-danger">Saída</span>
                                        </td>
                                        <td>Venda 789</td>
                                        <td>-5</td>
                                        <td>R$ 2.100,00</td>
                                        <td><strong>100</strong></td>
                                        <td>R$ 2.100,00</td>
                                    </tr>
                                    <tr class="movement-transfer">
                                        <td>10/01/2024</td>
                                        <td>
                                            <span class="badge bg-primary">Transfer. Entrada</span>
                                        </td>
                                        <td>TRANS-001</td>
                                        <td>+20</td>
                                        <td>R$ 2.100,00</td>
                                        <td><strong>105</strong></td>
                                        <td>R$ 2.100,00</td>
                                    </tr>
                                    <tr class="movement-adjustment">
                                        <td>08/01/2024</td>
                                        <td>
                                            <span class="badge bg-warning">Ajuste</span>
                                        </td>
                                        <td>AJ-001</td>
                                        <td>-2</td>
                                        <td>R$ 2.100,00</td>
                                        <td><strong>85</strong></td>
                                        <td>R$ 2.100,00</td>
                                    </tr>
                                    <tr class="movement-entry">
                                        <td>05/01/2024</td>
                                        <td>
                                            <span class="badge bg-success">Entrada</span>
                                        </td>
                                        <td>NF-e 123455</td>
                                        <td>+87</td>
                                        <td>R$ 2.150,00</td>
                                        <td><strong>87</strong></td>
                                        <td>R$ 2.150,00</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <nav class="mt-3">
                            <ul class="pagination pagination-sm justify-content-center">
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

    <!-- Movement Modal -->
    <div class="modal fade" id="movementModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Nova Movimentação</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Tipo de Movimentação</label>
                                <select class="form-select" required>
                                    <option value="">Selecione...</option>
                                    <option value="entry">Entrada</option>
                                    <option value="exit">Saída</option>
                                    <option value="adjustment">Ajuste</option>
                                    <option value="transfer">Transferência</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Filial</label>
                                <select class="form-select" required>
                                    <option value="">Selecione...</option>
                                    <option value="matriz">Matriz - São Paulo</option>
                                    <option value="filial1">Filial 1 - Rio de Janeiro</option>
                                    <option value="filial2">Filial 2 - Belo Horizonte</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Quantidade</label>
                                <input type="number" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Custo Unitário</label>
                                <input type="number" class="form-control" step="0.01" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Documento/Referência</label>
                                <input type="text" class="form-control" placeholder="Ex: NF-e 123456">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Observações</label>
                                <textarea class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary">Confirmar Movimentação</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function selectBranch(branchId) {
            // Remove selected class from all cards
            document.querySelectorAll('.branch-card').forEach(card => {
                card.classList.remove('selected');
            });
            
            // Add selected class to clicked card
            event.currentTarget.classList.add('selected');
            
            // Update the stock movement history for selected branch
            updateStockHistory(branchId);
        }

        function updateStockHistory(branchId) {
            // Simulate updating the stock history based on selected branch
            const branchNames = {
                'matriz': 'Matriz São Paulo',
                'filial1': 'Filial 1 Rio de Janeiro',
                'filial2': 'Filial 2 Belo Horizonte'
            };
            
            document.querySelector('.card-header h5').textContent = `Ficha de Estoque - ${branchNames[branchId]}`;
        }

        function openStockReport() {
            alert('Abrir relatório completo de estoque');
        }

        function openTransferModal() {
            alert('Abrir modal de transferência');
        }

        function openAdjustmentModal() {
            alert('Abrir modal de ajuste');
        }

        function openInventoryModal() {
            alert('Abrir modal de inventário');
        }
    </script>
</body>
</html>
