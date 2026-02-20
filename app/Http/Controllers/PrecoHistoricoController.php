<?php

namespace App\Http\Controllers;

use App\Models\PrecoHistorico;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class PrecoHistoricoController extends Controller
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

    public function index(Request $request)
    {
        $empresaId = $this->resolveEmpresaId($request);

        $query = PrecoHistorico::with(['produto', 'fornecedor', 'usuario'])
            ->where('id_empresa', $empresaId);

        if ($request->filled('id_produto')) {
            $query->where('id_produto', $request->get('id_produto'));
        }
        if ($request->filled('tipo_alteracao')) {
            $query->where('tipo_alteracao', $request->get('tipo_alteracao'));
        }
        if ($request->filled('data_inicio')) {
            $query->where('data_alteracao', '>=', $request->get('data_inicio'));
        }
        if ($request->filled('data_fim')) {
            $query->where('data_alteracao', '<=', $request->get('data_fim'));
        }

        return response()->json($query->orderBy('data_alteracao', 'desc')->paginate(50));
    }

    public function show(Request $request, $id)
    {
        $empresaId = $this->resolveEmpresaId($request);
        $registro = PrecoHistorico::with(['produto', 'fornecedor', 'usuario'])
            ->where('id_empresa', $empresaId)
            ->findOrFail($id);

        return response()->json($registro);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_empresa' => 'nullable|integer|exists:tb_empresas,id_empresa',
            'id_produto' => 'required|integer|exists:tb_produtos,id_produto',
            'tipo_alteracao' => 'required|in:custo,venda,promocao',
            'preco_anterior' => 'required|numeric',
            'preco_novo' => 'required|numeric',
            'motivo' => 'nullable|string|max:255',
            'id_fornecedor' => 'nullable|integer|exists:tb_fornecedores,id_fornecedor',
            'id_usuario' => 'nullable|integer|exists:tb_usuarios,id_usuario',
            'ip_origem' => 'nullable|string|max:45',
            'data_alteracao' => 'nullable|date',
        ]);

        $data['id_empresa'] = $this->resolveEmpresaId($request);
        $data['id_usuario'] = $data['id_usuario'] ?? (auth()->user()->id_usuario ?? null);
        $data['ip_origem'] = $data['ip_origem'] ?? $request->ip();

        $registro = PrecoHistorico::create($data);

        return response()->json($registro, Response::HTTP_CREATED);
    }

    public function update(Request $request, $id)
    {
        $empresaId = $this->resolveEmpresaId($request);
        $registro = PrecoHistorico::where('id_empresa', $empresaId)->findOrFail($id);

        $data = $request->validate([
            'tipo_alteracao' => 'sometimes|required|in:custo,venda,promocao',
            'preco_anterior' => 'sometimes|required|numeric',
            'preco_novo' => 'sometimes|required|numeric',
            'motivo' => 'nullable|string|max:255',
            'id_fornecedor' => 'nullable|integer|exists:tb_fornecedores,id_fornecedor',
            'id_usuario' => 'nullable|integer|exists:tb_usuarios,id_usuario',
            'ip_origem' => 'nullable|string|max:45',
            'data_alteracao' => 'nullable|date',
        ]);

        $registro->update($data);

        return response()->json($registro);
    }

    public function destroy(Request $request, $id)
    {
        $empresaId = $this->resolveEmpresaId($request);
        $registro = PrecoHistorico::where('id_empresa', $empresaId)->findOrFail($id);
        $registro->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
