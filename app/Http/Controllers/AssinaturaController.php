<?php

namespace App\Http\Controllers;

use App\Models\Assinatura;
use Illuminate\Http\Request;

class AssinaturaController extends Controller
{
    public function index()
    {
        return response()->json(Assinatura::with(['empresa', 'pagamentos'])->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'empresa_id' => 'required|exists:tb_empresas,id',
            'data_inicio' => 'required|date',
            'data_fim' => 'nullable|date|after_or_equal:data_inicio',
            'plano' => 'required|string',
            'ativa' => 'boolean'
        ]);

        $assinatura = Assinatura::create($validated);

        return response()->json($assinatura, 201);
    }

    public function show(Assinatura $assinatura)
    {
        return response()->json($assinatura->load(['empresa', 'pagamentos']));
    }

    public function update(Request $request, Assinatura $assinatura)
    {
        $validated = $request->validate([
            'data_fim' => 'nullable|date|after_or_equal:data_inicio',
            'plano' => 'string',
            'ativa' => 'boolean'
        ]);

        $assinatura->update($validated);

        return response()->json($assinatura);
    }

    public function destroy(Assinatura $assinatura)
    {
        $assinatura->delete();
        return response()->json(null, 204);
    }
}
