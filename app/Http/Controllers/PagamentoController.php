<?php

namespace App\Http\Controllers;

use App\Models\Pagamento;
use Illuminate\Http\Request;

class PagamentoController extends Controller
{
    public function index()
    {
        return response()->json(Pagamento::with('assinatura')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'assinatura_id' => 'required|exists:tb_assinaturas,id',
            'valor' => 'required|numeric|min:0',
            'metodo' => 'required|string',
            'data_pagamento' => 'nullable|date',
            'status' => 'required|string|in:pendente,pago,falhado'
        ]);

        $pagamento = Pagamento::create($validated);

        return response()->json($pagamento, 201);
    }

    public function show(Pagamento $pagamento)
    {
        return response()->json($pagamento->load('assinatura'));
    }

    public function update(Request $request, Pagamento $pagamento)
    {
        $validated = $request->validate([
            'valor' => 'numeric|min:0',
            'metodo' => 'string',
            'data_pagamento' => 'nullable|date',
            'status' => 'string|in:pendente,pago,falhado'
        ]);

        $pagamento->update($validated);

        return response()->json($pagamento);
    }

    public function destroy(Pagamento $pagamento)
    {
        $pagamento->delete();
        return response()->json(null, 204);
    }
}
