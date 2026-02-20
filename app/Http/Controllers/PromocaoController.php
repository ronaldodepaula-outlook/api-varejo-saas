<?php

namespace App\Http\Controllers;

use App\Models\Filial;
use App\Models\PrecoVigente;
use App\Models\Produto;
use App\Models\PromocaoCabecalho;
use App\Models\PromocaoProduto;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PromocaoController extends Controller
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

    private function aplicarPromocaoPrecoVigente(PromocaoCabecalho $promocao, PromocaoProduto $produtoPromocao, Produto $produto): void
    {
        $filiais = $promocao->aplicar_em_todas_filiais
            ? Filial::where('id_empresa', $promocao->id_empresa)->pluck('id_filial')->all()
            : [$promocao->id_filial];

        $precoBase = $produtoPromocao->preco_normal ?? $produto->preco_venda ?? 0;

        foreach ($filiais as $idFilial) {
            PrecoVigente::updateOrCreate(
                [
                    'id_empresa' => $promocao->id_empresa,
                    'id_filial' => $idFilial,
                    'id_produto' => $produto->id_produto,
                ],
                [
                    'preco_base' => $precoBase,
                    'preco_promocional' => $produtoPromocao->preco_promocional,
                    'preco_atual' => $produtoPromocao->preco_promocional,
                    'em_promocao' => 1,
                    'id_promocao_ativa' => $promocao->id_promocao,
                    'data_inicio_promocao' => $promocao->data_inicio,
                    'data_fim_promocao' => $promocao->data_fim,
                ]
            );
        }
    }

    private function removerPromocaoPrecoVigente(PromocaoCabecalho $promocao, PromocaoProduto $produtoPromocao): void
    {
        $query = PrecoVigente::where('id_promocao_ativa', $promocao->id_promocao)
            ->where('id_empresa', $promocao->id_empresa)
            ->where('id_produto', $produtoPromocao->id_produto);

        if (!$promocao->aplicar_em_todas_filiais) {
            $query->where('id_filial', $promocao->id_filial);
        }

        $query->update([
            'em_promocao' => 0,
            'preco_atual' => DB::raw('preco_base'),
            'preco_promocional' => null,
            'id_promocao_ativa' => null,
            'data_inicio_promocao' => null,
            'data_fim_promocao' => null,
        ]);
    }

    public function index(Request $request)
    {
        $empresaId = $this->resolveEmpresaId($request);

        $query = PromocaoCabecalho::with(['produtos.produto', 'usuarioCriador'])
            ->where('id_empresa', $empresaId);

        if ($request->filled('id_filial')) {
            $query->where('id_filial', $request->get('id_filial'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }
        if ($request->filled('tipo_promocao')) {
            $query->where('tipo_promocao', $request->get('tipo_promocao'));
        }
        if ($request->filled('data_inicio')) {
            $query->where('data_inicio', '>=', $request->get('data_inicio'));
        }
        if ($request->filled('data_fim')) {
            $query->where('data_fim', '<=', $request->get('data_fim'));
        }

        return response()->json($query->orderBy('data_inicio', 'desc')->paginate(50));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_empresa' => 'nullable|integer|exists:tb_empresas,id_empresa',
            'id_filial' => 'required|integer|exists:tb_filiais,id_filial',
            'codigo_promocao' => 'required|string|max:50|unique:tb_promocoes_cabecalho,codigo_promocao',
            'nome_promocao' => 'required|string|max:150',
            'descricao' => 'nullable|string',
            'tipo_promocao' => 'required|in:desconto_percentual,desconto_fixo,leve_pague,combo,tabloid',
            'data_inicio' => 'required|date',
            'data_fim' => 'required|date|after_or_equal:data_inicio',
            'status' => 'nullable|in:rascunho,ativa,pausada,encerrada,cancelada',
            'prioridade' => 'nullable|integer',
            'aplicar_em_todas_filiais' => 'nullable|boolean',
            'observacoes' => 'nullable|string',
            'id_usuario_criador' => 'nullable|integer|exists:tb_usuarios,id_usuario',
        ]);

        $data['id_empresa'] = $this->resolveEmpresaId($request);
        $data['id_usuario_criador'] = $data['id_usuario_criador'] ?? (auth()->user()->id_usuario ?? null);

        $registro = PromocaoCabecalho::create($data);

        return response()->json($registro, Response::HTTP_CREATED);
    }

    public function show(Request $request, $id)
    {
        $empresaId = $this->resolveEmpresaId($request);
        $registro = PromocaoCabecalho::with(['produtos.produto', 'usuarioCriador'])
            ->where('id_empresa', $empresaId)
            ->findOrFail($id);

        return response()->json($registro);
    }

    public function update(Request $request, $id)
    {
        $empresaId = $this->resolveEmpresaId($request);
        $registro = PromocaoCabecalho::where('id_empresa', $empresaId)->findOrFail($id);

        $data = $request->validate([
            'id_filial' => 'sometimes|required|integer|exists:tb_filiais,id_filial',
            'codigo_promocao' => 'sometimes|required|string|max:50|unique:tb_promocoes_cabecalho,codigo_promocao,' . $registro->id_promocao . ',id_promocao',
            'nome_promocao' => 'sometimes|required|string|max:150',
            'descricao' => 'nullable|string',
            'tipo_promocao' => 'sometimes|required|in:desconto_percentual,desconto_fixo,leve_pague,combo,tabloid',
            'data_inicio' => 'sometimes|required|date',
            'data_fim' => 'sometimes|required|date|after_or_equal:data_inicio',
            'status' => 'nullable|in:rascunho,ativa,pausada,encerrada,cancelada',
            'prioridade' => 'nullable|integer',
            'aplicar_em_todas_filiais' => 'nullable|boolean',
            'observacoes' => 'nullable|string',
        ]);

        $registro->update($data);

        return response()->json($registro);
    }

    public function destroy(Request $request, $id)
    {
        $empresaId = $this->resolveEmpresaId($request);
        $registro = PromocaoCabecalho::where('id_empresa', $empresaId)->findOrFail($id);

        if ($registro->status === 'ativa') {
            $registro->status = 'encerrada';
            $registro->save();
        }

        $registro->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function storeProduto(Request $request, $id_promocao)
    {
        $empresaId = $this->resolveEmpresaId($request);
        $promocao = PromocaoCabecalho::where('id_empresa', $empresaId)->findOrFail($id_promocao);

        $data = $request->validate([
            'id_produto' => 'required|integer|exists:tb_produtos,id_produto',
            'preco_normal' => 'nullable|numeric|min:0',
            'preco_promocional' => 'required|numeric|min:0',
            'quantidade_minima' => 'nullable|integer|min:1',
            'quantidade_pague' => 'nullable|integer|min:1',
            'observacao' => 'nullable|string|max:255',
        ]);

        if ($promocao->tipo_promocao === 'leve_pague' && empty($data['quantidade_pague'])) {
            throw ValidationException::withMessages([
                'quantidade_pague' => ['quantidade_pague e obrigatorio para leve_pague.'],
            ]);
        }

        $produto = Produto::findOrFail($data['id_produto']);
        $data['preco_normal'] = $data['preco_normal'] ?? $produto->preco_venda ?? 0;
        $data['id_promocao'] = $promocao->id_promocao;

        $item = PromocaoProduto::create($data);

        if ($promocao->status === 'ativa') {
            $this->aplicarPromocaoPrecoVigente($promocao, $item, $produto);
        }

        return response()->json($item, Response::HTTP_CREATED);
    }

    public function updateProduto(Request $request, $id_promocao, $id_promocao_produto)
    {
        $empresaId = $this->resolveEmpresaId($request);
        $promocao = PromocaoCabecalho::where('id_empresa', $empresaId)->findOrFail($id_promocao);

        $item = PromocaoProduto::where('id_promocao', $promocao->id_promocao)->findOrFail($id_promocao_produto);

        $data = $request->validate([
            'preco_normal' => 'nullable|numeric|min:0',
            'preco_promocional' => 'nullable|numeric|min:0',
            'quantidade_minima' => 'nullable|integer|min:1',
            'quantidade_pague' => 'nullable|integer|min:1',
            'observacao' => 'nullable|string|max:255',
        ]);

        $item->update($data);

        if ($promocao->status === 'ativa') {
            $produto = Produto::findOrFail($item->id_produto);
            $this->aplicarPromocaoPrecoVigente($promocao, $item, $produto);
        }

        return response()->json($item);
    }

    public function destroyProduto(Request $request, $id_promocao, $id_promocao_produto)
    {
        $empresaId = $this->resolveEmpresaId($request);
        $promocao = PromocaoCabecalho::where('id_empresa', $empresaId)->findOrFail($id_promocao);

        $item = PromocaoProduto::where('id_promocao', $promocao->id_promocao)->findOrFail($id_promocao_produto);

        if ($promocao->status === 'ativa') {
            $this->removerPromocaoPrecoVigente($promocao, $item);
        }

        $item->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function ativar(Request $request, $id_promocao)
    {
        $empresaId = $this->resolveEmpresaId($request);
        $promocao = PromocaoCabecalho::where('id_empresa', $empresaId)->findOrFail($id_promocao);
        $promocao->status = 'ativa';
        $promocao->save();

        return response()->json($promocao);
    }

    public function pausar(Request $request, $id_promocao)
    {
        $empresaId = $this->resolveEmpresaId($request);
        $promocao = PromocaoCabecalho::where('id_empresa', $empresaId)->findOrFail($id_promocao);
        $promocao->status = 'pausada';
        $promocao->save();

        return response()->json($promocao);
    }

    public function encerrar(Request $request, $id_promocao)
    {
        $empresaId = $this->resolveEmpresaId($request);
        $promocao = PromocaoCabecalho::where('id_empresa', $empresaId)->findOrFail($id_promocao);
        $promocao->status = 'encerrada';
        $promocao->save();

        return response()->json($promocao);
    }
}
