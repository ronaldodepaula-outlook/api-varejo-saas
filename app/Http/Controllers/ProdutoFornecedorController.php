<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\ProdutoFornecedor;
use App\Models\Produto;
use App\Models\Fornecedor;

class ProdutoFornecedorController extends Controller
{
    // Paginated index, filters by id_empresa (auth or query)
    public function index(Request $request)
    {
        $empresaId = auth()->user()->id_empresa ?? $request->get('id_empresa');

        $query = DB::table('tb_protuto_fornecedor as pf')
            ->join('tb_fornecedores as f', 'pf.id_fornecedor', '=', 'f.id_fornecedor')
            ->join('tb_produtos as p', 'pf.id_produto', '=', 'p.id_produto')
            ->select('pf.*', 'f.razao_social as fornecedor_razao_social', 'p.descricao as produto_descricao');

        if ($empresaId) {
            $query->where(function ($q) use ($empresaId) {
                $q->where('f.id_empresa', $empresaId)
                  ->orWhere('p.id_empresa', $empresaId);
            });
        }

        $results = $query->orderBy('f.razao_social')->paginate(20);

        return response()->json($results);
    }

    // List all relations for an empresa
    public function listarPorEmpresa($id_empresa)
    {
        $rows = DB::table('tb_protuto_fornecedor as pf')
            ->join('tb_fornecedores as f', 'pf.id_fornecedor', '=', 'f.id_fornecedor')
            ->join('tb_produtos as p', 'pf.id_produto', '=', 'p.id_produto')
            ->where(function ($q) use ($id_empresa) {
                $q->where('f.id_empresa', $id_empresa)
                  ->orWhere('p.id_empresa', $id_empresa);
            })
            ->select('pf.*', 'f.razao_social as fornecedor_razao_social', 'p.descricao as produto_descricao')
            ->orderBy('f.razao_social')
            ->get();

        return response()->json($rows);
    }

    // Show a specific relation by composite key
    public function show($id_fornecedor, $id_produto)
    {
        $row = DB::table('tb_protuto_fornecedor')
            ->where('id_fornecedor', $id_fornecedor)
            ->where('id_produto', $id_produto)
            ->first();

        if (!$row) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        return response()->json($row);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_fornecedor' => 'required|integer|exists:tb_fornecedores,id_fornecedor',
            'id_produto' => 'required|integer|exists:tb_produtos,id_produto',
            'status' => 'nullable|string|max:50',
        ]);

        // Business rule: fornecedor and produto should belong to same empresa
        $fornecedor = Fornecedor::find($data['id_fornecedor']);
        $produto = Produto::find($data['id_produto']);

        if (!$fornecedor || !$produto) {
            return response()->json(['message' => 'Fornecedor or Produto not found'], 404);
        }

        if ($fornecedor->id_empresa != $produto->id_empresa) {
            return response()->json([
                'message' => 'Fornecedor and Produto must belong to the same empresa'
            ], 422);
        }

        $exists = DB::table('tb_protuto_fornecedor')
            ->where('id_fornecedor', $data['id_fornecedor'])
            ->where('id_produto', $data['id_produto'])
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Relation already exists'], 409);
        }

        $insertData = [
            'id_fornecedor' => $data['id_fornecedor'],
            'id_produto' => $data['id_produto'],
        ];

        if (isset($data['status']) && Schema::hasColumn('tb_protuto_fornecedor', 'status')) {
            $insertData['status'] = $data['status'];
        }

        DB::table('tb_protuto_fornecedor')->insert($insertData);

        return response()->json(['success' => true, 'data' => $insertData], 201);
    }

    public function update(Request $request, $id_fornecedor, $id_produto)
    {
        $data = $request->validate([
            'status' => 'nullable|string|max:50',
        ]);

        $updateData = [];
        if (isset($data['status']) && Schema::hasColumn('tb_protuto_fornecedor', 'status')) {
            $updateData['status'] = $data['status'];
        }

        if (empty($updateData)) {
            return response()->json(['message' => 'Nothing to update or column not present'], 400);
        }

        $updated = DB::table('tb_protuto_fornecedor')
            ->where('id_fornecedor', $id_fornecedor)
            ->where('id_produto', $id_produto)
            ->update($updateData);

        if (!$updated) {
            return response()->json(['message' => 'Not Found or nothing changed'], 404);
        }

        $row = DB::table('tb_protuto_fornecedor')
            ->where('id_fornecedor', $id_fornecedor)
            ->where('id_produto', $id_produto)
            ->first();

        return response()->json(['success' => true, 'data' => $row]);
    }

    public function destroy($id_fornecedor, $id_produto)
    {
        $deleted = DB::table('tb_protuto_fornecedor')
            ->where('id_fornecedor', $id_fornecedor)
            ->where('id_produto', $id_produto)
            ->delete();

        if (!$deleted) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        return response()->json(['success' => true]);
    }
}
