<?php
session_start();
if (!isset($_SESSION['authToken'], $_SESSION['usuario'], $_SESSION['empresa'], $_SESSION['licenca'])) {
    header('Location: login.php');
    exit;
}

// Verificar e converter o tipo de dados das variáveis de sessão
$usuario = is_array($_SESSION['usuario']) ? $_SESSION['usuario'] : ['nome' => $_SESSION['usuario']];
$empresa = $_SESSION['empresa'];
// Determinar id da empresa de forma robusta (pode vir dentro do array empresa ou em empresa_id)
$id_empresa = null;
if (is_array($empresa)) {
    $id_empresa = $empresa['id'] ?? $empresa['id_empresa'] ?? $empresa['empresa_id'] ?? $empresa['id_filial'] ?? null;
}
$id_empresa = $id_empresa ?? $_SESSION['empresa_id'] ?? null;
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
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
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

    <!-- Loading Overlay -->
    <div class="loading-overlay d-none" id="loadingOverlay">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Carregando...</span>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    // Variáveis globais (injetadas com json_encode para evitar problemas de escape)
    const idEmpresa = <?php echo json_encode($id_empresa); ?>;
    const idUsuario = <?php echo json_encode($_SESSION['usuario_id'] ?? 1); ?>;
    const token = <?php echo json_encode($token); ?>;
        const BASE_URL = 'http://localhost/saas-multiempresas-api/public';
        
        let modalAberturaCaixa = null;
        let modalFechamentoCaixa = null;
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

            FILIAIS: (idEmpresa) =>  `${BASE_URL}/api/filiais/empresa/${idEmpresa}`,

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
                
                const raw = await response.json();
                // Aceitar respostas no formato { data: [...] } ou diretamente [...] ou objeto único
                let caixas = raw && raw.data ? raw.data : raw;

                // Verificar se há caixa aberto
                let caixaAberto = null;
                if (Array.isArray(caixas) && caixas.length > 0) {
                    caixaAberto = caixas[0];
                } else if (caixas && typeof caixas === 'object' && (caixas.id_caixa || caixas.id)) {
                    // API pode retornar um objeto único
                    caixaAberto = caixas;
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
                            const rawFechados = await responseFechado.json();
                            const caixasFechados = rawFechados && rawFechados.data ? rawFechados.data : rawFechados;
                            if (Array.isArray(caixasFechados) && caixasFechados.length > 0) {
                                // Ordenar por data de fechamento e pegar o mais recente
                                caixasFechados.sort((a, b) => new Date(b.data_fechamento) - new Date(a.data_fechamento));
                                ultimoFechamento = caixasFechados[0];
                            } else if (caixasFechados && typeof caixasFechados === 'object' && (caixasFechados.id_caixa || caixasFechados.id)) {
                                ultimoFechamento = caixasFechados;
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
                    • Aberto por: ${caixaAberto.usuario?.nome || 'N/A'} 
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
                    `• Último fechamento: ${formatarData(ultimoFechamento.data_fechamento)} • Valor: R$ ${formatarPreco(ultimoFechamento.valor_fechamento)}` : 
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
                    ticket_medio: 0
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
                                    <td class="text-end">R$ ${formatarPreco(resumo.total_outros || 0)}</td>
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
                                    <td class="text-end">${caixaAtual.vendas ? caixaAtual.vendas.length : 0}</td>
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
            if (dados.formas_pagamento) {
                resumoPagamentos.innerHTML = Object.entries(dados.formas_pagamento).map(([forma, valor]) => `
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>${formatarFormaPagamento(forma)}</span>
                        <span class="fw-semibold">R$ ${formatarPreco(valor)}</span>
                    </div>
                `).join('');
            } else {
                resumoPagamentos.innerHTML = '<p class="text-muted text-center">Nenhum pagamento registrado hoje</p>';
            }
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
            window.location.href = 'view/pdv-venda.php';
        }

        function verDetalhesVenda(idVenda) {
            mostrarNotificacao(`Abrindo detalhes da venda #${idVenda}`, 'info');
            // Implementar abertura de modal com detalhes da venda
        }

        function abrirModalSangria() {
            mostrarNotificacao('Funcionalidade de sangria em desenvolvimento', 'info');
        }

        function abrirModalRelatorio() {
            mostrarNotificacao('Funcionalidade de relatórios em desenvolvimento', 'info');
        }

        function abrirModalConfiguracao() {
            mostrarNotificacao('Funcionalidade de configurações em desenvolvimento', 'info');
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