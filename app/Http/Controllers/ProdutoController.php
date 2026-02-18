<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use Illuminate\Http\Request;

class ProdutoController extends Controller
{
    /**
     * Lista todos os produtos com filtros dinâmicos.
     */
    public function index(Request $request)
    {
        $query = Produto::query();

        // Filtros dinâmicos
        if ($request->has('id_produto')) {
            $query->where('id_produto', $request->id_produto);
        }

        if ($request->has('id_empresa')) {
            $query->where('id_empresa', $request->id_empresa);
        }

        if ($request->has('id_categoria')) {
            $query->where('id_categoria', $request->id_categoria);
        }

        if ($request->has('id_secao')) {
            $query->where('id_secao', $request->id_secao);
        }

        if ($request->has('id_grupo')) {
            $query->where('id_grupo', $request->id_grupo);
        }

        if ($request->has('id_subgrupo')) {
            $query->where('id_subgrupo', $request->id_subgrupo);
        }

        if ($request->has('codigo_barras')) {
            $query->where('codigo_barras', $request->codigo_barras);
        }

        if ($request->has('descricao')) {
            $query->where('descricao', 'like', '%' . $request->descricao . '%');
        }

        // Paginação (20 por página)
        $produtos = $query->orderBy('id_produto', 'desc')->paginate(20);

        return response()->json($produtos);
    }

    /**
     * Mostra um produto específico.
     */
    public function show($id)
    {
        $produto = Produto::find($id);

        if (!$produto) {
            return response()->json(['message' => 'Produto não encontrado.'], 404);
        }

        return response()->json($produto);
    }

    /**
     * Cadastra um novo produto.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_empresa' => 'required|integer',
            'descricao' => 'required|string|max:255',
            'preco_custo' => 'nullable|numeric',
            'preco_venda' => 'nullable|numeric',
        ]);

        $produto = Produto::create($validated + $request->only([
            'id_categoria',
            'id_secao',
            'id_grupo',
            'id_subgrupo',
            'codigo_barras',
            'unidade_medida',
            'ativo'
        ]));

        return response()->json([
            'message' => 'Produto criado com sucesso.',
            'produto' => $produto
        ], 201);
    }

    /**
     * Atualiza um produto existente.
     */
    public function update(Request $request, $id)
    {
        $produto = Produto::find($id);

        if (!$produto) {
            return response()->json(['message' => 'Produto não encontrado.'], 404);
        }

        $produto->update($request->all());

        return response()->json([
            'message' => 'Produto atualizado com sucesso.',
            'produto' => $produto
        ]);
    }

    /**
     * Exclui um produto.
     */
    public function destroy($id)
    {
        $produto = Produto::find($id);

        if (!$produto) {
            return response()->json(['message' => 'Produto não encontrado.'], 404);
        }

        $produto->delete();

        return response()->json(['message' => 'Produto excluído com sucesso.']);
    }

    /**
     * Lista produtos por empresa.
     */
    public function listarPorEmpresa($id_empresa)
    {
        $produtos = Produto::where('id_empresa', $id_empresa)
            ->orderBy('id_produto', 'desc')
            ->paginate(20);

        return response()->json($produtos);
    }

    /**
     * Lista produtos por categoria.
     */
    public function listarPorCategoria($id_empresa, $id_categoria)
    {
        $produtos = Produto::where('id_empresa', $id_empresa)
            ->where('id_categoria', $id_categoria)
            ->paginate(20);
        return response()->json($produtos);
    }

    /**
     * Lista produtos por seção.
     */
    public function listarPorSecao($id_empresa, $id_secao)
    {
        $produtos = Produto::where('id_empresa', $id_empresa)
            ->where('id_secao', $id_secao)
            ->paginate(20);
        return response()->json($produtos);
    }

    /**
     * Lista produtos por grupo.
     */
    public function listarPorGrupo($id_empresa, $id_grupo)
    {
        $produtos = Produto::where('id_empresa', $id_empresa)
            ->where('id_grupo', $id_grupo)
            ->paginate(20);
        return response()->json($produtos);
    }

    /**
     * Lista produtos por subgrupo.
     */
    public function listarPorSubgrupo($id_empresa, $id_subgrupo)
    {
        $produtos = Produto::where('id_empresa', $id_empresa)
            ->where('id_subgrupo', $id_subgrupo)
            ->paginate(20);
        return response()->json($produtos);
    }
}
