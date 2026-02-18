<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\ContagemInventario;
use Exception;

class ContagemInventarioController extends Controller
{
    /**
     * Listar todas as contagens de inventário (com filtros opcionais).
     */
    public function index(Request $request)
    {
        try {
            $query = ContagemInventario::query()
                ->with(['empresa', 'filial', 'produto', 'usuario', 'inventario']);

            // Filtros opcionais
            if ($request->filled('id_empresa')) {
                $query->where('id_empresa', $request->id_empresa);
            }

            if ($request->filled('id_filial')) {
                $query->where('id_filial', $request->id_filial);
            }

            if ($request->filled('id_inventario')) {
                $query->where('id_inventario', $request->id_inventario);
            }

            if ($request->filled('id_produto')) {
                $query->where('id_produto', $request->id_produto);
            }

            if ($request->filled('id_usuario')) {
                $query->where('id_usuario', $request->id_usuario);
            }

            if ($request->filled('tipo_operacao')) {
                $query->where('tipo_operacao', $request->tipo_operacao);
            }

            if ($request->filled('data_inicial') && $request->filled('data_final')) {
                $query->whereBetween('data_contagem', [
                    $request->data_inicial,
                    $request->data_final
                ]);
            }

            $contagens = $query->orderBy('data_contagem', 'desc')->get();

            return response()->json($contagens, Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Erro ao listar contagens: ' . $e->getMessage());
            return response()->json(['message' => 'Erro ao listar contagens de inventário'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Criar nova contagem de inventário.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_inventario'  => 'required|exists:tb_inventario,id_inventario',
            'id_empresa'     => 'required|exists:tb_empresas,id_empresa',
            'id_filial'      => 'required|exists:tb_filiais,id_filial',
            'id_produto'     => 'required|exists:tb_produtos,id_produto',
            'tipo_operacao'  => 'required|in:Adicionar,Substituir,Excluir',
            'quantidade'     => 'required|numeric|min:0',
            'observacao'     => 'nullable|string|max:255',
            'id_usuario'     => 'required|exists:tb_usuarios,id_usuario'
        ]);

        try {
            DB::beginTransaction();

            $contagem = ContagemInventario::create($request->all());

            DB::commit();

            return response()->json($contagem, Response::HTTP_CREATED);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erro ao criar contagem: ' . $e->getMessage());
            return response()->json(['message' => 'Erro ao registrar contagem'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Exibir detalhes de uma contagem específica.
     */
    public function show($id)
    {
        try {
            $contagem = ContagemInventario::with(['empresa', 'filial', 'produto', 'usuario', 'inventario'])
                ->findOrFail($id);

            return response()->json($contagem, Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json(['message' => 'Contagem não encontrada'], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Atualizar uma contagem existente.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'id_inventario'  => 'sometimes|required|exists:tb_inventario,id_inventario',
            'id_empresa'     => 'sometimes|required|exists:tb_empresas,id_empresa',
            'id_filial'      => 'sometimes|required|exists:tb_filiais,id_filial',
            'id_produto'     => 'sometimes|required|exists:tb_produtos,id_produto',
            'tipo_operacao'  => 'sometimes|required|in:Adicionar,Substituir,Excluir',
            'quantidade'     => 'sometimes|required|numeric|min:0',
            'observacao'     => 'nullable|string|max:255',
            'id_usuario'     => 'sometimes|required|exists:tb_usuarios,id_usuario'
        ]);

        try {
            DB::beginTransaction();

            $contagem = ContagemInventario::findOrFail($id);
            $contagem->fill($request->all());
            $contagem->save();

            DB::commit();

            return response()->json($contagem, Response::HTTP_OK);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erro ao atualizar contagem: ' . $e->getMessage());
            return response()->json(['message' => 'Erro ao atualizar contagem'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Excluir uma contagem.
     */
    public function destroy($id)
    {
        try {
            $contagem = ContagemInventario::findOrFail($id);
            $contagem->delete();

            return response()->json(['message' => 'Contagem excluída com sucesso'], Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Erro ao excluir contagem: ' . $e->getMessage());
            return response()->json(['message' => 'Erro ao excluir contagem'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Listar contagens por inventário (com filtros opcionais).
     */
    public function listarPorInventario($id_inventario, Request $request)
    {
        try {
            $query = ContagemInventario::with(['produto', 'usuario'])
                ->where('id_inventario', $id_inventario);

            if ($request->filled('tipo_operacao')) {
                $query->where('tipo_operacao', $request->tipo_operacao);
            }

            if ($request->filled('id_filial')) {
                $query->where('id_filial', $request->id_filial);
            }

            $result = $query->orderBy('data_contagem', 'desc')->get();

            return response()->json($result, Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Erro ao listar contagens por inventário: ' . $e->getMessage());
            return response()->json(['message' => 'Erro ao listar contagens'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Listar produtos contados por id_inventario.
     * Retorna agregação por produto com soma de quantidades e últimos dados relevantes.
     */
    public function listarProdutosPorInventario($id_inventario, Request $request)
    {
        try {
            // Base query na tabela de contagens
            $query = DB::table('tb_contagem_inventario as c')
                ->select(
                    'c.id_produto',
                    DB::raw('SUM(c.quantidade) as quantidade_total'),
                    DB::raw('MAX(c.data_contagem) as ultima_contagem'),
                    DB::raw('COUNT(c.id_contagem) as total_registros')
                )
                ->where('c.id_inventario', $id_inventario)
                ->groupBy('c.id_produto');

            if ($request->filled('id_filial')) {
                $query->where('c.id_filial', $request->id_filial);
            }

            // Se quiser incluir dados do produto (descrição, código de barras), fazemos join com tb_produtos
            $result = $query
                ->leftJoin('tb_produtos as p', 'p.id_produto', '=', 'c.id_produto')
                ->selectRaw('c.id_produto, p.descricao, p.codigo_barras, SUM(c.quantidade) as quantidade_total, MAX(c.data_contagem) as ultima_contagem, COUNT(c.id_contagem) as total_registros')
                ->groupBy('c.id_produto', 'p.descricao', 'p.codigo_barras')
                ->orderBy('p.descricao')
                ->get();

            return response()->json($result, Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Erro ao listar produtos por inventário: ' . $e->getMessage());
            return response()->json(['message' => 'Erro ao listar produtos contados'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
