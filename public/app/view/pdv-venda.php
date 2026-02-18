[file name]: pdv-venda.php
[file content begin]
<?php
session_start();
if (!isset($_SESSION['authToken'], $_SESSION['usuario'], $_SESSION['empresa'], $_SESSION['licenca'])) {
    header('Location: login.php');
    exit;
}

// Verificar e converter o tipo de dados das variáveis de sessão
$usuario = is_array($_SESSION['usuario']) ? $_SESSION['usuario'] : ['nome' => $_SESSION['usuario']];
$empresa = $_SESSION['empresa'];
// Determinar id da empresa de forma segura (pode vir dentro do array empresa)
$id_empresa = null;
if (is_array($empresa)) {
    $id_empresa = $empresa['id_empresa'] ?? $empresa['id'] ?? null;
}
// Fallback: tentar ler diretamente da sessão caso exista
if (empty($id_empresa) && isset($_SESSION['empresa_id'])) {
    $id_empresa = $_SESSION['empresa_id'];
}
// Se ainda não tivermos id da empresa, redirecionar para login (sessão incompleta)
if (empty($id_empresa)) {
    header('Location: login.php');
    exit;
}

// garantir tipo inteiro quando possível
$id_empresa = is_numeric($id_empresa) ? (int)$id_empresa : $id_empresa;
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

// Obter id_filial da URL (parâmetro GET)
$id_filial = isset($_GET['id_filial']) ? (int)$_GET['id_filial'] : null;
if (empty($id_filial)) {
    die('Filial não especificada. Acesso inválido.');
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDV - Ponto de Venda - SAS Multi</title>
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
            background-color: #f0f2f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }
        
        .pdv-container {
            display: grid;
            grid-template-columns: 1fr 400px;
            height: 100vh;
            gap: 0;
        }
        
        .produtos-area {
            background: white;
            padding: 20px;
            overflow-y: auto;
        }
        
        .venda-area {
            background: var(--secondary-color);
            color: white;
            display: flex;
            flex-direction: column;
            height: 100vh;
        }
        
        .header-pdv {
            background: var(--primary-color);
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .carrinho-area {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
        }
        
        .total-area {
            background: rgba(255,255,255,0.1);
            padding: 20px;
            border-top: 1px solid rgba(255,255,255,0.2);
        }
        
        .produto-card {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .produto-card:hover {
            border-color: var(--primary-color);
            box-shadow: 0 2px 8px rgba(52, 152, 219, 0.2);
        }
        
        .produto-card .estoque-info {
            color: var(--success-color);
        }
        
        .item-carrinho {
            background: rgba(255,255,255,0.1);
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
        }
        
        .quantidade-controller {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .btn-quantidade {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            background: var(--primary-color);
            color: white;
        }
        
        .search-box {
            position: relative;
            margin-bottom: 20px;
        }
        
        .search-box .form-control {
            padding-left: 40px;
            border-radius: 25px;
        }
        
        .search-box .bi-search {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }
        
        .categorias-tabs {
            margin-bottom: 20px;
        }
        
        .categorias-tabs .nav-link {
            border: none;
            color: #6c757d;
            font-weight: 500;
        }
        
        .categorias-tabs .nav-link.active {
            color: var(--primary-color);
            border-bottom: 2px solid var(--primary-color);
            background: transparent;
        }
        
        .btn-pagamento {
            width: 100%;
            padding: 12px;
            margin-bottom: 10px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            text-align: left;
        }
        
        .btn-dinheiro { background: var(--success-color); color: white; }
        .btn-cartao { background: var(--info-color); color: white; }
        .btn-pix { background: #32BCAD; color: white; }
        .btn-outros { background: var(--warning-color); color: white; }
        
        .valor-total {
            font-size: 2rem;
            font-weight: bold;
            text-align: center;
            margin: 10px 0;
        }
        
        /* teclado escondido por padrão; será exibido apenas na finalização da venda */
        .teclado-numerico {
            display: none; /* oculto por padrão */
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-top: 15px;
        }
        
        .btn-numero {
            padding: 15px;
            border: none;
            border-radius: 8px;
            background: rgba(255,255,255,0.2);
            color: white;
            font-size: 1.2rem;
            font-weight: bold;
        }
        
        .btn-numero:hover {
            background: rgba(255,255,255,0.3);
        }
        
        .btn-limpar {
            grid-column: span 2;
            background: var(--danger-color);
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
        
        .caixa-status {
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: bold;
        }
        
        .caixa-aberto { background: var(--success-color); }
        .caixa-fechado { background: var(--danger-color); }
        
        @media (max-width: 768px) {
            .pdv-container {
                grid-template-columns: 1fr;
            }
            
            .venda-area {
                height: 50vh;
            }
        }
    </style>
</head>
<body>
    <div class="pdv-container">
        <!-- Área de Produtos -->
        <div class="produtos-area">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="mb-0">Produtos</h3>
                <button class="btn btn-outline-secondary" onclick="voltarDashboard()">
                    <i class="bi bi-arrow-left me-2"></i>Voltar
                </button>
            </div>
            
            <!-- Status do Caixa -->
            <div class="alert alert-warning mb-3" id="caixaAlert" style="display: none;">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <span id="caixaAlertText"></span>
            </div>
            
            <!-- Busca -->
            <div class="search-box">
                <i class="bi bi-search"></i>
                <input type="text" class="form-control" id="searchProduto" placeholder="Buscar por código, descrição ou código de barras..." autofocus>
            </div>
            
            <!-- Categorias -->
            <ul class="nav nav-tabs categorias-tabs" id="categoriasTabs">
                <li class="nav-item">
                    <button class="nav-link active" data-categoria="">Todos</button>
                </li>
                <!-- Categorias serão carregadas dinamicamente -->
            </ul>
            
            <!-- Lista de Produtos -->
            <div id="listaProdutos" class="row">
                <!-- Produtos serão carregados dinamicamente -->
            </div>
        </div>
        
        <!-- Área da Venda -->
        <div class="venda-area">
            <!-- Header -->
            <div class="header-pdv">
                <div>
                    <h4 class="mb-0">Venda Atual</h4>
                    <small id="infoCaixa">Caixa: <?php echo $nomeUsuario; ?> | Filial: <?php echo $id_filial; ?></small>
                    <div class="caixa-status mt-1" id="statusCaixa">Carregando...</div>
                </div>
                <div class="text-end">
                    <div id="horaAtual" class="h5 mb-0">--:--:--</div>
                    <small><?php echo date('d/m/Y'); ?></small>
                </div>
            </div>
            
            <!-- Carrinho -->
            <div class="carrinho-area">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5>Itens da Venda</h5>
                    <button class="btn btn-sm btn-outline-light" onclick="limparCarrinho()">
                        <i class="bi bi-trash me-1"></i>Limpar
                    </button>
                </div>
                
                <div id="carrinhoItens">
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-cart-x" style="font-size: 3rem;"></i>
                        <p class="mt-3">Nenhum item adicionado</p>
                    </div>
                </div>
            </div>
            
            <!-- Total e Ações -->
            <div class="total-area">
                <div class="valor-total" id="valorTotalVenda">R$ 0,00</div>
                
                <!-- teclado numerico: oculto por padrão; mostrado apenas ao finalizar a venda -->
                <div class="teclado-numerico" id="tecladoNumerico">
                    <button class="btn-numero" onclick="adicionarNumero('1')">1</button>
                    <button class="btn-numero" onclick="adicionarNumero('2')">2</button>
                    <button class="btn-numero" onclick="adicionarNumero('3')">3</button>
                    <button class="btn-numero" onclick="adicionarNumero('4')">4</button>
                    <button class="btn-numero" onclick="adicionarNumero('5')">5</button>
                    <button class="btn-numero" onclick="adicionarNumero('6')">6</button>
                    <button class="btn-numero" onclick="adicionarNumero('7')">7</button>
                    <button class="btn-numero" onclick="adicionarNumero('8')">8</button>
                    <button class="btn-numero" onclick="adicionarNumero('9')">9</button>
                    <button class="btn-numero" onclick="adicionarNumero('0')">0</button>
                    <button class="btn-numero" onclick="adicionarNumero('00')">00</button>
                    <button class="btn-numero btn-limpar" onclick="limparValor()">C</button>
                </div>
                
                <div class="mt-3">
                    <button class="btn btn-pagamento btn-dinheiro" onclick="selecionarPagamento('dinheiro')" id="btnDinheiro">
                        <i class="bi bi-cash-coin me-2"></i>Dinheiro
                    </button>
                    <button class="btn btn-pagamento btn-cartao" onclick="selecionarPagamento('cartao')" id="btnCartao">
                        <i class="bi bi-credit-card me-2"></i>Cartão
                    </button>
                    <button class="btn btn-pagamento btn-pix" onclick="selecionarPagamento('pix')" id="btnPix">
                        <i class="bi bi-qr-code me-2"></i>PIX
                    </button>
                    <button class="btn btn-pagamento btn-outros" onclick="selecionarPagamento('outros')" id="btnOutros">
                        <i class="bi bi-three-dots me-2"></i>Outros
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Pagamento -->
    <div class="modal fade" id="modalPagamento" tabindex="-1" aria-labelledby="modalPagamentoLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalPagamentoLabel">Finalizar Venda</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formPagamento">
                        <div class="mb-3">
                            <label class="form-label">Forma de Pagamento</label>
                            <select class="form-select" id="formaPagamento" required>
                                <option value="dinheiro">Dinheiro</option>
                                <option value="cartao_credito">Cartão de Crédito</option>
                                <option value="cartao_debito">Cartão de Débito</option>
                                <option value="pix">PIX</option>
                                <option value="outros">Outros</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Valor Total</label>
                            <input type="text" class="form-control" id="valorTotalModal" readonly>
                        </div>
                        
                        <div class="mb-3" id="campoValorRecebido">
                            <label class="form-label">Valor Recebido</label>
                            <input type="number" class="form-control" id="valorRecebido" step="0.01">
                        </div>
                        
                        <div class="mb-3" id="campoTroco">
                            <label class="form-label">Troco</label>
                            <input type="text" class="form-control" id="valorTroco" readonly>
                        </div>

                        <!-- Pagamentos adicionais: permite dividir o pagamento em várias formas -->
                        <div class="mb-3" id="pagamentosAdicionaisWrapper">
                            <label class="form-label">Outras formas de pagamento (se necessário)</label>
                            <div id="pagamentosAdicionais"></div>
                            <div class="mt-2 d-flex gap-2">
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="btnAdicionarPagamento">Adicionar pagamento</button>
                                <small class="text-muted align-self-center">Use quando o cliente pagar com mais de uma forma</small>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Observações</label>
                            <textarea class="form-control" id="observacoesVenda" rows="2"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="finalizarVenda()">Finalizar Venda</button>
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
    // Variáveis globais (injetadas com json_encode para segurança)
    const idEmpresa = <?php echo json_encode($id_empresa); ?>;
    const token = <?php echo json_encode($token); ?>;
    const idFilial = <?php echo json_encode($id_filial); ?>;
    const BASE_URL = 'http://localhost/saas-multiempresas-api/public';
    
    let produtos = [];
    let carrinho = [];
    let modalPagamento = null;
    let caixaAberto = null; // Armazenará os dados do caixa aberto

    // Configuração da API
    const API_CONFIG = {
        // Endpoints PDV
        PRODUTOS_PDV: (idEmpresa) => 
            `${BASE_URL}/api/v1/empresas/${idEmpresa}/produtos`,
            
        CATEGORIAS_PDV: (idEmpresa) => 
            `${BASE_URL}/api/v1/categorias/empresa/${idEmpresa}`,
            
        // CORREÇÃO: Endpoint correto para registrar vendas
        REGISTRAR_VENDA: `${BASE_URL}/api/pdv/vendas`,
        
        // CORREÇÃO: Endpoint correto para buscar caixa aberto
        CAIXA_ABERTO: (idEmpresa, idFilial) => 
            `${BASE_URL}/api/v1/empresas/${idEmpresa}/pdv/caixas/status?status=aberto&id_filial=${idFilial}`,
        
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
        // Inicializar modal
        modalPagamento = new bootstrap.Modal(document.getElementById('modalPagamento'));
        
        // Carregar dados iniciais - PRIMEIRO buscar caixa aberto
        carregarCaixaAberto().then(() => {
            // Após carregar caixa, carregar produtos e categorias
            carregarProdutos();
            carregarCategorias();
        });
        
        atualizarHora();
        
        // Configurar eventos
        document.getElementById('searchProduto').addEventListener('input', filtrarProdutos);
        document.getElementById('valorRecebido').addEventListener('input', calcularTroco);
        
        // Atualizar hora a cada segundo
        setInterval(atualizarHora, 1000);
        
        // Focar na busca
        document.getElementById('searchProduto').focus();
    });

    // ========== CAIXA ==========
    async function carregarCaixaAberto() {
        try {
            mostrarLoading(true);
            
            const response = await fetch(
                API_CONFIG.CAIXA_ABERTO(idEmpresa, idFilial),
                {
                    method: 'GET',
                    headers: API_CONFIG.getHeaders()
                }
            );
            
            if (response.status === 401) {
                mostrarNotificacao('Sessão expirada. Redirecionando para login...', 'error');
                setTimeout(() => {
                    window.location.href = 'login.php';
                }, 2000);
                return;
            }
            
            if (response.status === 404) {
                // Nenhum caixa aberto encontrado
                atualizarStatusCaixa(false, 'Nenhum caixa aberto encontrado');
                mostrarNotificacao('Nenhum caixa aberto encontrado. Abra um caixa primeiro.', 'error');
                desabilitarVenda();
                return;
            }
            
            if (!response.ok) {
                throw new Error(`Erro ${response.status}: ${response.statusText}`);
            }
            
            const data = await response.json();
            
            // CORREÇÃO: A API pode retornar um array ou um objeto único
            if (Array.isArray(data) && data.length > 0) {
                caixaAberto = data[0]; // Pega o primeiro caixa aberto
            } else if (data && typeof data === 'object') {
                caixaAberto = data; // Já é um objeto único
            } else {
                throw new Error('Formato de resposta inválido da API');
            }
            
            // Verificar se o caixa está realmente aberto
            if (caixaAberto.status && caixaAberto.status.toLowerCase() === 'aberto') {
                atualizarStatusCaixa(true, `Caixa #${caixaAberto.id_caixa} - Aberto`);
                habilitarVenda();
                mostrarNotificacao(`Caixa #${caixaAberto.id_caixa} carregado com sucesso`, 'success');
            } else {
                atualizarStatusCaixa(false, `Caixa #${caixaAberto.id_caixa} - Fechado`);
                mostrarNotificacao('O caixa está fechado. Abra um caixa primeiro.', 'error');
                desabilitarVenda();
            }
            
        } catch (error) {
            console.error('Erro ao carregar caixa:', error);
            atualizarStatusCaixa(false, 'Erro ao carregar caixa');
            mostrarNotificacao('Erro ao carregar caixa: ' + error.message, 'error');
            desabilitarVenda();
        } finally {
            mostrarLoading(false);
        }
    }

    function atualizarStatusCaixa(aberto, mensagem) {
        const statusElement = document.getElementById('statusCaixa');
        const alertElement = document.getElementById('caixaAlert');
        const alertText = document.getElementById('caixaAlertText');
        
        if (aberto) {
            statusElement.textContent = mensagem;
            statusElement.className = 'caixa-status caixa-aberto';
            alertElement.style.display = 'none';
        } else {
            statusElement.textContent = mensagem;
            statusElement.className = 'caixa-status caixa-fechado';
            alertElement.style.display = 'block';
            alertText.textContent = mensagem;
        }
    }

    function desabilitarVenda() {
        // Desabilitar botões de pagamento
        document.getElementById('btnDinheiro').disabled = true;
        document.getElementById('btnCartao').disabled = true;
        document.getElementById('btnPix').disabled = true;
        document.getElementById('btnOutros').disabled = true;
        
        // Adicionar tooltip ou mensagem
        document.querySelectorAll('.btn-pagamento').forEach(btn => {
            btn.title = 'Abra um caixa primeiro para realizar vendas';
        });
    }

    function habilitarVenda() {
        // Habilitar botões de pagamento
        document.getElementById('btnDinheiro').disabled = false;
        document.getElementById('btnCartao').disabled = false;
        document.getElementById('btnPix').disabled = false;
        document.getElementById('btnOutros').disabled = false;
        
        // Remover tooltip
        document.querySelectorAll('.btn-pagamento').forEach(btn => {
            btn.title = '';
        });
    }

    // ========== PRODUTOS ==========
    async function carregarProdutos() {
        try {
            mostrarLoading(true);
            
            const response = await fetch(
                API_CONFIG.PRODUTOS_PDV(idEmpresa),
                {
                    method: 'GET',
                    headers: API_CONFIG.getHeaders()
                }
            );
            
            if (response.status === 401) {
                mostrarNotificacao('Sessão expirada. Redirecionando para login...', 'error');
                setTimeout(() => {
                    window.location.href = 'login.php';
                }, 2000);
                return;
            }
            
            if (!response.ok) {
                throw new Error(`Erro ${response.status}: ${response.statusText}`);
            }
            
            const data = await response.json();
            
            if (Array.isArray(data)) {
                produtos = data;
            } else if (data.data && Array.isArray(data.data)) {
                produtos = data.data;
            } else {
                console.warn('Resposta da API não é um array:', data);
                produtos = [];
            }
            
            exibirProdutos(produtos);
            
        } catch (error) {
            console.error('Erro ao carregar produtos:', error);
            document.getElementById('listaProdutos').innerHTML = `
                <div class="col-12 text-center text-muted py-5">
                    <i class="bi bi-exclamation-triangle" style="font-size: 3rem;"></i>
                    <p class="mt-3">Erro ao carregar produtos: ${error.message}</p>
                </div>
            `;
        } finally {
            mostrarLoading(false);
        }
    }

    async function carregarCategorias() {
        try {
            const response = await fetch(
                API_CONFIG.CATEGORIAS_PDV(idEmpresa),
                {
                    method: 'GET',
                    headers: API_CONFIG.getHeaders()
                }
            );
            
            if (response.status === 401) {
                mostrarNotificacao('Sessão expirada. Redirecionando para login...', 'error');
                setTimeout(() => {
                    window.location.href = 'login.php';
                }, 2000);
                return;
            }
            
            if (!response.ok) {
                throw new Error(`Erro ${response.status}: ${response.statusText}`);
            }
            
            const data = await response.json();
            
            const categorias = Array.isArray(data) ? data : (data.data || []);
            preencherCategorias(categorias);
            
        } catch (error) {
            console.error('Erro ao carregar categorias:', error);
            mostrarNotificacao('Erro ao carregar categorias', 'error');
        }
    }

    function preencherCategorias(categorias) {
        const container = document.getElementById('categoriasTabs');
        
        // Limpar categorias existentes (exceto "Todos")
        const todosBtn = container.querySelector('button[data-categoria=""]').parentNode;
        container.innerHTML = '';
        container.appendChild(todosBtn);
        
        categorias.forEach(categoria => {
            const li = document.createElement('li');
            li.className = 'nav-item';
            li.innerHTML = `
                <button class="nav-link" data-categoria="${categoria.id_categoria}">
                    ${categoria.nome_categoria}
                </button>
            `;
            container.appendChild(li);
        });
        
        // Adicionar eventos às abas
        document.querySelectorAll('#categoriasTabs .nav-link').forEach(aba => {
            aba.addEventListener('click', function() {
                const categoriaId = this.getAttribute('data-categoria');
                filtrarPorCategoria(categoriaId);
                
                // Ativar aba
                document.querySelectorAll('#categoriasTabs .nav-link').forEach(a => a.classList.remove('active'));
                this.classList.add('active');
            });
        });
    }

    function exibirProdutos(listaProdutos) {
        const container = document.getElementById('listaProdutos');
        
        if (!Array.isArray(listaProdutos)) {
            console.error('listaProdutos não é um array:', listaProdutos);
            listaProdutos = [];
        }
        
        if (listaProdutos.length === 0) {
            container.innerHTML = `
                <div class="col-12 text-center text-muted py-5">
                    <i class="bi bi-search" style="font-size: 3rem;"></i>
                    <p class="mt-3">Nenhum produto encontrado</p>
                </div>
            `;
            return;
        }
        
        container.innerHTML = listaProdutos.map(produto => {
            const precoVenda = parseFloat(produto.preco_venda || 0);
            
            return `
                <div class="col-md-4 col-sm-6 mb-3">
                    <div class="produto-card" onclick="adicionarAoCarrinho(${produto.id_produto})">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 class="mb-0">${produto.descricao}</h6>
                            <small class="text-muted">#${produto.id_produto}</small>
                        </div>
                        <p class="text-muted small mb-2">${produto.categoria || 'Sem categoria'}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="estoque-info">
                                <small>Disponível</small>
                            </div>
                            <div class="preco fw-bold text-primary">
                                R$ ${formatarPreco(precoVenda)}
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    }

    function filtrarProdutos() {
        const termo = document.getElementById('searchProduto').value.toLowerCase();
        
        if (termo.length === 0) {
            exibirProdutos(produtos);
            return;
        }
        
        const produtosFiltrados = produtos.filter(produto => 
            produto.id_produto.toString().includes(termo) ||
            produto.descricao.toLowerCase().includes(termo) ||
            (produto.codigo_barras && produto.codigo_barras.toLowerCase().includes(termo))
        );
        
        exibirProdutos(produtosFiltrados);
    }

    function filtrarPorCategoria(categoriaId) {
        let produtosFiltrados = produtos;
        
        if (categoriaId) {
            produtosFiltrados = produtos.filter(produto => 
                produto.id_categoria == categoriaId
            );
        }
        
        exibirProdutos(produtosFiltrados);
    }

    // ========== CARRINHO ==========
    function adicionarAoCarrinho(idProduto) {
        // Verificar se há caixa aberto
        if (!caixaAberto || caixaAberto.status.toLowerCase() !== 'aberto') {
            mostrarNotificacao('Abra um caixa primeiro para adicionar produtos', 'error');
            return;
        }
        
        const produto = produtos.find(p => p.id_produto === idProduto);
        
        if (!produto) {
            mostrarNotificacao('Produto não encontrado', 'error');
            return;
        }
        
        // Verificar se produto já está no carrinho
        const itemExistente = carrinho.find(item => item.id_produto === idProduto);
        
        if (itemExistente) {
            itemExistente.quantidade += 1;
        } else {
            carrinho.push({
                id_produto: produto.id_produto,
                descricao: produto.descricao,
                preco_unitario: parseFloat(produto.preco_venda),
                quantidade: 1,
                unidade_medida: produto.unidade_medida
            });
        }
        
        atualizarCarrinho();
        mostrarNotificacao(`${produto.descricao} adicionado ao carrinho`, 'success');
    }

    function removerDoCarrinho(idProduto) {
        const index = carrinho.findIndex(item => item.id_produto === idProduto);
        
        if (index !== -1) {
            carrinho.splice(index, 1);
            atualizarCarrinho();
            mostrarNotificacao('Item removido do carrinho', 'success');
        }
    }

    function alterarQuantidade(idProduto, novaQuantidade) {
        if (novaQuantidade <= 0) {
            removerDoCarrinho(idProduto);
            return;
        }
        
        const item = carrinho.find(item => item.id_produto === idProduto);
        
        if (item) {
            item.quantidade = novaQuantidade;
            atualizarCarrinho();
        }
    }

    function atualizarCarrinho() {
        const container = document.getElementById('carrinhoItens');
        const totalElement = document.getElementById('valorTotalVenda');
        
        if (carrinho.length === 0) {
            container.innerHTML = `
                <div class="text-center text-muted py-5">
                    <i class="bi bi-cart-x" style="font-size: 3rem;"></i>
                    <p class="mt-3">Nenhum item adicionado</p>
                </div>
            `;
            totalElement.textContent = 'R$ 0,00';
            return;
        }
        
        let total = 0;
        container.innerHTML = '';
        
        carrinho.forEach(item => {
            const subtotal = item.preco_unitario * item.quantidade;
            total += subtotal;
            
            const div = document.createElement('div');
            div.className = 'item-carrinho';
            div.innerHTML = `
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <h6 class="mb-1">${item.descricao}</h6>
                        <small class="text-light">R$ ${formatarPreco(item.preco_unitario)} / ${item.unidade_medida}</small>
                    </div>
                    <button class="btn btn-sm btn-outline-light" onclick="removerDoCarrinho(${item.id_produto})">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="quantidade-controller">
                        <button class="btn-quantidade" onclick="alterarQuantidade(${item.id_produto}, ${item.quantidade - 1})">
                            <i class="bi bi-dash"></i>
                        </button>
                        <span class="fw-bold">${item.quantidade}</span>
                        <button class="btn-quantidade" onclick="alterarQuantidade(${item.id_produto}, ${item.quantidade + 1})">
                            <i class="bi bi-plus"></i>
                        </button>
                    </div>
                    <div class="fw-bold">
                        R$ ${formatarPreco(subtotal)}
                    </div>
                </div>
            `;
            container.appendChild(div);
        });
        
        totalElement.textContent = `R$ ${formatarPreco(total)}`;
    }

    function limparCarrinho() {
        if (carrinho.length === 0) return;
        
        if (confirm('Tem certeza que deseja limpar o carrinho?')) {
            carrinho = [];
            atualizarCarrinho();
            mostrarNotificacao('Carrinho limpo', 'success');
        }
    }

    // ========== PAGAMENTO ==========
    function selecionarPagamento(forma) {
        // Verificar se há caixa aberto
        if (!caixaAberto || caixaAberto.status.toLowerCase() !== 'aberto') {
            mostrarNotificacao('Abra um caixa primeiro para realizar vendas', 'error');
            return;
        }
        
        if (carrinho.length === 0) {
            mostrarNotificacao('Adicione produtos ao carrinho primeiro', 'error');
            return;
        }
        
        document.getElementById('formaPagamento').value = forma === 'cartao' ? 'cartao_debito' : forma;
        document.getElementById('valorTotalModal').value = document.getElementById('valorTotalVenda').textContent;
        
        // Mostrar/ocultar campos de troco
        const campoValorRecebido = document.getElementById('campoValorRecebido');
        const campoTroco = document.getElementById('campoTroco');
        const teclado = document.getElementById('tecladoNumerico');
        
        if (forma === 'dinheiro') {
            campoValorRecebido.style.display = 'block';
            campoTroco.style.display = 'block';
            document.getElementById('valorRecebido').value = '';
            document.getElementById('valorTroco').value = '';
            // Mostrar teclado para facilitar entrada do valor recebido
            teclado.style.display = 'grid';
            // Preencher primeira forma de pagamento como dinheiro
            prepararPagamentoInicial('dinheiro');
        } else {
            campoValorRecebido.style.display = 'none';
            campoTroco.style.display = 'none';
            // Ocultar teclado para formas que não precisam de entrada manual
            teclado.style.display = 'none';
            prepararPagamentoInicial(forma);
        }

        // Mostrar modal de pagamento
        modalPagamento.show();

        // Quando o modal fechar, garantir que o teclado fique oculto
        const modalEl = document.getElementById('modalPagamento');
        modalEl.addEventListener('hidden.bs.modal', function () {
            const tecladoEl = document.getElementById('tecladoNumerico');
            if (tecladoEl) tecladoEl.style.display = 'none';
        }, { once: true });
    }

    // Pagamentos múltiplos
    let pagamentosAdicionais = [];

    function prepararPagamentoInicial(forma) {
        // Limpar pagamentos adicionais e criar a linha inicial
        pagamentosAdicionais = [];
        renderizarPagamentosAdicionais();
        // Se forma dinheiro, preenche o campo valorRecebido para que o usuário use o teclado
        if (forma === 'dinheiro') {
            document.getElementById('valorRecebido').value = '';
            document.getElementById('valorTroco').value = 'R$ 0,00';
        }
        // Definir o select de forma de pagamento inicialmente
        document.getElementById('formaPagamento').value = forma === 'cartao' ? 'cartao_debito' : forma;
    }

    document.addEventListener('click', function(e){
        if (e.target && e.target.id === 'btnAdicionarPagamento') {
            e.preventDefault();
            adicionarLinhaPagamento();
        }
    });

    function adicionarLinhaPagamento() {
        const id = Date.now();
        pagamentosAdicionais.push({ id, forma: 'cartao_debito', valor: 0 });
        renderizarPagamentosAdicionais();
    }

    function removerLinhaPagamento(id) {
        pagamentosAdicionais = pagamentosAdicionais.filter(p => p.id !== id);
        renderizarPagamentosAdicionais();
        atualizarTotaisPagamento();
    }

    function renderizarPagamentosAdicionais() {
        const container = document.getElementById('pagamentosAdicionais');
        container.innerHTML = '';
        pagamentosAdicionais.forEach(p => {
            const div = document.createElement('div');
            div.className = 'd-flex gap-2 align-items-center mb-2';
            div.innerHTML = `
                <select class="form-select form-select-sm w-50" onchange="atualizarFormaPagamento(${p.id}, this.value)">
                    <option value="cartao_debito">Cartão Débito</option>
                    <option value="cartao_credito">Cartão Crédito</option>
                    <option value="pix">PIX</option>
                    <option value="dinheiro">Dinheiro</option>
                    <option value="outros">Outros</option>
                </select>
                <input type="number" min="0" step="0.01" class="form-control form-control-sm w-25" value="${p.valor}" onchange="atualizarValorPagamento(${p.id}, this.value)">
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removerLinhaPagamento(${p.id})">Remover</button>
            `;
            container.appendChild(div);
        });
    }

    function atualizarFormaPagamento(id, forma) {
        pagamentosAdicionais = pagamentosAdicionais.map(p => p.id === id ? { ...p, forma } : p);
    }

    function atualizarValorPagamento(id, valor) {
        pagamentosAdicionais = pagamentosAdicionais.map(p => p.id === id ? { ...p, valor: parseFloat(valor) || 0 } : p);
        atualizarTotaisPagamento();
    }

    function atualizarTotaisPagamento() {
        const totalVenda = calcularTotal();
        const valorRecebido = parseFloat(document.getElementById('valorRecebido').value) || 0;
        const somaAdicionais = pagamentosAdicionais.reduce((s, p) => s + (parseFloat(p.valor) || 0), 0);
        const totalPago = valorRecebido + somaAdicionais;

        if (totalPago >= totalVenda) {
            const troco = totalPago - totalVenda;
            document.getElementById('valorTroco').value = `R$ ${formatarPreco(troco)}`;
        } else {
            const restante = totalVenda - totalPago;
            document.getElementById('valorTroco').value = `Falta R$ ${formatarPreco(restante)}`;
        }
    }

    // Atualizar automaticamente quando digitar no campo valorRecebido
    document.getElementById('valorRecebido').addEventListener('input', function(){
        atualizarTotaisPagamento();
    });

    function calcularTroco() {
        const valorTotal = calcularTotal();
        const valorRecebido = parseFloat(document.getElementById('valorRecebido').value) || 0;
        const troco = valorRecebido - valorTotal;
        
        document.getElementById('valorTroco').value = troco > 0 ? `R$ ${formatarPreco(troco)}` : 'R$ 0,00';
    }

    function calcularTotal() {
        return carrinho.reduce((total, item) => total + (item.preco_unitario * item.quantidade), 0);
    }

    async function finalizarVenda() {
        // Verificar se há caixa aberto
        if (!caixaAberto || caixaAberto.status.toLowerCase() !== 'aberto') {
            mostrarNotificacao('Caixa não está aberto. Não é possível finalizar a venda.', 'error');
            return;
        }

        const formaPagamento = document.getElementById('formaPagamento').value;
        const observacoes = document.getElementById('observacoesVenda').value;
        const valorRecebido = parseFloat(document.getElementById('valorRecebido').value) || 0;
        const valorTotal = calcularTotal();

        // Construir pagamentos: incluir dinheiro (valorRecebido) se >0 e pagamentosAdicionais
        const pagamentos = [];
        if (valorRecebido > 0) {
            pagamentos.push({ forma_pagamento: 'dinheiro', valor_pago: valorRecebido });
        }
        pagamentosAdicionais.forEach(p => {
            pagamentos.push({ forma_pagamento: p.forma, valor_pago: parseFloat(p.valor) || 0 });
        });

        const totalPago = pagamentos.reduce((s, p) => s + (parseFloat(p.valor_pago) || 0), 0);

        // Se não há pagamentos definidos, usar a forma de pagamento selecionada
        if (pagamentos.length === 0) {
            pagamentos.push({ forma_pagamento: formaPagamento, valor_pago: valorTotal });
        }

        // CORREÇÃO: Usar dados do caixa aberto
        const venda = {
            id_caixa: caixaAberto.id_caixa, // Usar ID do caixa aberto
            id_empresa: caixaAberto.id_empresa, // Usar empresa do caixa
            id_filial: caixaAberto.id_filial, // Usar filial do caixa
            valor_total: valorTotal,
            tipo_venda: "venda",
            itens: carrinho.map(item => ({
                id_produto: item.id_produto,
                quantidade: item.quantidade,
                preco_unitario: item.preco_unitario
            })),
            pagamentos: pagamentos
        };
        
        // Adicionar observações se existirem
        if (observacoes.trim() !== '') {
            venda.observacoes = observacoes;
        }
        
        mostrarLoading(true);
        
        try {
            const response = await fetch(
                API_CONFIG.REGISTRAR_VENDA,
                {
                    method: 'POST',
                    headers: API_CONFIG.getHeaders(),
                    body: JSON.stringify(venda)
                }
            );
            
            if (response.status === 401) {
                mostrarNotificacao('Sessão expirada. Redirecionando para login...', 'error');
                setTimeout(() => {
                    window.location.href = 'login.php';
                }, 2000);
                return;
            }
            
            if (!response.ok) {
                const errorText = await response.text();
                throw new Error(`Erro ${response.status}: ${response.statusText} - ${errorText}`);
            }
            
            const data = await response.json();
            mostrarNotificacao('Venda registrada com sucesso!', 'success');
            modalPagamento.hide();
            
            // Limpar carrinho
            carrinho = [];
            atualizarCarrinho();
            
            // Focar na busca para próxima venda
            document.getElementById('searchProduto').focus();
            
        } catch (error) {
            console.error('Erro ao registrar venda:', error);
            mostrarNotificacao('Erro ao registrar venda: ' + error.message, 'error');
        } finally {
            mostrarLoading(false);
        }
    }

    // ========== TECLADO NUMÉRICO ==========
    let valorAtual = '';
    
    function adicionarNumero(numero) {
        valorAtual += numero;
        
        // Encontrar produto pelo código de barras ou ID
        if (valorAtual.length >= 2) {
            const produto = produtos.find(p => 
                p.id_produto.toString() === valorAtual || 
                (p.codigo_barras && p.codigo_barras.toString() === valorAtual)
            );
            
            if (produto) {
                adicionarAoCarrinho(produto.id_produto);
                valorAtual = '';
                return;
            }
        }
        
        // Se chegou a 13 dígitos (tamanho padrão de código de barras) sem encontrar, limpar
        if (valorAtual.length >= 13) {
            valorAtual = '';
            mostrarNotificacao('Produto não encontrado', 'error');
        }
    }

    function limparValor() {
        valorAtual = '';
    }

    // ========== FUNÇÕES AUXILIARES ==========
    function formatarPreco(preco) {
        if (!preco) return '0,00';
        return parseFloat(preco).toFixed(2).replace('.', ',');
    }

    function atualizarHora() {
        const agora = new Date();
        const hora = agora.getHours().toString().padStart(2, '0');
        const minutos = agora.getMinutes().toString().padStart(2, '0');
        const segundos = agora.getSeconds().toString().padStart(2, '0');
        
        document.getElementById('horaAtual').textContent = `${hora}:${minutos}:${segundos}`;
    }

    function voltarDashboard() {
        if (carrinho.length > 0 && !confirm('Há itens no carrinho. Tem certeza que deseja sair?')) {
            return;
        }
        window.location.href = '../?view=home-DashboardPDV';
    }

    function mostrarLoading(mostrar) {
        document.getElementById('loadingOverlay').classList.toggle('d-none', !mostrar);
    }

    // Função para mostrar notificações
    function mostrarNotificacao(message, type) {
        // Remover notificações anteriores
        document.querySelectorAll('.alert.position-fixed').forEach(alert => {
            alert.remove();
        });
        
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
        }, 3000);
    }
    </script>
</body>
</html>
[file content end]