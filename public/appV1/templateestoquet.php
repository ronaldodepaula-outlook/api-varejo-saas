<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Transferências</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .transfer-card {
            transition: all 0.3s ease;
            border-left: 4px solid #dee2e6;
        }
        .transfer-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .transfer-card.status-pending { border-left-color: #ffc107; }
        .transfer-card.status-in-transit { border-left-color: #17a2b8; }
        .transfer-card.status-completed { border-left-color: #28a745; }
        .transfer-card.status-cancelled { border-left-color: #dc3545; }
        .product-transfer-item {
            border-left: 3px solid transparent;
            transition: all 0.3s ease;
        }
        .product-transfer-item:hover {
            background-color: #f8f9fa;
            border-left-color: #007bff;
        }
        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2rem;
        }
        .step {
            flex: 1;
            text-align: center;
            position: relative;
        }
        .step::after {
            content: '';
            position: absolute;
            top: 20px;
            left: 50%;
            width: 100%;
            height: 2px;
            background: #dee2e6;
            z-index: -1;
        }
        .step:last-child::after {
            display: none;
        }
        .step.active .step-circle {
            background: #007bff;
            color: white;
        }
        .step.completed .step-circle {
            background: #28a745;
            color: white;
        }
        .step-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #dee2e6;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            font-weight: bold;
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
                            <h2 class="mb-0">Gestão de Transferências</h2>
                            <p class="text-muted mb-0">Controle de transferências entre filiais</p>
                        </div>
                    </div>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newTransferModal">
                        <i class="bi bi-plus-lg me-1"></i>
                        Nova Transferência
                    </button>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Pendentes</h6>
                                <h3 class="mb-0">8</h3>
                            </div>
                            <i class="bi bi-clock fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Em Trânsito</h6>
                                <h3 class="mb-0">5</h3>
                            </div>
                            <i class="bi bi-truck fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Concluídas</h6>
                                <h3 class="mb-0">47</h3>
                            </div>
                            <i class="bi bi-check-circle fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Este Mês</h6>
                                <h3 class="mb-0">23</h3>
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
                        <input type="text" class="form-control" placeholder="TRANS-001">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select class="form-select">
                            <option value="">Todos</option>
                            <option value="pending">Pendente</option>
                            <option value="in-transit">Em Trânsito</option>
                            <option value="completed">Concluída</option>
                            <option value="cancelled">Cancelada</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Filial Origem</label>
                        <select class="form-select">
                            <option value="">Todas</option>
                            <option value="matriz">Matriz</option>
                            <option value="filial1">Filial 1</option>
                            <option value="filial2">Filial 2</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Filial Destino</label>
                        <select class="form-select">
                            <option value="">Todas</option>
                            <option value="matriz">Matriz</option>
                            <option value="filial1">Filial 1</option>
                            <option value="filial2">Filial 2</option>
                        </select>
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

        <!-- Transfers List -->
        <div class="row">
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card transfer-card status-pending h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">TRANS-2024-001</h6>
                        <span class="badge bg-warning">Pendente</span>
                    </div>
                    <div class="card-body">
                        <h6 class="card-title">Matriz → Filial 1</h6>
                        <p class="card-text text-muted small">
                            <i class="bi bi-geo-alt me-1"></i>São Paulo → Rio de Janeiro<br>
                            <i class="bi bi-calendar me-1"></i>Solicitado em: 15/01/2024<br>
                            <i class="bi bi-person me-1"></i>Solicitante: João Silva
                        </p>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <small class="text-muted">Itens:</small>
                                <div><strong>3</strong></div>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Valor Total:</small>
                                <div><strong>R$ 15.847</strong></div>
                            </div>
                        </div>
                        <div class="d-flex gap-1">
                            <button class="btn btn-primary btn-sm flex-fill" onclick="openTransferDetail('TRANS-2024-001')">
                                <i class="bi bi-eye"></i> Ver
                            </button>
                            <button class="btn btn-outline-success btn-sm flex-fill">
                                <i class="bi bi-truck"></i> Enviar
                            </button>
                            <button class="btn btn-outline-danger btn-sm">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card transfer-card status-in-transit h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">TRANS-2024-002</h6>
                        <span class="badge bg-info">Em Trânsito</span>
                    </div>
                    <div class="card-body">
                        <h6 class="card-title">Filial 2 → Matriz</h6>
                        <p class="card-text text-muted small">
                            <i class="bi bi-geo-alt me-1"></i>Belo Horizonte → São Paulo<br>
                            <i class="bi bi-calendar me-1"></i>Enviado em: 12/01/2024<br>
                            <i class="bi bi-person me-1"></i>Responsável: Maria Santos
                        </p>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <small class="text-muted">Itens:</small>
                                <div><strong>7</strong></div>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Valor Total:</small>
                                <div><strong>R$ 8.234</strong></div>
                            </div>
                        </div>
                        <div class="d-flex gap-1">
                            <button class="btn btn-primary btn-sm flex-fill" onclick="openTransferDetail('TRANS-2024-002')">
                                <i class="bi bi-eye"></i> Ver
                            </button>
                            <button class="btn btn-outline-success btn-sm flex-fill">
                                <i class="bi bi-check"></i> Receber
                            </button>
                            <button class="btn btn-outline-info btn-sm">
                                <i class="bi bi-printer"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card transfer-card status-completed h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">TRANS-2024-003</h6>
                        <span class="badge bg-success">Concluída</span>
                    </div>
                    <div class="card-body">
                        <h6 class="card-title">Filial 1 → Filial 2</h6>
                        <p class="card-text text-muted small">
                            <i class="bi bi-geo-alt me-1"></i>Rio de Janeiro → Belo Horizonte<br>
                            <i class="bi bi-calendar me-1"></i>Concluída em: 08/01/2024<br>
                            <i class="bi bi-person me-1"></i>Recebido por: Carlos Lima
                        </p>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <small class="text-muted">Itens:</small>
                                <div><strong>2</strong></div>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Valor Total:</small>
                                <div><strong>R$ 3.456</strong></div>
                            </div>
                        </div>
                        <div class="d-flex gap-1">
                            <button class="btn btn-primary btn-sm flex-fill" onclick="openTransferDetail('TRANS-2024-003')">
                                <i class="bi bi-eye"></i> Ver
                            </button>
                            <button class="btn btn-outline-info btn-sm flex-fill">
                                <i class="bi bi-file-text"></i> Relatório
                            </button>
                            <button class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-download"></i>
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

    <!-- New Transfer Modal -->
    <div class="modal fade" id="newTransferModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Nova Transferência</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Step Indicator -->
                    <div class="step-indicator">
                        <div class="step active">
                            <div class="step-circle">1</div>
                            <div>Configuração</div>
                        </div>
                        <div class="step">
                            <div class="step-circle">2</div>
                            <div>Seleção de Produtos</div>
                        </div>
                        <div class="step">
                            <div class="step-circle">3</div>
                            <div>Confirmação</div>
                        </div>
                    </div>

                    <!-- Step 1: Configuration -->
                    <div id="transferStep1" class="step-content">
                        <form>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Código da Transferência</label>
                                    <input type="text" class="form-control" value="TRANS-2024-004" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Data da Solicitação</label>
                                    <input type="date" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Filial de Origem</label>
                                    <select class="form-select" required>
                                        <option value="">Selecione a origem</option>
                                        <option value="matriz">Matriz - São Paulo</option>
                                        <option value="filial1">Filial 1 - Rio de Janeiro</option>
                                        <option value="filial2">Filial 2 - Belo Horizonte</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Filial de Destino</label>
                                    <select class="form-select" required>
                                        <option value="">Selecione o destino</option>
                                        <option value="matriz">Matriz - São Paulo</option>
                                        <option value="filial1">Filial 1 - Rio de Janeiro</option>
                                        <option value="filial2">Filial 2 - Belo Horizonte</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Responsável pela Solicitação</label>
                                    <select class="form-select" required>
                                        <option value="">Selecione o responsável</option>
                                        <option value="joao">João Silva</option>
                                        <option value="maria">Maria Santos</option>
                                        <option value="carlos">Carlos Lima</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Prioridade</label>
                                    <select class="form-select">
                                        <option value="normal">Normal</option>
                                        <option value="alta">Alta</option>
                                        <option value="urgente">Urgente</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Motivo da Transferência</label>
                                    <textarea class="form-control" rows="3" placeholder="Descreva o motivo da transferência..."></textarea>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Step 2: Product Selection -->
                    <div id="transferStep2" class="step-content" style="display: none;">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Buscar Produto</label>
                                <input type="text" class="form-control" placeholder="Nome ou código do produto">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Categoria</label>
                                <select class="form-select">
                                    <option value="">Todas as categorias</option>
                                    <option value="eletronicos">Eletrônicos</option>
                                    <option value="roupas">Roupas</option>
                                    <option value="casa">Casa & Jardim</option>
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button class="btn btn-primary w-100">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <h6>Produtos Disponíveis</h6>
                                <div class="border rounded p-3" style="height: 400px; overflow-y: auto;">
                                    <div class="product-transfer-item p-2 mb-2 border rounded" onclick="addProductToTransfer('PRD001')">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong>PRD001</strong> - Smartphone Galaxy S23
                                                <br>
                                                <small class="text-muted">Estoque: 150 UN | Custo: R$ 2.100,00</small>
                                            </div>
                                            <button class="btn btn-outline-primary btn-sm">
                                                <i class="bi bi-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div class="product-transfer-item p-2 mb-2 border rounded" onclick="addProductToTransfer('PRD002')">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong>PRD002</strong> - Notebook Dell Inspiron
                                                <br>
                                                <small class="text-muted">Estoque: 25 UN | Custo: R$ 3.100,00</small>
                                            </div>
                                            <button class="btn btn-outline-primary btn-sm">
                                                <i class="bi bi-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <h6>Produtos Selecionados</h6>
                                <div class="border rounded p-3" style="height: 400px; overflow-y: auto;" id="selectedProductsList">
                                    <div class="text-center text-muted py-5">
                                        <i class="bi bi-box fs-1"></i>
                                        <p>Nenhum produto selecionado</p>
                                    </div>
                                </div>
                                
                                <div class="mt-3">
                                    <div class="row">
                                        <div class="col-6">
                                            <strong>Total de Itens: <span id="totalItems">0</span></strong>
                                        </div>
                                        <div class="col-6 text-end">
                                            <strong>Valor Total: R$ <span id="totalValue">0,00</span></strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Confirmation -->
                    <div id="transferStep3" class="step-content" style="display: none;">
                        <div class="alert alert-info">
                            <h6><i class="bi bi-info-circle me-2"></i>Resumo da Transferência</h6>
                            <ul class="mb-0">
                                <li><strong>Código:</strong> TRANS-2024-004</li>
                                <li><strong>Origem:</strong> Matriz - São Paulo</li>
                                <li><strong>Destino:</strong> Filial 1 - Rio de Janeiro</li>
                                <li><strong>Produtos:</strong> 2 itens selecionados</li>
                                <li><strong>Valor Total:</strong> R$ 15.847,32</li>
                            </ul>
                        </div>
                        
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="confirmTransfer" required>
                            <label class="form-check-label" for="confirmTransfer">
                                Confirmo que todas as informações estão corretas e autorizo a transferência
                            </label>
                        </div>
                        
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="notifyDestination">
                            <label class="form-check-label" for="notifyDestination">
                                Notificar filial de destino sobre a transferência
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-outline-primary" id="transferPrevBtn" style="display: none;" onclick="previousTransferStep()">Anterior</button>
                    <button type="button" class="btn btn-primary" id="transferNextBtn" onclick="nextTransferStep()">Próximo</button>
                    <button type="button" class="btn btn-success" id="transferCreateBtn" style="display: none;" onclick="createTransfer()">Criar Transferência</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentTransferStep = 1;
        const totalTransferSteps = 3;
        let selectedProducts = [];

        function nextTransferStep() {
            if (currentTransferStep < totalTransferSteps) {
                document.getElementById(`transferStep${currentTransferStep}`).style.display = 'none';
                document.querySelector(`.step:nth-child(${currentTransferStep})`).classList.remove('active');
                document.querySelector(`.step:nth-child(${currentTransferStep})`).classList.add('completed');
                
                currentTransferStep++;
                
                document.getElementById(`transferStep${currentTransferStep}`).style.display = 'block';
                document.querySelector(`.step:nth-child(${currentTransferStep})`).classList.add('active');
                
                updateTransferButtons();
            }
        }

        function previousTransferStep() {
            if (currentTransferStep > 1) {
                document.getElementById(`transferStep${currentTransferStep}`).style.display = 'none';
                document.querySelector(`.step:nth-child(${currentTransferStep})`).classList.remove('active');
                
                currentTransferStep--;
                
                document.getElementById(`transferStep${currentTransferStep}`).style.display = 'block';
                document.querySelector(`.step:nth-child(${currentTransferStep})`).classList.remove('completed');
                document.querySelector(`.step:nth-child(${currentTransferStep})`).classList.add('active');
                
                updateTransferButtons();
            }
        }

        function updateTransferButtons() {
            const prevBtn = document.getElementById('transferPrevBtn');
            const nextBtn = document.getElementById('transferNextBtn');
            const createBtn = document.getElementById('transferCreateBtn');
            
            prevBtn.style.display = currentTransferStep > 1 ? 'inline-block' : 'none';
            nextBtn.style.display = currentTransferStep < totalTransferSteps ? 'inline-block' : 'none';
            createBtn.style.display = currentTransferStep === totalTransferSteps ? 'inline-block' : 'none';
        }

        function addProductToTransfer(productCode) {
            const product = {
                code: productCode,
                name: productCode === 'PRD001' ? 'Smartphone Galaxy S23' : 'Notebook Dell Inspiron',
                quantity: 1,
                cost: productCode === 'PRD001' ? 2100.00 : 3100.00
            };
            
            selectedProducts.push(product);
            updateSelectedProductsList();
        }

        function removeProductFromTransfer(index) {
            selectedProducts.splice(index, 1);
            updateSelectedProductsList();
        }

        function updateSelectedProductsList() {
            const container = document.getElementById('selectedProductsList');
            
            if (selectedProducts.length === 0) {
                container.innerHTML = `
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-box fs-1"></i>
                        <p>Nenhum produto selecionado</p>
                    </div>
                `;
            } else {
                container.innerHTML = selectedProducts.map((product, index) => `
                    <div class="border rounded p-2 mb-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>${product.code}</strong> - ${product.name}
                                <br>
                                <small class="text-muted">Custo: R$ ${product.cost.toFixed(2)}</small>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <input type="number" class="form-control form-control-sm" style="width: 80px;" value="${product.quantity}" min="1" onchange="updateProductQuantity(${index}, this.value)">
                                <button class="btn btn-outline-danger btn-sm" onclick="removeProductFromTransfer(${index})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `).join('');
            }
            
            updateTotals();
        }

        function updateProductQuantity(index, quantity) {
            selectedProducts[index].quantity = parseInt(quantity);
            updateTotals();
        }

        function updateTotals() {
            const totalItems = selectedProducts.reduce((sum, product) => sum + product.quantity, 0);
            const totalValue = selectedProducts.reduce((sum, product) => sum + (product.quantity * product.cost), 0);
            
            document.getElementById('totalItems').textContent = totalItems;
            document.getElementById('totalValue').textContent = totalValue.toFixed(2).replace('.', ',');
        }

        function createTransfer() {
            alert('Transferência criada com sucesso!');
            bootstrap.Modal.getInstance(document.getElementById('newTransferModal')).hide();
            resetTransferModal();
        }

        function resetTransferModal() {
            currentTransferStep = 1;
            selectedProducts = [];
            document.querySelectorAll('.step-content').forEach(step => step.style.display = 'none');
            document.getElementById('transferStep1').style.display = 'block';
            document.querySelectorAll('.step').forEach(step => {
                step.classList.remove('active', 'completed');
            });
            document.querySelector('.step:first-child').classList.add('active');
            updateTransferButtons();
            updateSelectedProductsList();
        }

        function openTransferDetail(transferCode) {
            alert(`Abrir detalhes da transferência ${transferCode}`);
        }
    </script>
</body>
</html>
