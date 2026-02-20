<?php

namespace App\Http\Controllers;

use App\Models\AtualizacaoPrecoCabecalho;
use App\Models\AtualizacaoPrecoItem;
use App\Models\Produto;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AtualizacaoPrecoController extends Controller
{
    private function resolveEmpresaId(Request $request): int
    {
        $empresaId = auth()->user()->id_empresa ?? $request->get('id_empresa');
        if (!$empresaId) {
            throw ValidationException::withMessages([
                'id_empresa' => ['id_empresa e obrigatorio quando nao ha usuario autenticado.'],
            ]);
        }
        return (int) $empresaId;
    }

    private function gerarNumeroLote(): string
    {
        return 'LOTE-' . now()->format('Ymd-His') . '-' . Str::upper(Str::random(4));
    }

    public function index(Request $request)
    {
        $empresaId = $this->resolveEmpresaId($request);

        $query = AtualizacaoPrecoCabecalho::with(['fornecedor', 'usuarioCriador'])
            ->where('id_empresa', $empresaId);

        if ($request->filled('id_filial')) {
            $query->where('id_filial', $request->get('id_filial'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }
        if ($request->filled('tipo_atualizacao')) {
            $query->where('tipo_atualizacao', $request->get('tipo_atualizacao'));
        }
        if ($request->filled('id_fornecedor')) {
            $query->where('id_fornecedor', $request->get('id_fornecedor'));
        }
        if ($request->filled('data_inicio')) {
            $query->where('data_atualizacao', '>=', $request->get('data_inicio'));
        }
        if ($request->filled('data_fim')) {
            $query->where('data_atualizacao', '<=', $request->get('data_fim'));
        }

        return response()->json($query->orderBy('data_atualizacao', 'desc')->paginate(50));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_empresa' => 'nullable|integer|exists:tb_empresas,id_empresa',
            'id_filial' => 'required|integer|exists:tb_filiais,id_filial',
            'numero_lote' => 'nullable|string|max:50|unique:tb_atualizacao_precos_cabecalho,numero_lote',
            'descricao' => 'nullable|string|max:255',
            'tipo_atualizacao' => 'required|in:custo_fornecedor,custo_avulso,venda_geral,promocao',
            'id_fornecedor' => 'nullable|integer|exists:tb_fornecedores,id_fornecedor',
            'data_atualizacao' => 'required|date',
            'status' => 'nullable|in:rascunho,processado,cancelado',
            'observacoes' => 'nullable|string',
            'id_usuario_criador' => 'nullable|integer|exists:tb_usuarios,id_usuario',
        ]);

        $data['id_empresa'] = $this->resolveEmpresaId($request);
        $data['numero_lote'] = $data['numero_lote'] ?? $this->gerarNumeroLote();
        $data['id_usuario_criador'] = $data['id_usuario_criador'] ?? (auth()->user()->id_usuario ?? null);

        $registro = AtualizacaoPrecoCabecalho::create($data);

        return response()->json($registro, Response::HTTP_CREATED);
    }

    public function show(Request $request, $id)
    {
        $empresaId = $this->resolveEmpresaId($request);
        $registro = AtualizacaoPrecoCabecalho::with(['itens.produto', 'fornecedor', 'usuarioCriador'])
            ->where('id_empresa', $empresaId)
            ->findOrFail($id);

        return response()->json($registro);
    }

    public function update(Request $request, $id)
    {
        $empresaId = $this->resolveEmpresaId($request);
        $registro = AtualizacaoPrecoCabecalho::where('id_empresa', $empresaId)->findOrFail($id);

        $data = $request->validate([
            'id_filial' => 'sometimes|required|integer|exists:tb_filiais,id_filial',
            'numero_lote' => 'sometimes|required|string|max:50|unique:tb_atualizacao_precos_cabecalho,numero_lote,' . $registro->id_atualizacao . ',id_atualizacao',
            'descricao' => 'nullable|string|max:255',
            'tipo_atualizacao' => 'sometimes|required|in:custo_fornecedor,custo_avulso,venda_geral,promocao',
            'id_fornecedor' => 'nullable|integer|exists:tb_fornecedores,id_fornecedor',
            'data_atualizacao' => 'sometimes|required|date',
            'status' => 'nullable|in:rascunho,processado,cancelado',
            'observacoes' => 'nullable|string',
        ]);

        if (isset($data['status']) && $data['status'] === 'processado' && $registro->status !== 'rascunho') {
            throw ValidationException::withMessages([
                'status' => ['Somente registros em rascunho podem ser processados.'],
            ]);
        }

        if (isset($data['status']) && $data['status'] === 'processado') {
            $data['processed_at'] = now();
        }

        $registro->update($data);

        return response()->json($registro);
    }

    public function destroy(Request $request, $id)
    {
        $empresaId = $this->resolveEmpresaId($request);
        $registro = AtualizacaoPrecoCabecalho::where('id_empresa', $empresaId)->findOrFail($id);

        AtualizacaoPrecoItem::where('id_atualizacao', $registro->id_atualizacao)->delete();
        $registro->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function storeItem(Request $request, $id_atualizacao)
    {
        $empresaId = $this->resolveEmpresaId($request);
        $cabecalho = AtualizacaoPrecoCabecalho::where('id_empresa', $empresaId)->findOrFail($id_atualizacao);

        $data = $request->validate([
            'id_produto' => 'required|integer|exists:tb_produtos,id_produto',
            'preco_custo_anterior' => 'nullable|numeric|min:0',
            'preco_custo_novo' => 'nullable|numeric|min:0',
            'preco_venda_anterior' => 'nullable|numeric|min:0',
            'preco_venda_novo' => 'nullable|numeric|min:0',
            'status' => 'nullable|in:pendente,processado,erro',
            'observacao' => 'nullable|string|max:255',
        ]);

        if (empty($data['preco_custo_novo']) && empty($data['preco_venda_novo'])) {
            throw ValidationException::withMessages([
                'preco_custo_novo' => ['Informe preco_custo_novo ou preco_venda_novo.'],
                'preco_venda_novo' => ['Informe preco_custo_novo ou preco_venda_novo.'],
            ]);
        }

        $produto = Produto::findOrFail($data['id_produto']);
        $data['preco_custo_anterior'] = $data['preco_custo_anterior'] ?? $produto->preco_custo;
        $data['preco_venda_anterior'] = $data['preco_venda_anterior'] ?? $produto->preco_venda;
        $data['id_atualizacao'] = $cabecalho->id_atualizacao;

        $item = AtualizacaoPrecoItem::create($data);

        return response()->json($item, Response::HTTP_CREATED);
    }

    public function updateItem(Request $request, $id_atualizacao, $id_item)
    {
        $empresaId = $this->resolveEmpresaId($request);
        $cabecalho = AtualizacaoPrecoCabecalho::where('id_empresa', $empresaId)->findOrFail($id_atualizacao);

        $item = AtualizacaoPrecoItem::where('id_atualizacao', $cabecalho->id_atualizacao)->findOrFail($id_item);

        $data = $request->validate([
            'preco_custo_anterior' => 'nullable|numeric|min:0',
            'preco_custo_novo' => 'nullable|numeric|min:0',
            'preco_venda_anterior' => 'nullable|numeric|min:0',
            'preco_venda_novo' => 'nullable|numeric|min:0',
            'status' => 'nullable|in:pendente,processado,erro',
            'observacao' => 'nullable|string|max:255',
        ]);

        $item->update($data);

        return response()->json($item);
    }

    public function destroyItem(Request $request, $id_atualizacao, $id_item)
    {
        $empresaId = $this->resolveEmpresaId($request);
        $cabecalho = AtualizacaoPrecoCabecalho::where('id_empresa', $empresaId)->findOrFail($id_atualizacao);
        $item = AtualizacaoPrecoItem::where('id_atualizacao', $cabecalho->id_atualizacao)->findOrFail($id_item);
        $item->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function processar(Request $request, $id_atualizacao)
    {
        $empresaId = $this->resolveEmpresaId($request);
        $registro = AtualizacaoPrecoCabecalho::where('id_empresa', $empresaId)->findOrFail($id_atualizacao);

        if ($registro->status !== 'rascunho') {
            throw ValidationException::withMessages([
                'status' => ['Somente registros em rascunho podem ser processados.'],
            ]);
        }

        $registro->status = 'processado';
        $registro->processed_at = now();
        $registro->save();

        return response()->json($registro);
    }

    public function cancelar(Request $request, $id_atualizacao)
    {
        $empresaId = $this->resolveEmpresaId($request);
        $registro = AtualizacaoPrecoCabecalho::where('id_empresa', $empresaId)->findOrFail($id_atualizacao);

        if ($registro->status === 'processado') {
            throw ValidationException::withMessages([
                'status' => ['Registros processados nao podem ser cancelados.'],
            ]);
        }

        $registro->status = 'cancelado';
        $registro->save();

        return response()->json($registro);
    }
}
