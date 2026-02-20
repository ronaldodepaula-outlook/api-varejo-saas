<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\FornecedorController;
use App\Http\Controllers\CapaInventarioController;
use App\Http\Controllers\CapaTransferenciaController;
use App\Http\Controllers\CompanyUserController;
use App\Http\Controllers\ContagemInventarioController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\EstoqueController;
use App\Http\Controllers\FilialController;
use App\Http\Controllers\GestaoNfeController;
use App\Http\Controllers\GrupoController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\LicencaController;
use App\Http\Controllers\ListApiRoutes;
use App\Http\Controllers\MovimentacaoController;
use App\Http\Controllers\NotificacaoController;
use App\Http\Controllers\PagamentoController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\PasswordUpdateController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\RelatorioKardexController;
use App\Http\Controllers\SecaoController;
use App\Http\Controllers\SubgrupoController;
use App\Http\Controllers\TransferenciaController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\AssinaturaController;

use App\Http\Controllers\Api\DebugController;
use App\Http\Controllers\Api\DiagnosticoController;
use App\Http\Controllers\Api\NfeCabecalhoController;
use App\Http\Controllers\Api\NfeCobrancaController;
use App\Http\Controllers\Api\NfeDestinatarioController;
use App\Http\Controllers\Api\NfeDetalhesController;
use App\Http\Controllers\Api\NfeDuplicatasController;
use App\Http\Controllers\Api\NfeEmitenteController;
use App\Http\Controllers\Api\NfeImpostosController;
use App\Http\Controllers\Api\NfeImportacaoController;
use App\Http\Controllers\Api\NfeInformacoesAdicionaisController;
use App\Http\Controllers\Api\NfeItensController;
use App\Http\Controllers\Api\NfeListaController;
use App\Http\Controllers\Api\NfePagamentosController;
use App\Http\Controllers\Api\NfeTransporteController;
use App\Http\Controllers\Api\Pdv\PdvCaixaController;
use App\Http\Controllers\Api\Pdv\PdvVendaController;
use App\Http\Controllers\Api\SefazCearaController;

use App\Http\Controllers\Vendas\ClienteController;
use App\Http\Controllers\Vendas\DebitoClienteController;
use App\Http\Controllers\Vendas\ItemVendaAssistidaController;
use App\Http\Controllers\Vendas\VendaAssistidaController;
use App\Http\Controllers\CotacaoController;
use App\Http\Controllers\PedidoCompraController;
use App\Http\Controllers\EntradaCompraController;
use App\Http\Controllers\ProdutoFornecedorController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// =======================
// Auth (primeiro)
// =======================
Route::post('registrar', [CompanyUserController::class, 'store']);
Route::get('verificar-email/{token}', [AuthController::class, 'verificarEmail']);
Route::post('login', [AuthController::class, 'login']);
// Alias público para geração de token
Route::post('token', [AuthController::class, 'login']);

// Senhas (fluxos legados e atuais)
Route::prefix('v1')->group(function () {
    Route::get('password/test', [PasswordResetController::class, 'test']);

    Route::post('password/solicitar-reset', [PasswordResetController::class, 'solicitarReset']);
    Route::post('password/validar-token', [PasswordResetController::class, 'validarToken']);
    Route::post('password/resetar-senha', [PasswordResetController::class, 'resetarSenha']);

    // Rotas legadas
    Route::post('password/email', [PasswordResetController::class, 'solicitarReset']);
    Route::post('password/reset/{token}', [PasswordUpdateController::class, 'reset']);
    Route::post('esqueci-senha', [PasswordResetController::class, 'solicitarReset']);
    Route::post('redefinir-senha', [PasswordResetController::class, 'resetarSenha']);
});

Route::post('password/solicitar-reset', [PasswordResetController::class, 'solicitarReset']);

Route::middleware('auth:sanctum')->get('user', function (Request $request) {
    return $request->user();
});

// =======================
// Core (Empresa / Filial / Usuario / Licenca)
// =======================
Route::apiResource('empresas', EmpresaController::class);
Route::get('empresas/usuario/{id_usuario}', [EmpresaController::class, 'empresaDoUsuario']);
Route::get('empresas/por-usuario/{id_usuario}', [EmpresaController::class, 'empresaPorUsuario']);

Route::apiResource('filiais', FilialController::class);
Route::get('filiais/empresa/{id_empresa}', [FilialController::class, 'filiaisPorEmpresa']);

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('usuarios', UsuarioController::class);
    Route::get('usuarios/empresa/{id_empresa}', [UsuarioController::class, 'usuariosPorEmpresa']);
});

Route::apiResource('licencas', LicencaController::class);
Route::apiResource('assinaturas', AssinaturaController::class);
Route::apiResource('pagamentos', PagamentoController::class);

// =======================
// Notificacoes e Backup
// =======================
Route::prefix('notificacoes')->group(function () {
    Route::get('licencas-expirando', [NotificacaoController::class, 'licencasExpirando']);
    Route::get('tentativas-login', [NotificacaoController::class, 'tentativasLoginSuspeitas']);
    Route::get('backup', [NotificacaoController::class, 'backupStatus']);
    Route::get('versao', [NotificacaoController::class, 'versaoSistema']);
});
Route::post('backup/executar', [BackupController::class, 'executar']);

// =======================
// V1 (protegido)
// =======================
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    Route::get('dashboard/resumo', [DashboardController::class, 'resumo']);

    Route::apiResource('empresas', EmpresaController::class);
    Route::apiResource('filiais', FilialController::class);
    Route::apiResource('assinaturas', AssinaturaController::class);
    Route::apiResource('pagamentos', PagamentoController::class);

    // Catalogo
    Route::apiResource('categorias', CategoriaController::class);
    Route::get('categorias/{id}/secoes', [CategoriaController::class, 'secoes']);
    Route::get('categorias/empresa/{id_empresa}', [CategoriaController::class, 'categoriasPorEmpresa']);

    Route::apiResource('secoes', SecaoController::class);
    Route::get('secoes/por-categoria/{idCategoria}', [SecaoController::class, 'porCategoria']);
    Route::get('secoes/empresa/{id_empresa}/categoria/{id_categoria}', [SecaoController::class, 'secoesPorEmpresaCategoria']);

    Route::apiResource('grupos', GrupoController::class);
    Route::get('grupos/por-secao/{idSecao}', [GrupoController::class, 'porSecao']);
    Route::get('grupos/empresa/{id_empresa}/secao/{id_secao}', [GrupoController::class, 'gruposPorEmpresaSecao']);

    Route::apiResource('subgrupos', SubgrupoController::class);
    Route::get('subgrupos/por-grupo/{idGrupo}', [SubgrupoController::class, 'porGrupo']);
    Route::get('subgrupos/empresa/{id_empresa}/grupo/{id_grupo}', [SubgrupoController::class, 'subgruposPorEmpresaGrupo']);

    // Fornecedores
    Route::apiResource('fornecedores', FornecedorController::class);
    Route::get('fornecedores/empresa/{id_empresa}', [FornecedorController::class, 'fornecedoresPorEmpresa']);

    // Produtos
    Route::get('produtos', [ProdutoController::class, 'index']);
    Route::get('produtos/{id}', [ProdutoController::class, 'show']);
    Route::post('produtos', [ProdutoController::class, 'store']);
    Route::put('produtos/{id}', [ProdutoController::class, 'update']);
    Route::delete('produtos/{id}', [ProdutoController::class, 'destroy']);

    Route::get('empresas/{id_empresa}/produtos', [ProdutoController::class, 'listarPorEmpresa']);
    Route::get('empresas/{id_empresa}/categorias/{id_categoria}/produtos', [ProdutoController::class, 'listarPorCategoria']);
    Route::get('empresas/{id_empresa}/secoes/{id_secao}/produtos', [ProdutoController::class, 'listarPorSecao']);
    Route::get('empresas/{id_empresa}/grupos/{id_grupo}/produtos', [ProdutoController::class, 'listarPorGrupo']);
    Route::get('empresas/{id_empresa}/subgrupos/{id_subgrupo}/produtos', [ProdutoController::class, 'listarPorSubgrupo']);

    // Utilitarios
    Route::get('rotas', [ListApiRoutes::class, 'index']);

    // Compras - Cotações
    Route::get('compras/cotacoes', [CotacaoController::class, 'index']);
    Route::get('compras/cotacoes/todos', [CotacaoController::class, 'todosPorEmpresa']);
    Route::post('compras/cotacoes', [CotacaoController::class, 'store']);
    Route::get('compras/cotacoes/{id}', [CotacaoController::class, 'show']);
    Route::put('compras/cotacoes/{id}', [CotacaoController::class, 'update']);
    Route::delete('compras/cotacoes/{id}', [CotacaoController::class, 'destroy']);

    // Cotacao items
    Route::post('compras/cotacoes/{id_cotacao}/itens', [CotacaoController::class, 'storeItem']);
    Route::put('compras/cotacoes/{id_cotacao}/itens/{id_item}', [CotacaoController::class, 'updateItem']);
    Route::delete('compras/cotacoes/{id_cotacao}/itens/{id_item}', [CotacaoController::class, 'destroyItem']);

    // Cotacao fornecedores/respostas
    Route::post('compras/cotacoes/{id_cotacao}/fornecedores', [CotacaoController::class, 'addFornecedor']);
    Route::post('compras/cotacoes/fornecedor/{id_cotacao_fornecedor}/respostas', [CotacaoController::class, 'storeResposta']);

    // Pedidos de compra
    Route::get('compras/pedidos', [PedidoCompraController::class, 'index']);
    Route::post('compras/pedidos', [PedidoCompraController::class, 'store']);
    Route::get('compras/pedidos/{id}', [PedidoCompraController::class, 'show']);
    Route::put('compras/pedidos/{id}', [PedidoCompraController::class, 'update']);
    Route::delete('compras/pedidos/{id}', [PedidoCompraController::class, 'destroy']);

    Route::post('compras/pedidos/{id_pedido}/itens', [PedidoCompraController::class, 'storeItem']);
    Route::put('compras/pedidos/{id_pedido}/itens/{id_item}', [PedidoCompraController::class, 'updateItem']);
    Route::delete('compras/pedidos/{id_pedido}/itens/{id_item}', [PedidoCompraController::class, 'destroyItem']);

    // Entradas de compra
    Route::get('compras/entradas', [EntradaCompraController::class, 'index']);
    Route::post('compras/entradas', [EntradaCompraController::class, 'store']);
    Route::get('compras/entradas/{id}', [EntradaCompraController::class, 'show']);
    Route::put('compras/entradas/{id}', [EntradaCompraController::class, 'update']);
    Route::delete('compras/entradas/{id}', [EntradaCompraController::class, 'destroy']);

    Route::post('compras/entradas/{id_entrada}/itens', [EntradaCompraController::class, 'storeItem']);
    Route::post('compras/entradas/{id_entrada}/historico', [EntradaCompraController::class, 'storeHistorico']);

    // Relação Fornecedor <-> Produto
    Route::get('fornecedor-produto', [ProdutoFornecedorController::class, 'index']);
    Route::get('fornecedor-produto/empresa/{id_empresa}', [ProdutoFornecedorController::class, 'listarPorEmpresa']);
    Route::get('fornecedor-produto/fornecedor/{id_fornecedor}/produto/{id_produto}', [ProdutoFornecedorController::class, 'show']);
    Route::post('fornecedor-produto', [ProdutoFornecedorController::class, 'store']);
    Route::put('fornecedor-produto/fornecedor/{id_fornecedor}/produto/{id_produto}', [ProdutoFornecedorController::class, 'update']);
    Route::delete('fornecedor-produto/fornecedor/{id_fornecedor}/produto/{id_produto}', [ProdutoFornecedorController::class, 'destroy']);
});

// =======================
// Inventario e Contagens
// =======================
Route::apiResource('capa-inventarios', CapaInventarioController::class);
Route::get('v1/capas-inventario/empresa/{id_empresa}', [CapaInventarioController::class, 'listarPorEmpresa']);

Route::apiResource('inventarios', InventarioController::class);
Route::get('inventarios/capa/{id_capa_inventario}', [InventarioController::class, 'listarPorCapa']);
Route::get('v1/inventarios/capa/{id_capa_inventario}', [InventarioController::class, 'listarPorCapa']);

Route::get('contagens', [ContagemInventarioController::class, 'index']);
Route::post('contagens', [ContagemInventarioController::class, 'store']);
Route::get('contagens/{id}', [ContagemInventarioController::class, 'show']);
Route::put('contagens/{id}', [ContagemInventarioController::class, 'update']);
Route::delete('contagens/{id}', [ContagemInventarioController::class, 'destroy']);
Route::get('contagens/inventario/{id_inventario}', [ContagemInventarioController::class, 'listarPorInventario']);
Route::get('contagens/inventario/{id_inventario}/produtos', [ContagemInventarioController::class, 'listarProdutosPorInventario']);

// =======================
// Estoque, Movimentacoes e Transferencias
// =======================
Route::apiResource('estoques', EstoqueController::class);
Route::get('estoque/empresa/{id_empresa}/filial/{id_filial}/produto/{id_produto}', [EstoqueController::class, 'porFilialProduto']);
Route::get('estoques/empresa/{id_empresa}/filial/{id_filial}/produto/{id_produto}', [EstoqueController::class, 'porFilialProduto']);
Route::get('estoques/empresa/{id_empresa}/produto/{id_produto}/filiais', [EstoqueController::class, 'filiaisComEstoquePorEmpresaProduto']);
Route::get('estoque/relatorios/kardex', [RelatorioKardexController::class, 'kardex']);
Route::get('estoque/relatorios/kardex/export', [RelatorioKardexController::class, 'kardexExport']);
Route::get('estoque/relatorios/kardex/resumo', [RelatorioKardexController::class, 'kardexResumo']);

Route::apiResource('movimentacoes', MovimentacaoController::class);
Route::get('movimentacoes/empresa/{id_empresa}/filial/{id_filial}/produto/{id_produto}', [MovimentacaoController::class, 'fichaEstoquePorProdutoEmpresaFilial']);

Route::apiResource('capa-transferencias', CapaTransferenciaController::class);
Route::apiResource('transferencias', TransferenciaController::class);

// =======================
// NFE / DANFE
// =======================
Route::get('nfe', [NfeCabecalhoController::class, 'index']);
Route::get('nfe/{id}', [NfeCabecalhoController::class, 'show']);
Route::post('nfe', [NfeCabecalhoController::class, 'store']);
Route::put('nfe/{id}', [NfeCabecalhoController::class, 'update']);
Route::delete('nfe/{id}', [NfeCabecalhoController::class, 'destroy']);

Route::post('nfe/importar-xml', [NfeCabecalhoController::class, 'importarXml']);
Route::post('nfe/importar', [NfeImportacaoController::class, 'importarXML']);

Route::get('nfe/emitentes', [NfeEmitenteController::class, 'index']);
Route::get('nfe/emitentes/{id}', [NfeEmitenteController::class, 'show']);
Route::post('nfe/emitentes', [NfeEmitenteController::class, 'store']);
Route::put('nfe/emitentes/{id}', [NfeEmitenteController::class, 'update']);
Route::delete('nfe/emitentes/{id}', [NfeEmitenteController::class, 'destroy']);

Route::get('nfe/destinatarios', [NfeDestinatarioController::class, 'index']);
Route::get('nfe/destinatarios/{id}', [NfeDestinatarioController::class, 'show']);
Route::post('nfe/destinatarios', [NfeDestinatarioController::class, 'store']);
Route::put('nfe/destinatarios/{id}', [NfeDestinatarioController::class, 'update']);
Route::delete('nfe/destinatarios/{id}', [NfeDestinatarioController::class, 'destroy']);

Route::get('nfe/itens', [NfeItensController::class, 'index']);
Route::get('nfe/itens/{id}', [NfeItensController::class, 'show']);
Route::post('nfe/itens', [NfeItensController::class, 'store']);
Route::put('nfe/itens/{id}', [NfeItensController::class, 'update']);
Route::delete('nfe/itens/{id}', [NfeItensController::class, 'destroy']);

Route::get('nfe/impostos', [NfeImpostosController::class, 'index']);
Route::get('nfe/impostos/{id}', [NfeImpostosController::class, 'show']);
Route::post('nfe/impostos', [NfeImpostosController::class, 'store']);
Route::put('nfe/impostos/{id}', [NfeImpostosController::class, 'update']);
Route::delete('nfe/impostos/{id}', [NfeImpostosController::class, 'destroy']);

Route::get('nfe/transporte', [NfeTransporteController::class, 'index']);
Route::get('nfe/transporte/{id}', [NfeTransporteController::class, 'show']);
Route::post('nfe/transporte', [NfeTransporteController::class, 'store']);
Route::put('nfe/transporte/{id}', [NfeTransporteController::class, 'update']);
Route::delete('nfe/transporte/{id}', [NfeTransporteController::class, 'destroy']);

Route::get('nfe/cobrancas', [NfeCobrancaController::class, 'index']);
Route::get('nfe/cobrancas/{id}', [NfeCobrancaController::class, 'show']);
Route::post('nfe/cobrancas', [NfeCobrancaController::class, 'store']);
Route::put('nfe/cobrancas/{id}', [NfeCobrancaController::class, 'update']);
Route::delete('nfe/cobrancas/{id}', [NfeCobrancaController::class, 'destroy']);

Route::get('nfe/duplicatas', [NfeDuplicatasController::class, 'index']);
Route::get('nfe/duplicatas/{id}', [NfeDuplicatasController::class, 'show']);
Route::post('nfe/duplicatas', [NfeDuplicatasController::class, 'store']);
Route::put('nfe/duplicatas/{id}', [NfeDuplicatasController::class, 'update']);
Route::delete('nfe/duplicatas/{id}', [NfeDuplicatasController::class, 'destroy']);

Route::get('nfe/pagamentos', [NfePagamentosController::class, 'index']);
Route::get('nfe/pagamentos/{id}', [NfePagamentosController::class, 'show']);
Route::post('nfe/pagamentos', [NfePagamentosController::class, 'store']);
Route::put('nfe/pagamentos/{id}', [NfePagamentosController::class, 'update']);
Route::delete('nfe/pagamentos/{id}', [NfePagamentosController::class, 'destroy']);

Route::get('nfe/informacoes-adicionais', [NfeInformacoesAdicionaisController::class, 'index']);
Route::get('nfe/informacoes-adicionais/{id}', [NfeInformacoesAdicionaisController::class, 'show']);
Route::post('nfe/informacoes-adicionais', [NfeInformacoesAdicionaisController::class, 'store']);
Route::put('nfe/informacoes-adicionais/{id}', [NfeInformacoesAdicionaisController::class, 'update']);
Route::delete('nfe/informacoes-adicionais/{id}', [NfeInformacoesAdicionaisController::class, 'destroy']);

Route::get('sefaz/ceara/{chave}', [SefazCearaController::class, 'consultarPorChave']);

Route::prefix('nfe')->group(function () {
    Route::get('empresa/{id_empresa}', [GestaoNfeController::class, 'listarPorEmpresa']);
    Route::get('empresa/{id_empresa}/filial/{id_filial}', [GestaoNfeController::class, 'listarPorEmpresaFilial']);
    Route::get('empresa/{id_empresa}/cnf/{cNF}', [GestaoNfeController::class, 'listarPorEmpresaECnf']);
    Route::get('empresa/{id_empresa}/periodo', [GestaoNfeController::class, 'listarPorEmpresaEPeriodo']);
    Route::get('emitente/{cnpj}', [GestaoNfeController::class, 'listarPorEmitente']);
    Route::get('destinatario/{cnpj}', [GestaoNfeController::class, 'listarPorDestinatario']);
    Route::get('chave/{chave_acesso}', [GestaoNfeController::class, 'listarPorChaveAcesso']);
});

Route::prefix('nfe')->group(function () {
    // Rotas especificas primeiro
    Route::get('lista', [NfeListaController::class, 'index']);
    Route::get('estatisticas', [NfeListaController::class, 'estatisticas']);
    Route::post('importar', [NfeImportacaoController::class, 'importarXML']);

    // Rotas com parametro depois
    Route::get('{id}', [NfeDetalhesController::class, 'show']);
    Route::get('{id}/itens', [NfeDetalhesController::class, 'itens']);
    Route::delete('{id}', [NfeImportacaoController::class, 'destroy']);

    // Rota raiz
    Route::get('/', [NfeImportacaoController::class, 'index']);
});

Route::prefix('diagnostico')->group(function () {
    Route::get('nfe', [DiagnosticoController::class, 'verificarDados']);
    Route::get('nfe/filtros', [DiagnosticoController::class, 'testarFiltros']);
});

Route::get('nfe-health', function () {
    return response()->json([
        'status' => 'ok',
        'message' => 'API NF-e funcionando',
        'timestamp' => now()->toDateTimeString()
    ]);
});

// =======================
// Debug e Testes
// =======================
Route::get('teste-dados', function () {
    try {
        $dados = DB::table('tb_nfe_cabecalho')->get();

        return response()->json([
            'total_registros' => $dados->count(),
            'dados' => $dados
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage()
        ], 500);
    }
});

Route::get('teste-nfe-lista', function (Request $request) {
    try {
        $dados = DB::table('tb_nfe_cabecalho as cab')
            ->leftJoin('tb_nfe_emitente as emit', 'cab.id_nfe', '=', 'emit.id_nfe')
            ->select(
                'cab.id_nfe',
                'cab.id_empresa',
                'cab.status',
                'cab.nNF',
                'cab.dhEmi',
                'emit.CNPJ as emitente_cnpj'
            )
            ->get();

        return response()->json([
            'success' => true,
            'total_registros' => $dados->count(),
            'dados' => $dados
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
});

Route::get('debug/join', [DebugController::class, 'testarJoin']);

// =======================
// PDV
// =======================
Route::prefix('pdv')->group(function () {
    // Caixas
    Route::get('caixas/status', [PdvCaixaController::class, 'status']);
    Route::get('caixas', [PdvCaixaController::class, 'index']);
    Route::post('caixas/abrir', [PdvCaixaController::class, 'abrir']);
    Route::get('caixas/detalhes', [PdvCaixaController::class, 'detalhes']);
    Route::get('caixas/{id}', [PdvCaixaController::class, 'show']);
    Route::post('caixas/{id}/fechar', [PdvCaixaController::class, 'fechar']);

    // Vendas
    Route::post('vendas', [PdvVendaController::class, 'store']);
    Route::get('vendas/caixa/{id_caixa}', [PdvVendaController::class, 'porCaixa']);
    Route::get('vendas/detalhes', [PdvVendaController::class, 'vendasDetalhes']);
    Route::get('dashboard', [PdvVendaController::class, 'dashboard']);
    Route::get('vendas/cupons', [PdvVendaController::class, 'vendasPorCupons']);
    Route::get('vendas/pagamentos-resumo', [PdvVendaController::class, 'pagamentosResumo']);
});

Route::get('v1/empresas/{id_empresa}/pdv/caixas/status', [PdvCaixaController::class, 'porEmpresaStatus']);
Route::get('v1/empresas/{id_empresa}/pdv/resumo/dia', [PdvCaixaController::class, 'resumoDia']);
Route::get('v1/empresas/{id_empresa}/pdv/vendas', [PdvVendaController::class, 'vendasPorEmpresaData']);
Route::post('v1/empresas/{id_empresa}/pdv/caixas/abertura', [PdvCaixaController::class, 'aberturaPorEmpresa']);

// =======================
// Vendas
// =======================
Route::prefix('vendasapi')->group(function () {
    Route::get('clientes', [ClienteController::class, 'index']);
    Route::post('clientes', [ClienteController::class, 'store']);
    Route::get('clientes/{id}', [ClienteController::class, 'show']);
    Route::put('clientes/{id}', [ClienteController::class, 'update']);
    Route::delete('clientes/{id}', [ClienteController::class, 'destroy']);

    Route::get('assistidas', [VendaAssistidaController::class, 'index']);
    Route::post('assistidas', [VendaAssistidaController::class, 'store']);
    Route::get('assistidas/{id}', [VendaAssistidaController::class, 'show']);
    Route::put('assistidas/{id}', [VendaAssistidaController::class, 'update']);
});

Route::prefix('vendasAssistidas')->group(function () {
    Route::get('clientes', [ClienteController::class, 'index']);
    Route::post('clientes', [ClienteController::class, 'store']);
    Route::get('clientes/empresa/{id_empresa}', [ClienteController::class, 'listarPorEmpresa']);
    Route::get('clientes/{id}', [ClienteController::class, 'show']);
    Route::put('clientes/{id}', [ClienteController::class, 'update']);
    Route::delete('clientes/{id}', [ClienteController::class, 'destroy']);

    Route::get('assistidas', [VendaAssistidaController::class, 'index']);
    Route::get('assistidas/empresa/{id_empresa}', [VendaAssistidaController::class, 'listarPorEmpresa']);
    Route::post('assistidas', [VendaAssistidaController::class, 'store']);
    Route::get('assistidas/{id}', [VendaAssistidaController::class, 'show']);
    Route::put('assistidas/{id}', [VendaAssistidaController::class, 'update']);
    Route::post('assistidas/{id}', [VendaAssistidaController::class, 'update']);
    Route::post('assistidas/{id}/finalizar', [VendaAssistidaController::class, 'finalizar']);
    Route::post('assistidas/{id}/cancelar', [VendaAssistidaController::class, 'cancelar']);

    Route::get('itens-assistida', [ItemVendaAssistidaController::class, 'index']);
    Route::post('itens-assistida', [ItemVendaAssistidaController::class, 'store']);
    Route::get('itens-assistida/{id}', [ItemVendaAssistidaController::class, 'show']);
    Route::put('itens-assistida/{id}', [ItemVendaAssistidaController::class, 'update']);
    Route::delete('itens-assistida/{id}', [ItemVendaAssistidaController::class, 'destroy']);
});

Route::prefix('debitos-clientes')->group(function () {
    Route::get('/', [DebitoClienteController::class, 'index']);
    Route::get('{id}', [DebitoClienteController::class, 'show']);
    Route::put('{id}', [DebitoClienteController::class, 'update']);
    Route::get('cliente/{id_cliente}', [DebitoClienteController::class, 'porCliente']);
    Route::get('empresa/{id_empresa}', [DebitoClienteController::class, 'porEmpresa']);
    Route::get('status/{status}', [DebitoClienteController::class, 'porStatus']);
});
