<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Ajustes de Estoque</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .adjustment-card {
            transition: all 0.3s ease;
            border-left: 4px solid #dee2e6;
        }
        .adjustment-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .adjustment-card.type-positive { border-left-color: #28a745; }
        .adjustment-card.type-negative { border-left-color: #dc3545; }
        .adjustment-card.type-correction { border-left-color: #ffc107; }
        .product-adjustment-item {
            border-left: 3px solid transparent;
            transition: all 0.3s ease;
        }
        .product-adjustment-item:hover {
            background-color: #f8f9fa;
            border-left-color: #007bff;
        }
        .adjustment-type-card {
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        .adjustment-type-card:hover {
            border-color: #007bff;
            transform: translateY(-2px);
        }
        .adjustment-type-card.selected {
            border-color: #007bff;
            background-color: #f8f9fa;
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
                            <h2 class="mb-0">Gestão de Ajustes de Estoque</h2>
                            <p class="text-muted mb-0">Movimentações avulsas e correções de estoque</p>
                        </div>
                    </div>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newAdjustmentModal">
                        <i class="bi bi-plus-lg me-1"></i>
                        Novo Ajuste
                    </button>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Ajustes Positivos</h6>
                                <h3 class="mb-0">23</h3>
                                <small>+R$ 45.678</small>
                            </div>
                            <i class="bi bi-arrow-up-circle fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Ajustes Negativos</h6>
                                <h3 class="mb-0">18</h3>
                                <small>-R$ 12.345</small>
                            </div>
                            <i class="bi bi-arrow-down-circle fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Correções</h6>
                                <h3 class="mb-0">12</h3>
                                <small>Divergências</small>
                            </div>
                            <i class="bi bi-exclamation-triangle fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Este Mês</h6>
                                <h3 class="mb-0">53</h3>
                                <small>Total de ajustes</small>
                            </div>
                            <i class="bi bi-calendar-month fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-2">
                        <label class="form-label">Código</label>
                        <input type="text" class="form-control" placeholder="AJ-001">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Tipo</label>
                        <select class="form-select">
                            <option value="">Todos</option>
                            <option value="positive">Positivo</option>
                            <option value="negative">Negativo</option>
                            <option value="correction">Correção</option>
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
                        <label class="form-label">Produto</label>
                        <input type="text" class="form-control" placeholder="Nome ou código">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Período</label>
                        <input type="date" class="form-control">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button class="btn btn-primary w-100">
                            <i class="bi bi-search me-1"></i>
                            Filtrar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Adjustments List -->
        <div class="row">
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card adjustment-card type-positive h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">AJ-2024-001</h6>
                        <span class="badge bg-success">Ajuste Positivo</span>
                    </div>
                    <div class="card-body">
                        <h6 class="card-title">Entrada por Doação</h6>
                        <p class="card-text text-muted small">
                            <i class="bi bi-geo-alt me-1"></i>Matriz - São Paulo<br>
                            <i class="bi bi-calendar me-1"></i>Realizado em: 15/01/2024<br>
                            <i class="bi bi-person me-1"></i>Responsável: João Silva
                        </p>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <small class="text-muted">Produto:</small>
                                <div><strong>PRD001</strong></div>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Quantidade:</small>
                                <div><strong class="text-success">+10 UN</strong></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted">Valor do Ajuste:</small>
                            <div><strong class="text-success">+R$ 21.000,00</strong></div>
                        </div>
                        <div class="d-flex gap-1">
                            <button class="btn btn-primary btn-sm flex-fill" onclick="openAdjustmentDetail('AJ-2024-001')">
                                <i class="bi bi-eye"></i> Ver
                            </button>
                            <button class="btn btn-outline-info btn-sm flex-fill">
                                <i class="bi bi-file-text"></i> Relatório
                            </button>
                            <button class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-printer"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card adjustment-card type-negative h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">AJ-2024-002</h6>
                        <span class="badge bg-danger">Ajuste Negativo</span>
                    </div>
                    <div class="card-body">
                        <h6 class="card-title">Perda por Avaria</h6>
                        <p class="card-text text-muted small">
                            <i class="bi bi-geo-alt me-1"></i>Filial 1 - Rio de Janeiro<br>
                            <i class="bi bi-calendar me-1"></i>Realizado em: 12/01/2024<br>
                            <i class="bi bi-person me-1"></i>Responsável: Maria Santos
                        </p>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <small class="text-muted">Produto:</small>
                                <div><strong>PRD002</strong></div>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Quantidade:</small>
                                <div><strong class="text-danger">-3 UN</strong></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted">Valor do Ajuste:</small>
                            <div><strong class="text-danger">-R$ 9.300,00</strong></div>
                        </div>
                        <div class="d-flex gap-1">
                            <button class="btn btn-primary btn-sm flex-fill" onclick="openAdjustmentDetail('AJ-2024-002')">
                                <i class="bi bi-eye"></i> Ver
                            </button>
                            <button class="btn btn-outline-info btn-sm flex-fill">
                                <i class="bi bi-file-text"></i> Relatório
                            </button>
                            <button class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-printer"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card adjustment-card type-correction h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">AJ-2024-003</h6>
                        <span class="badge bg-warning">Correção</span>
                    </div>
                    <div class="card-body">
                        <h6 class="card-title">Correção de Inventário</h6>
                        <p class="card-text text-muted small">
                            <i class="bi bi-geo-alt me-1"></i>Filial 2 - Belo Horizonte<br>
                            <i class="bi bi-calendar me-1"></i>Realizado em: 08/01/2024<br>
                            <i class="bi bi-person me-1"></i>Responsável: Carlos Lima
                        </p>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <small class="text-muted">Produto:</small>
                                <div><strong>PRD003</strong></div>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Quantidade:</small>
                                <div><strong class="text-warning">+2 UN</strong></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted">Valor do Ajuste:</small>
                            <div><strong class="text-warning">+R$ 179,80</strong></div>
                        </div>
                        <div class="d-flex gap-1">
                            <button class="btn btn-primary btn-sm flex-fill" onclick="openAdjustmentDetail('AJ-2024-003')">
                                <i class="bi bi-eye"></i> Ver
                            </button>
                            <button class="btn btn-outline-info btn-sm flex-fill">
                                <i class="bi bi-file-text"></i> Relatório
                            </button>
                            <button class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-printer"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div class="row">
            <div class="col-12">
                <nav>
                    <ul class="pagination justify-content-center">
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

    <!-- New Adjustment Modal -->
    <div class="modal fade" id="newAdjustmentModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Novo Ajuste de Estoque</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Adjustment Type Selection -->
                    <div class="mb-4">
                        <h6 class="mb-3">Selecione o Tipo de Ajuste</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="card adjustment-type-card text-center p-3" onclick="selectAdjustmentType('positive')">
                                    <i class="bi bi-arrow-up-circle fs-1 text-success mb-2"></i>
                                    <h6>Ajuste Positivo</h6>
                                    <small class="text-muted">Adicionar produtos ao estoque</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card adjustment-type-card text-center p-3" onclick="selectAdjustmentType('negative')">
                                    <i class="bi bi-arrow-down-circle fs-1 text-danger mb-2"></i>
                                    <h6>Ajuste Negativo</h6>
                                    <small class="text-muted">Remover produtos do estoque</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card adjustment-type-card text-center p-3" onclick="selectAdjustmentType('correction')">
                                    <i class="bi bi-exclamation-triangle fs-1 text-warning mb-2"></i>
                                    <h6>Correção</h6>
                                    <small class="text-muted">Corrigir divergências</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Adjustment Form -->
                    <div id="adjustmentForm" style="display: none;">
                        <form>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Código do Ajuste</label>
                                    <input type="text" class="form-control" value="AJ-2024-004" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Data do Ajuste</label>
                                    <input type="date" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Filial</label>
                                    <select class="form-select" required>
                                        <option value="">Selecione a filial</option>
                                        <option value="matriz">Matriz - São Paulo</option>
                                        <option value="filial1">Filial 1 - Rio de Janeiro</option>
                                        <option value="filial2">Filial 2 - Belo Horizonte</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Responsável</label>
                                    <select class="form-select" required>
                                        <option value="">Selecione o responsável</option>
                                        <option value="joao">João Silva</option>
                                        <option value="maria">Maria Santos</option>
                                        <option value="carlos">Carlos Lima</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Produto</label>
                                    <select class="form-select" required>
                                        <option value="">Selecione o produto</option>
                                        <option value="PRD001">PRD001 - Smartphone Galaxy S23</option>
                                        <option value="PRD002">PRD002 - Notebook Dell Inspiron</option>
                                        <option value="PRD003">PRD003 - Camiseta Polo Masculina</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Estoque Atual</label>
                                    <input type="number" class="form-control" value="150" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Quantidade do Ajuste</label>
                                    <input type="number" class="form-control" required placeholder="Ex: 10 ou -5">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Custo Unitário</label>
                                    <input type="number" class="form-control" step="0.01" required placeholder="0,00">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Motivo do Ajuste</label>
                                    <select class="form-select" required>
                                        <option value="">Selecione o motivo</option>
                                        <option value="avaria">Avaria/Quebra</option>
                                        <option value="vencimento">Vencimento</option>
                                        <option value="furto">Furto/Roubo</option>
                                        <option value="doacao">Doação</option>
                                        <option value="correcao">Correção de Inventário</option>
                                        <option value="outros">Outros</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Observações</label>
                                    <textarea class="form-control" rows="3" placeholder="Descreva detalhes sobre o ajuste..."></textarea>
                                </div>
                            </div>
                        </form>

                        <!-- Adjustment Summary -->
                        <div class="mt-4">
                            <div class="alert alert-info">
                                <h6><i class="bi bi-info-circle me-2"></i>Resumo do Ajuste</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <ul class="mb-0">
                                            <li><strong>Tipo:</strong> <span id="adjustmentTypeSummary">-</span></li>
                                            <li><strong>Produto:</strong> <span id="productSummary">-</span></li>
                                            <li><strong>Estoque Atual:</strong> <span id="currentStockSummary">-</span></li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <ul class="mb-0">
                                            <li><strong>Quantidade Ajuste:</strong> <span id="quantitySummary">-</span></li>
                                            <li><strong>Novo Estoque:</strong> <span id="newStockSummary">-</span></li>
                                            <li><strong>Valor Total:</strong> <span id="totalValueSummary">-</span></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="confirmAdjustmentBtn" style="display: none;" onclick="confirmAdjustment()">Confirmar Ajuste</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let selectedAdjustmentType = null;

        function selectAdjustmentType(type) {
            // Remove selection from all cards
            document.querySelectorAll('.adjustment-type-card').forEach(card => {
                card.classList.remove('selected');
            });
            
            // Add selection to clicked card
            event.currentTarget.classList.add('selected');
            
            selectedAdjustmentType = type;
            
            // Show form
            document.getElementById('adjustmentForm').style.display = 'block';
            document.getElementById('confirmAdjustmentBtn').style.display = 'inline-block';
            
            // Update summary
            const typeNames = {
                'positive': 'Ajuste Positivo',
                'negative': 'Ajuste Negativo',
                'correction': 'Correção'
            };
            
            document.getElementById('adjustmentTypeSummary').textContent = typeNames[type];
        }

        function confirmAdjustment() {
            if (!selectedAdjustmentType) {
                alert('Selecione o tipo de ajuste');
                return;
            }
            
            alert('Ajuste de estoque realizado com sucesso!');
            bootstrap.Modal.getInstance(document.getElementById('newAdjustmentModal')).hide();
            resetAdjustmentModal();
        }

        function resetAdjustmentModal() {
            selectedAdjustmentType = null;
            document.querySelectorAll('.adjustment-type-card').forEach(card => {
                card.classList.remove('selected');
            });
            document.getElementById('adjustmentForm').style.display = 'none';
            document.getElementById('confirmAdjustmentBtn').style.display = 'none';
        }

        function openAdjustmentDetail(adjustmentCode) {
            alert(`Abrir detalhes do ajuste ${adjustmentCode}`);
        }

        // Reset modal when closed
        document.getElementById('newAdjustmentModal').addEventListener('hidden.bs.modal', function () {
            resetAdjustmentModal();
        });
    </script>
</body>
</html>
