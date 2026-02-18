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

// Extrair ID do usuário da sessão
$id_usuario = $_SESSION['usuario_id'] ?? ($usuario['id'] ?? 1);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contagem Rápida - SAS Multi</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #3498DB;
            --secondary-color: #2C3E50;
            --success-color: #27AE60;
        }
        
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .header {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 20px;
            text-align: center;
        }
        
        .card-custom {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 20px;
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
        
        .product-item {
            border-left: 3px solid transparent;
            transition: all 0.3s ease;
        }
        
        .product-item:hover {
            background-color: #f8f9fa;
            border-left-color: var(--primary-color);
        }
        
        .count-input {
            width: 120px;
        }
        
        .radio-group {
            display: flex;
            gap: 15px;
            align-items: center;
        }
        
        .radio-option {
            display: flex;
            align-items: center;
            gap: 5px;
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
        
        .btn-save {
            background: var(--success-color);
            border-color: var(--success-color);
            color: white;
        }
        
        .btn-save:hover {
            background: #219653;
            border-color: #219653;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1 class="mb-3">Contagem Rápida de Inventário</h1>
            <p class="text-muted mb-0">Sistema simplificado para contagem de itens em inventário</p>
        </div>

        <!-- Formulário de Acesso ao Inventário -->
        <div class="card-custom">
            <div class="card-header-custom">
                <h5 class="mb-0">Acessar Inventário</h5>
            </div>
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-6">
                        <label class="form-label">Número do Inventário</label>
                        <input type="number" class="form-control" id="numeroInventario" placeholder="Digite o número do inventário" required>
                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-primary" onclick="carregarInventario()">
                            <i class="bi bi-search me-2"></i>
                            Carregar Inventário
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informações do Inventário (aparece após carregar) -->
        <div class="card-custom d-none" id="infoInventario">
            <div class="card-header-custom d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Informações do Inventário</h5>
                <button class="btn btn-success btn-save" onclick="salvarTodasContagens()">
                    <i class="bi bi-check-circle me-2"></i>
                    Salvar Todas as Contagens
                </button>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <small class="text-muted">Código:</small>
                        <div><strong id="codigoInventario">-</strong></div>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted">Descrição:</small>
                        <div><strong id="descricaoInventario">-</strong></div>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted">Filial:</small>
                        <div><strong id="filialInventario">-</strong></div>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted">Status:</small>
                        <div><span class="badge bg-warning" id="statusInventario">-</span></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista de Produtos (aparece após carregar) -->
        <div class="card-custom d-none" id="listaProdutos">
            <div class="card-header-custom">
                <h5 class="mb-0">Itens do Inventário</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="100">Código</th>
                                <th>Produto</th>
                                <th width="150">Estoque Sistema</th>
                                <th width="200">Quantidade Contada</th>
                                <th width="250">Tipo de Operação</th>
                                <th width="100">Ações</th>
                            </tr>
                        </thead>
                        <tbody id="tabelaProdutos">
                            <!-- Produtos serão carregados dinamicamente -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Mensagem quando não há itens -->
        <div class="card-custom d-none" id="mensagemVazia">
            <div class="card-body text-center py-5">
                <i class="bi bi-inbox" style="font-size: 3rem; color: #6c757d;"></i>
                <h5 class="mt-3">Nenhum item encontrado</h5>
                <p class="text-muted">Este inventário não possui itens para contagem.</p>
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
        const BASE_URL = 'http://localhost/saas-multiempresas-api/public';
        
        let inventario = null;
        let produtosInventario = [];
        let contagensSalvas = [];

        // Configuração da API
        const API_CONFIG = {
            // Endpoint para obter a capa do inventário
            CAPA_INVENTARIO: (idCapa) => 
                `${BASE_URL}/api/capa-inventarios/${idCapa}`,
            
            // Endpoints para contagem
            CONTAGENS_CREATE: () => 
                `${BASE_URL}/api/contagens`,
            
            CONTAGENS_UPDATE: (idContagem) => 
                `${BASE_URL}/api/contagens/${idContagem}`,
            
            // Endpoint para obter os itens do inventário
            INVENTARIOS_CAPA: (idCapa) => 
                `${BASE_URL}/api/v1/inventarios/capa/${idCapa}`,
            
            getHeaders: function() {
                return {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-ID-EMPRESA': idEmpresa.toString()
                };
            }
        };

        // Função para carregar inventário
        async function carregarInventario() {
            const numeroInventario = document.getElementById('numeroInventario').value;
            
            if (!numeroInventario) {
                mostrarNotificacao('Por favor, informe o número do inventário', 'error');
                return;
            }
            
            mostrarLoading(true);
            
            try {
                // Buscar capa do inventário
                const responseCapa = await fetch(
                    API_CONFIG.CAPA_INVENTARIO(numeroInventario),
                    {
                        method: 'GET',
                        headers: API_CONFIG.getHeaders()
                    }
                );
                
                if (!responseCapa.ok) {
                    throw new Error(`Inventário não encontrado ou sem acesso`);
                }
                
                inventario = await responseCapa.json();
                
                // Buscar itens do inventário
                const responseItens = await fetch(
                    API_CONFIG.INVENTARIOS_CAPA(numeroInventario),
                    {
                        method: 'GET',
                        headers: API_CONFIG.getHeaders()
                    }
                );
                
                if (!responseItens.ok) {
                    throw new Error(`Erro ao carregar itens do inventário`);
                }
                
                const itens = await responseItens.json();
                produtosInventario = itens || [];
                
                // Exibir informações do inventário
                exibirInformacoesInventario();
                
                // Exibir produtos
                if (produtosInventario.length > 0) {
                    exibirProdutos();
                    document.getElementById('listaProdutos').classList.remove('d-none');
                    document.getElementById('mensagemVazia').classList.add('d-none');
                } else {
                    document.getElementById('listaProdutos').classList.add('d-none');
                    document.getElementById('mensagemVazia').classList.remove('d-none');
                }
                
            } catch (error) {
                console.error('Erro ao carregar inventário:', error);
                mostrarNotificacao('Erro: ' + error.message, 'error');
                document.getElementById('infoInventario').classList.add('d-none');
                document.getElementById('listaProdutos').classList.add('d-none');
                document.getElementById('mensagemVazia').classList.add('d-none');
            } finally {
                mostrarLoading(false);
            }
        }

        function exibirInformacoesInventario() {
            document.getElementById('infoInventario').classList.remove('d-none');
            document.getElementById('codigoInventario').textContent = `#${inventario.id_capa_inventario}`;
            document.getElementById('descricaoInventario').textContent = inventario.descricao;
            document.getElementById('filialInventario').textContent = inventario.filial ? inventario.filial.nome_filial : 'N/A';
            document.getElementById('statusInventario').textContent = formatarStatus(inventario.status);
        }

        function exibirProdutos() {
            const tbody = document.getElementById('tabelaProdutos');
            
            tbody.innerHTML = produtosInventario.map(produto => {
                const produtoInfo = produto.produto;
                const quantidadeSistema = parseFloat(produto.quantidade_sistema || 0);
                const quantidadeFisica = produto.quantidade_fisica || '';
                
                return `
                    <tr class="product-item" data-id-produto="${produto.id_produto}">
                        <td>
                            <strong>${produtoInfo ? produtoInfo.id_produto : 'N/A'}</strong>
                        </td>
                        <td>
                            <div>
                                <strong>${produtoInfo ? produtoInfo.descricao : 'N/A'}</strong>
                                <br>
                                <small class="text-muted">${produtoInfo ? produtoInfo.categoria : 'Sem categoria'}</small>
                            </div>
                        </td>
                        <td>
                            <strong>${quantidadeSistema}</strong>
                            <br>
                            <small class="text-muted">${produtoInfo ? produtoInfo.unidade_medida : ''}</small>
                        </td>
                        <td>
                            <input type="number" class="form-control count-input" 
                                   value="${quantidadeFisica}" 
                                   data-id-produto="${produto.id_produto}"
                                   step="0.01" min="0" placeholder="0.00">
                        </td>
                        <td>
                            <div class="radio-group">
                                <div class="radio-option">
                                    <input class="form-check-input" type="radio" 
                                           name="tipo_operacao_${produto.id_produto}" 
                                           value="Adicionar" checked>
                                    <label class="form-check-label">Adicionar</label>
                                </div>
                                <div class="radio-option">
                                    <input class="form-check-input" type="radio" 
                                           name="tipo_operacao_${produto.id_produto}" 
                                           value="Substituir">
                                    <label class="form-check-label">Substituir</label>
                                </div>
                                <div class="radio-option">
                                    <input class="form-check-input" type="radio" 
                                           name="tipo_operacao_${produto.id_produto}" 
                                           value="Excluir">
                                    <label class="form-check-label">Excluir</label>
                                </div>
                            </div>
                        </td>
                        <td>
                            <button class="btn btn-outline-success btn-sm" onclick="salvarContagemIndividual(${produto.id_produto})">
                                <i class="bi bi-check"></i>
                            </button>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        // Salvar contagem individual
        async function salvarContagemIndividual(idProduto) {
            const produto = produtosInventario.find(p => p.id_produto === idProduto);
            if (!produto) {
                mostrarNotificacao('Produto não encontrado', 'error');
                return;
            }
            
            const inputQuantidade = document.querySelector(`input[data-id-produto="${idProduto}"]`);
            const quantidadeFisica = parseFloat(inputQuantidade.value) || 0;
            
            const radioOperacao = document.querySelector(`input[name="tipo_operacao_${idProduto}"]:checked`);
            const tipoOperacao = radioOperacao ? radioOperacao.value : 'Adicionar';
            
            await salvarContagem(produto, quantidadeFisica, tipoOperacao);
        }

        // Salvar todas as contagens
        async function salvarTodasContagens() {
            mostrarLoading(true);
            let contagensSalvasComSucesso = 0;
            
            for (const produto of produtosInventario) {
                const inputQuantidade = document.querySelector(`input[data-id-produto="${produto.id_produto}"]`);
                const quantidadeFisica = parseFloat(inputQuantidade.value) || 0;
                
                // Se não informou quantidade, pula
                if (!quantidadeFisica) continue;
                
                const radioOperacao = document.querySelector(`input[name="tipo_operacao_${produto.id_produto}"]:checked`);
                const tipoOperacao = radioOperacao ? radioOperacao.value : 'Adicionar';
                
                try {
                    await salvarContagem(produto, quantidadeFisica, tipoOperacao);
                    contagensSalvasComSucesso++;
                } catch (error) {
                    console.error(`Erro ao salvar contagem do produto ${produto.id_produto}:`, error);
                }
            }
            
            mostrarLoading(false);
            
            if (contagensSalvasComSucesso > 0) {
                mostrarNotificacao(`${contagensSalvasComSucesso} contagens salvas com sucesso!`, 'success');
            } else {
                mostrarNotificacao('Nenhuma contagem foi salva. Informe as quantidades físicas.', 'warning');
            }
        }

        // Função genérica para salvar contagem
        async function salvarContagem(produto, quantidadeFisica, tipoOperacao) {
            // Formatar data no formato MySQL (YYYY-MM-DD HH:MM:SS)
            const agora = new Date();
            const dataFormatada = agora.toISOString().slice(0, 19).replace('T', ' ');
            
            const dadosContagem = {
                id_inventario: parseInt(produto.id_inventario),
                id_empresa: parseInt(idEmpresa),
                id_filial: parseInt(produto.id_filial),
                id_produto: parseInt(produto.id_produto),
                tipo_operacao: tipoOperacao,
                quantidade: quantidadeFisica,
                observacao: null,
                id_usuario: parseInt(idUsuario),
                data_contagem: dataFormatada
            };
            
            try {
                const response = await fetch(
                    API_CONFIG.CONTAGENS_CREATE(),
                    {
                        method: 'POST',
                        headers: API_CONFIG.getHeaders(),
                        body: JSON.stringify(dadosContagem)
                    }
                );
                
                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(`Erro ${response.status}: ${JSON.stringify(errorData)}`);
                }
                
                const contagemSalva = await response.json();
                contagensSalvas.push(contagemSalva);
                
                // Marcar visualmente que foi salvo
                const input = document.querySelector(`input[data-id-produto="${produto.id_produto}"]`);
                input.classList.add('is-valid');
                
                mostrarNotificacao('Contagem salva com sucesso!', 'success');
                
            } catch (error) {
                console.error('Erro ao salvar contagem:', error);
                mostrarNotificacao('Erro ao salvar contagem: ' + error.message, 'error');
                throw error;
            }
        }

        // ========== FUNÇÕES AUXILIARES ==========
        function formatarStatus(status) {
            const statusMap = {
                'em_andamento': 'Em Andamento',
                'concluido': 'Concluído',
                'cancelado': 'Cancelado'
            };
            return statusMap[status] || status;
        }

        function mostrarLoading(mostrar) {
            document.getElementById('loadingOverlay').classList.toggle('d-none', !mostrar);
        }

        function mostrarNotificacao(message, type) {
            // Remover notificações anteriores
            const notificacoesAntigas = document.querySelectorAll('.alert-notification');
            notificacoesAntigas.forEach(notif => notif.remove());
            
            const notification = document.createElement('div');
            notification.className = `alert alert-${type === 'error' ? 'danger' : type} alert-notification alert-dismissible fade show position-fixed`;
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

        // Permitir Enter no campo de número do inventário
        document.getElementById('numeroInventario').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                carregarInventario();
            }
        });
    </script>
</body>
</html>