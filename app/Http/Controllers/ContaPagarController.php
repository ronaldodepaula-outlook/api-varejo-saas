<?php

namespace App\Http\Controllers;

use App\Models\ContaPagar;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class ContaPagarController extends Controller
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

    private function aplicarStatus(array $data): array
    {
        $status = $data['status'] ?? null;
        if ($status === 'cancelada' || $status === 'atrasada') {
            return $data;
        }

        $valorTotal = (float) ($data['valor_total'] ?? 0);
        $valorPago = (float) ($data['valor_pago'] ?? 0);

        if ($valorTotal > 0 && $valorPago >= $valorTotal) {
            $data['status'] = 'quitada';
            if (empty($data['data_pagamento'])) {
                $data['data_pagamento'] = now()->toDateString();
            }
            return $data;
        }

        if ($valorPago > 0) {
            $data['status'] = 'parcial';
        } else {
            $data['status'] = 'aberta';
        }

        return $data;
    }

    public function index(Request $request)
    {
        $empresaId = $this->resolveEmpresaId($request);

        $query = ContaPagar::with(['empresa', 'filial', 'fornecedor'])
            ->where('id_empresa', $empresaId);

        if ($request->filled('id_filial')) {
            $query->where('id_filial', $request->get('id_filial'));
        }
        if ($request->filled('id_fornecedor')) {
            $query->where('id_fornecedor', $request->get('id_fornecedor'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }
        if ($request->filled('categoria')) {
            $query->where('categoria', $request->get('categoria'));
        }
        if ($request->filled('data_emissao_inicio')) {
            $query->where('data_emissao', '>=', $request->get('data_emissao_inicio'));
        }
        if ($request->filled('data_emissao_fim')) {
            $query->where('data_emissao', '<=', $request->get('data_emissao_fim'));
        }
        if ($request->filled('data_vencimento_inicio')) {
            $query->where('data_vencimento', '>=', $request->get('data_vencimento_inicio'));
        }
        if ($request->filled('data_vencimento_fim')) {
            $query->where('data_vencimento', '<=', $request->get('data_vencimento_fim'));
        }

        return response()->json($query->orderBy('data_vencimento', 'asc')->paginate(50));
    }

    public function show(Request $request, $id)
    {
        $empresaId = $this->resolveEmpresaId($request);
        $registro = ContaPagar::with(['empresa', 'filial', 'fornecedor'])
            ->where('id_empresa', $empresaId)
            ->findOrFail($id);

        return response()->json($registro);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_empresa' => 'nullable|integer|exists:tb_empresas,id_empresa',
            'id_filial' => 'required|integer|exists:tb_filiais,id_filial',
            'id_fornecedor' => 'nullable|integer|exists:tb_fornecedores,id_fornecedor',
            'descricao' => 'nullable|string|max:255',
            'valor_total' => 'required|numeric|min:0',
            'valor_pago' => 'nullable|numeric|min:0',
            'data_emissao' => 'nullable|date',
            'data_vencimento' => 'nullable|date',
            'data_pagamento' => 'nullable|date',
            'status' => 'nullable|in:aberta,parcial,quitada,atrasada,cancelada',
            'categoria' => 'nullable|in:material,mao_obra,servico_terceiro,outros',
            'observacoes' => 'nullable|string',
        ]);

        $data['id_empresa'] = $this->resolveEmpresaId($request);
        $data['valor_pago'] = $data['valor_pago'] ?? 0;
        $data = $this->aplicarStatus($data);

        $registro = ContaPagar::create($data);

        return response()->json($registro, Response::HTTP_CREATED);
    }

    public function update(Request $request, $id)
    {
        $empresaId = $this->resolveEmpresaId($request);
        $registro = ContaPagar::where('id_empresa', $empresaId)->findOrFail($id);

        $data = $request->validate([
            'id_filial' => 'sometimes|required|integer|exists:tb_filiais,id_filial',
            'id_fornecedor' => 'nullable|integer|exists:tb_fornecedores,id_fornecedor',
            'descricao' => 'nullable|string|max:255',
            'valor_total' => 'sometimes|required|numeric|min:0',
            'valor_pago' => 'nullable|numeric|min:0',
            'data_emissao' => 'nullable|date',
            'data_vencimento' => 'nullable|date',
            'data_pagamento' => 'nullable|date',
            'status' => 'nullable|in:aberta,parcial,quitada,atrasada,cancelada',
            'categoria' => 'nullable|in:material,mao_obra,servico_terceiro,outros',
            'observacoes' => 'nullable|string',
        ]);

        $data = $this->aplicarStatus(array_merge($registro->toArray(), $data));
        $registro->update($data);

        return response()->json($registro);
    }

    public function destroy(Request $request, $id)
    {
        $empresaId = $this->resolveEmpresaId($request);
        $registro = ContaPagar::where('id_empresa', $empresaId)->findOrFail($id);
        $registro->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
