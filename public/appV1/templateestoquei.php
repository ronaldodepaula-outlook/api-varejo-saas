<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Inventários</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .inventory-card {
            transition: all 0.3s ease;
            border-left: 4px solid #dee2e6;
        }
        .inventory-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .inventory-card.status-open { border-left-color: #28a745; }
        .inventory-card.status-counting { border-left-color: #ffc107; }
        .inventory-card.status-closed { border-left-color: #007bff; }
        .inventory-card.status-cancelled { border-left-color: #dc3545; }
        .product-item {
            border-left: 3px solid transparent;
            transition: all 0.3s ease;
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
                            <h2 class="mb-0">Gestão de Inventários</h2>
                            <p class="text-muted mb-0">Controle completo de inventários por filial</p>
                        </div>
                    </div>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newInventoryModal">
                        <i class="bi bi-plus-lg me-1"></i>
                        Novo Inventário
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
                                <h6 class="card-title">Inventários Abertos</h6>
                                <h3 class="mb-0">3</h3>
                            </div>
                            <i class="bi bi-clipboard-check fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Em Contagem</h6>
                                <h3 class="mb-0">1</h3>
                            </div>
                            <i class="bi bi-hourglass-split fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Finalizados</h6>
                                <h3 class="mb-0">12</h3>
                            </div>
                            <i class="bi bi-check-circle fs-1 opacity-50"></i>
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
                                <h3 class="mb-0">8</h3>
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
                    <div class="col-md-3">
                        <label class="form-label">Filial</label>
                        <select class="form-select">
                            <option value="">Todas as filiais</option>
                            <option value="matriz">Matriz - São Paulo</option>
                            <option value="filial1">Filial 1 - Rio de Janeiro</option>
                            <option value="filial2">Filial 2 - Belo Horizonte</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select class="form-select">
                            <option value="">Todos</option>
                            <option value="open">Aberto</option>
                            <option value="counting">Em Contagem</option>
                            <option value="closed">Finalizado</option>
                            <option value="cancelled">Cancelado</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Período</label>
                        <div class="input-group">
                            <input type="date" class="form-control">
                            <span class="input-group-text">até</span>
                            <input type="date" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Código</label>
                        <input type="text" class="form-control" placeholder="INV-001">
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

        <!-- Inventories List -->
        <div class="row">
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card inventory-card status-open h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">INV-2024-001</h6>
                        <span class="badge bg-success">Aberto</span>
                    </div>
                    <div class="card-body">
                        <h6 class="card-title">Inventário Geral - Matriz</h6>
                        <p class="card-text text-muted small">
                            <i class="bi bi-geo-alt me-1"></i>Matriz - São Paulo<br>
                            <i class="bi bi-calendar me-1"></i>Iniciado em: 15/01/2024<br>
                            <i class="bi bi-person me-1"></i>Responsável: João Silva
                        </p>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <small class="text-muted">Total de Itens:</small>
                                <div><strong>1,247</strong></div>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Contados:</small>
                                <div><strong>0</strong></div>
                            </div>
                        </div>
                        <div class="progress mb-3" style="height: 6px;">
                            <div class="progress-bar bg-success" style="width: 0%"></div>
                        </div>
                        <div class="d-flex gap-1">
                            <button class="btn btn-primary btn-sm flex-fill" onclick="openInventoryDetail('INV-2024-001')">
                                <i class="bi bi-eye"></i> Abrir
                            </button>
                            <button class="btn btn-outline-success btn-sm flex-fill">
                                <i class="bi bi-play"></i> Iniciar
                            </button>
                            <button class="btn btn-outline-danger btn-sm">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card inventory-card status-counting h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">INV-2024-002</h6>
                        <span class="badge bg-warning">Em Contagem</span>
                    </div>
                    <div class="card-body">
                        <h6 class="card-title">Inventário Eletrônicos</h6>
                        <p class="card-text text-muted small">
                            <i class="bi bi-geo-alt me-1"></i>Filial 1 - Rio de Janeiro<br>
                            <i class="bi bi-calendar me-1"></i>Iniciado em: 12/01/2024<br>
                            <i class="bi bi-person me-1"></i>Responsável: Maria Santos
                        </p>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <small class="text-muted">Total de Itens:</small>
                                <div><strong>156</strong></div>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Contados:</small>
                                <div><strong>89</strong></div>
                            </div>
                        </div>
                        <div class="progress mb-3" style="height: 6px;">
                            <div class="progress-bar bg-warning" style="width: 57%"></div>
                        </div>
                        <div class="d-flex gap-1">
                            <button class="btn btn-primary btn-sm flex-fill" onclick="openInventoryDetail('INV-2024-002')">
                                <i class="bi bi-eye"></i> Abrir
                            </button>
                            <button class="btn btn-outline-success btn-sm flex-fill">
                                <i class="bi bi-check"></i> Finalizar
                            </button>
                            <button class="btn btn-outline-danger btn-sm">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card inventory-card status-closed h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">INV-2024-003</h6>
                        <span class="badge bg-primary">Finalizado</span>
                    </div>
                    <div class="card-body">
                        <h6 class="card-title">Inventário Roupas</h6>
                        <p class="card-text text-muted small">
                            <i class="bi bi-geo-alt me-1"></i>Filial 2 - Belo Horizonte<br>
                            <i class="bi bi-calendar me-1"></i>Finalizado em: 08/01/2024<br>
                            <i class="bi bi-person me-1"></i>Responsável: Carlos Lima
                        </p>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <small class="text-muted">Total de Itens:</small>
                                <div><strong>89</strong></div>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Diferenças:</small>
                                <div><strong class="text-warning">12</strong></div>
                            </div>
                        </div>
                        <div class="progress mb-3" style="height: 6px;">
                            <div class="progress-bar bg-primary" style="width: 100%"></div>
                        </div>
                        <div class="d-flex gap-1">
                            <button class="btn btn-primary btn-sm flex-fill" onclick="openInventoryDetail('INV-2024-003')">
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

    <!-- New Inventory Modal -->
    <div class="modal fade" id="newInventoryModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Novo Inventário</h5>
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
                    <div id="step1" class="step-content">
                        <form>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Código do Inventário</label>
                                    <input type="text" class="form-control" value="INV-2024-004" readonly>
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
                                <div class="col-12">
                                    <label class="form-label">Descrição</label>
                                    <input type="text" class="form-control" placeholder="Ex: Inventário Geral Janeiro 2024" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Data de Abertura</label>
                                    <input type="date" class="form-control" required>
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
                                <div class="col-12">
                                    <label class="form-label">Observações</label>
                                    <textarea class="form-control" rows="3" placeholder="Observações sobre o inventário..."></textarea>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Step 2: Product Selection (Hidden initially) -->
                    <div id="step2" class="step-content" style="display: none;">
                        <div class="mb-3">
                            <label class="form-label">Selecionar Produtos</label>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <select class="form-select">
                                        <option value="all">Todos os produtos</option>
                                        <option value="category">Por categoria</option>
                                        <option value="custom">Seleção personalizada</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <select class="form-select">
                                        <option value="">Todas as categorias</option>
                                        <option value="eletronicos">Eletrônicos</option>
                                        <option value="roupas">Roupas</option>
                                        <option value="casa">Casa & Jardim</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="selectAll">
                                <label class="form-check-label fw-bold" for="selectAll">
                                    Selecionar todos (1,247 produtos)
                                </label>
                            </div>
                            <hr>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" checked>
                                <label class="form-check-label">
                                    <strong>PRD001</strong> - Smartphone Galaxy S23
                                    <small class="text-muted d-block">Estoque atual: 245 UN</small>
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" checked>
                                <label class="form-check-label">
                                    <strong>PRD002</strong> - Notebook Dell Inspiron
                                    <small class="text-muted d-block">Estoque atual: 15 UN</small>
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox">
                                <label class="form-check-label">
                                    <strong>PRD003</strong> - Camiseta Polo Masculina
                                    <small class="text-muted d-block">Estoque atual: 0 UN</small>
                                </label>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <small class="text-muted">
                                <i class="bi bi-info-circle me-1"></i>
                                2 produtos selecionados para o inventário
                            </small>
                        </div>
                    </div>

                    <!-- Step 3: Confirmation (Hidden initially) -->
                    <div id="step3" class="step-content" style="display: none;">
                        <div class="alert alert-info">
                            <h6><i class="bi bi-info-circle me-2"></i>Resumo do Inventário</h6>
                            <ul class="mb-0">
                                <li><strong>Código:</strong> INV-2024-004</li>
                                <li><strong>Filial:</strong> Matriz - São Paulo</li>
                                <li><strong>Produtos:</strong> 2 selecionados</li>
                                <li><strong>Responsável:</strong> João Silva</li>
                            </ul>
                        </div>
                        
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="confirmInventory" required>
                            <label class="form-check-label" for="confirmInventory">
                                Confirmo que todas as informações estão corretas e desejo criar o inventário
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-outline-primary" id="prevBtn" style="display: none;" onclick="previousStep()">Anterior</button>
                    <button type="button" class="btn btn-primary" id="nextBtn" onclick="nextStep()">Próximo</button>
                    <button type="button" class="btn btn-success" id="createBtn" style="display: none;" onclick="createInventory()">Criar Inventário</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentStep = 1;
        const totalSteps = 3;

        function nextStep() {
            if (currentStep < totalSteps) {
                document.getElementById(`step${currentStep}`).style.display = 'none';
                document.querySelector(`.step:nth-child(${currentStep})`).classList.remove('active');
                document.querySelector(`.step:nth-child(${currentStep})`).classList.add('completed');
                
                currentStep++;
                
                document.getElementById(`step${currentStep}`).style.display = 'block';
                document.querySelector(`.step:nth-child(${currentStep})`).classList.add('active');
                
                updateButtons();
            }
        }

        function previousStep() {
            if (currentStep > 1) {
                document.getElementById(`step${currentStep}`).style.display = 'none';
                document.querySelector(`.step:nth-child(${currentStep})`).classList.remove('active');
                
                currentStep--;
                
                document.getElementById(`step${currentStep}`).style.display = 'block';
                document.querySelector(`.step:nth-child(${currentStep})`).classList.remove('completed');
                document.querySelector(`.step:nth-child(${currentStep})`).classList.add('active');
                
                updateButtons();
            }
        }

        function updateButtons() {
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');
            const createBtn = document.getElementById('createBtn');
            
            prevBtn.style.display = currentStep > 1 ? 'inline-block' : 'none';
            nextBtn.style.display = currentStep < totalSteps ? 'inline-block' : 'none';
            createBtn.style.display = currentStep === totalSteps ? 'inline-block' : 'none';
        }

        function createInventory() {
            alert('Inventário criado com sucesso!');
            bootstrap.Modal.getInstance(document.getElementById('newInventoryModal')).hide();
            // Reset modal
            currentStep = 1;
            document.querySelectorAll('.step-content').forEach(step => step.style.display = 'none');
            document.getElementById('step1').style.display = 'block';
            document.querySelectorAll('.step').forEach(step => {
                step.classList.remove('active', 'completed');
            });
            document.querySelector('.step:first-child').classList.add('active');
            updateButtons();
        }

        function openInventoryDetail(inventoryCode) {
            window.location.href = `inventory-detail.html?code=${inventoryCode}`;
        }

        // Select all checkbox functionality
        document.getElementById('selectAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('#step2 .form-check-input:not(#selectAll)');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    </script>
</body>
</html>
