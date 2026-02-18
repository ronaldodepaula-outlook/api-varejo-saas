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
    <title>PDV - Gestão Operacional - SAS Multi</title>
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
        
        .caixa-status {
            display: inline-flex;
            align-items: center;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        
        .caixa-aberto { background: rgba(39, 174, 96, 0.1); color: var(--success-color); }
        .caixa-fechado { background: rgba(231, 76, 60, 0.1); color: var(--danger-color); }
        
        .stat-card {
            text-align: center;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .stat-card .value {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .stat-card .label {
            font-size: 0.9rem;
            color: #6c757d;
        }
        
        .venda-status {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .venda-finalizada { background: rgba(39, 174, 96, 0.1); color: var(--success-color); }
        .venda-cancelada { background: rgba(231, 76, 60, 0.1); color: var(--danger-color); }
        .venda-pendente { background: rgba(243, 156, 18, 0.1); color: var(--warning-color); }
        
        .btn-action {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            margin: 0 2px;
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
        
        .filial-selector {
            max-width: 300px;
        }
        
        .pagamento-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .pagamento-item:last-child {
            border-bottom: none;
        }
            padding: 6px 0;
            /* Resumo de pagamentos - cards horizontais */
            .resumo-pagamentos-wrapper {
                display: flex;
                gap: 12px;
                align-items: stretch;
                justify-content: center;
                flex-wrap: wrap;
            }

            .pagamento-card {
                background: linear-gradient(180deg, #ffffff 0%, #f8f9fb 100%);
                border: 1px solid rgba(0,0,0,0.06);
                border-radius: 10px;
                padding: 14px 18px;
                min-width: 180px;
                box-shadow: 0 6px 18px rgba(16,24,40,0.04);
                display: flex;
                flex-direction: column;
                align-items: center;
                text-align: center;
            }

            .pagamento-card .icon {
                font-size: 28px;
                color: var(--primary-color);
                margin-bottom: 8px;
            }

            .pagamento-card .method-name {
                font-size: 0.85rem;
                color: #6c757d;
                margin-bottom: 6px;
            }

            .pagamento-card .method-value {
                font-size: 1.25rem;
                font-weight: 700;
                color: #111827;
                margin-bottom: 4px;
            }

            .pagamento-card .method-count {
                font-size: 0.8rem;
                color: #6c757d;
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
                        <li class="breadcrumb-item active">PDV - Gestão Operacional</li>
                    </ol>
                </nav>
            </div>
            
            <div class="header-right">
                <!-- Seletor de Filial -->
                <div class="filial-selector me-3">
                    <select class="form-select form-select-sm" id="seletorFilial">
                        <option value="">Selecionando filial...</option>
                    </select>
                </div>
                
                <!-- Notificações -->
                <div class="dropdown">
                    <button class="btn btn-link text-dark position-relative" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-bell" style="font-size: 1.25rem;"></i>
                        <span class="badge bg-danger position-absolute translate-middle rounded-pill" style="font-size: 0.6rem; top: 8px; right: 8px;">2</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><h6 class="dropdown-header">Alertas PDV</h6></li>
                        <li><a class="dropdown-item" href="#">Caixa precisa ser fechado</a></li>
                        <li><a class="dropdown-item" href="#">3 vendas pendentes de processamento</a></li>
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
                    <h1 class="page-title">PDV - Gestão Operacional</h1>
                    <p class="page-subtitle">Gerencie caixas, vendas e operações do PDV</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary" onclick="abrirModalConfiguracao()">
                        <i class="bi bi-gear me-2"></i>Configurações
                    </button>
                    <button class="btn btn-primary" onclick="abrirPDV()">
                        <i class="bi bi-cash-coin me-2"></i>Abrir PDV
                    </button>
                </div>
            </div>
            
            <!-- Status do Caixa -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card-custom">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="mb-1">Status do Caixa</h4>
                                    <p class="text-muted mb-0" id="statusCaixaInfo">Carregando informações do caixa...</p>
                                </div>
                                <div class="d-flex gap-2" id="botoesCaixa">
                                    <!-- Botões serão carregados dinamicamente -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Resumo do Dia -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="stat-card bg-primary text-white">
                        <div class="value" id="totalVendasDia">0</div>
                        <div class="label">Vendas Hoje</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card bg-success text-white">
                        <div class="value" id="valorTotalDia">R$ 0</div>
                        <div class="label">Faturamento</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card bg-info text-white">
                        <div class="value" id="ticketMedio">R$ 0</div>
                        <div class="label">Ticket Médio</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card bg-warning text-white">
                        <div class="value" id="produtosVendidos">0</div>
                        <div class="label">Itens Vendidos</div>
                    </div>
                </div>
            </div>
            
            <!-- Últimas Vendas -->
            <div class="row">
                <div class="col-md-8">
                    <div class="card-custom">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Últimas Vendas</h5>
                            <button class="btn btn-sm btn-outline-primary" onclick="carregarVendas()">
                                <i class="bi bi-arrow-clockwise"></i>
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Data/Hora</th>
                                            <th>Cliente</th>
                                            <th>Itens</th>
                                            <th>Valor</th>
                                            <th>Status</th>
                                            <th width="100">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyVendas">
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">Selecione uma filial para carregar as vendas</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Ações Rápidas -->
                <div class="col-md-4">
                    <div class="card-custom mb-4">
                        <div class="card-header-custom">
                            <h5 class="mb-0">Ações Rápidas</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <button class="btn btn-outline-primary" onclick="abrirModalAberturaCaixa()">
                                    <i class="bi bi-cash-stack me-2"></i>Abrir Caixa
                                </button>
                                <button class="btn btn-outline-success" onclick="abrirModalFechamentoCaixa()">
                                    <i class="bi bi-cash-coin me-2"></i>Fechar Caixa
                                </button>
                                <button class="btn btn-outline-info" onclick="abrirModalRelatorio()">
                                    <i class="bi bi-graph-up me-2"></i>Relatórios
                                </button>
                                <button class="btn btn-outline-warning" onclick="abrirModalSangria()">
                                    <i class="bi bi-arrow-down-up me-2"></i>Sangria/Reforço
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Métodos de Pagamento -->
                    <div class="card-custom">
                        <div class="card-header-custom">
                            <h5 class="mb-0">Métodos de Pagamento</h5>
                        </div>
                        <div class="card-body">
                            <div id="resumoPagamentos">
                                <p class="text-muted text-center">Selecione uma filial para ver os dados</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal para Abertura de Caixa -->
    <div class="modal fade" id="modalAberturaCaixa" tabindex="-1" aria-labelledby="modalAberturaCaixaLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalAberturaCaixaLabel">Abertura de Caixa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formAberturaCaixa">
                        <div class="mb-3">
                            <label class="form-label">Filial *</label>
                            <select class="form-select" id="filialAbertura" required>
                                <option value="">Selecione a filial...</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Valor Inicial *</label>
                            <input type="number" class="form-control" id="valorInicialCaixa" step="0.01" min="0" required>
                            <div class="form-text">Valor em dinheiro para iniciar o caixa.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Observações</label>
                            <textarea class="form-control" id="observacoesAbertura" rows="3" placeholder="Opcional"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="confirmarAberturaCaixa()">Confirmar Abertura</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Fechamento de Caixa -->
    <div class="modal fade" id="modalFechamentoCaixa" tabindex="-1" aria-labelledby="modalFechamentoCaixaLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalFechamentoCaixaLabel">Fechamento de Caixa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="resumoFechamento">
                        <div class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Carregando...</span>
                            </div>
                            <p class="mt-2">Carregando resumo do caixa...</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="confirmarFechamentoCaixa()">Confirmar Fechamento</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Detalhes da Venda -->
    <div class="modal fade" id="modalDetalhesVenda" tabindex="-1" aria-labelledby="modalDetalhesVendaLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalDetalhesVendaLabel">Detalhes da Venda</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="detalhesVendaBody">
                    <!-- Conteúdo será carregado dinamicamente -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Sangria/Reforço -->
    <div class="modal fade" id="modalSangria" tabindex="-1" aria-labelledby="modalSangriaLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalSangriaLabel">Sangria/Reforço de Caixa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formSangria">
                        <div class="mb-3">
                            <label class="form-label">Tipo *</label>
                            <select class="form-select" id="tipoSangria" required>
                                <option value="">Selecione...</option>
                                <option value="sangria">Sangria</option>
                                <option value="reforco">Reforço</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Valor *</label>
                            <input type="number" class="form-control" id="valorSangria" step="0.01" min="0" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Motivo</label>
                            <textarea class="form-control" id="motivoSangria" rows="3" placeholder="Opcional"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="confirmarSangria()">Confirmar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Relatórios -->
    <div class="modal fade" id="modalRelatorios" tabindex="-1" aria-labelledby="modalRelatoriosLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalRelatoriosLabel">Relatórios PDV</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <i class="bi bi-receipt display-4 text-primary mb-3"></i>
                                    <h5 class="card-title">Relatório de Vendas</h5>
                                    <p class="card-text">Relatório detalhado de vendas por período.</p>
                                    <button class="btn btn-primary" onclick="gerarRelatorio('vendas')">Gerar</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <i class="bi bi-cash-coin display-4 text-success mb-3"></i>
                                    <h5 class="card-title">Relatório de Caixa</h5>
                                    <p class="card-text">Relatório de movimentação de caixa.</p>
                                    <button class="btn btn-primary" onclick="gerarRelatorio('caixa')">Gerar</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <i class="bi bi-graph-up display-4 text-info mb-3"></i>
                                    <h5 class="card-title">Relatório Financeiro</h5>
                                    <p class="card-text">Análise financeira e performance.</p>
                                    <button class="btn btn-primary" onclick="gerarRelatorio('financeiro')">Gerar</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <i class="bi bi-box-seam display-4 text-warning mb-3"></i>
                                    <h5 class="card-title">Relatório de Produtos</h5>
                                    <p class="card-text">Produtos mais vendidos e estoque.</p>
                                    <button class="btn btn-primary" onclick="gerarRelatorio('produtos')">Gerar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Configurações -->
    <div class="modal fade" id="modalConfiguracao" tabindex="-1" aria-labelledby="modalConfiguracaoLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalConfiguracaoLabel">Configurações PDV</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formConfiguracao">
                        <div class="mb-3">
                            <label class="form-label">Impressora</label>
                            <select class="form-select" id="impressora">
                                <option value="">Selecione a impressora...</option>
                                <option value="termica">Impressora Térmica</option>
                                <option value="pdf">Gerar PDF</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tipo de Etiqueta</label>
                            <select class="form-select" id="tipoEtiqueta">
                                <option value="58mm">58mm</option>
                                <option value="80mm">80mm</option>
                            </select>
                        </div>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="exibirImposto">
                            <label class="form-check-label" for="exibirImposto">Exibir impostos no cupom</label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="salvarConfiguracoes()">Salvar</button>
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
        const idUsuario = <?php echo $_SESSION['usuario_id'] ?? 1; ?>;
        const token = '<?php echo $token; ?>';
    const BASE_URL = 'https://rdpsolutions.online/saas-multiempresas/public';
        
        let modalAberturaCaixa = null;
        let modalFechamentoCaixa = null;
        let modalDetalhesVenda = null;
        let modalSangria = null;
        let modalRelatorios = null;
        let modalConfiguracao = null;
        
        let caixaAtual = null;
        let filiais = [];
        let filialSelecionada = null;

        // Configuração da API - CORRIGIDA
        const API_CONFIG = {
            // Endpoints PDV
            CAIXA_STATUS: (idEmpresa, idFilial, status = '') => {
                let url = `${BASE_URL}/api/v1/empresas/${idEmpresa}/pdv/caixas/status?id_filial=${idFilial}`;
                if (status) {
                    url += `&status=${status}`;
                }
                return url;
            },
            
            CAIXA_ABERTURA: () => `${BASE_URL}/api/pdv/caixas/abrir`,
            
            CAIXA_FECHAMENTO: (idCaixa) => `${BASE_URL}/api/pdv/caixas/${idCaixa}/fechar`,
            
            VENDAS_DIA: (idEmpresa, idFilial) => 
                `${BASE_URL}/api/v1/empresas/${idEmpresa}/pdv/vendas?data=${hoje()}&id_filial=${idFilial}`,
            
            RESUMO_DIA: (idEmpresa, idFilial) => 
                `${BASE_URL}/api/v1/empresas/${idEmpresa}/pdv/resumo/dia?data=${hoje()}&id_filial=${idFilial}`,

            DETALHES_VENDA: (idEmpresa, idVenda) =>
                `${BASE_URL}/api/v1/empresas/${idEmpresa}/pdv/vendas/${idVenda}`,
            DASHBOARD: (idEmpresa, idFilial, dataInicio, dataFim) =>
                `${BASE_URL}/api/pdv/dashboard?id_empresa=${idEmpresa}&id_filial=${idFilial}&data_inicio=${dataInicio}&data_fim=${dataFim}`,
            PAGAMENTOS_RESUMO: (idEmpresa, idFilial, dataInicio, dataFim) =>
                `${BASE_URL}/api/pdv/vendas/pagamentos-resumo?id_empresa=${idEmpresa}&id_filial=${idFilial}&data_inicio=${dataInicio}&data_fim=${dataFim}`,
            // Endpoint alternativo que retorna detalhes via query string (usado por algumas rotas)
            DETALHES_VENDA_QUERY: (idEmpresa, idFilial, idVenda) =>
                `${BASE_URL}/api/pdv/vendas/detalhes?id_empresa=${idEmpresa}&id_filial=${idFilial}&id_venda=${idVenda}`,

            FILIAIS: (idEmpresa) =>  `${BASE_URL}/api/filiais/empresa/${idEmpresa}`,

            SANGRIA: () => `${BASE_URL}/api/pdv/caixas/sangria`,

            getHeaders: function() {
                return {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-ID-EMPRESA': idEmpresa.toString()
                };
            }
        };

        // Inicialização
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar modais
            modalAberturaCaixa = new bootstrap.Modal(document.getElementById('modalAberturaCaixa'));
            modalFechamentoCaixa = new bootstrap.Modal(document.getElementById('modalFechamentoCaixa'));
            modalDetalhesVenda = new bootstrap.Modal(document.getElementById('modalDetalhesVenda'));
            modalSangria = new bootstrap.Modal(document.getElementById('modalSangria'));
            modalRelatorios = new bootstrap.Modal(document.getElementById('modalRelatorios'));
            modalConfiguracao = new bootstrap.Modal(document.getElementById('modalConfiguracao'));
            
            // Configurar evento de mudança de filial
            document.getElementById('seletorFilial').addEventListener('change', function() {
                const idFilial = this.value;
                if (idFilial) {
                    filialSelecionada = idFilial;
                    carregarDadosFilial(idFilial);
                }
            });
            
            // Carregar dados iniciais
            carregarFiliais();
            
            // Logoff
            document.getElementById('logoutBtn').addEventListener('click', function(e) {
                e.preventDefault();
                fazerLogoff();
            });
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

        // ========== FILIAIS ==========
        async function carregarFiliais() {
            mostrarLoading(true);
            try {
                const response = await fetch(
                    API_CONFIG.FILIAIS(idEmpresa),
                    {
                        method: 'GET',
                        headers: API_CONFIG.getHeaders()
                    }
                );
                
                if (!response.ok) {
                    throw new Error(`Erro ${response.status}: ${response.statusText}`);
                }
                
                const data = await response.json();
                
                // Verificar estrutura da resposta
                if (data.data && Array.isArray(data.data)) {
                    filiais = data.data;
                } else if (Array.isArray(data)) {
                    filiais = data;
                } else {
                    throw new Error('Formato de dados inválido para filiais');
                }
                
                preencherSeletorFiliais();
                
                // Selecionar primeira filial por padrão
                if (filiais.length > 0) {
                    document.getElementById('seletorFilial').value = filiais[0].id_filial;
                    filialSelecionada = filiais[0].id_filial;
                    carregarDadosFilial(filiais[0].id_filial);
                } else {
                    document.getElementById('statusCaixaInfo').textContent = 'Nenhuma filial encontrada';
                }
                
            } catch (error) {
                console.error('Erro ao carregar filiais:', error);
                mostrarNotificacao('Erro ao carregar filiais: ' + error.message, 'error');
                document.getElementById('statusCaixaInfo').textContent = 'Erro ao carregar filiais';
            } finally {
                mostrarLoading(false);
            }
        }

        function preencherSeletorFiliais() {
            const select = document.getElementById('seletorFilial');
            const selectAbertura = document.getElementById('filialAbertura');
            
            select.innerHTML = '';
            selectAbertura.innerHTML = '<option value="">Selecione a filial...</option>';
            
            if (filiais.length === 0) {
                select.innerHTML = '<option value="">Nenhuma filial disponível</option>';
                return;
            }
            
            filiais.forEach(filial => {
                select.innerHTML += `<option value="${filial.id_filial}">${filial.nome_filial}</option>`;
                selectAbertura.innerHTML += `<option value="${filial.id_filial}">${filial.nome_filial}</option>`;
            });
        }

        function carregarDadosFilial(idFilial) {
            if (!idFilial) return;
            
            document.getElementById('statusCaixaInfo').textContent = 'Carregando informações do caixa...';
            document.getElementById('tbodyVendas').innerHTML = '<tr><td colspan="7" class="text-center text-muted">Carregando vendas...</td></tr>';
            document.getElementById('resumoPagamentos').innerHTML = '<p class="text-muted text-center">Carregando dados...</p>';
            
            // Resetar métricas
            document.getElementById('totalVendasDia').textContent = '0';
            document.getElementById('valorTotalDia').textContent = 'R$ 0';
            document.getElementById('ticketMedio').textContent = 'R$ 0';
            document.getElementById('produtosVendidos').textContent = '0';
            
            carregarStatusCaixa(idFilial);
            carregarResumoDia(idFilial);
            carregarVendas(idFilial);
            // Carregar métricas de dashboard (cards laterais)
            carregarDashboard(idFilial);
            // Carregar resumo de pagamentos destacado
            carregarPagamentosResumo(idFilial);
        }

        // ========== DASHBOARD METRICS (cards) ==========
        function primeiroDiaMes() {
            const now = new Date();
            return new Date(now.getFullYear(), now.getMonth(), 1).toISOString().split('T')[0];
        }

        async function carregarDashboard(idFilial, dataInicio, dataFim) {
            if (!idFilial) return;

            // Defaults: início do mês até hoje
            const inicio = dataInicio || primeiroDiaMes();
            const fim = dataFim || hoje();

            try {
                const response = await fetch(API_CONFIG.DASHBOARD(idEmpresa, idFilial, inicio, fim), {
                    method: 'GET',
                    headers: API_CONFIG.getHeaders()
                });

                if (!response.ok) {
                    throw new Error(`Erro ${response.status}: ${response.statusText}`);
                }

                const raw = await response.json();
                const dados = raw.data || raw;

                // Preencher cards principais
                const totalVendas = parseInt(dados.total_vendas || 0, 10) || 0;
                const somaValor = parseFloat(dados.soma_valor_total || dados.soma_valor_total || 0) || 0;

                document.getElementById('totalVendasDia').textContent = totalVendas;
                document.getElementById('valorTotalDia').textContent = `R$ ${formatarPreco(somaValor)}`;

                const ticketMedio = totalVendas > 0 ? (somaValor / totalVendas) : 0;
                document.getElementById('ticketMedio').textContent = `R$ ${formatarPreco(ticketMedio)}`;

                // itens vendidos: se o endpoint trouxer total_itens, usa; caso contrário manter 0 ou '-'
                const itensVendidos = (typeof dados.total_itens !== 'undefined') ? dados.total_itens : '-';
                document.getElementById('produtosVendidos').textContent = itensVendidos;

                // Opcional: log dos pagamentos_por_forma para debug
                if (dados.pagamentos_por_forma) {
                    console.debug('Pagamentos por forma:', dados.pagamentos_por_forma);
                }

            } catch (error) {
                console.error('Erro ao carregar dashboard metrics:', error);
            }
        }

        // ========== PAGAMENTOS RESUMO (destaque) ==========
        async function carregarPagamentosResumo(idFilial, dataInicio, dataFim) {
            if (!idFilial) return;

            const inicio = dataInicio || primeiroDiaMes();
            const fim = dataFim || hoje();

            try {
                const response = await fetch(API_CONFIG.PAGAMENTOS_RESUMO(idEmpresa, idFilial, inicio, fim), {
                    method: 'GET',
                    headers: API_CONFIG.getHeaders()
                });

                if (!response.ok) {
                    throw new Error(`Erro ${response.status}: ${response.statusText}`);
                }

                const data = await response.json();
                const dados = data.data || data;

                const container = document.getElementById('resumoPagamentos');
                if (!dados || !dados.por_forma || dados.por_forma.length === 0) {
                    container.innerHTML = '<p class="text-muted text-center">Nenhum pagamento registrado no período</p>';
                    return;
                }

                // helper para mapear ícones por forma
                const iconFor = (forma) => {
                    switch ((forma || '').toString().toLowerCase()) {
                        case 'pix': return '<i class="bi bi-qr-code"></i>';
                        case 'cartao_debito':
                        case 'cartao_credito': return '<i class="bi bi-credit-card"></i>';
                        case 'dinheiro': return '<i class="bi bi-cash-coin"></i>';
                        default: return '<i class="bi bi-wallet2"></i>';
                    }
                };

                // Montar HTML com cards horizontais
                let html = `<div class="resumo-pagamentos-wrapper">`;

                // Total geral (destaque maior)
                html += `
                    <div class="pagamento-card" style="min-width:220px;background:linear-gradient(180deg,#f0f7ff,#ffffff);">
                        <div class="icon"><i class="bi bi-receipt" style="color:var(--primary-color)"></i></div>
                        <div class="method-name">Total Recebido</div>
                        <div class="method-value">R$ ${formatarPreco(dados.total_geral || 0)}</div>
                        <div class="method-count">Período: ${dados.periodo?.inicio || inicio} → ${dados.periodo?.fim || fim}</div>
                    </div>
                `;

                // Cards por forma de pagamento
                dados.por_forma.forEach(p => {
                    const icon = iconFor(p.forma_pagamento);
                    html += `
                        <div class="pagamento-card">
                            <div class="icon">${icon}</div>
                            <div class="method-name">${formatarFormaPagamento(p.forma_pagamento)}</div>
                            <div class="method-value">R$ ${formatarPreco(p.total_pago)}</div>
                            <div class="method-count">${p.total_pagamentos || 0} pagamentos</div>
                        </div>
                    `;
                });

                html += `</div>`;

                container.innerHTML = html;
                // marcar que o resumo de pagamentos foi renderizado para evitar sobreposição
                window.pagamentosResumoRendered = true;

            } catch (error) {
                console.error('Erro ao carregar resumo de pagamentos:', error);
                document.getElementById('resumoPagamentos').innerHTML = '<p class="text-muted text-center">Erro ao carregar pagamentos</p>';
            }
        }

        // ========== CAIXA ==========
        async function carregarStatusCaixa(idFilial) {
            try {
                // Buscar caixa aberto
                const response = await fetch(
                    API_CONFIG.CAIXA_STATUS(idEmpresa, idFilial, 'aberto'),
                    {
                        method: 'GET',
                        headers: API_CONFIG.getHeaders()
                    }
                );
                
                if (!response.ok) {
                    throw new Error(`Erro ${response.status}: ${response.statusText}`);
                }
                
                const data = await response.json();
                
                // Verificar se há caixa aberto - CORREÇÃO DA LÓGICA
                let caixaAberto = null;
                if (data.data && Array.isArray(data.data) && data.data.length > 0) {
                    caixaAberto = data.data[0];
                } else if (Array.isArray(data) && data.length > 0) {
                    caixaAberto = data[0];
                } else if (data.data && data.data.id_caixa) {
                    caixaAberto = data.data;
                } else if (data.id_caixa) {
                    caixaAberto = data;
                }
                
                caixaAtual = caixaAberto;
                
                // Buscar último caixa fechado se não houver aberto
                let ultimoFechamento = null;
                if (!caixaAberto) {
                    const responseFechado = await fetch(
                        API_CONFIG.CAIXA_STATUS(idEmpresa, idFilial, 'fechado'),
                        {
                            method: 'GET',
                            headers: API_CONFIG.getHeaders()
                        }
                    );
                    
                    if (responseFechado.ok) {
                        const caixasFechados = await responseFechado.json();
                        let caixasArray = [];
                        
                        if (caixasFechados.data && Array.isArray(caixasFechados.data)) {
                            caixasArray = caixasFechados.data;
                        } else if (Array.isArray(caixasFechados)) {
                            caixasArray = caixasFechados;
                        }
                        
                        if (caixasArray.length > 0) {
                            // Ordenar por data de fechamento e pegar o mais recente
                            caixasArray.sort((a, b) => new Date(b.data_fechamento || b.data_abertura) - new Date(a.data_fechamento || a.data_abertura));
                            ultimoFechamento = caixasArray[0];
                        }
                    }
                }
                
                atualizarStatusCaixa(caixaAberto, ultimoFechamento, idFilial);
                
            } catch (error) {
                console.error('Erro ao carregar status do caixa:', error);
                document.getElementById('statusCaixaInfo').textContent = 'Erro ao carregar status do caixa';
                document.getElementById('botoesCaixa').innerHTML = '';
            }
        }

        function atualizarStatusCaixa(caixaAberto, ultimoFechamento, idFilial) {
            const statusInfo = document.getElementById('statusCaixaInfo');
            const botoesCaixa = document.getElementById('botoesCaixa');
            
            const filial = filiais.find(f => f.id_filial == idFilial);
            const nomeFilial = filial ? filial.nome_filial : 'Filial ' + idFilial;
            
            if (caixaAberto) {
                statusInfo.innerHTML = `
                    <span class="caixa-status caixa-aberto">
                        <i class="bi bi-check-circle me-1"></i>Caixa Aberto - ${nomeFilial}
                    </span>
                    • Aberto por: ${caixaAberto.usuario?.nome || caixaAberto.nome_usuario || 'N/A'} 
                    • Valor inicial: R$ ${formatarPreco(caixaAberto.valor_abertura)}
                    • Data: ${formatarData(caixaAberto.data_abertura)}
                `;
                
                botoesCaixa.innerHTML = `
                    <button class="btn btn-outline-success" onclick="abrirPDV()">
                        <i class="bi bi-cash-coin me-1"></i>Abrir PDV
                    </button>
                    <button class="btn btn-outline-warning" onclick="abrirModalSangria()">
                        <i class="bi bi-arrow-down-up me-1"></i>Sangria
                    </button>
                    <button class="btn btn-outline-danger" onclick="abrirModalFechamentoCaixa()">
                        <i class="bi bi-x-circle me-1"></i>Fechar Caixa
                    </button>
                `;
            } else {
                const infoFechamento = ultimoFechamento ? 
                    `• Último fechamento: ${formatarData(ultimoFechamento.data_fechamento || ultimoFechamento.data_abertura)} • Valor: R$ ${formatarPreco(ultimoFechamento.valor_fechamento || 0)}` : 
                    '• Nenhum caixa foi aberto hoje';
                
                statusInfo.innerHTML = `
                    <span class="caixa-status caixa-fechado">
                        <i class="bi bi-x-circle me-1"></i>Caixa Fechado - ${nomeFilial}
                    </span>
                    ${infoFechamento}
                `;
                
                botoesCaixa.innerHTML = `
                    <button class="btn btn-primary" onclick="abrirModalAberturaCaixa()">
                        <i class="bi bi-cash-stack me-1"></i>Abrir Caixa
                    </button>
                `;
            }
        }

        function abrirModalAberturaCaixa() {
            document.getElementById('formAberturaCaixa').reset();
            
            // Pre-selecionar a filial atual se disponível
            if (filialSelecionada) {
                document.getElementById('filialAbertura').value = filialSelecionada;
            }
            
            modalAberturaCaixa.show();
        }

        async function confirmarAberturaCaixa() {
            const idFilial = document.getElementById('filialAbertura').value;
            const valorInicial = document.getElementById('valorInicialCaixa').value;
            const observacoes = document.getElementById('observacoesAbertura').value;
            
            if (!idFilial) {
                mostrarNotificacao('Selecione uma filial', 'error');
                return;
            }
            
            if (!valorInicial || parseFloat(valorInicial) <= 0) {
                mostrarNotificacao('Informe um valor inicial válido', 'error');
                return;
            }
            
            mostrarLoading(true);
            
            try {
                const response = await fetch(
                    API_CONFIG.CAIXA_ABERTURA(),
                    {
                        method: 'POST',
                        headers: API_CONFIG.getHeaders(),
                        body: JSON.stringify({
                            id_empresa: idEmpresa,
                            id_filial: parseInt(idFilial),
                            id_usuario: idUsuario,
                            valor_abertura: parseFloat(valorInicial),
                            observacoes: observacoes
                        })
                    }
                );
                
                const data = await response.json();
                
                if (!response.ok) {
                    throw new Error(data.error || data.message || `Erro ${response.status}: ${response.statusText}`);
                }
                
                mostrarNotificacao('Caixa aberto com sucesso!', 'success');
                modalAberturaCaixa.hide();
                
                // Atualizar status
                await carregarStatusCaixa(idFilial);
                
            } catch (error) {
                console.error('Erro ao abrir caixa:', error);
                mostrarNotificacao('Erro ao abrir caixa: ' + error.message, 'error');
            } finally {
                mostrarLoading(false);
            }
        }

        function abrirModalFechamentoCaixa() {
            if (!caixaAtual) {
                mostrarNotificacao('Não há caixa aberto para fechar', 'error');
                return;
            }
            
            carregarResumoFechamento();
            modalFechamentoCaixa.show();
        }

        async function carregarResumoFechamento() {
            try {
                if (!caixaAtual || !filialSelecionada) {
                    throw new Error('Caixa ou filial não selecionada');
                }
                
                // Buscar informações atualizadas do caixa
                await carregarStatusCaixa(filialSelecionada);
                
                if (!caixaAtual) {
                    throw new Error('Caixa não encontrado');
                }
                
                // Buscar resumo do dia
                let resumo = {
                    total_vendas: 0,
                    total_geral: 0,
                    total_dinheiro: 0,
                    total_cartao: 0,
                    total_itens: 0,
                    ticket_medio: 0,
                    formas_pagamento: {}
                };
                
                try {
                    const resumoResponse = await fetch(
                        API_CONFIG.RESUMO_DIA(idEmpresa, filialSelecionada),
                        {
                            method: 'GET',
                            headers: API_CONFIG.getHeaders()
                        }
                    );
                    
                    if (resumoResponse.ok) {
                        const resumoData = await resumoResponse.json();
                        resumo = { ...resumo, ...resumoData };
                    }
                } catch (e) {
                    console.warn('Não foi possível carregar resumo detalhado:', e);
                }
                
                // Calcular valor total das vendas do caixa
                const totalVendasCaixa = caixaAtual.vendas ? 
                    caixaAtual.vendas.reduce((total, venda) => total + parseFloat(venda.valor_total || 0), 0) : 0;
                
                document.getElementById('resumoFechamento').innerHTML = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Resumo Financeiro</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td>Valor Inicial:</td>
                                    <td class="text-end">R$ ${formatarPreco(caixaAtual.valor_abertura)}</td>
                                </tr>
                                <tr>
                                    <td>Total em Dinheiro:</td>
                                    <td class="text-end">R$ ${formatarPreco(resumo.total_dinheiro || 0)}</td>
                                </tr>
                                <tr>
                                    <td>Total Cartão:</td>
                                    <td class="text-end">R$ ${formatarPreco(resumo.total_cartao || 0)}</td>
                                </tr>
                                <tr>
                                    <td>Total Outros:</td>
                                    <td class="text-end">R$ ${formatarPreco((resumo.total_geral || 0) - (resumo.total_dinheiro || 0) - (resumo.total_cartao || 0))}</td>
                                </tr>
                                <tr class="table-primary">
                                    <td><strong>Total Geral:</strong></td>
                                    <td class="text-end"><strong>R$ ${formatarPreco(totalVendasCaixa)}</strong></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>Resumo Operacional</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td>Total de Vendas:</td>
                                    <td class="text-end">${resumo.total_vendas || 0}</td>
                                </tr>
                                <tr>
                                    <td>Itens Vendidos:</td>
                                    <td class="text-end">${resumo.total_itens || 0}</td>
                                </tr>
                                <tr>
                                    <td>Ticket Médio:</td>
                                    <td class="text-end">R$ ${formatarPreco(resumo.ticket_medio || 0)}</td>
                                </tr>
                            </table>
                            
                            <div class="mt-3">
                                <label class="form-label">Observações do Fechamento</label>
                                <textarea class="form-control" id="observacoesFechamento" rows="3" placeholder="Opcional"></textarea>
                            </div>
                            
                            <input type="hidden" id="idCaixaFechamento" value="${caixaAtual.id_caixa}">
                        </div>
                    </div>
                `;
                
            } catch (error) {
                console.error('Erro ao carregar resumo:', error);
                document.getElementById('resumoFechamento').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Erro ao carregar resumo: ${error.message}
                    </div>
                `;
            }
        }

        async function confirmarFechamentoCaixa() {
            const idCaixa = document.getElementById('idCaixaFechamento').value;
            const observacoes = document.getElementById('observacoesFechamento').value;
            
            if (!idCaixa) {
                mostrarNotificacao('ID do caixa não encontrado', 'error');
                return;
            }
            
            mostrarLoading(true);
            
            try {
                const response = await fetch(
                    API_CONFIG.CAIXA_FECHAMENTO(idCaixa),
                    {
                        method: 'POST',
                        headers: API_CONFIG.getHeaders(),
                        body: JSON.stringify({
                            observacoes: observacoes
                        })
                    }
                );
                
                const data = await response.json();
                
                if (!response.ok) {
                    throw new Error(data.error || data.message || `Erro ${response.status}: ${response.statusText}`);
                }
                
                mostrarNotificacao('Caixa fechado com sucesso!', 'success');
                modalFechamentoCaixa.hide();
                
                // Atualizar status
                await carregarStatusCaixa(filialSelecionada);
                
            } catch (error) {
                console.error('Erro ao fechar caixa:', error);
                mostrarNotificacao('Erro ao fechar caixa: ' + error.message, 'error');
            } finally {
                mostrarLoading(false);
            }
        }

        // ========== VENDAS ==========
        async function carregarVendas(idFilial) {
            if (!idFilial) return;
            
            try {
                const response = await fetch(
                    API_CONFIG.VENDAS_DIA(idEmpresa, idFilial),
                    {
                        method: 'GET',
                        headers: API_CONFIG.getHeaders()
                    }
                );
                
                if (!response.ok) {
                    throw new Error(`Erro ${response.status}: ${response.statusText}`);
                }
                
                const vendas = await response.json();
                exibirVendas(vendas);
                
            } catch (error) {
                console.error('Erro ao carregar vendas:', error);
                document.getElementById('tbodyVendas').innerHTML = `
                    <tr><td colspan="7" class="text-center text-muted">Erro ao carregar vendas: ${error.message}</td></tr>
                `;
            }
        }

        function exibirVendas(vendas) {
            const tbody = document.getElementById('tbodyVendas');
            
            if (!vendas || vendas.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">Nenhuma venda encontrada para hoje</td></tr>';
                return;
            }
            
            // Se vendas for um objeto com propriedade data, usar data
            const listaVendas = vendas.data || vendas;
            
            if (!Array.isArray(listaVendas) || listaVendas.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">Nenhuma venda encontrada para hoje</td></tr>';
                return;
            }
            
            tbody.innerHTML = listaVendas.map(venda => `
                <tr>
                    <td>#${venda.id_venda || venda.id}</td>
                    <td>${formatarDataHora(venda.data_venda || venda.created_at)}</td>
                    <td>${venda.cliente_nome || venda.cliente?.nome || 'Consumidor'}</td>
                    <td>${venda.total_itens || 0} itens</td>
                    <td class="fw-semibold">R$ ${formatarPreco(venda.valor_total)}</td>
                    <td>
                        <span class="venda-status venda-${venda.status}">
                            ${formatarStatusVenda(venda.status)}
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" onclick="verDetalhesVenda(${venda.id_venda || venda.id})">
                            <i class="bi bi-eye"></i>
                        </button>
                    </td>
                </tr>
            `).join('');
        }

        // ========== DETALHES DA VENDA ==========
        async function verDetalhesVenda(idVenda) {
            mostrarLoading(true);
            try {
                // Determinar filial (usar filialSelecionada quando disponível)
                const idFilial = filialSelecionada || (typeof idFilialDefault !== 'undefined' ? idFilialDefault : 1);

                // Usar endpoint de detalhes por query se disponível no servidor
                const url = API_CONFIG.DETALHES_VENDA_QUERY
                    ? API_CONFIG.DETALHES_VENDA_QUERY(idEmpresa, idFilial, idVenda)
                    : API_CONFIG.DETALHES_VENDA(idEmpresa, idVenda);

                const response = await fetch(url, {
                    method: 'GET',
                    headers: API_CONFIG.getHeaders()
                });

                if (!response.ok) {
                    throw new Error(`Erro ${response.status}: ${response.statusText}`);
                }

                const venda = await response.json();
                exibirDetalhesVenda(venda);

            } catch (error) {
                console.error('Erro ao carregar detalhes da venda:', error);
                mostrarNotificacao('Erro ao carregar detalhes da venda: ' + error.message, 'error');
            } finally {
                mostrarLoading(false);
            }
        }

        function exibirDetalhesVenda(venda) {
            const detalhesBody = document.getElementById('detalhesVendaBody');
            let dados = venda.data || venda;
            if (Array.isArray(dados)) dados = dados[0] || {};

            // Itens
            const itens = dados.itens && Array.isArray(dados.itens) ? dados.itens : [];
            const itensHTML = itens.length > 0 ? itens.map(item => `
                <tr>
                    <td>${item.produto?.descricao || item.nome_produto || 'N/A'}</td>
                    <td class="text-center">${item.quantidade || 0}</td>
                    <td class="text-end">R$ ${formatarPreco(item.preco_unitario)}</td>
                    <td class="text-end">R$ ${formatarPreco(item.subtotal || (item.quantidade * item.preco_unitario))}</td>
                </tr>
            `).join('') : '<tr><td colspan="4" class="text-center">Nenhum item encontrado</td></tr>';

            // Pagamentos
            const pagamentos = dados.pagamentos && Array.isArray(dados.pagamentos) ? dados.pagamentos : [];
            const pagamentosHTML = pagamentos.length > 0 ? pagamentos.map(p => `
                <tr>
                    <td>${formatarFormaPagamento(p.forma_pagamento)}</td>
                    <td class="text-end">R$ ${formatarPreco(p.valor_pago || p.valor)}</td>
                    <td>${p.valor_troco ? `Troco: R$ ${formatarPreco(p.valor_troco)}` : '-'}<br>${p.observacoes || ''}</td>
                    <td>${p.data_pagamento ? formatarDataHora(p.data_pagamento) : ''}</td>
                </tr>
            `).join('') : '<tr><td colspan="4" class="text-center">Nenhum pagamento registrado</td></tr>';

            // Informações gerais
            const infoHTML = `
                <table class="table table-sm">
                    <tr><td>ID:</td><td>#${dados.id_venda || dados.id}</td></tr>
                    <tr><td>Data:</td><td>${formatarDataHora(dados.data_venda || dados.created_at)}</td></tr>
                    <tr><td>Cliente:</td><td>${dados.cliente_nome || dados.cliente?.nome || 'Consumidor'}</td></tr>
                    <tr><td>Vendedor:</td><td>${dados.vendedor_nome || dados.usuario?.nome || 'N/A'}</td></tr>
                    <tr><td>Total Itens:</td><td>${dados.total_itens || (itens.length)}</td></tr>
                    <tr><td>Valor Total:</td><td>R$ ${formatarPreco(dados.valor_total)}</td></tr>
                </table>
            `;

            detalhesBody.innerHTML = `
                <ul class="nav nav-tabs" id="detalhesTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="info-tab" data-bs-toggle="tab" data-bs-target="#tab-info" type="button" role="tab">Informações</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="itens-tab" data-bs-toggle="tab" data-bs-target="#tab-itens" type="button" role="tab">Itens</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="pag-tab" data-bs-toggle="tab" data-bs-target="#tab-pagamentos" type="button" role="tab">Pagamentos</button>
                    </li>
                </ul>
                <div class="tab-content mt-3">
                    <div class="tab-pane fade show active" id="tab-info" role="tabpanel">
                        ${infoHTML}
                    </div>
                    <div class="tab-pane fade" id="tab-itens" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Produto</th>
                                        <th class="text-center">Qtd</th>
                                        <th class="text-end">Preço Unit.</th>
                                        <th class="text-end">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${itensHTML}
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="tab-pagamentos" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Forma</th>
                                        <th class="text-end">Valor</th>
                                        <th>Observações / Troco</th>
                                        <th>Data</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${pagamentosHTML}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            `;

            // Mostrar modal
            modalDetalhesVenda.show();
        }

        // ========== RESUMO DO DIA ==========
        async function carregarResumoDia(idFilial) {
            if (!idFilial) return;
            
            try {
                const response = await fetch(
                    API_CONFIG.RESUMO_DIA(idEmpresa, idFilial),
                    {
                        method: 'GET',
                        headers: API_CONFIG.getHeaders()
                    }
                );
                
                if (!response.ok) {
                    throw new Error(`Erro ${response.status}: ${response.statusText}`);
                }
                
                const resumo = await response.json();
                atualizarResumoDia(resumo);
                
            } catch (error) {
                console.error('Erro ao carregar resumo:', error);
                // Não mostrar erro para o usuário, apenas usar valores padrão
            }
        }

        function atualizarResumoDia(resumo) {
            // Se resumo for um objeto com propriedade data, usar data
            const dados = resumo.data || resumo;
            
            document.getElementById('totalVendasDia').textContent = dados.total_vendas || 0;
            document.getElementById('valorTotalDia').textContent = `R$ ${formatarPreco(dados.total_geral || 0)}`;
            document.getElementById('ticketMedio').textContent = `R$ ${formatarPreco(dados.ticket_medio || 0)}`;
            document.getElementById('produtosVendidos').textContent = dados.total_itens || 0;
            
            // Atualizar resumo de pagamentos
            const resumoPagamentos = document.getElementById('resumoPagamentos');
            if (dados.formas_pagamento && Object.keys(dados.formas_pagamento).length > 0) {
                let html = '';
                Object.entries(dados.formas_pagamento).forEach(([forma, valor]) => {
                    if (parseFloat(valor) > 0) {
                        html += `
                            <div class="pagamento-item">
                                <span class="forma-pagamento">${formatarFormaPagamento(forma)}</span>
                                <span class="valor-pagamento fw-semibold">R$ ${formatarPreco(valor)}</span>
                            </div>
                        `;
                    }
                });
                resumoPagamentos.innerHTML = html || '<p class="text-muted text-center">Nenhum pagamento registrado hoje</p>';
            } else {
                // Não sobrescrever quando já renderizamos o resumo de pagamentos específico
                if (!window.pagamentosResumoRendered) {
                    resumoPagamentos.innerHTML = '<p class="text-muted text-center">Nenhum pagamento registrado hoje</p>';
                }
            }
        }

        // ========== SANGRIA/REFORÇO ==========
        function abrirModalSangria() {
            if (!caixaAtual) {
                mostrarNotificacao('É necessário ter um caixa aberto para realizar sangria/reforço', 'error');
                return;
            }
            
            document.getElementById('formSangria').reset();
            modalSangria.show();
        }

        async function confirmarSangria() {
            const tipo = document.getElementById('tipoSangria').value;
            const valor = document.getElementById('valorSangria').value;
            const motivo = document.getElementById('motivoSangria').value;
            
            if (!tipo || !valor) {
                mostrarNotificacao('Preencha todos os campos obrigatórios', 'error');
                return;
            }
            
            if (parseFloat(valor) <= 0) {
                mostrarNotificacao('O valor deve ser maior que zero', 'error');
                return;
            }
            
            mostrarLoading(true);
            
            try {
                const response = await fetch(
                    API_CONFIG.SANGRIA(),
                    {
                        method: 'POST',
                        headers: API_CONFIG.getHeaders(),
                        body: JSON.stringify({
                            id_caixa: caixaAtual.id_caixa,
                            tipo: tipo,
                            valor: parseFloat(valor),
                            motivo: motivo,
                            id_usuario: idUsuario
                        })
                    }
                );
                
                const data = await response.json();
                
                if (!response.ok) {
                    throw new Error(data.error || data.message || `Erro ${response.status}: ${response.statusText}`);
                }
                
                mostrarNotificacao(`${tipo === 'sangria' ? 'Sangria' : 'Reforço'} realizado com sucesso!`, 'success');
                modalSangria.hide();
                
                // Atualizar dados
                await carregarStatusCaixa(filialSelecionada);
                await carregarResumoDia(filialSelecionada);
                
            } catch (error) {
                console.error('Erro ao realizar operação:', error);
                mostrarNotificacao('Erro ao realizar operação: ' + error.message, 'error');
            } finally {
                mostrarLoading(false);
            }
        }

        // ========== RELATÓRIOS ==========
        function abrirModalRelatorio() {
            modalRelatorios.show();
        }

        function gerarRelatorio(tipo) {
            mostrarLoading(true);
            
            // Simular geração de relatório
            setTimeout(() => {
                mostrarLoading(false);
                
                let mensagem = '';
                switch(tipo) {
                    case 'vendas':
                        mensagem = 'Relatório de vendas gerado com sucesso!';
                        break;
                    case 'caixa':
                        mensagem = 'Relatório de caixa gerado com sucesso!';
                        break;
                    case 'financeiro':
                        mensagem = 'Relatório financeiro gerado com sucesso!';
                        break;
                    case 'produtos':
                        mensagem = 'Relatório de produtos gerado com sucesso!';
                        break;
                    default:
                        mensagem = 'Relatório gerado com sucesso!';
                }
                
                mostrarNotificacao(mensagem, 'success');
                
                // Em um ambiente real, aqui você faria o download do relatório
                // window.open(`${BASE_URL}/api/relatorios/${tipo}?id_empresa=${idEmpresa}&id_filial=${filialSelecionada}`, '_blank');
                
            }, 2000);
        }

        // ========== CONFIGURAÇÕES ==========
        function abrirModalConfiguracao() {
            modalConfiguracao.show();
        }

        function salvarConfiguracoes() {
            // Simular salvamento de configurações
            mostrarNotificacao('Configurações salvas com sucesso!', 'success');
            modalConfiguracao.hide();
        }

        // ========== FUNÇÕES AUXILIARES ==========
        function hoje() {
            const now = new Date();
            return now.toISOString().split('T')[0];
        }

        function formatarPreco(preco) {
            if (!preco) return '0,00';
            return parseFloat(preco).toFixed(2).replace('.', ',');
        }

        function formatarData(data) {
            if (!data) return '';
            try {
                const date = new Date(data);
                return date.toLocaleDateString('pt-BR');
            } catch (e) {
                return data;
            }
        }

        function formatarDataHora(data) {
            if (!data) return '';
            try {
                const date = new Date(data);
                return date.toLocaleString('pt-BR');
            } catch (e) {
                return data;
            }
        }

        function formatarStatusVenda(status) {
            const statusMap = {
                'finalizada': 'Finalizada',
                'cancelada': 'Cancelada',
                'pendente': 'Pendente',
                'fechada': 'Fechada'
            };
            return statusMap[status] || status;
        }

        function formatarFormaPagamento(forma) {
            const formasMap = {
                'dinheiro': 'Dinheiro',
                'cartao_credito': 'Cartão Crédito',
                'cartao_debito': 'Cartão Débito',
                'pix': 'PIX',
                'outros': 'Outros'
            };
            return formasMap[forma] || forma;
        }

        function mostrarLoading(mostrar) {
            document.getElementById('loadingOverlay').classList.toggle('d-none', !mostrar);
        }

        function abrirPDV() {
            if (!caixaAtual) {
                mostrarNotificacao('É necessário abrir o caixa antes de acessar o PDV', 'error');
                return;
            }
            
            // Verificar se há uma filial selecionada
            if (!filialSelecionada) {
                mostrarNotificacao('Selecione uma filial antes de abrir o PDV', 'error');
                return;
            }
            
            // Redirecionar para a página do PDV com o id_filial como parâmetro
            window.location.href = `view/pdv-venda.php?id_filial=${filialSelecionada}`;
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
