<?php
namespace App\Http\Controllers\Vendas;
use App\Http\Controllers\Controller;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class ClienteController extends Controller {
    public function index(Request $request) {
        $empresa = $request->header('X-Empresa-Id');
        $query = Cliente::where('flag_excluido_logico',0);
        if ($empresa) $query->where('id_empresa',$empresa);
        return response()->json($query->paginate(25));
    }

    /**
     * Listar todos os clientes de uma determinada empresa.
     * - Se query string `per_page` for fornecida, retorna paginação.
     * - Caso contrário, retorna a lista completa (sem soft-deleted).
     * Rota esperada: GET /vendasAssistidas/clientes/empresa/{id_empresa}
     */
    public function listarPorEmpresa(Request $request, $id_empresa)
    {
        $empresaId = (int) $id_empresa;

        // Verifica se a empresa existe na base (tabela padrão tb_empresas)
        $empresaExists = DB::table('tb_empresas')->where('id_empresa', $empresaId)->exists();
        if (! $empresaExists) {
            return response()->json(['message' => 'Empresa não encontrada'], 404);
        }

        $query = Cliente::where('flag_excluido_logico', 0)
            ->where('id_empresa', $empresaId)
            ->orderBy('nome_cliente');

        $perPage = $request->query('per_page');
        if ($perPage && is_numeric($perPage) && (int)$perPage > 0) {
            return response()->json($query->paginate((int) $perPage));
        }

        $clientes = $query->get();
        return response()->json($clientes);
    }

    public function store(Request $request)
    {
        try {
            Log::info('Iniciando criação de cliente');
            Log::debug('Request data:', $request->all());

            $validated = $request->validate([
                'id_empresa' => 'required|integer|exists:tb_empresas,id_empresa',
                'id_usuario' => 'required|integer|exists:tb_usuarios,id_usuario',
                'nome_cliente' => 'required|string|max:150',
                'email' => 'nullable|email|max:150',
                'telefone' => 'nullable|string|max:20',
                'whatsapp' => 'nullable|string|max:20',
                'endereco' => 'nullable|string|max:255',
                'cidade' => 'nullable|string|max:100',
                'estado' => 'nullable|string|max:2',
                'cep' => 'nullable|string|max:10',
                'document_type' => ['nullable', Rule::in(Cliente::DOCUMENT_TYPES)],
                'consent_marketing' => ['nullable', Rule::in(Cliente::CONSENT_OPTIONS)],
                'classificacao' => ['nullable', Rule::in(Cliente::CLASSIFICACOES)],
                'status' => ['nullable', Rule::in(Cliente::STATUS_OPTIONS)],
                'data_reter_ate' => 'nullable|date',
            ]);

            DB::beginTransaction();

            // ✅ Sempre converta explicitamente para inteiro (resolve o NULL)
            $idEmpresa = (int) ($request->header('X-Empresa-Id') ?? $validated['id_empresa']);
            $idUsuario = (int) $validated['id_usuario'];

            $dadosCliente = [
                'id_empresa'          => $idEmpresa,
                'id_usuario'          => $idUsuario,
                'nome_cliente'        => $validated['nome_cliente'],
                'email'               => $validated['email'] ?? null,
                'telefone'            => $validated['telefone'] ?? null,
                'whatsapp'            => $validated['whatsapp'] ?? null,
                'endereco'            => $validated['endereco'] ?? null,
                'cidade'              => $validated['cidade'] ?? null,
                'estado'              => $validated['estado'] ?? null,
                'cep'                 => $validated['cep'] ?? null,
                'document_type'       => $validated['document_type'] ?? null,
                'consent_marketing'   => $validated['consent_marketing'] ?? 'nao',
                'classificacao'       => $validated['classificacao'] ?? 'bronze',
                'status'              => $validated['status'] ?? 'ativo',
                'flag_excluido_logico'=> 0
            ];

            Log::debug('Dados preparados para inserção:', $dadosCliente);

            $cliente = Cliente::create($dadosCliente);

            DB::table('tb_clientes_audit')->insert([
                'id_empresa' => $cliente->id_empresa,
                'id_cliente' => $cliente->id_cliente,
                'id_usuario' => $cliente->id_usuario,
                'acao' => 'CREATE',
                'campos_afetados' => json_encode($dadosCliente),
                'timestamp' => now()
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Cliente criado com sucesso',
                'data' => $cliente
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao criar cliente: ' . $e->getMessage());
            return response()->json([
                'message' => 'Erro ao criar cliente',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function show($id) {
        $c = Cliente::findOrFail($id);
        return response()->json($c);
    }

    public function update(Request $r, $id) {
        $c = Cliente::findOrFail($id);
        
        // Validação dos campos na atualização
        $r->validate([
            'id_usuario' => 'required|exists:tb_usuarios,id_usuario',
            'nome_cliente' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'telefone' => 'nullable|string|max:20',
            'whatsapp' => 'nullable|string|max:20'
        ]);

        $c->update($r->all());
        
        DB::table('tb_clientes_audit')->insert([
            'id_empresa' => $c->id_empresa,
            'id_cliente' => $c->id_cliente,
            'id_usuario' => $r->id_usuario,
            'acao' => 'UPDATE',
            'campos_afetados' => 'update',
            'timestamp' => now()
        ]);
        return response()->json($c);
    }

    public function destroy(Request $request, $id) {
        // Validar id_usuario
        $request->validate([
            'id_usuario' => 'required|exists:tb_usuarios,id_usuario'
        ]);

        // pseudonimizar via procedure style: mark logical deleted and null PII
        $c = Cliente::findOrFail($id);
        $c->nome_cliente = 'EXCLUIDO_'.$c->id_cliente.'_'.date('Ymd');
        $c->email = null; $c->telefone = null; $c->whatsapp = null; $c->endereco=null;
        $c->cpf_hash = null; $c->cpf_encrypted = null; $c->document_number_encrypted = null;
        $c->flag_excluido_logico = 1; $c->data_exclusao_solicitada = now();
        $c->save();
        
        DB::table('tb_clientes_audit')->insert([
            'id_empresa' => $c->id_empresa,
            'id_cliente' => $c->id_cliente,
            'id_usuario' => $request->id_usuario,
            'acao' => 'DELETE',
            'campos_afetados' => 'pseudonimizacao',
            'timestamp' => now()
        ]);
        return response()->json(['deleted'=>true]);
    }
}
