<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventário INV-2024-002 - Detalhes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .product-item {
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }
        .product-item:hover {
            background-color: #f8f9fa;
            border-left-color: #007bff;
        }
        .product-item.counted {
            background-color: #d4edda;
            border-left-color: #28a745;
        }
        .product-item.difference {
            background-color: #fff3cd;
            border-left-color: #ffc107;
        }
        .product-item.negative-difference {
            background-color: #f8d7da;
            border-left-color: #dc3545;
        }
        .count-input {
            width: 100px;
        }
        .difference-positive {
            color: #28a745;
            font-weight: bold;
        }
        .difference-negative {
            color: #dc3545;
            font-weight: bold;
        }
        .difference-zero {
            color: #6c757d;
        }
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
                            <h2 class="mb-0">Inventário INV-2024-002</h2>
                            <p class="text-muted mb-0">Inventário Eletrônicos - Filial 1 Rio de Janeiro</p>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-info" onclick="exportInventory()">
                            <i class="bi bi-download me-1"></i>
                            Exportar
                        </button>
                        <button class="btn btn-success" onclick="finalizeInventory()">
                            <i class="bi bi-check-circle me-1"></i>
                            Finalizar Inventário
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Inventory Summary -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h5 class="card-title">Informações do Inventário</h5>
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <small class="text-muted">Código:</small>
                                        <div><strong>INV-2024-002</strong></div>
                                    </div>
                                    <div class="col-md-3">
                                        <small class="text-muted">Status:</small>
                                        <div><span class="badge bg-warning">Em Contagem</span></div>
                                    </div>
                                    <div class="col-md-3">
                                        <small class="text-muted">Responsável:</small>
                                        <div><strong>Maria Santos</strong></div>
                                    </div>
                                    <div class="col-md-3">
                                        <small class="text-muted">Data Abertura:</small>
                                        <div><strong>12/01/2024</strong></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <div class="card bg-primary text-white text-center">
                                            <div class="card-body py-2">
                                                <h6 class="card-title mb-1">Total Itens</h6>
                                                <h4 class="mb-0">156</h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="card bg-success text-white text-center">
                                            <div class="card-body py-2">
                                                <h6 class="card-title mb-1">Contados</h6>
                                                <h4 class="mb-0">89</h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="card bg-warning text-white text-center">
                                            <div class="card-body py-2">
                                                <h6 class="card-title mb-1">Pendentes</h6>
                                                <h4 class="mb-0">67</h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="card bg-info text-white text-center">
                                            <div class="card-body py-2">
                                                <h6 class="card-title mb-1">Diferenças</h6>
                                                <h4 class="mb-0">8</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Progress Bar -->
                        <div class="mt-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted">Progresso da Contagem</span>
                                <span class="text-muted">57% (89/156)</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-success" style="width: 57%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters and Actions -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Buscar Produto</label>
                        <input type="text" class="form-control" placeholder="Nome ou código">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select class="form-select">
                            <option value="">Todos</option>
                            <option value="pending">Pendentes</option>
                            <option value="counted">Contados</option>
                            <option value="difference">Com Diferença</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Categoria</label>
                        <select class="form-select">
                            <option value="">Todas</option>
                            <option value="smartphones">Smartphones</option>
                            <option value="notebooks">Notebooks</option>
                            <option value="tablets">Tablets</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-primary me-2">
                            <i class="bi bi-search me-1"></i>
                            Filtrar
                        </button>
                        <button class="btn btn-outline-secondary me-2">
                            <i class="bi bi-arrow-clockwise"></i>
                        </button>
                        <button class="btn btn-outline-success" onclick="countAllVisible()">
                            <i class="bi bi-check-all me-1"></i>
                            Contar Visíveis
                        </button>
                    </div>
                    <div class="col-md-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="showOnlyDifferences">
                            <label class="form-check-label" for="showOnlyDifferences">
                                Apenas diferenças
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products List -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Produtos do Inventário</h5>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary btn-sm" onclick="saveProgress()">
                        <i class="bi bi-save me-1"></i>
                        Salvar Progresso
                    </button>
                    <button class="btn btn-outline-info btn-sm" onclick="printCountSheet()">
                        <i class="bi bi-printer me-1"></i>
                        Folha de Contagem
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
                                <th>Localização</th>
                                <th>Estoque Sistema</th>
                                <th>Quantidade Contada</th>
                                <th>Diferença</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="product-item counted">
                                <td><strong>PRD001</strong></td>
                                <td>
                                    <div>
                                        <strong>Smartphone Galaxy S23</strong>
                                        <br>
                                        <small class="text-muted">128GB, Preto</small>
                                    </div>
                                </td>
                                <td>
                                    <small>Setor A - Prateleira 1</small>
                                </td>
                                <td><strong>45</strong></td>
                                <td>
                                    <input type="number" class="form-control count-input" value="47" onchange="calculateDifference(this)">
                                </td>
                                <td>
                                    <span class="difference-positive">+2</span>
                                </td>
                                <td>
                                    <span class="badge bg-success">Contado</span>
                                </td>
                                <td>
                                    <button class="btn btn-outline-primary btn-sm" onclick="addNote(this)">
                                        <i class="bi bi-chat-text"></i>
                                    </button>
                                    <button class="btn btn-outline-warning btn-sm" onclick="recount(this)">
                                        <i class="bi bi-arrow-repeat"></i>
                                    </button>
                                </td>
                            </tr>
                            
                            <tr class="product-item counted difference">
                                <td><strong>PRD002</strong></td>
                                <td>
                                    <div>
                                        <strong>Notebook Dell Inspiron</strong>
                                        <br>
                                        <small class="text-muted">i5, 8GB RAM, 256GB SSD</small>
                                    </div>
                                </td>
                                <td>
                                    <small>Setor B - Prateleira 3</small>
                                </td>
                                <td><strong>12</strong></td>
                                <td>
                                    <input type="number" class="form-control count-input" value="10" onchange="calculateDifference(this)">
                                </td>
                                <td>
                                    <span class="difference-negative">-2</span>
                                </td>
                                <td>
                                    <span class="badge bg-warning">Diferença</span>
                                </td>
                                <td>
                                    <button class="btn btn-outline-primary btn-sm" onclick="addNote(this)">
                                        <i class="bi bi-chat-text"></i>
                                    </button>
                                    <button class="btn btn-outline-warning btn-sm" onclick="recount(this)">
                                        <i class="bi bi-arrow-repeat"></i>
                                    </button>
                                </td>
                            </tr>
                            
                            <tr class="product-item">
                                <td><strong>PRD003</strong></td>
                                <td>
                                    <div>
                                        <strong>Tablet Samsung Galaxy Tab</strong>
                                        <br>
                                        <small class="text-muted">10.1", 64GB, WiFi</small>
                                    </div>
                                </td>
                                <td>
                                    <small>Setor A - Prateleira 2</small>
                                </td>
                                <td><strong>8</strong></td>
                                <td>
                                    <input type="number" class="form-control count-input" placeholder="0" onchange="calculateDifference(this)">
                                </td>
                                <td>
                                    <span class="difference-zero">-</span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">Pendente</span>
                                </td>
                                <td>
                                    <button class="btn btn-outline-primary btn-sm" onclick="addNote(this)">
                                        <i class="bi bi-chat-text"></i>
                                    </button>
                                    <button class="btn btn-outline-success btn-sm" onclick="quickCount(this)">
                                        <i class="bi bi-check"></i>
                                    </button>
                                </td>
                            </tr>
                            
                            <tr class="product-item">
                                <td><strong>PRD004</strong></td>
                                <td>
                                    <div>
                                        <strong>Smartwatch Apple Watch</strong>
                                        <br>
                                        <small class="text-muted">Series 9, 45mm, GPS</small>
                                    </div>
                                </td>
                                <td>
                                    <small>Setor C - Vitrine 1</small>
                                </td>
                                <td><strong>15</strong></td>
                                <td>
                                    <input type="number" class="form-control count-input" placeholder="0" onchange="calculateDifference(this)">
                                </td>
                                <td>
                                    <span class="difference-zero">-</span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">Pendente</span>
                                </td>
                                <td>
                                    <button class="btn btn-outline-primary btn-sm" onclick="addNote(this)">
                                        <i class="bi bi-chat-text"></i>
                                    </button>
                                    <button class="btn btn-outline-success btn-sm" onclick="quickCount(this)">
                                        <i class="bi bi-check"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">Mostrando 4 de 156 produtos</small>
                    <nav>
                        <ul class="pagination pagination-sm mb-0">
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

    <!-- Note Modal -->
    <div class="modal fade" id="noteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Adicionar Observação</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Produto</label>
                        <input type="text" class="form-control" id="noteProductName" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Observação</label>
                        <textarea class="form-control" rows="4" placeholder="Digite sua observação sobre este item..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary">Salvar Observação</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function calculateDifference(input) {
            const row = input.closest('tr');
            const systemStock = parseInt(row.cells[3].textContent);
            const countedQty = parseInt(input.value) || 0;
            const difference = countedQty - systemStock;
            
            const differenceCell = row.cells[5];
            const statusCell = row.cells[6];
            
            // Update difference display
            if (difference > 0) {
                differenceCell.innerHTML = `<span class="difference-positive">+${difference}</span>`;
                statusCell.innerHTML = '<span class="badge bg-warning">Diferença</span>';
                row.classList.add('difference');
            } else if (difference < 0) {
                differenceCell.innerHTML = `<span class="difference-negative">${difference}</span>`;
                statusCell.innerHTML = '<span class="badge bg-warning">Diferença</span>';
                row.classList.add('difference', 'negative-difference');
            } else if (difference === 0 && input.value !== '') {
                differenceCell.innerHTML = '<span class="difference-zero">0</span>';
                statusCell.innerHTML = '<span class="badge bg-success">Contado</span>';
                row.classList.remove('difference', 'negative-difference');
                row.classList.add('counted');
            }
        }

        function addNote(button) {
            const row = button.closest('tr');
            const productName = row.cells[1].querySelector('strong').textContent + ' - ' + 
                              row.cells[1].querySelector('small').textContent;
            
            document.getElementById('noteProductName').value = productName;
            new bootstrap.Modal(document.getElementById('noteModal')).show();
        }

        function recount(button) {
            const row = button.closest('tr');
            const input = row.querySelector('.count-input');
            input.value = '';
            input.focus();
            
            // Reset status
            row.classList.remove('counted', 'difference', 'negative-difference');
            row.cells[5].innerHTML = '<span class="difference-zero">-</span>';
            row.cells[6].innerHTML = '<span class="badge bg-secondary">Pendente</span>';
        }

        function quickCount(button) {
            const row = button.closest('tr');
            const systemStock = parseInt(row.cells[3].textContent);
            const input = row.querySelector('.count-input');
            
            input.value = systemStock;
            calculateDifference(input);
        }

        function countAllVisible() {
            const rows = document.querySelectorAll('.product-item');
            rows.forEach(row => {
                const input = row.querySelector('.count-input');
                if (!input.value) {
                    const systemStock = parseInt(row.cells[3].textContent);
                    input.value = systemStock;
                    calculateDifference(input);
                }
            });
            
            alert('Contagem automática realizada para todos os produtos visíveis!');
        }

        function saveProgress() {
            alert('Progresso salvo com sucesso!');
        }

        function printCountSheet() {
            alert('Gerando folha de contagem...');
        }

        function exportInventory() {
            alert('Exportando dados do inventário...');
        }

        function finalizeInventory() {
            if (confirm('Tem certeza que deseja finalizar este inventário? Esta ação não pode ser desfeita.')) {
                alert('Inventário finalizado com sucesso! O estoque foi atualizado.');
            }
        }
    </script>
</body>
</html>
