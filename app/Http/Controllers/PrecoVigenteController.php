<?php

namespace App\Http\Controllers;

use App\Models\PrecoVigente;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class PrecoVigenteController extends Controller
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

        $query = PrecoVigente::with(['empresa', 'filial', 'produto', 'promocaoAtiva'])
            ->where('id_empresa', $empresaId);

        if ($request->filled('id_filial')) {
            $query->where('id_filial', $request->get('id_filial'));
        }
        if ($request->filled('id_produto')) {
            $query->where('id_produto', $request->get('id_produto'));
        }
        if ($request->filled('em_promocao')) {
            $query->where('em_promocao', (int) $request->get('em_promocao'));
        }

        return response()->json($query->orderBy('id_produto')->paginate(50));
    }

    public function show(Request $request, $id)
    {
        $empresaId = $this->resolveEmpresaId($request);
        $registro = PrecoVigente::with(['empresa', 'filial', 'produto', 'promocaoAtiva'])
            ->where('id_empresa', $empresaId)
            ->findOrFail($id);

        return response()->json($registro);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_empresa' => 'nullable|integer|exists:tb_empresas,id_empresa',
            'id_filial' => 'required|integer|exists:tb_filiais,id_filial',
            'id_produto' => 'required|integer|exists:tb_produtos,id_produto',
            'id_promocao_ativa' => 'nullable|integer|exists:tb_promocoes_cabecalho,id_promocao',
            'preco_base' => 'required|numeric|min:0',
            'preco_atual' => 'nullable|numeric|min:0',
            'preco_promocional' => 'nullable|numeric|min:0',
            'em_promocao' => 'nullable|boolean',
            'data_inicio_promocao' => 'nullable|date',
            'data_fim_promocao' => 'nullable|date',
        ]);

        $data['id_empresa'] = $this->resolveEmpresaId($request);
        $data['preco_atual'] = $data['preco_atual'] ?? $data['preco_base'];

        $registro = PrecoVigente::updateOrCreate(
            [
                'id_empresa' => $data['id_empresa'],
                'id_filial' => $data['id_filial'],
                'id_produto' => $data['id_produto'],
            ],
            $data
        );

        return response()->json($registro, Response::HTTP_CREATED);
    }

    public function update(Request $request, $id)
    {
        $empresaId = $this->resolveEmpresaId($request);
        $registro = PrecoVigente::where('id_empresa', $empresaId)->findOrFail($id);

        $data = $request->validate([
            'id_filial' => 'sometimes|required|integer|exists:tb_filiais,id_filial',
            'id_produto' => 'sometimes|required|integer|exists:tb_produtos,id_produto',
            'id_promocao_ativa' => 'nullable|integer|exists:tb_promocoes_cabecalho,id_promocao',
            'preco_base' => 'sometimes|required|numeric|min:0',
            'preco_atual' => 'nullable|numeric|min:0',
            'preco_promocional' => 'nullable|numeric|min:0',
            'em_promocao' => 'nullable|boolean',
            'data_inicio_promocao' => 'nullable|date',
            'data_fim_promocao' => 'nullable|date',
        ]);

        $registro->update($data);

        return response()->json($registro);
    }

    public function destroy(Request $request, $id)
    {
        $empresaId = $this->resolveEmpresaId($request);
        $registro = PrecoVigente::where('id_empresa', $empresaId)->findOrFail($id);
        $registro->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function porEmpresaFilialProduto(Request $request, $id_empresa, $id_filial, $id_produto)
    {
        $registro = PrecoVigente::with(['empresa', 'filial', 'produto', 'promocaoAtiva'])
            ->where('id_empresa', $id_empresa)
            ->where('id_filial', $id_filial)
            ->where('id_produto', $id_produto)
            ->firstOrFail();

        return response()->json($registro);
    }
}
