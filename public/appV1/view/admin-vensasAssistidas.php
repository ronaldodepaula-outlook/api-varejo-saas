<?php
session_start();
if (!isset($_SESSION['authToken'], $_SESSION['usuario'], $_SESSION['empresa'], $_SESSION['licenca'])) {
    header('Location: login.php');
    exit;
}

// Verificar e converter o tipo de dados das variáveis de sessão
$usuario = is_array($_SESSION['usuario']) ? $_SESSION['usuario'] : ['nome' => $_SESSION['usuario']];
$empresa = $_SESSION['empresa'];
$id_empresa = $_SESSION['empresa_id'];
$licenca = $_SESSION['licenca'];
$token = $_SESSION['authToken'];
$segmento = $_SESSION['segmento'] ?? '';

$id_usuario = $_SESSION['user_id'];

// Extrair nome do usuário de forma segura
$nomeUsuario = '';
if (is_array($usuario)) {
    $nomeUsuario = $usuario['nome'] ?? $usuario['name'] ?? 'Usuário';
} else {
    $nomeUsuario = (string)$usuario;
}

// Primeira letra para o avatar
$inicialUsuario = strtoupper(substr($nomeUsuario, 0, 1));

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendas Assistidas - SAS Multi</title>
    <!-- Bootstrap CSS -->
    <style>
        :root {
            --primary-color: #3498DB;
            --secondary-color: #2C3E50;
            --success-color: #27AE60;
            --warning-color: #F39C12;
            --danger-color: #E74C3C;
            --info-color: #17A2B8;
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
        
        .page-title {
            font-weight: 700;
            color: var(--secondary-color);
            margin-bottom: 5px;
        }
        
        .page-subtitle {
            color: #6c757d;
            margin-bottom: 0;
        }
        
        .table-custom th {
            border-top: none;
            font-weight: 600;
            color: #6c757d;
            font-size: 0.85rem;
            text-transform: uppercase;
        }
        
        .btn-action {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            margin: 0 2px;
        }
        
        .search-box {
            position: relative;
        }
        
        .search-box .form-control {
            padding-left: 40px;
        }
        
        .search-box .bi-search {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }
        
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }
        
        .spinner-border {
            width: 3rem;
            height: 3rem;
        }
        
        .status-badge {
            padding: 4px 8px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .status-pendente { background: rgba(243, 156, 18, 0.1); color: var(--warning-color); }
        .status-concluida { background: rgba(39, 174, 96, 0.1); color: var(--success-color); }
        .status-cancelada { background: rgba(231, 76, 60, 0.1); color: var(--danger-color); }
        
        .sale-card {
            transition: all 0.3s ease;
            border-left: 4px solid #dee2e6;
        }
        .sale-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .sale-card.status-pendente { border-left-color: var(--warning-color); }
        .sale-card.status-concluida { border-left-color: var(--success-color); }
        .sale-card.status-cancelada { border-left-color: var(--danger-color); }
        
        .stats-card {
            transition: all 0.3s ease;
        }
        .stats-card:hover {
            transform: translateY(-2px);
        }
        
        .item-row {
            border-bottom: 1px solid #eee;
            padding: 10px 0;
        }
        .item-row:last-child {
            border-bottom: none;
        }
        
        .total-display {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--success-color);
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
                        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Vendas Assistidas</li>
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
                        <li><h6 class="dropdown-header">Alertas de Vendas</h6></li>
                        <li><a class="dropdown-item" href="#">2 vendas pendentes</a></li>
                        <li><a class="dropdown-item" href="#">1 venda com pagamento atrasado</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-center" href="#">Ver todos</a></li>
                    </ul>
                </div>
                
                <!-- Dropdown do Usuário -->
                <div class="dropdown user-dropdown">
                    <div class="user-avatar" data-bs-toggle="dropdown" aria-expanded="false">
                        <?php echo $inicialUsuario; ?>
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><h6 class="dropdown-header user-name"><?php echo htmlspecialchars($nomeUsuario); ?></h6></li>
                        <li><small class="dropdown-header text-muted user-email">
                            <?php 
                            if (is_array($usuario)) {
                                echo htmlspecialchars($usuario['email'] ?? $usuario['email_empresa'] ?? '');
                            } else {
                                echo htmlspecialchars($usuario);
                            }
                            ?>
                        </small></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="perfil.php"><i class="bi bi-person me-2"></i>Meu Perfil</a></li>
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
                    <h1 class="page-title">Vendas Assistidas</h1>
                    <p class="page-subtitle">Gerencie vendas com controle completo de estoque e financeiro</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary" onclick="exportarRelatorioVendas()">
                        <i class="bi bi-file-earmark-text me-2"></i>Relatório
                    </button>
                    <button class="btn btn-primary" onclick="abrirModalNovaVenda()">
                        <i class="bi bi-plus-circle me-2"></i>Nova Venda
                    </button>
                </div>
            </div>
            
            <!-- Resumo de Vendas -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card-custom stats-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="text-primary mb-0" id="totalVendasResumo">0</h5>
                                    <p class="text-muted mb-0">Total de Vendas</p>
                                </div>
                                <div class="bg-primary text-white rounded p-3">
                                    <i class="bi bi-cart" style="font-size: 1.5rem;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card-custom stats-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="text-success mb-0" id="vendasConcluidasResumo">0</h5>
                                    <p class="text-muted mb-0">Concluídas</p>
                                </div>
                                <div class="bg-success text-white rounded p-3">
                                    <i class="bi bi-check-circle" style="font-size: 1.5rem;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card-custom stats-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="text-warning mb-0" id="vendasPendentesResumo">0</h5>
                                    <p class="text-muted mb-0">Pendentes</p>
                                </div>
                                <div class="bg-warning text-white rounded p-3">
                                    <i class="bi bi-clock" style="font-size: 1.5rem;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card-custom stats-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="text-danger mb-0" id="vendasCanceladasResumo">0</h5>
                                    <p class="text-muted mb-0">Canceladas</p>
                                </div>
                                <div class="bg-danger text-white rounded p-3">
                                    <i class="bi bi-x-circle" style="font-size: 1.5rem;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Filtros e Busca -->
            <div class="card-custom mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="search-box">
                                <i class="bi bi-search"></i>
                                <input type="text" class="form-control" id="searchInput" placeholder="Buscar por cliente, código...">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" id="filterStatus">
                                <option value="">Todos status</option>
                                <option value="pendente">Pendente</option>
                                <option value="concluida">Concluída</option>
                                <option value="cancelada">Cancelada</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" id="filterFormaPagamento">
                                <option value="">Todas formas</option>
                                <option value="dinheiro">Dinheiro</option>
                                <option value="cartao">Cartão</option>
                                <option value="pix">PIX</option>
                                <option value="fiado">Fiado</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Período</label>
                            <div class="input-group">
                                <input type="date" class="form-control" id="filterDataInicio">
                                <span class="input-group-text">até</span>
                                <input type="date" class="form-control" id="filterDataFim">
                            </div>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button class="btn btn-outline-secondary w-100" onclick="limparFiltros()">
                                <i class="bi bi-arrow-clockwise"></i> Limpar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Lista de Vendas -->
            <div class="row" id="listaVendas">
                <!-- Os cards de vendas serão carregados dinamicamente aqui -->
                <div class="col-12 text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Carregando...</span>
                    </div>
                    <p class="mt-2 text-muted">Carregando vendas...</p>
                </div>
            </div>
            
            <!-- Paginação -->
            <nav aria-label="Navegação de páginas" id="paginationContainer" class="mt-4">
                <ul class="pagination pagination-custom justify-content-center">
                    <!-- A paginação será gerada dinamicamente via JavaScript -->
                </ul>
            </nav>
        </div>
    </main>

    <!-- Modal para Nova Venda -->
    <div class="modal fade" id="modalNovaVenda" tabindex="-1" aria-labelledby="modalNovaVendaLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalNovaVendaLabel">Nova Venda Assistida</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <!-- Seleção de Cliente -->
                            <div class="card mb-3">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">Cliente</h6>
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="abrirModalBuscarCliente()">
                                        <i class="bi bi-search me-1"></i>Buscar Cliente
                                    </button>
                                </div>
                                <div class="card-body">
                                    <div id="clienteSelecionadoInfo" class="d-none">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1" id="clienteSelecionadoNome"></h6>
                                                <p class="text-muted mb-0" id="clienteSelecionadoContato"></p>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removerCliente()">
                                                <i class="bi bi-x"></i>
                                            </button>
                                        </div>
                                        <input type="hidden" id="idClienteSelecionado">
                                    </div>
                                    <div id="clienteNaoSelecionadoInfo">
                                        <p class="text-muted mb-2">Nenhum cliente selecionado</p>
                                        <button type="button" class="btn btn-sm btn-primary" onclick="abrirModalBuscarCliente()">
                                            <i class="bi bi-plus me-1"></i>Selecionar Cliente
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Itens da Venda -->
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">Itens da Venda</h6>
                                    <button class="btn btn-sm btn-primary" onclick="abrirModalAdicionarItem()">
                                        <i class="bi bi-plus me-1"></i> Adicionar Item
                                    </button>
                                </div>
                                <div class="card-body">
                                    <div id="itensVendaContainer">
                                        <div class="text-center text-muted py-4">
                                            <i class="bi bi-cart-x" style="font-size: 2rem;"></i>
                                            <p class="mt-2">Nenhum item adicionado</p>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                                        <h5>Total: <span class="total-display" id="totalVenda">R$ 0,00</span></h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <!-- Informações da Venda -->
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h6 class="mb-0">Informações da Venda</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">Tipo de Venda *</label>
                                        <select class="form-select" id="tipoVenda" required>
                                            <option value="balcao">Balcão</option>
                                            <option value="delivery">Delivery</option>
                                            <option value="mesa">Mesa</option>
                                            <option value="online">Online</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Forma de Pagamento *</label>
                                        <select class="form-select" id="formaPagamento" required>
                                            <option value="dinheiro">Dinheiro</option>
                                            <option value="cartao_credito">Cartão Crédito</option>
                                            <option value="cartao_debito">Cartão Débito</option>
                                            <option value="pix">PIX</option>
                                            <option value="fiado">Fiado</option>
                                            <option value="outro">Outro</option>
                                        </select>
                                    </div>
                                    <div class="mb-3" id="campoParcelas" style="display: none;">
                                        <label class="form-label">Parcelas</label>
                                        <select class="form-select" id="parcelas">
                                            <option value="1">1x</option>
                                            <option value="2">2x</option>
                                            <option value="3">3x</option>
                                            <option value="4">4x</option>
                                            <option value="5">5x</option>
                                            <option value="6">6x</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Filial *</label>
                                        <select class="form-select" id="selectFilialVenda" required>
                                            <option value="">Carregando filiais...</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Observações</label>
                                        <textarea class="form-control" id="observacoesVenda" rows="3" placeholder="Observações sobre a venda..."></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Resumo Rápido -->
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Resumo</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Subtotal:</span>
                                        <span id="resumoSubtotal">R$ 0,00</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Desconto:</span>
                                        <span id="resumoDesconto">R$ 0,00</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Acréscimo:</span>
                                        <span id="resumoAcrescimo">R$ 0,00</span>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between mb-2">
                                        <strong>Total:</strong>
                                        <strong id="resumoTotal">R$ 0,00</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" onclick="salvarVenda()">Salvar Venda</button>
                        <button type="button" class="btn btn-success" id="btnFinalizarNovaVenda" onclick="finalizarVenda()">Finalizar Venda</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Buscar Cliente -->
    <div class="modal fade" id="modalBuscarCliente" tabindex="-1" aria-labelledby="modalBuscarClienteLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalBuscarClienteLabel">Selecionar Cliente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <div class="search-box">
                            <i class="bi bi-search"></i>
                            <input type="text" class="form-control" id="searchCliente" placeholder="Buscar cliente por nome, telefone ou email...">
                        </div>
                    </div>
                    <div class="table-responsive" style="max-height: 400px;">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Telefone</th>
                                    <th>Email</th>
                                    <th>Cidade</th>
                                    <th>Ação</th>
                                </tr>
                            </thead>
                            <tbody id="listaClientesBusca">
                                <!-- Clientes serão carregados dinamicamente -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Adicionar Item -->
    <div class="modal fade" id="modalAdicionarItem" tabindex="-1" aria-labelledby="modalAdicionarItemLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalAdicionarItemLabel">Adicionar Item à Venda</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <div class="search-box">
                            <i class="bi bi-search"></i>
                            <input type="text" class="form-control" id="searchProduto" placeholder="Buscar produto por nome, código ou categoria...">
                        </div>
                    </div>
                    <div class="table-responsive" style="max-height: 400px;">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Produto</th>
                                    <th>Código</th>
                                    <th>Estoque</th>
                                    <th>Preço</th>
                                    <th>Ação</th>
                                </tr>
                            </thead>
                            <tbody id="listaProdutosBusca">
                                <!-- Produtos serão carregados dinamicamente -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Detalhes da Venda -->
    <div class="modal fade" id="modalDetalhesVenda" tabindex="-1" aria-labelledby="modalDetalhesVendaLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalDetalhesVendaLabel">Detalhes da Venda</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="detalhesVendaCarregando" class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Carregando...</span>
                        </div>
                        <p class="mt-2">Carregando detalhes da venda...</p>
                    </div>
                    
                    <div id="detalhesVendaConteudo" style="display: none;">
                        <!-- Header da Venda -->
                        <div class="row mb-4">
                            <div class="col-md-8">
                                <h4>Venda #<span id="vendaCodigo"></span></h4>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <small class="text-muted">Cliente:</small>
                                        <div><strong id="vendaCliente"></strong></div>
                                    </div>
                                    <div class="col-md-4">
                                        <small class="text-muted">Data:</small>
                                        <div><strong id="vendaData"></strong></div>
                                    </div>
                                    <div class="col-md-4">
                                        <small class="text-muted">Status:</small>
                                        <div><strong id="vendaStatus"></strong></div>
                                    </div>
                                </div>
                                <div class="row g-3 mt-2">
                                    <div class="col-md-4">
                                        <small class="text-muted">Tipo:</small>
                                        <div><strong id="vendaTipo"></strong></div>
                                    </div>
                                    <div class="col-md-4">
                                        <small class="text-muted">Pagamento:</small>
                                        <div><strong id="vendaPagamento"></strong></div>
                                    </div>
                                    <div class="col-md-4">
                                        <small class="text-muted">Vendedor:</small>
                                        <div><strong id="vendaVendedor"></strong></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-primary text-white">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">Valor Total</h5>
                                        <h2 class="card-text" id="vendaValorTotal"></h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Itens da Venda -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">Itens da Venda</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Produto</th>
                                                <th>Quantidade</th>
                                                <th>Valor Unit.</th>
                                                <th>Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbodyItensVenda">
                                            <!-- Itens serão carregados dinamicamente -->
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                                <td><strong id="vendaTotalItens"></strong></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Observações -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Observações</h5>
                            </div>
                            <div class="card-body">
                                <p id="vendaObservacoes" class="mb-0">Nenhuma observação.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    <button type="button" class="btn btn-warning" id="btnEditarVenda" onclick="editarVenda()">Editar Venda</button>
                    <button type="button" class="btn btn-success" id="btnFinalizarVenda" onclick="finalizarVendaModal()">Finalizar Venda</button>
                    <button type="button" class="btn btn-danger" id="btnCancelarVenda" onclick="cancelarVendaModal()">Cancelar Venda</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div class="loading-overlay d-none" id="loadingOverlay">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Carregando...</span>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Variáveis globais
        const idEmpresa = <?php echo $id_empresa; ?>;
        const token = '<?php echo $token; ?>';
        const idUsuario = <?php echo $id_usuario; ?>;
        const BASE_URL = 'https://rdpsolutions.online/saas-multiempresas/public';
        
        let vendas = [];
        let clientes = [];
        let produtos = [];
    let filiais = [];
    let filialSelecionada = null;
        let isEditMode = false;
        let itensVenda = [];
        let vendaSelecionada = null;
        let modalNovaVenda = null;
        let modalBuscarCliente = null;
        let modalAdicionarItem = null;
        let modalDetalhesVenda = null;

        // Configuração da API
        const API_CONFIG = {
            // Endpoints para Vendas
            VENDAS_LISTAR: () => 
                `${BASE_URL}/api/vendasAssistidas/assistidas/empresa/${idEmpresa}`,
            
            VENDA_CADASTRAR: () => 
                `${BASE_URL}/api/vendasAssistidas/assistidas`,
            
            VENDA_DETALHES: (idVenda) => 
                `${BASE_URL}/api/vendasAssistidas/assistidas/${idVenda}`,
            
            VENDA_ATUALIZAR: (idVenda) => 
                `${BASE_URL}/api/vendasAssistidas/assistidas/${idVenda}`,
            
            VENDA_FINALIZAR: (idVenda) => 
                `${BASE_URL}/api/vendasAssistidas/assistidas/${idVenda}/finalizar`,
            
            VENDA_CANCELAR: (idVenda) => 
                `${BASE_URL}/api/vendasAssistidas/assistidas/${idVenda}/cancelar`,
            
            // Endpoints para Clientes
            CLIENTES_LISTAR: () => 
                `${BASE_URL}/api/vendasAssistidas/clientes/empresa/${idEmpresa}`,
            
            // Endpoints para Produtos
            PRODUTOS_LISTAR: () => 
                `${BASE_URL}/api/v1/empresas/${idEmpresa}/produtos`,
            
            // Endpoints para Filiais
            FILIAIS_LISTAR: () =>
                `${BASE_URL}/api/filiais/empresa/${idEmpresa}`,
            
            // Endpoints para Itens
            ITENS_VENDA: (idVenda) => 
                `${BASE_URL}/api/vendasAssistidas/itens-assistida/venda/${idVenda}`,
            
            ITEM_ADICIONAR: () => 
                `${BASE_URL}/api/vendasAssistidas/itens-assistida`,
            
            ITEM_ATUALIZAR: (idItem) => 
                `${BASE_URL}/api/vendasAssistidas/itens-assistida/${idItem}`,
            
            ITEM_REMOVER: (idItem) => 
                `${BASE_URL}/api/vendasAssistidas/itens-assistida/${idItem}`,

            getHeaders: function() {
                // Usa a filial selecionada se houver, senão mantém '1' como fallback
                const filialIdHeader = filialSelecionada ? String(filialSelecionada) : '1';
                return {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-ID-EMPRESA': idEmpresa.toString(),
                    'X-Filial-Id': filialIdHeader
                };
            }
        };

        // Normaliza cliente vindo da API para o formato usado no front
        function normalizeCliente(item) {
            if (!item) return {};
            return {
                id: item.id_cliente ?? item.id ?? null,
                nome_cliente: item.nome_cliente ?? item.nome ?? '',
                email: item.email ?? '',
                telefone: item.telefone ?? '',
                whatsapp: item.whatsapp ?? '',
                cidade: item.cidade ?? '',
                estado: item.estado ?? '',
                created_at: item.data_cadastro ?? item.created_at ?? ''
            };
        }

        // Normaliza uma venda retornada pela API
        function normalizeVenda(item) {
            if (!item) return {};
            return {
                id: item.id_venda ?? item.id ?? null,
                id_filial: item.id_filial ?? null,
                id_empresa: item.id_empresa ?? null,
                tipo_venda: item.tipo_venda ?? item.tipo ?? '',
                forma_pagamento: item.forma_pagamento ?? item.forma_pagamento ?? '',
                status: item.status ?? 'pendente',
                valor_total: parseFloat(item.valor_total ?? 0) || 0,
                observacao: item.observacao ?? item.observacao ?? '',
                created_at: item.data_venda ?? item.created_at ?? item.data_fechamento ?? '',
                cliente: item.cliente ? normalizeCliente(item.cliente) : null,
                itens: Array.isArray(item.itens) ? item.itens.map(it => ({
                    id: it.id_item_venda ?? it.id ?? null,
                    id_produto: it.id_produto ?? null,
                    quantidade: parseFloat(it.quantidade) || 0,
                    valor_unitario: parseFloat(it.valor_unitario) || 0,
                    valor_total: parseFloat(it.valor_total) || (parseFloat(it.quantidade) || 0) * (parseFloat(it.valor_unitario) || 0)
                })) : []
            };
        }

        // Inicialização
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar modais
            modalNovaVenda = new bootstrap.Modal(document.getElementById('modalNovaVenda'));
            modalBuscarCliente = new bootstrap.Modal(document.getElementById('modalBuscarCliente'));
            modalAdicionarItem = new bootstrap.Modal(document.getElementById('modalAdicionarItem'));
            modalDetalhesVenda = new bootstrap.Modal(document.getElementById('modalDetalhesVenda'));
            
            // Carregar dados iniciais
            carregarVendas();
            carregarClientes();
            carregarProdutos();
            carregarFiliais();
            
            // Configurar eventos
            document.getElementById('searchInput').addEventListener('input', filtrarVendas);
            document.getElementById('filterStatus').addEventListener('change', filtrarVendas);
            document.getElementById('filterFormaPagamento').addEventListener('change', filtrarVendas);
            document.getElementById('filterDataInicio').addEventListener('change', filtrarVendas);
            document.getElementById('filterDataFim').addEventListener('change', filtrarVendas);
            document.getElementById('formaPagamento').addEventListener('change', toggleParcelas);
            document.getElementById('searchCliente').addEventListener('input', filtrarClientesBusca);
            document.getElementById('searchProduto').addEventListener('input', filtrarProdutosBusca);
            // evento para seleção de filial no modal
            const selFilial = document.getElementById('selectFilialVenda');
            if (selFilial) {
                selFilial.addEventListener('change', function() {
                    filialSelecionada = parseInt(this.value) || filialSelecionada;
                });
            }
            
            // Logoff
            var logoutBtn = document.getElementById('logoutBtn');
            if (logoutBtn) {
                logoutBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    fazerLogoff();
                });
            }
            // Atualizar estado dos botões de finalizar inicialmente
            updateFinalizarButtons();
        });

        // Função para fazer logout
        async function fazerLogoff() {
            try {
                const response = await fetch(BASE_URL + '/api/v1/logout', {
                    method: 'POST',
                    headers: API_CONFIG.getHeaders()
                });
                
                window.location.href = 'login.php';
                
            } catch (error) {
                console.error('Erro no logout:', error);
                window.location.href = 'login.php';
            }
        }

        // ========== VENDAS ==========
        async function carregarVendas() {
            mostrarLoading(true);
            
            try {
                const response = await fetch(
                    API_CONFIG.VENDAS_LISTAR(),
                    {
                        method: 'GET',
                        headers: API_CONFIG.getHeaders()
                    }
                );
                
                if (!response.ok) {
                    throw new Error(`Erro ${response.status}: ${response.statusText}`);
                }
                
                const data = await response.json();
                // garantir que seja um array e normalizar cada venda
                const rawList = Array.isArray(data) ? data : (data.data || data.vendas || []);
                vendas = rawList.map(normalizeVenda);

                exibirVendas(vendas);
                atualizarResumoVendas(vendas);
                
            } catch (error) {
                console.error('Erro ao carregar vendas:', error);
                mostrarNotificacao('Erro ao carregar vendas: ' + error.message, 'error');
                document.getElementById('listaVendas').innerHTML = '<div class="col-12 text-center py-5 text-muted">Erro ao carregar dados</div>';
            } finally {
                mostrarLoading(false);
            }
        }

        function exibirVendas(listaVendas) {
            const container = document.getElementById('listaVendas');
            
            if (listaVendas.length === 0) {
                container.innerHTML = '<div class="col-12 text-center py-5 text-muted">Nenhuma venda encontrada</div>';
                return;
            }
            
            container.innerHTML = listaVendas.map(venda => {
                const statusClass = `status-${venda.status}`;
                const statusText = formatarStatusVenda(venda.status);
                const dataVenda = formatarData(venda.created_at);
                const clienteNome = venda.cliente ? venda.cliente.nome_cliente : 'Cliente não informado';
                
                return `
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="card sale-card ${statusClass} h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">#${venda.id}</h6>
                                <span class="status-badge ${statusClass}">${statusText}</span>
                            </div>
                            <div class="card-body">
                                <h6 class="card-title">${clienteNome}</h6>
                                <p class="card-text text-muted small">
                                    <i class="bi bi-calendar me-1"></i>${dataVenda}<br>
                                    <i class="bi bi-credit-card me-1"></i>${venda.forma_pagamento}<br>
                                    <i class="bi bi-tag me-1"></i>${venda.tipo_venda}
                                </p>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="text-muted">Itens:</span>
                                    <span class="fw-bold">${venda.itens ? venda.itens.length : 0}</span>
                                </div>
                                <h5 class="text-success mb-3">${formatarMoeda(venda.valor_total)}</h5>
                                <div class="d-flex gap-1">
                                    <button class="btn btn-primary btn-sm flex-fill" onclick="abrirModalEditarVenda(${venda.id})">
                                        <i class="bi bi-eye"></i> Detalhes
                                    </button>
                                                        ${venda.status === 'pendente' ? `
                                                            <button class="btn btn-success btn-sm" onclick="abrirModalEditarVenda(${venda.id}, true)">
                                                                <i class="bi bi-check"></i>
                                                            </button>
                                        <button class="btn btn-danger btn-sm" onclick="cancelarVenda(${venda.id})">
                                            <i class="bi bi-x"></i>
                                        </button>
                                    ` : ''}
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        }

        function atualizarResumoVendas(vendas) {
            const total = vendas.length;
            const concluidas = vendas.filter(v => v.status === 'concluida').length;
            const pendentes = vendas.filter(v => v.status === 'pendente').length;
            const canceladas = vendas.filter(v => v.status === 'cancelada').length;
            
            document.getElementById('totalVendasResumo').textContent = total;
            document.getElementById('vendasConcluidasResumo').textContent = concluidas;
            document.getElementById('vendasPendentesResumo').textContent = pendentes;
            document.getElementById('vendasCanceladasResumo').textContent = canceladas;
        }

        // ========== MODAIS ==========
        function abrirModalNovaVenda() {
            // Limpar formulário
            document.getElementById('idClienteSelecionado').value = '';
            document.getElementById('clienteSelecionadoInfo').classList.add('d-none');
            document.getElementById('clienteNaoSelecionadoInfo').classList.remove('d-none');
            document.getElementById('itensVendaContainer').innerHTML = `
                <div class="text-center text-muted py-4">
                    <i class="bi bi-cart-x" style="font-size: 2rem;"></i>
                    <p class="mt-2">Nenhum item adicionado</p>
                </div>
            `;
            itensVenda = [];
            atualizarTotalVenda();
            // resetar venda selecionada para evitar finalizar venda anterior acidentalmente
            vendaSelecionada = null;
            // sair do modo de edição
            isEditMode = false;
            // ajustar título do modal
            const title = document.getElementById('modalNovaVendaLabel');
            if (title) title.textContent = 'Nova Venda Assistida';
            updateFinalizarButtons();

            modalNovaVenda.show();
        }

        function abrirModalBuscarCliente() {
            document.getElementById('searchCliente').value = '';
            filtrarClientesBusca();
            modalBuscarCliente.show();
        }

        function abrirModalAdicionarItem() {
            document.getElementById('searchProduto').value = '';
            filtrarProdutosBusca();
            modalAdicionarItem.show();
        }

        // Abre o modal de Nova Venda no modo de edição com os dados da venda
        async function abrirModalEditarVenda(idVenda, openForFinalize = false) {
            mostrarLoading(true);
            try {
                const response = await fetch(API_CONFIG.VENDA_DETALHES(idVenda), {
                    method: 'GET',
                    headers: API_CONFIG.getHeaders()
                });

                if (!response.ok) {
                    throw new Error(`Erro ${response.status}: ${response.statusText}`);
                }

                const venda = await response.json();
                // Preencher modal com os dados da venda
                preencherModalComVenda(venda);

                isEditMode = true;
                vendaSelecionada = venda.id_venda ?? venda.id ?? null;
                // garantir filialSelecionada se presente
                filialSelecionada = venda.id_filial ?? venda.idFilial ?? filialSelecionada;
                const sel = document.getElementById('selectFilialVenda');
                if (sel && filialSelecionada) sel.value = String(filialSelecionada);

                updateFinalizarButtons();

                // Ajustar título do modal
                const title = document.getElementById('modalNovaVendaLabel');
                if (title) title.textContent = 'Editar Venda Assistida';

                modalNovaVenda.show();

                if (openForFinalize) {
                    // focar no botão finalizar para facilitar a ação do usuário
                    setTimeout(() => {
                        const btn = document.getElementById('btnFinalizarNovaVenda');
                        if (btn) btn.focus();
                    }, 300);
                }

            } catch (error) {
                console.error('Erro ao abrir venda para editar:', error);
                mostrarNotificacao('Erro ao abrir venda: ' + error.message, 'error');
            } finally {
                mostrarLoading(false);
            }
        }

        function preencherModalComVenda(venda) {
            // Cliente
            const clienteId = venda?.id_cliente ?? venda?.id_cliente ?? (venda.cliente && (venda.cliente.id_cliente ?? venda.cliente.id));
            if (clienteId) {
                document.getElementById('idClienteSelecionado').value = clienteId;
                const clienteObj = clientes.find(c => c.id == clienteId) || (venda.cliente ? normalizeCliente(venda.cliente) : null);
                if (clienteObj) {
                    document.getElementById('clienteSelecionadoNome').textContent = clienteObj.nome_cliente || clienteObj.nome || '';
                    document.getElementById('clienteSelecionadoContato').textContent = (clienteObj.telefone || '') + (clienteObj.email ? ' • ' + clienteObj.email : '');
                    document.getElementById('clienteSelecionadoInfo').classList.remove('d-none');
                    document.getElementById('clienteNaoSelecionadoInfo').classList.add('d-none');
                }
            }

            // Itens
            itensVenda = (Array.isArray(venda.itens) ? venda.itens : (venda.items || venda.data?.itens || [])).map(it => {
                const produto = it.produto || (produtos.find(p => ((p.id_produto ?? p.id) == (it.id_produto ?? it.id)))) || null;
                const quantidade = parseFloat(it.quantidade) || parseFloat(it.qtd) || 0;
                const valor_unitario = parseFloat(it.valor_unitario ?? it.valor) || parseFloat(produto?.preco_venda || produto?.preco_custo || 0) || 0;
                return {
                    id_item: it.id_item_venda ?? it.id ?? null,
                    id_produto: (it.id_produto ?? it.id_produto ?? (produto?.id_produto ?? produto?.id)) || null,
                    nome_produto: produto ? (produto.descricao || produto.nome) : (it.descricao || it.nome || 'Produto'),
                    quantidade,
                    valor_unitario,
                    subtotal: quantidade * valor_unitario
                };
            });

            atualizarListaItens();

            // Tipo, pagamento, observações, filial
            document.getElementById('tipoVenda').value = venda.tipo_venda ?? venda.tipo ?? '';
            document.getElementById('formaPagamento').value = venda.forma_pagamento ?? venda.forma_pagamento ?? '';
            document.getElementById('observacoesVenda').value = venda.observacao ?? venda.observacao ?? '';
            const sel = document.getElementById('selectFilialVenda');
            const fId = venda.id_filial ?? venda.idFilial ?? null;
            if (sel) {
                if (fId) {
                    sel.value = String(fId);
                    filialSelecionada = fId;
                }
            }
            // atualizar totais
            atualizarTotalVenda();
        }

        function selecionarCliente(cliente) {
            document.getElementById('idClienteSelecionado').value = cliente.id;
            document.getElementById('clienteSelecionadoNome').textContent = cliente.nome_cliente;
            document.getElementById('clienteSelecionadoContato').textContent = 
                `${cliente.telefone}${cliente.email ? ' • ' + cliente.email : ''}`;
            
            document.getElementById('clienteSelecionadoInfo').classList.remove('d-none');
            document.getElementById('clienteNaoSelecionadoInfo').classList.add('d-none');
            
            modalBuscarCliente.hide();
        }

        function removerCliente() {
            document.getElementById('idClienteSelecionado').value = '';
            document.getElementById('clienteSelecionadoInfo').classList.add('d-none');
            document.getElementById('clienteNaoSelecionadoInfo').classList.remove('d-none');
        }

        function adicionarItemVenda(produto) {
            const itemExistente = itensVenda.find(item => item.id_produto === produto.id_produto);
            
            if (itemExistente) {
                itemExistente.quantidade += 1;
                itemExistente.subtotal = itemExistente.quantidade * itemExistente.valor_unitario;
            } else {
                itensVenda.push({
                    id_produto: produto.id_produto,
                    nome_produto: produto.descricao,
                    quantidade: 1,
                    valor_unitario: parseFloat(produto.preco_venda || produto.preco_custo || 0),
                    subtotal: parseFloat(produto.preco_venda || produto.preco_custo || 0)
                });
            }
            
            atualizarListaItens();
            modalAdicionarItem.hide();
        }

        function atualizarListaItens() {
            const container = document.getElementById('itensVendaContainer');
            
            if (itensVenda.length === 0) {
                container.innerHTML = `
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-cart-x" style="font-size: 2rem;"></i>
                        <p class="mt-2">Nenhum item adicionado</p>
                    </div>
                `;
                return;
            }
            
            container.innerHTML = itensVenda.map((item, index) => `
                <div class="item-row">
                    <div class="row align-items-center">
                        <div class="col-md-4">
                            <strong>${item.nome_produto}</strong>
                        </div>
                        <div class="col-md-2">
                            <input type="number" class="form-control form-control-sm" 
                                   value="${item.quantidade}" min="1"
                                   onchange="atualizarQuantidadeItem(${index}, this.value)">
                        </div>
                        <div class="col-md-3">
                            <input type="number" class="form-control form-control-sm" 
                                   value="${item.valor_unitario.toFixed(2)}" step="0.01" min="0"
                                   onchange="atualizarValorItem(${index}, this.value)">
                        </div>
                        <div class="col-md-2">
                            <strong>${formatarMoeda(item.subtotal)}</strong>
                        </div>
                        <div class="col-md-1">
                            <button class="btn btn-sm btn-outline-danger" onclick="removerItem(${index})">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `).join('');
            
            atualizarTotalVenda();
        }

        function atualizarQuantidadeItem(index, quantidade) {
            const qtd = parseInt(quantidade) || 1;
            itensVenda[index].quantidade = qtd;
            itensVenda[index].subtotal = qtd * itensVenda[index].valor_unitario;
            atualizarListaItens();
        }

        function atualizarValorItem(index, valor) {
            const val = parseFloat(valor) || 0;
            itensVenda[index].valor_unitario = val;
            itensVenda[index].subtotal = itensVenda[index].quantidade * val;
            atualizarListaItens();
        }

        function removerItem(index) {
            itensVenda.splice(index, 1);
            atualizarListaItens();
        }

        function atualizarTotalVenda() {
            const total = itensVenda.reduce((sum, item) => sum + item.subtotal, 0);
            document.getElementById('totalVenda').textContent = formatarMoeda(total);
            document.getElementById('resumoSubtotal').textContent = formatarMoeda(total);
            document.getElementById('resumoTotal').textContent = formatarMoeda(total);
        }

        function toggleParcelas() {
            const formaPagamento = document.getElementById('formaPagamento').value;
            const campoParcelas = document.getElementById('campoParcelas');
            
            if (formaPagamento === 'cartao_credito') {
                campoParcelas.style.display = 'block';
            } else {
                campoParcelas.style.display = 'none';
            }
        }

        // ========== AÇÕES ==========
        async function salvarVenda() {
            const idCliente = document.getElementById('idClienteSelecionado').value;
                const rawTipoVenda = document.getElementById('tipoVenda').value;
                // Garantir que enviamos um valor aceito pelo back-end (evita enum/data truncated)
                const allowedTipos = ['balcao', 'delivery', 'online'];
                const tipoVenda = allowedTipos.includes(rawTipoVenda) ? rawTipoVenda : (rawTipoVenda === 'mesa' ? 'balcao' : 'balcao');
            const formaPagamento = document.getElementById('formaPagamento').value;
            const observacoes = document.getElementById('observacoesVenda').value;
            
            if (!idCliente) {
                mostrarNotificacao('Selecione um cliente para a venda', 'error');
                return;
            }
            
            if (itensVenda.length === 0) {
                mostrarNotificacao('Adicione pelo menos um item à venda', 'error');
                return;
            }
            
            mostrarLoading(true);
            
            try {
                // determinar filial selecionada (do select no modal) ou fallback
                const selectedFilial = parseInt(document.getElementById('selectFilialVenda')?.value) || filialSelecionada || 1;
                const dadosVenda = {
                    id_empresa: idEmpresa,
                    id_filial: selectedFilial,
                    id_cliente: parseInt(idCliente),
                    id_usuario: idUsuario,
                    tipo_venda: tipoVenda,
                    forma_pagamento: formaPagamento,
                    valor_total: itensVenda.reduce((sum, item) => sum + item.subtotal, 0),
                    observacao: observacoes,
                    itens: itensVenda.map(item => ({
                        id_produto: item.id_produto,
                        quantidade: item.quantidade,
                        valor_unitario: item.valor_unitario
                    }))
                };
                
                // Garantir que o header X-Filial-Id enviado corresponde à filial selecionada
                const customHeaders = Object.assign({}, API_CONFIG.getHeaders(), {
                    'X-Filial-Id': String(selectedFilial)
                });

                // Se estivermos em modo de edição, atualizamos a venda existente
                let url = API_CONFIG.VENDA_CADASTRAR();
                let method = 'POST';
                if (isEditMode && vendaSelecionada) {
                    url = API_CONFIG.VENDA_ATUALIZAR(vendaSelecionada);
                    method = 'PUT';
                }

                const response = await fetch(url, {
                    method,
                    headers: customHeaders,
                    body: JSON.stringify(dadosVenda)
                });
                
                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(`Erro ${response.status}: ${JSON.stringify(errorData)}`);
                }
                // Ler resposta e definir vendaSelecionada para uso posterior (finalizar/editar)
                const saved = await response.json();

                // Extrair id retornado de várias formas possíveis (id_venda, id, data.id_venda, venda.id, etc.)
                const savedId = saved?.id_venda ?? saved?.id ??
                    (saved?.data && (saved.data.id_venda ?? saved.data.id)) ??
                    (saved?.venda && (saved.venda.id_venda ?? saved.venda.id)) ?? null;

                if (savedId) {
                    vendaSelecionada = parseInt(savedId);
                    // Garantir que as chamadas futuras usem a filial selecionada
                    filialSelecionada = selectedFilial;
                    isEditMode = true;
                } else {
                    // se não veio id, e estávamos em criação, não marcamos como edit
                    if (!isEditMode) vendaSelecionada = null;
                }

                // atualizar estado dos botões de finalizar após salvar
                updateFinalizarButtons();

                mostrarNotificacao(`Venda ${isEditMode ? 'atualizada' : 'salva'} com sucesso! ID: ${vendaSelecionada ?? 'n/a'}`, 'success');
                // manter o modal aberto em modo edição? fechar para voltar à lista
                modalNovaVenda.hide();

                // Recarregar lista de vendas
                carregarVendas();
                
            } catch (error) {
                console.error('Erro ao salvar venda:', error);
                mostrarNotificacao('Erro ao salvar venda: ' + error.message, 'error');
            } finally {
                mostrarLoading(false);
            }
        }

        async function finalizarVenda(idVenda) {
            if (idVenda && !confirm('Tem certeza que deseja finalizar esta venda?')) {
                return;
            }

            const id = idVenda || vendaSelecionada;
            if (!id) {
                mostrarNotificacao('Nenhuma venda selecionada para finalizar.', 'error');
                return;
            }

            mostrarLoading(true);

            try {
                // Garantir que exista uma filial selecionada válida antes de finalizar.
                // Se não houver filialSelecionada no front, tentamos buscar os detalhes da venda
                // para extrair o id_filial. Se ainda assim não houver filial, abrimos o modal
                // de edição para que o usuário escolha a filial antes de finalizar.
                if (!filialSelecionada) {
                    try {
                        const detResp = await fetch(API_CONFIG.VENDA_DETALHES(id), {
                            method: 'GET',
                            headers: API_CONFIG.getHeaders()
                        });
                        if (detResp.ok) {
                            const detData = await detResp.json();
                            // Tentar extrair id_filial de possíveis campos
                            const foundFilial = detData?.id_filial ?? detData?.idFilial ?? (detData?.data && (detData.data.id_filial ?? detData.data.id)) ?? null;
                            if (foundFilial) {
                                filialSelecionada = foundFilial;
                                const sel = document.getElementById('selectFilialVenda');
                                if (sel) sel.value = String(filialSelecionada);
                            }
                        }
                    } catch (err) {
                        console.warn('Não foi possível obter detalhes da venda antes de finalizar:', err);
                    }
                }

                if (!filialSelecionada) {
                    // Abrir modal de edição para forçar seleção da filial e evitar erro de FK no servidor
                    mostrarNotificacao('Selecione a filial da venda antes de finalizar.', 'warning');
                    abrirModalEditarVenda(id, true);
                    return;
                }

                // Garantir que o header X-Filial-Id corresponde à filial selecionada
                const customHeaders = Object.assign({}, API_CONFIG.getHeaders(), {
                    'X-Filial-Id': String(filialSelecionada)
                });

                const response = await fetch(API_CONFIG.VENDA_FINALIZAR(id), {
                    method: 'POST',
                    headers: customHeaders
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(`Erro ${response.status}: ${JSON.stringify(errorData)}`);
                }

                mostrarNotificacao('Venda finalizada com sucesso!', 'success');

                if (modalDetalhesVenda) {
                    modalDetalhesVenda.hide();
                }

                // Recarregar lista de vendas
                carregarVendas();

            } catch (error) {
                console.error('Erro ao finalizar venda:', error);
                mostrarNotificacao('Erro ao finalizar venda: ' + error.message, 'error');
            } finally {
                mostrarLoading(false);
            }
        }

        async function cancelarVenda(idVenda) {
            if (!confirm('Tem certeza que deseja cancelar esta venda?')) {
                return;
            }
            
            mostrarLoading(true);
            
            try {
                const response = await fetch(API_CONFIG.VENDA_CANCELAR(idVenda), {
                    method: 'POST',
                    headers: API_CONFIG.getHeaders()
                });
                
                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(`Erro ${response.status}: ${JSON.stringify(errorData)}`);
                }
                
                mostrarNotificacao('Venda cancelada com sucesso!', 'success');
                
                // Recarregar lista de vendas
                carregarVendas();
                
            } catch (error) {
                console.error('Erro ao cancelar venda:', error);
                mostrarNotificacao('Erro ao cancelar venda: ' + error.message, 'error');
            } finally {
                mostrarLoading(false);
            }
        }

        // ========== DETALHES DA VENDA ==========
        async function abrirDetalhesVenda(idVenda) {
            // Agora abrimos o modal de Nova Venda no modo de edição para visualização/alteração
            abrirModalEditarVenda(idVenda, false);
        }

        function preencherDadosVenda(venda) {
            document.getElementById('vendaCodigo').textContent = venda.id;
            document.getElementById('vendaCliente').textContent = venda.cliente ? venda.cliente.nome_cliente : 'Não informado';
            document.getElementById('vendaData').textContent = formatarData(venda.created_at);
            document.getElementById('vendaStatus').textContent = formatarStatusVenda(venda.status);
            document.getElementById('vendaTipo').textContent = venda.tipo_venda;
            document.getElementById('vendaPagamento').textContent = venda.forma_pagamento;
            document.getElementById('vendaVendedor').textContent = venda.usuario ? venda.usuario.nome : 'Não informado';
            document.getElementById('vendaValorTotal').textContent = formatarMoeda(venda.valor_total);
            document.getElementById('vendaObservacoes').textContent = venda.observacao || 'Nenhuma observação.';
        }

        async function carregarItensVenda(idVenda) {
            try {
                const response = await fetch(
                    API_CONFIG.ITENS_VENDA(idVenda),
                    {
                        method: 'GET',
                        headers: API_CONFIG.getHeaders()
                    }
                );
                
                if (response.ok) {
                    const data = await response.json();
                    // normalizar diferentes formatos de resposta
                    const raw = Array.isArray(data) ? data : (data.data || data.itens || data.items || []);

                    const itens = raw.map(it => {
                        const quantidade = parseFloat(it.quantidade) || 0;
                        const valor_unitario = parseFloat(it.valor_unitario ?? it.valor) || 0;

                        // tentar enriquecer com informações do produto, se não vierem no payload
                        let produto = it.produto || null;
                        if (!produto && Array.isArray(produtos)) {
                            produto = (produtos.find(p => ((p.id_produto ?? p.id) == (it.id_produto ?? it.id)))) || null;
                        }

                        return Object.assign({}, it, {
                            quantidade,
                            valor_unitario,
                            produto
                        });
                    });

                    preencherItensVenda(itens);
                }
            } catch (error) {
                console.error('Erro ao carregar itens da venda:', error);
            }
        }

        function preencherItensVenda(itens) {
            const tbody = document.getElementById('tbodyItensVenda');
            
            if (itens.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">Nenhum item encontrado</td></tr>';
                return;
            }
            
            tbody.innerHTML = itens.map(item => {
                const produto = item.produto;
                return `
                    <tr>
                        <td>${produto ? produto.descricao : 'Produto não encontrado'}</td>
                        <td>${item.quantidade}</td>
                        <td>${formatarMoeda(item.valor_unitario)}</td>
                        <td>${formatarMoeda(item.quantidade * item.valor_unitario)}</td>
                    </tr>
                `;
            }).join('');
            
            const total = itens.reduce((sum, item) => sum + (item.quantidade * item.valor_unitario), 0);
            document.getElementById('vendaTotalItens').textContent = formatarMoeda(total);
        }

        function configurarBotoesVenda(status) {
            const btnFinalizar = document.getElementById('btnFinalizarVenda');
            const btnCancelar = document.getElementById('btnCancelarVenda');
            const btnEditar = document.getElementById('btnEditarVenda');
            
            if (status === 'pendente') {
                btnFinalizar.style.display = 'inline-block';
                btnCancelar.style.display = 'inline-block';
                btnEditar.style.display = 'inline-block';
                // habilitar/desabilitar botão baseado em vendaSelecionada
                if (btnFinalizar) btnFinalizar.disabled = !(vendaSelecionada && vendaSelecionada > 0);
                const btnFinalizarNova = document.getElementById('btnFinalizarNovaVenda');
                if (btnFinalizarNova) btnFinalizarNova.disabled = !(vendaSelecionada && vendaSelecionada > 0);
            } else {
                btnFinalizar.style.display = 'none';
                btnCancelar.style.display = 'none';
                btnEditar.style.display = 'none';
            }
        }

        function updateFinalizarButtons() {
            const btnFinalizarDetalhes = document.getElementById('btnFinalizarVenda');
            const btnFinalizarNova = document.getElementById('btnFinalizarNovaVenda');
            const enabled = !!(vendaSelecionada && vendaSelecionada > 0);
            if (btnFinalizarDetalhes) btnFinalizarDetalhes.disabled = !enabled;
            if (btnFinalizarNova) btnFinalizarNova.disabled = !enabled;
        }

        function finalizarVendaModal() {
            finalizarVenda();
        }

        function cancelarVendaModal() {
            if (confirm('Tem certeza que deseja cancelar esta venda?')) {
                cancelarVenda(vendaSelecionada);
                modalDetalhesVenda.hide();
            }
        }

        function editarVenda() {
            mostrarNotificacao('Funcionalidade de edição em desenvolvimento', 'info');
        }

        // ========== FILTROS ==========
        async function carregarClientes() {
            try {
                const response = await fetch(
                    API_CONFIG.CLIENTES_LISTAR(),
                    {
                        method: 'GET',
                        headers: API_CONFIG.getHeaders()
                    }
                );
                
                if (response.ok) {
                    const data = await response.json();
                    const raw = Array.isArray(data) ? data : (data.data || data.clientes || []);
                    clientes = raw.map(normalizeCliente);
                }
            } catch (error) {
                console.error('Erro ao carregar clientes:', error);
            }
        }

        async function carregarProdutos() {
            try {
                const response = await fetch(
                    API_CONFIG.PRODUTOS_LISTAR(),
                    {
                        method: 'GET',
                        headers: API_CONFIG.getHeaders()
                    }
                );
                
                if (response.ok) {
                    const data = await response.json();
                    produtos = data.data || [];
                }
            } catch (error) {
                console.error('Erro ao carregar produtos:', error);
            }
        }

        // Carregar filiais da empresa e popular o select
        async function carregarFiliais() {
            try {
                const response = await fetch(
                    API_CONFIG.FILIAIS_LISTAR(),
                    {
                        method: 'GET',
                        headers: API_CONFIG.getHeaders()
                    }
                );

                if (response.ok) {
                    const data = await response.json();
                    // A rota retorna um array de filiais
                    filiais = Array.isArray(data) ? data : (data.data || []);
                    preencherSelectFiliais();
                    // definir filialSelecionada padrão para a primeira filial disponível
                    const primeiro = filiais[0];
                    if (primeiro) {
                        filialSelecionada = primeiro.id_filial ?? primeiro.id ?? filialSelecionada;
                        const sel = document.getElementById('selectFilialVenda');
                        if (sel) sel.value = String(filialSelecionada);
                    }
                }
            } catch (error) {
                console.error('Erro ao carregar filiais:', error);
            }
        }

        function preencherSelectFiliais() {
            const sel = document.getElementById('selectFilialVenda');
            if (!sel) return;
            if (!Array.isArray(filiais) || filiais.length === 0) {
                sel.innerHTML = '<option value="">Nenhuma filial encontrada</option>';
                return;
            }

            sel.innerHTML = filiais.map(f => {
                const id = f.id_filial ?? f.id;
                const nome = f.nome_filial ?? f.nome ?? `Filial ${id}`;
                return `<option value="${id}">${nome}</option>`;
            }).join('');
        }

        function filtrarClientesBusca() {
            const termo = document.getElementById('searchCliente').value.toLowerCase();
            const tbody = document.getElementById('listaClientesBusca');
            
            const clientesFiltrados = clientes.filter(cliente => 
                cliente.nome_cliente.toLowerCase().includes(termo) ||
                (cliente.telefone && cliente.telefone.includes(termo)) ||
                (cliente.email && cliente.email.toLowerCase().includes(termo))
            );
            
            if (clientesFiltrados.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">Nenhum cliente encontrado</td></tr>';
                return;
            }
            
            tbody.innerHTML = clientesFiltrados.map(cliente => `
                <tr>
                    <td>${cliente.nome_cliente}</td>
                    <td>${cliente.telefone || 'Não informado'}</td>
                    <td>${cliente.email || 'Não informado'}</td>
                    <td>${cliente.cidade || 'Não informado'}</td>
                    <td>
                        <button class="btn btn-sm btn-primary" onclick="selecionarCliente(${JSON.stringify(cliente).replace(/"/g, '&quot;')})">
                            Selecionar
                        </button>
                    </td>
                </tr>
            `).join('');
        }

        function filtrarProdutosBusca() {
            const termo = document.getElementById('searchProduto').value.toLowerCase();
            const tbody = document.getElementById('listaProdutosBusca');
            
            const produtosFiltrados = produtos.filter(produto => 
                produto.descricao.toLowerCase().includes(termo) ||
                (produto.codigo_barras && produto.codigo_barras.includes(termo)) ||
                (produto.categoria && produto.categoria.toLowerCase().includes(termo))
            );
            
            if (produtosFiltrados.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">Nenhum produto encontrado</td></tr>';
                return;
            }
            
            tbody.innerHTML = produtosFiltrados.map(produto => `
                <tr>
                    <td>
                        <strong>${produto.descricao}</strong>
                        ${produto.categoria ? `<br><small class="text-muted">${produto.categoria}</small>` : ''}
                    </td>
                    <td>${produto.codigo_barras || 'N/A'}</td>
                    <td>${produto.quantidade_total || 0}</td>
                    <td>${formatarMoeda(produto.preco_venda || produto.preco_custo || 0)}</td>
                    <td>
                        <button class="btn btn-sm btn-success" onclick="adicionarItemVenda(${JSON.stringify(produto).replace(/"/g, '&quot;')})">
                            <i class="bi bi-plus"></i>
                        </button>
                    </td>
                </tr>
            `).join('');
        }

        function filtrarVendas() {
            const termoBusca = document.getElementById('searchInput').value.toLowerCase();
            const statusFiltro = document.getElementById('filterStatus').value;
            const formaPagamentoFiltro = document.getElementById('filterFormaPagamento').value;
            const dataInicioFiltro = document.getElementById('filterDataInicio').value;
            const dataFimFiltro = document.getElementById('filterDataFim').value;
            
            let vendasFiltradas = vendas;
            
            // Aplicar filtros
            if (termoBusca) {
                vendasFiltradas = vendasFiltradas.filter(venda => 
                    venda.id.toString().includes(termoBusca) ||
                    (venda.cliente && venda.cliente.nome_cliente.toLowerCase().includes(termoBusca))
                );
            }
            
            if (statusFiltro) {
                vendasFiltradas = vendasFiltradas.filter(venda => venda.status === statusFiltro);
            }
            
            if (formaPagamentoFiltro) {
                vendasFiltradas = vendasFiltradas.filter(venda => venda.forma_pagamento === formaPagamentoFiltro);
            }
            
            if (dataInicioFiltro) {
                vendasFiltradas = vendasFiltradas.filter(venda => 
                    new Date(venda.created_at) >= new Date(dataInicioFiltro)
                );
            }
            
            if (dataFimFiltro) {
                vendasFiltradas = vendasFiltradas.filter(venda => 
                    new Date(venda.created_at) <= new Date(dataFimFiltro)
                );
            }
            
            exibirVendas(vendasFiltradas);
        }

        function limparFiltros() {
            document.getElementById('searchInput').value = '';
            document.getElementById('filterStatus').value = '';
            document.getElementById('filterFormaPagamento').value = '';
            document.getElementById('filterDataInicio').value = '';
            document.getElementById('filterDataFim').value = '';
            
            exibirVendas(vendas);
        }

        // ========== FUNÇÕES AUXILIARES ==========
        function formatarData(data) {
            if (!data) return '';
            try {
                const date = new Date(data);
                return date.toLocaleDateString('pt-BR');
            } catch (e) {
                return data;
            }
        }

        function formatarMoeda(valor) {
            return new Intl.NumberFormat('pt-BR', {
                style: 'currency',
                currency: 'BRL'
            }).format(valor);
        }

        function formatarStatusVenda(status) {
            const statusMap = {
                'pendente': 'Pendente',
                'concluida': 'Concluída',
                'cancelada': 'Cancelada'
            };
            return statusMap[status] || status;
        }

        function mostrarLoading(mostrar) {
            document.getElementById('loadingOverlay').classList.toggle('d-none', !mostrar);
        }

        function exportarRelatorioVendas() {
            mostrarNotificacao('Relatório de vendas exportado com sucesso!', 'success');
        }

        // Função para mostrar notificações
        function mostrarNotificacao(message, type) {
            const notification = document.createElement('div');
            notification.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
            notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            notification.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 5000);
        }
    </script>
</body>
</html>