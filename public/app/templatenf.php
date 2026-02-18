<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Notas Fiscais</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .filter-section {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .invoice-card {
            transition: all 0.3s ease;
            border-left: 4px solid #dee2e6;
        }
        .invoice-card:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .invoice-card.status-emitida {
            border-left-color: #28a745;
        }
        .invoice-card.status-cancelada {
            border-left-color: #dc3545;
        }
        .invoice-card.status-pendente {
            border-left-color: #ffc107;
        }
        .invoice-card.status-processada {
            border-left-color: #007bff;
        }
        .upload-area {
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            padding: 40px;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .upload-area:hover {
            border-color: #007bff;
            background-color: #f8f9fa;
        }
        .upload-area.dragover {
            border-color: #007bff;
            background-color: #e3f2fd;
        }
        .status-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }
        .value-highlight {
            font-weight: 600;
            color: #28a745;
        }
    </style>
</head>
<body>
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h1 class="h3 mb-0">
                        <i class="bi bi-receipt-cutoff me-2"></i>
                        Gestão de Notas Fiscais
                    </h1>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#importModal">
                            <i class="bi bi-upload me-1"></i>
                            Importar XML
                        </button>
                        <button class="btn btn-primary">
                            <i class="bi bi-plus-lg me-1"></i>
                            Nova Nota
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros Avançados -->
        <div class="filter-section">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Período</label>
                    <div class="input-group">
                        <input type="date" class="form-control" id="dataInicio">
                        <span class="input-group-text">até</span>
                        <input type="date" class="form-control" id="dataFim">
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Tipo</label>
                    <select class="form-select">
                        <option value="">Todos</option>
                        <option value="entrada">Entrada</option>
                        <option value="saida">Saída</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select class="form-select">
                        <option value="">Todos</option>
                        <option value="emitida">Emitida</option>
                        <option value="cancelada">Cancelada</option>
                        <option value="pendente">Pendente</option>
                        <option value="processada">Processada</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Fornecedor/Cliente</label>
                    <input type="text" class="form-control" placeholder="Buscar por nome ou CNPJ">
                </div>
                <div class="col-md-2">
                    <label class="form-label">CFOP</label>
                    <input type="text" class="form-control" placeholder="Ex: 5102">
                </div>
            </div>
            <div class="row g-3 mt-2">
                <div class="col-md-2">
                    <label class="form-label">Valor Mínimo</label>
                    <input type="number" class="form-control" placeholder="0,00" step="0.01">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Valor Máximo</label>
                    <input type="number" class="form-control" placeholder="0,00" step="0.01">
                </div>
                <div class="col-md-2">
                    <label class="form-label">NCM</label>
                    <input type="text" class="form-control" placeholder="Ex: 84159090">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Chave de Acesso</label>
                    <input type="text" class="form-control" placeholder="44 dígitos">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button class="btn btn-primary me-2">
                        <i class="bi bi-search me-1"></i>
                        Filtrar
                    </button>
                    <button class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-clockwise me-1"></i>
                        Limpar
                    </button>
                </div>
            </div>
        </div>

        <!-- Estatísticas Rápidas -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Total de Notas</h6>
                                <h3 class="mb-0">1,247</h3>
                            </div>
                            <i class="bi bi-receipt fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Valor Total</h6>
                                <h3 class="mb-0">R$ 2.847.392</h3>
                            </div>
                            <i class="bi bi-currency-dollar fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Pendentes</h6>
                                <h3 class="mb-0">23</h3>
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
                                <h6 class="card-title">Este Mês</h6>
                                <h3 class="mb-0">156</h3>
                            </div>
                            <i class="bi bi-calendar-month fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ações em Lote -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="selectAll">
                            <label class="form-check-label" for="selectAll">
                                Selecionar todos
                            </label>
                        </div>
                        <div class="btn-group" role="group">
                            <button class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-download me-1"></i>
                                Baixar XMLs
                            </button>
                            <button class="btn btn-outline-success btn-sm">
                                <i class="bi bi-check-circle me-1"></i>
                                Processar
                            </button>
                            <button class="btn btn-outline-danger btn-sm">
                                <i class="bi bi-trash me-1"></i>
                                Excluir
                            </button>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-muted">Mostrando 1-20 de 1,247 registros</span>
                        <div class="btn-group" role="group">
                            <button class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-list"></i>
                            </button>
                            <button class="btn btn-outline-secondary btn-sm active">
                                <i class="bi bi-grid"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista de Notas Fiscais -->
        <div class="row">
            <!-- Nota Fiscal 1 -->
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card invoice-card status-emitida h-100">
                    <div class="card-header d-flex justify-content-between align-items-center py-2">
                        <div class="d-flex align-items-center">
                            <input class="form-check-input me-2" type="checkbox">
                            <small class="text-muted">NF-e 000123456</small>
                        </div>
                        <span class="badge bg-success status-badge">Emitida</span>
                    </div>
                    <div class="card-body">
                        <h6 class="card-title mb-2">FORNECEDOR ABC LTDA</h6>
                        <p class="card-text text-muted small mb-2">
                            CNPJ: 12.345.678/0001-90<br>
                            Chave: 35200714200166000166550010000000271023456789
                        </p>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <small class="text-muted">Data Emissão:</small><br>
                                <small>15/01/2024</small>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">CFOP:</small><br>
                                <small>5102</small>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted">Valor Total:</span>
                            <span class="value-highlight">R$ 15.847,32</span>
                        </div>
                        <div class="d-flex gap-1">
                            <button class="btn btn-outline-primary btn-sm flex-fill" data-bs-toggle="modal" data-bs-target="#detailsModal">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button class="btn btn-outline-success btn-sm flex-fill">
                                <i class="bi bi-download"></i>
                            </button>
                            <button class="btn btn-outline-info btn-sm flex-fill">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-outline-danger btn-sm flex-fill">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Nota Fiscal 2 -->
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card invoice-card status-pendente h-100">
                    <div class="card-header d-flex justify-content-between align-items-center py-2">
                        <div class="d-flex align-items-center">
                            <input class="form-check-input me-2" type="checkbox">
                            <small class="text-muted">NF-e 000123457</small>
                        </div>
                        <span class="badge bg-warning status-badge">Pendente</span>
                    </div>
                    <div class="card-body">
                        <h6 class="card-title mb-2">DISTRIBUIDORA XYZ S/A</h6>
                        <p class="card-text text-muted small mb-2">
                            CNPJ: 98.765.432/0001-10<br>
                            Chave: 35200714200166000166550010000000281023456790
                        </p>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <small class="text-muted">Data Emissão:</small><br>
                                <small>16/01/2024</small>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">CFOP:</small><br>
                                <small>1102</small>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted">Valor Total:</span>
                            <span class="value-highlight">R$ 8.234,56</span>
                        </div>
                        <div class="d-flex gap-1">
                            <button class="btn btn-outline-primary btn-sm flex-fill">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button class="btn btn-outline-success btn-sm flex-fill">
                                <i class="bi bi-download"></i>
                            </button>
                            <button class="btn btn-outline-info btn-sm flex-fill">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-outline-danger btn-sm flex-fill">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Nota Fiscal 3 -->
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card invoice-card status-cancelada h-100">
                    <div class="card-header d-flex justify-content-between align-items-center py-2">
                        <div class="d-flex align-items-center">
                            <input class="form-check-input me-2" type="checkbox">
                            <small class="text-muted">NF-e 000123458</small>
                        </div>
                        <span class="badge bg-danger status-badge">Cancelada</span>
                    </div>
                    <div class="card-body">
                        <h6 class="card-title mb-2">COMERCIAL 123 EIRELI</h6>
                        <p class="card-text text-muted small mb-2">
                            CNPJ: 11.222.333/0001-44<br>
                            Chave: 35200714200166000166550010000000291023456791
                        </p>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <small class="text-muted">Data Emissão:</small><br>
                                <small>17/01/2024</small>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">CFOP:</small><br>
                                <small>5405</small>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted">Valor Total:</span>
                            <span class="value-highlight text-decoration-line-through">R$ 3.456,78</span>
                        </div>
                        <div class="d-flex gap-1">
                            <button class="btn btn-outline-primary btn-sm flex-fill">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button class="btn btn-outline-success btn-sm flex-fill">
                                <i class="bi bi-download"></i>
                            </button>
                            <button class="btn btn-outline-info btn-sm flex-fill">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-outline-danger btn-sm flex-fill">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Paginação -->
        <div class="row">
            <div class="col-12">
                <nav aria-label="Paginação das notas fiscais">
                    <ul class="pagination justify-content-center">
                        <li class="page-item disabled">
                            <a class="page-link" href="#" tabindex="-1">Anterior</a>
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

    <!-- Modal de Importação de XML -->
    <div class="modal fade" id="importModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-upload me-2"></i>
                        Importar Arquivos XML
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="upload-area" id="uploadArea">
                        <i class="bi bi-cloud-upload fs-1 text-muted mb-3"></i>
                        <h5>Arraste os arquivos XML aqui</h5>
                        <p class="text-muted">ou clique para selecionar arquivos</p>
                        <input type="file" id="fileInput" multiple accept=".xml" class="d-none">
                        <button class="btn btn-outline-primary" onclick="document.getElementById('fileInput').click()">
                            Selecionar Arquivos
                        </button>
                    </div>
                    
                    <div class="mt-4" id="fileList" style="display: none;">
                        <h6>Arquivos Selecionados:</h6>
                        <div class="list-group" id="selectedFiles">
                            <!-- Arquivos selecionados aparecerão aqui -->
                        </div>
                    </div>

                    <div class="mt-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="autoProcess" checked>
                            <label class="form-check-label" for="autoProcess">
                                Processar automaticamente após importação
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="validateDuplicate" checked>
                            <label class="form-check-label" for="validateDuplicate">
                                Verificar duplicatas por chave de acesso
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="importBtn" disabled>
                        <i class="bi bi-upload me-1"></i>
                        Importar XMLs
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Detalhes da Nota Fiscal -->
    <div class="modal fade" id="detailsModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-receipt-cutoff me-2"></i>
                        Detalhes da Nota Fiscal - NF-e 000123456
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Tabs de Navegação -->
                    <ul class="nav nav-tabs" id="detailsTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab">
                                <i class="bi bi-info-circle me-1"></i>Geral
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="items-tab" data-bs-toggle="tab" data-bs-target="#items" type="button" role="tab">
                                <i class="bi bi-list-ul me-1"></i>Itens
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="taxes-tab" data-bs-toggle="tab" data-bs-target="#taxes" type="button" role="tab">
                                <i class="bi bi-calculator me-1"></i>Impostos
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="payment-tab" data-bs-toggle="tab" data-bs-target="#payment" type="button" role="tab">
                                <i class="bi bi-credit-card me-1"></i>Pagamento
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#history" type="button" role="tab">
                                <i class="bi bi-clock-history me-1"></i>Histórico
                            </button>
                        </li>
                    </ul>

                    <!-- Conteúdo das Tabs -->
                    <div class="tab-content mt-3" id="detailsTabContent">
                        <!-- Tab Geral -->
                        <div class="tab-pane fade show active" id="general" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="border-bottom pb-2 mb-3">Dados da Nota</h6>
                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>Número:</strong></td>
                                            <td>000123456</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Série:</strong></td>
                                            <td>001</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Data Emissão:</strong></td>
                                            <td>15/01/2024 14:30:25</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Natureza:</strong></td>
                                            <td>Venda de mercadoria adquirida</td>
                                        </tr>
                                        <tr>
                                            <td><strong>CFOP:</strong></td>
                                            <td>5102</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Chave de Acesso:</strong></td>
                                            <td><small>35200714200166000166550010000000271023456789</small></td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="border-bottom pb-2 mb-3">Fornecedor</h6>
                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>Razão Social:</strong></td>
                                            <td>FORNECEDOR ABC LTDA</td>
                                        </tr>
                                        <tr>
                                            <td><strong>CNPJ:</strong></td>
                                            <td>12.345.678/0001-90</td>
                                        </tr>
                                        <tr>
                                            <td><strong>IE:</strong></td>
                                            <td>123.456.789.012</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Endereço:</strong></td>
                                            <td>Rua das Flores, 123<br>Centro - São Paulo/SP<br>CEP: 01234-567</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h6 class="border-bottom pb-2 mb-3">Valores Totais</h6>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="card bg-light">
                                                <div class="card-body text-center">
                                                    <h6 class="card-title">Produtos</h6>
                                                    <h4 class="text-primary">R$ 13.456,78</h4>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card bg-light">
                                                <div class="card-body text-center">
                                                    <h6 class="card-title">ICMS</h6>
                                                    <h4 class="text-info">R$ 1.614,81</h4>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card bg-light">
                                                <div class="card-body text-center">
                                                    <h6 class="card-title">Frete</h6>
                                                    <h4 class="text-warning">R$ 775,73</h4>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card bg-success text-white">
                                                <div class="card-body text-center">
                                                    <h6 class="card-title">Total</h6>
                                                    <h4>R$ 15.847,32</h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tab Itens -->
                        <div class="tab-pane fade" id="items" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Código</th>
                                            <th>Descrição</th>
                                            <th>NCM</th>
                                            <th>Qtd</th>
                                            <th>Unid</th>
                                            <th>Vl. Unit</th>
                                            <th>Vl. Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>001</td>
                                            <td>PRODUTO EXEMPLO A</td>
                                            <td>84159090</td>
                                            <td>10</td>
                                            <td>UN</td>
                                            <td>R$ 125,50</td>
                                            <td>R$ 1.255,00</td>
                                        </tr>
                                        <tr>
                                            <td>002</td>
                                            <td>PRODUTO EXEMPLO B</td>
                                            <td>84159091</td>
                                            <td>5</td>
                                            <td>UN</td>
                                            <td>R$ 2.440,36</td>
                                            <td>R$ 12.201,78</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Tab Impostos -->
                        <div class="tab-pane fade" id="taxes" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="border-bottom pb-2 mb-3">ICMS</h6>
                                    <table class="table table-sm">
                                        <tr>
                                            <td>Base de Cálculo:</td>
                                            <td>R$ 13.456,78</td>
                                        </tr>
                                        <tr>
                                            <td>Alíquota:</td>
                                            <td>12%</td>
                                        </tr>
                                        <tr>
                                            <td>Valor:</td>
                                            <td><strong>R$ 1.614,81</strong></td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="border-bottom pb-2 mb-3">IPI</h6>
                                    <table class="table table-sm">
                                        <tr>
                                            <td>Base de Cálculo:</td>
                                            <td>R$ 13.456,78</td>
                                        </tr>
                                        <tr>
                                            <td>Alíquota:</td>
                                            <td>5%</td>
                                        </tr>
                                        <tr>
                                            <td>Valor:</td>
                                            <td><strong>R$ 672,84</strong></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <h6 class="border-bottom pb-2 mb-3">PIS</h6>
                                    <table class="table table-sm">
                                        <tr>
                                            <td>Base de Cálculo:</td>
                                            <td>R$ 13.456,78</td>
                                        </tr>
                                        <tr>
                                            <td>Alíquota:</td>
                                            <td>1,65%</td>
                                        </tr>
                                        <tr>
                                            <td>Valor:</td>
                                            <td><strong>R$ 222,04</strong></td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="border-bottom pb-2 mb-3">COFINS</h6>
                                    <table class="table table-sm">
                                        <tr>
                                            <td>Base de Cálculo:</td>
                                            <td>R$ 13.456,78</td>
                                        </tr>
                                        <tr>
                                            <td>Alíquota:</td>
                                            <td>7,6%</td>
                                        </tr>
                                        <tr>
                                            <td>Valor:</td>
                                            <td><strong>R$ 1.022,72</strong></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Tab Pagamento -->
                        <div class="tab-pane fade" id="payment" role="tabpanel">
                            <h6 class="border-bottom pb-2 mb-3">Condições de Pagamento</h6>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Parcela</th>
                                            <th>Vencimento</th>
                                            <th>Valor</th>
                                            <th>Forma</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>1/3</td>
                                            <td>15/02/2024</td>
                                            <td>R$ 5.282,44</td>
                                            <td>Boleto Bancário</td>
                                        </tr>
                                        <tr>
                                            <td>2/3</td>
                                            <td>15/03/2024</td>
                                            <td>R$ 5.282,44</td>
                                            <td>Boleto Bancário</td>
                                        </tr>
                                        <tr>
                                            <td>3/3</td>
                                            <td>15/04/2024</td>
                                            <td>R$ 5.282,44</td>
                                            <td>Boleto Bancário</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Tab Histórico -->
                        <div class="tab-pane fade" id="history" role="tabpanel">
                            <div class="timeline">
                                <div class="d-flex mb-3">
                                    <div class="flex-shrink-0">
                                        <div class="bg-success rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class="bi bi-upload text-white"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1">XML Importado</h6>
                                        <p class="text-muted mb-1">Arquivo XML importado com sucesso</p>
                                        <small class="text-muted">15/01/2024 às 14:35 por João Silva</small>
                                    </div>
                                </div>
                                <div class="d-flex mb-3">
                                    <div class="flex-shrink-0">
                                        <div class="bg-info rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class="bi bi-check-circle text-white"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1">Nota Processada</h6>
                                        <p class="text-muted mb-1">Dados validados e processados automaticamente</p>
                                        <small class="text-muted">15/01/2024 às 14:36 pelo Sistema</small>
                                    </div>
                                </div>
                                <div class="d-flex mb-3">
                                    <div class="flex-shrink-0">
                                        <div class="bg-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class="bi bi-box-seam text-white"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1">Estoque Atualizado</h6>
                                        <p class="text-muted mb-1">Produtos lançados no estoque</p>
                                        <small class="text-muted">15/01/2024 às 14:37 pelo Sistema</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary">
                        <i class="bi bi-download me-1"></i>
                        Baixar XML
                    </button>
                    <button type="button" class="btn btn-outline-success">
                        <i class="bi bi-printer me-1"></i>
                        Imprimir DANFE
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Funcionalidade de Upload de Arquivos
        const uploadArea = document.getElementById('uploadArea');
        const fileInput = document.getElementById('fileInput');
        const fileList = document.getElementById('fileList');
        const selectedFiles = document.getElementById('selectedFiles');
        const importBtn = document.getElementById('importBtn');

        // Drag and Drop
        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });

        uploadArea.addEventListener('dragleave', () => {
            uploadArea.classList.remove('dragover');
        });

        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            const files = e.dataTransfer.files;
            handleFiles(files);
        });

        // Click para selecionar arquivos
        uploadArea.addEventListener('click', () => {
            fileInput.click();
        });

        fileInput.addEventListener('change', (e) => {
            handleFiles(e.target.files);
        });

        function handleFiles(files) {
            selectedFiles.innerHTML = '';
            fileList.style.display = 'block';
            importBtn.disabled = files.length === 0;

            Array.from(files).forEach((file, index) => {
                const fileItem = document.createElement('div');
                fileItem.className = 'list-group-item d-flex justify-content-between align-items-center';
                fileItem.innerHTML = `
                    <div>
                        <i class="bi bi-file-earmark-text me-2"></i>
                        <strong>${file.name}</strong>
                        <small class="text-muted ms-2">(${(file.size / 1024).toFixed(1)} KB)</small>
                    </div>
                    <button class="btn btn-outline-danger btn-sm" onclick="removeFile(${index})">
                        <i class="bi bi-trash"></i>
                    </button>
                `;
                selectedFiles.appendChild(fileItem);
            });
        }

        function removeFile(index) {
            // Implementar remoção de arquivo específico
            console.log('Remover arquivo:', index);
        }

        // Selecionar todos os checkboxes
        document.getElementById('selectAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.invoice-card input[type="checkbox"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });

        // Simular importação
        importBtn.addEventListener('click', () => {
            importBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Importando...';
            importBtn.disabled = true;
            
            setTimeout(() => {
                alert('XMLs importados com sucesso!');
                bootstrap.Modal.getInstance(document.getElementById('importModal')).hide();
                importBtn.innerHTML = '<i class="bi bi-upload me-1"></i>Importar XMLs';
                importBtn.disabled = false;
                fileList.style.display = 'none';
                selectedFiles.innerHTML = '';
            }, 2000);
        });
    </script>
</body>
</html>
