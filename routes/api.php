<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\FilialController;
use App\Http\Controllers\LicencaController;
use App\Http\Controllers\AssinaturaController;
use App\Http\Controllers\PagamentoController;
use App\Http\Controllers\AuthController;

use App\Http\Controllers\NotificacaoController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\PasswordUpdateController;

use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\SecaoController;
use App\Http\Controllers\GrupoController;
use App\Http\Controllers\SubgrupoController;

use App\Http\Controllers\ProdutoController;

use App\Http\Controllers\Api\NfeCabecalhoController;
use App\Http\Controllers\Api\NfeEmitenteController;
use App\Http\Controllers\Api\NfeDestinatarioController;
use App\Http\Controllers\Api\NfeItensController;
use App\Http\Controllers\Api\NfeImpostosController;
use App\Http\Controllers\Api\NfeTransporteController;
use App\Http\Controllers\Api\NfeCobrancaController;
use App\Http\Controllers\Api\NfeDuplicatasController;
use App\Http\Controllers\Api\NfePagamentosController;
use App\Http\Controllers\Api\NfeInformacoesAdicionaisController;
use App\Http\Controllers\Api\NfeImportacaoController;

use App\Http\Controllers\Api\NfeListaController;
use App\Http\Controllers\Api\NfeDetalhesController;
use App\Http\Controllers\Api\DiagnosticoController;
use App\Http\Controllers\Api\DebugController;
use App\Http\Controllers\GestaoNfeController;
use App\Http\Controllers\EstoqueController;
use App\Http\Controllers\CapaInventarioController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\MovimentacaoController;
use App\Http\Controllers\CapaTransferenciaController;
use App\Http\Controllers\TransferenciaController;
use App\Http\Controllers\Vendas\DebitoClienteController;
use App\Http\Controllers\ContagemInventarioController;
use App\Http\Controllers\Vendas\ClienteController;
use App\Http\Controllers\Api\Pdv\PdvCaixaController;
use App\Http\Controllers\Api\Pdv\PdvVendaController;
use App\Http\Controllers\Vendas\VendaAssistidaController;
use App\Http\Controllers\Vendas\ItemVendaAssistidaController;

// Notificações e alertas

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\PasswordUpdateController;
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResource('empresas', EmpresaController::class);
// Rota para empresaDoUsuario
Route::get('empresas/usuario/{id_usuario}', [EmpresaController::class, 'empresaDoUsuario']);
// Rota para empresaPorUsuario
Route::get('empresas/por-usuario/{id_usuario}', [EmpresaController::class, 'empresaPorUsuario']);
// Rota para listar todas as filiais de uma empresa
Route::get('filiais/empresa/{id_empresa}', [FilialController::class, 'filiaisPorEmpresa']);
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('usuarios', UsuarioController::class);
    Route::get('usuarios/empresa/{id_empresa}', [UsuarioController::class, 'usuariosPorEmpresa']);
});
Route::apiResource('filiais', FilialController::class);
Route::apiResource('licencas', LicencaController::class);
Route::apiResource('assinaturas', AssinaturaController::class);
Route::apiResource('pagamentos', PagamentoController::class);
Route::post('/registrar', [\App\Http\Controllers\CompanyUserController::class, 'store']);
Route::get('/verificar-email/{token}', [AuthController::class, 'verificarEmail']);
Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login']);
// Removidas rotas para métodos inexistentes em AuthController
// Recuperação de senha
Route::post('v1/password/email', [PasswordResetController::class, 'sendResetLinkEmail']);
// Redefinição de senha
Route::post('v1/password/reset/{token}', [PasswordUpdateController::class, 'reset']);
Route::prefix('v1')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        Route::apiResource('empresas', EmpresaController::class);
        Route::apiResource('filiais', FilialController::class);
        Route::apiResource('assinaturas', AssinaturaController::class);
        Route::apiResource('pagamentos', PagamentoController::class);
    });
});

Route::get('/notificacoes/licencas-expirando', [NotificacaoController::class, 'licencasExpirando']);
Route::get('/notificacoes/tentativas-login', [NotificacaoController::class, 'tentativasLoginSuspeitas']);
Route::get('/notificacoes/backup', [NotificacaoController::class, 'backupStatus']);
Route::get('/notificacoes/versao', [NotificacaoController::class, 'versaoSistema']);
Route::post('/backup/executar', [BackupController::class, 'executar']);

Route::prefix('v1')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/dashboard/resumo', [\App\Http\Controllers\DashboardController::class, 'resumo']);
        Route::apiResource('empresas', EmpresaController::class);
        Route::apiResource('filiais', FilialController::class);
        Route::apiResource('assinaturas', AssinaturaController::class);
        Route::apiResource('pagamentos', PagamentoController::class);
    });
});

// Rota de teste para controller
Route::get('v1/password/test', [PasswordResetController::class, 'test']);

Route::post('v1/password/solicitar-reset', [PasswordResetController::class, 'solicitarReset']);

Route::post('v1/esqueci-senha', [PasswordResetController::class, 'sendResetLink']);
Route::post('v1/redefinir-senha', [PasswordResetController::class, 'resetPassword']);

// Solicitar reset de senha
Route::post('/password/solicitar-reset', [PasswordResetController::class, 'solicitarReset']);

// Validar token
Route::post('v1/password/validar-token', [PasswordResetController::class, 'validarToken']);

// Resetar senha
Route::post('v1/password/resetar-senha', [PasswordResetController::class, 'resetarSenha']);


Route::prefix('v1')->middleware('auth:sanctum')->group(function () {

    // =======================
    // Categorias
    // =======================
    Route::apiResource('categorias', CategoriaController::class);
    Route::get('categorias/{id}/secoes', [CategoriaController::class, 'secoes']);
    // Rota para listar todas as categorias de uma empresa
    Route::get('categorias/empresa/{id_empresa}', [CategoriaController::class, 'categoriasPorEmpresa']);

    // =======================
    // Seções
    // =======================
    Route::apiResource('secoes', SecaoController::class);
    Route::get('secoes/por-categoria/{idCategoria}', [SecaoController::class, 'porCategoria']);

    // Rota para listar seções por empresa e categoria
    Route::get('secoes/empresa/{id_empresa}/categoria/{id_categoria}', [SecaoController::class, 'secoesPorEmpresaCategoria']);


    // =======================
    // Grupos
    // =======================
    Route::apiResource('grupos', GrupoController::class);
    Route::get('grupos/por-secao/{idSecao}', [GrupoController::class, 'porSecao']);

    // Rota para listar grupos por empresa e seção
    Route::get('grupos/empresa/{id_empresa}/secao/{id_secao}', [GrupoController::class, 'gruposPorEmpresaSecao']);

    // =======================
    // Subgrupos
    // =======================
    Route::apiResource('subgrupos', SubgrupoController::class);
    Route::get('subgrupos/por-grupo/{idGrupo}', [SubgrupoController::class, 'porGrupo']);

    // Rota para listar subgrupos por empresa e grupo
    Route::get('subgrupos/empresa/{id_empresa}/grupo/{id_grupo}', [SubgrupoController::class, 'subgruposPorEmpresaGrupo']);


});

// Capas de Inventário: listar por empresa
Route::get('v1/capas-inventario/empresa/{id_empresa}', [\App\Http\Controllers\CapaInventarioController::class, 'listarPorEmpresa']);

Route::prefix('v1')->middleware(['auth:sanctum'])->group(function () {
    // CRUD principal
    Route::get('/produtos', [ProdutoController::class, 'index']);
    Route::get('/produtos/{id}', [ProdutoController::class, 'show']);
    Route::post('/produtos', [ProdutoController::class, 'store']);
    Route::put('/produtos/{id}', [ProdutoController::class, 'update']);
    Route::delete('/produtos/{id}', [ProdutoController::class, 'destroy']);

    // Rotas de filtro por hierarquia
    Route::get('/empresas/{id_empresa}/produtos', [ProdutoController::class, 'listarPorEmpresa']);
    Route::get('/empresas/{id_empresa}/categorias/{id_categoria}/produtos', [ProdutoController::class, 'listarPorCategoria']);
    Route::get('/empresas/{id_empresa}/secoes/{id_secao}/produtos', [ProdutoController::class, 'listarPorSecao']);
    Route::get('/empresas/{id_empresa}/grupos/{id_grupo}/produtos', [ProdutoController::class, 'listarPorGrupo']);
    Route::get('/empresas/{id_empresa}/subgrupos/{id_subgrupo}/produtos', [ProdutoController::class, 'listarPorSubgrupo']);
});

// Estoque: buscar por empresa, filial e produto
Route::get('estoque/empresa/{id_empresa}/filial/{id_filial}/produto/{id_produto}', [EstoqueController::class, 'porFilialProduto']);

/*
Route::prefix('nfe')->group(function () {
    Route::get('/', [NfeCabecalhoController::class, 'index']);
    Route::get('/{id}', [NfeCabecalhoController::class, 'show']);
    Route::post('/', [NfeCabecalhoController::class, 'store']);
    Route::put('/{id}', [NfeCabecalhoController::class, 'update']);
    Route::delete('/{id}', [NfeCabecalhoController::class, 'destroy']);
    Route::post('/importar-xml', [NfeCabecalhoController::class, 'importarXml']);
});
*/



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Aqui registramos todas as rotas REST para o módulo de NFe/DANFE.
|
*/

// -------------------- NFE CABEÇALHO --------------------
Route::get('nfe', [NfeCabecalhoController::class, 'index']);
Route::get('nfe/{id}', [NfeCabecalhoController::class, 'show']);
Route::post('nfe', [NfeCabecalhoController::class, 'store']);
Route::put('nfe/{id}', [NfeCabecalhoController::class, 'update']);
Route::delete('nfe/{id}', [NfeCabecalhoController::class, 'destroy']);

// Endpoint para importação de XML
Route::post('nfe/importar-xml', [NfeCabecalhoController::class, 'importarXml']);
Route::post('/nfe/importar', [NfeImportacaoController::class, 'importarXML']);

// -------------------- EMITENTE --------------------
Route::get('nfe/emitentes', [NfeEmitenteController::class, 'index']);
Route::get('nfe/emitentes/{id}', [NfeEmitenteController::class, 'show']);
Route::post('nfe/emitentes', [NfeEmitenteController::class, 'store']);
Route::put('nfe/emitentes/{id}', [NfeEmitenteController::class, 'update']);
Route::delete('nfe/emitentes/{id}', [NfeEmitenteController::class, 'destroy']);

// -------------------- DESTINATÁRIO --------------------
Route::get('nfe/destinatarios', [NfeDestinatarioController::class, 'index']);
Route::get('nfe/destinatarios/{id}', [NfeDestinatarioController::class, 'show']);
Route::post('nfe/destinatarios', [NfeDestinatarioController::class, 'store']);
Route::put('nfe/destinatarios/{id}', [NfeDestinatarioController::class, 'update']);
Route::delete('nfe/destinatarios/{id}', [NfeDestinatarioController::class, 'destroy']);

// -------------------- ITENS --------------------
Route::get('nfe/itens', [NfeItensController::class, 'index']);
Route::get('nfe/itens/{id}', [NfeItensController::class, 'show']);
Route::post('nfe/itens', [NfeItensController::class, 'store']);
Route::put('nfe/itens/{id}', [NfeItensController::class, 'update']);
Route::delete('nfe/itens/{id}', [NfeItensController::class, 'destroy']);

// -------------------- IMPOSTOS --------------------
Route::get('nfe/impostos', [NfeImpostosController::class, 'index']);
Route::get('nfe/impostos/{id}', [NfeImpostosController::class, 'show']);
Route::post('nfe/impostos', [NfeImpostosController::class, 'store']);
Route::put('nfe/impostos/{id}', [NfeImpostosController::class, 'update']);
Route::delete('nfe/impostos/{id}', [NfeImpostosController::class, 'destroy']);

// -------------------- TRANSPORTE --------------------
Route::get('nfe/transporte', [NfeTransporteController::class, 'index']);
Route::get('nfe/transporte/{id}', [NfeTransporteController::class, 'show']);
Route::post('nfe/transporte', [NfeTransporteController::class, 'store']);
Route::put('nfe/transporte/{id}', [NfeTransporteController::class, 'update']);
Route::delete('nfe/transporte/{id}', [NfeTransporteController::class, 'destroy']);

// -------------------- COBRANÇA --------------------
Route::get('nfe/cobrancas', [NfeCobrancaController::class, 'index']);
Route::get('nfe/cobrancas/{id}', [NfeCobrancaController::class, 'show']);
Route::post('nfe/cobrancas', [NfeCobrancaController::class, 'store']);
Route::put('nfe/cobrancas/{id}', [NfeCobrancaController::class, 'update']);
Route::delete('nfe/cobrancas/{id}', [NfeCobrancaController::class, 'destroy']);

// -------------------- DUPLICATAS --------------------
Route::get('nfe/duplicatas', [NfeDuplicatasController::class, 'index']);
Route::get('nfe/duplicatas/{id}', [NfeDuplicatasController::class, 'show']);
Route::post('nfe/duplicatas', [NfeDuplicatasController::class, 'store']);
Route::put('nfe/duplicatas/{id}', [NfeDuplicatasController::class, 'update']);
Route::delete('nfe/duplicatas/{id}', [NfeDuplicatasController::class, 'destroy']);

// -------------------- PAGAMENTOS --------------------
Route::get('nfe/pagamentos', [NfePagamentosController::class, 'index']);
Route::get('nfe/pagamentos/{id}', [NfePagamentosController::class, 'show']);
Route::post('nfe/pagamentos', [NfePagamentosController::class, 'store']);
Route::put('nfe/pagamentos/{id}', [NfePagamentosController::class, 'update']);
Route::delete('nfe/pagamentos/{id}', [NfePagamentosController::class, 'destroy']);

// -------------------- INFORMAÇÕES ADICIONAIS --------------------
Route::get('nfe/informacoes-adicionais', [NfeInformacoesAdicionaisController::class, 'index']);
Route::get('nfe/informacoes-adicionais/{id}', [NfeInformacoesAdicionaisController::class, 'show']);
Route::post('nfe/informacoes-adicionais', [NfeInformacoesAdicionaisController::class, 'store']);
Route::put('nfe/informacoes-adicionais/{id}', [NfeInformacoesAdicionaisController::class, 'update']);
Route::delete('nfe/informacoes-adicionais/{id}', [NfeInformacoesAdicionaisController::class, 'destroy']);


//Route::post('/consulta/ceara', [App\Http\Controllers\SefazCearaController::class, 'consultarPorChave']);

use App\Http\Controllers\Api\SefazCearaController;

Route::get('/sefaz/ceara/{chave}', [SefazCearaController::class, 'consultarPorChave']);

Route::prefix('nfe')->group(function () {
    // Listar por empresa
    Route::get('empresa/{id_empresa}', [GestaoNfeController::class, 'listarPorEmpresa']);
    
    // Listar por empresa e filial
    Route::get('empresa/{id_empresa}/filial/{id_filial}', [GestaoNfeController::class, 'listarPorEmpresaFilial']);
    
    // Listar por empresa e cNF
    Route::get('empresa/{id_empresa}/cnf/{cNF}', [GestaoNfeController::class, 'listarPorEmpresaECnf']);
    
    // Listar por empresa e período
    Route::get('empresa/{id_empresa}/periodo', [GestaoNfeController::class, 'listarPorEmpresaEPeriodo']);
    
    // Listar por emitente
    Route::get('emitente/{cnpj}', [GestaoNfeController::class, 'listarPorEmitente']);
    
    // Listar por destinatário
    Route::get('destinatario/{cnpj}', [GestaoNfeController::class, 'listarPorDestinatario']);
    
    // Nova rota: buscar por chave de acesso
    Route::get('chave/{chave_acesso}', [GestaoNfeController::class, 'listarPorChaveAcesso']);
});

// Rotas para NF-e
Route::prefix('nfe')->group(function () {
    
    // Rotas específicas PRIMEIRO (antes das rotas com parâmetros)
    Route::get('/lista', [NfeListaController::class, 'index']);
    Route::get('/estatisticas', [NfeListaController::class, 'estatisticas']);
    Route::post('/importar', [NfeImportacaoController::class, 'importarXML']);
    
    // Rotas com parâmetros DEPOIS
    Route::get('/{id}', [NfeDetalhesController::class, 'show']);
    Route::get('/{id}/itens', [NfeDetalhesController::class, 'itens']);
    Route::delete('/{id}', [NfeImportacaoController::class, 'destroy']);
    
    // Rota raiz
    Route::get('/', [NfeImportacaoController::class, 'index']);
});

// Rotas de diagnóstico
Route::prefix('diagnostico')->group(function () {
    Route::get('/nfe', [DiagnosticoController::class, 'verificarDados']);
    Route::get('/nfe/filtros', [DiagnosticoController::class, 'testarFiltros']);
});
// Rota de health check
Route::get('/nfe-health', function () {
    return response()->json([
        'status' => 'ok',
        'message' => 'API NF-e funcionando',
        'timestamp' => now()->toDateTimeString()
    ]);
});

// Rota temporária para teste direto
Route::get('/teste-dados', function () {
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

// Rota de teste direto - SEM FILTROS
Route::get('/teste-nfe-lista', function (Request $request) {
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

Route::get('/debug/join', [DebugController::class, 'testarJoin']);

// Estoques
Route::apiResource('estoques', EstoqueController::class);
// Rota atualizada: busca estoque por empresa, filial e produto
Route::get('estoques/empresa/{id_empresa}/filial/{id_filial}/produto/{id_produto}', [EstoqueController::class, 'porFilialProduto']);
// Rota: listar filiais que possuem estoque para um produto em uma empresa
Route::get('estoques/empresa/{id_empresa}/produto/{id_produto}/filiais', [EstoqueController::class, 'filiaisComEstoquePorEmpresaProduto']);
// Ficha de estoque: listar movimentações por produto/empresa/filial
Route::get('movimentacoes/empresa/{id_empresa}/filial/{id_filial}/produto/{id_produto}', [MovimentacaoController::class, 'fichaEstoquePorProdutoEmpresaFilial']);

// Capa Inventários
Route::apiResource('capa-inventarios', CapaInventarioController::class);
    // Listar itens por capa de inventário
    // rota sem prefixo (compatibilidade)
    Route::get('inventarios/capa/{id_capa_inventario}', [\App\Http\Controllers\InventarioController::class, 'listarPorCapa']);
    // rota com prefixo v1 para /api/v1/inventarios/capa/{id}
    Route::get('v1/inventarios/capa/{id_capa_inventario}', [InventarioController::class, 'listarPorCapa']);

// Inventários
Route::apiResource('inventarios', InventarioController::class);

// Movimentações
Route::apiResource('movimentacoes', MovimentacaoController::class);

// Capa Transferências
Route::apiResource('capa-transferencias', CapaTransferenciaController::class);

// Transferências
Route::apiResource('transferencias', TransferenciaController::class);


// CRUD completo
Route::get('/contagens', [ContagemInventarioController::class, 'index']);
Route::post('/contagens', [ContagemInventarioController::class, 'store']);
Route::get('/contagens/{id}', [ContagemInventarioController::class, 'show']);
Route::put('/contagens/{id}', [ContagemInventarioController::class, 'update']);
Route::delete('/contagens/{id}', [ContagemInventarioController::class, 'destroy']);

// Filtros específicos
Route::get('/contagens/inventario/{id_inventario}', [ContagemInventarioController::class, 'listarPorInventario']);

// Listar produtos contados por inventário (agregado por produto)
Route::get('/contagens/inventario/{id_inventario}/produtos', [ContagemInventarioController::class, 'listarProdutosPorInventario']);

// Rotas para Vendas Assistidas
Route::prefix('vendasAssistidas')->group(function () {
    Route::prefix('clientes')->group(function () {
        Route::get('/', [ClienteController::class, 'index']);
        Route::post('/', [ClienteController::class, 'store']);
        Route::get('/{id}', [ClienteController::class, 'show']);
        Route::put('/{id}', [ClienteController::class, 'update']);
        Route::delete('/{id}', [ClienteController::class, 'destroy']);
        Route::delete('/{id}', [VendaClienteController::class, 'destroy']);
    });
});

Route::prefix('pdv')->group(function () {
    // 📦 Caixas
    // Rota: listar caixas por status e filial (ex: /api/pdv/caixas/status?status=aberto&id_filial=1)
    Route::get('/caixas/status', [PdvCaixaController::class, 'status']);
    Route::get('/caixas', [PdvCaixaController::class, 'index']);
    Route::post('/caixas/abrir', [PdvCaixaController::class, 'abrir']);
    Route::get('/caixas/detalhes', [PdvCaixaController::class, 'detalhes']);
    Route::get('/caixas/{id}', [PdvCaixaController::class, 'show']);
    Route::post('/caixas/{id}/fechar', [PdvCaixaController::class, 'fechar']);

    // 🧾 Vendas
    Route::post('/vendas', [PdvVendaController::class, 'store']);
    Route::get('/vendas/caixa/{id_caixa}', [PdvVendaController::class, 'porCaixa']);
        // Vendas detalhes (itens e produto)
        Route::get('/vendas/detalhes', [PdvVendaController::class, 'vendasDetalhes']);
    // Dashboard de PDV (métricas)
    Route::get('/dashboard', [PdvVendaController::class, 'dashboard']);
    // Vendas por cupons (com itens e totalizadores)
    Route::get('/vendas/cupons', [PdvVendaController::class, 'vendasPorCupons']);
        // Resumo de pagamentos por forma
        Route::get('/vendas/pagamentos-resumo', [PdvVendaController::class, 'pagamentosResumo']);
});

// Rota v1 para listar caixas por empresa e status
Route::get('v1/empresas/{id_empresa}/pdv/caixas/status', [\App\Http\Controllers\Api\Pdv\PdvCaixaController::class, 'porEmpresaStatus']);

// Rota v1: resumo diário de PDV por empresa
Route::get('v1/empresas/{id_empresa}/pdv/resumo/dia', [\App\Http\Controllers\Api\Pdv\PdvCaixaController::class, 'resumoDia']);

// Rota v1: listar vendas por empresa e data
Route::get('v1/empresas/{id_empresa}/pdv/vendas', [\App\Http\Controllers\Api\Pdv\PdvVendaController::class, 'vendasPorEmpresaData']);

// Rota v1: abrir caixa por empresa
Route::post('v1/empresas/{id_empresa}/pdv/caixas/abertura', [\App\Http\Controllers\Api\Pdv\PdvCaixaController::class, 'aberturaPorEmpresa']);


Route::prefix('vendasapi')->group(function() {
Route::get('/clientes',[ClienteController::class,'index']);
Route::post('/clientes',[ClienteController::class,'store']);
Route::get('/clientes/{id}',[ClienteController::class,'show']);
Route::put('/clientes/{id}',[ClienteController::class,'update']);
Route::delete('/clientes/{id}',[ClienteController::class,'destroy']);


Route::get('/assistidas',[VendaAssistidaController::class,'index']);
Route::post('/assistidas',[VendaAssistidaController::class,'store']);
Route::get('/assistidas/{id}',[VendaAssistidaController::class,'show']);
Route::put('/assistidas/{id}',[VendaAssistidaController::class,'update']);
});

Route::prefix('vendasAssistidas')->group(function() {
    Route::get('/clientes',[ClienteController::class,'index']);
    Route::post('/clientes',[ClienteController::class,'store']);
    // Listar clientes por empresa (sem paginação ou com ?per_page=25)
    Route::get('/clientes/empresa/{id_empresa}',[ClienteController::class,'listarPorEmpresa']);
    Route::get('/clientes/{id}',[ClienteController::class,'show']);
    Route::put('/clientes/{id}',[ClienteController::class,'update']);
    Route::delete('/clientes/{id}',[ClienteController::class,'destroy']);

    Route::get('/assistidas',[VendaAssistidaController::class,'index']);
    // Listar vendas assistidas por empresa (sem paginação ou com ?per_page=25)
    Route::get('/assistidas/empresa/{id_empresa}',[VendaAssistidaController::class,'listarPorEmpresa']);
    Route::post('/assistidas',[VendaAssistidaController::class,'store']);
    Route::get('/assistidas/{id}',[VendaAssistidaController::class,'show']);
    Route::put('/assistidas/{id}',[VendaAssistidaController::class,'update']);
    // Fallback for clients/servers that can't send PUT: accept POST to update as well
    Route::post('/assistidas/{id}',[VendaAssistidaController::class,'update']);
    Route::post('/assistidas/{id}/finalizar',[VendaAssistidaController::class,'finalizar']);
    Route::post('/assistidas/{id}/cancelar',[VendaAssistidaController::class,'cancelar']);

    Route::get('/itens-assistida',[ItemVendaAssistidaController::class,'index']);
    Route::post('/itens-assistida',[ItemVendaAssistidaController::class,'store']);
    Route::get('/itens-assistida/{id}',[ItemVendaAssistidaController::class,'show']);
    Route::put('/itens-assistida/{id}',[ItemVendaAssistidaController::class,'update']);
    Route::delete('/itens-assistida/{id}',[ItemVendaAssistidaController::class,'destroy']);
});

// Débitos de Clientes
Route::prefix('debitos-clientes')->group(function () {
    Route::get('/', [DebitoClienteController::class, 'index']);
    Route::get('/{id}', [DebitoClienteController::class, 'show']);
    Route::put('/{id}', [DebitoClienteController::class, 'update']);
    // Rotas adicionais para filtros específicos
    Route::get('/cliente/{id_cliente}', [DebitoClienteController::class, 'porCliente']);
    Route::get('/empresa/{id_empresa}', [DebitoClienteController::class, 'porEmpresa']);
    Route::get('/status/{status}', [DebitoClienteController::class, 'porStatus']);
});