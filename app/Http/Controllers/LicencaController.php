<?php

namespace App\Http\Controllers;

use App\Models\Licenca;
use Illuminate\Http\Request;

class LicencaController extends Controller
{
    public function index()
    {
        return response()->json(Licenca::with('empresa')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'empresa_id' => 'required|exists:tb_empresas,id',
            'data_inicio' => 'required|date',
            'data_fim' => 'nullable|date|after_or_equal:data_inicio',
            'ativa' => 'boolean'
        ]);

        $licenca = Licenca::create($validated);

        return response()->json($licenca, 201);
    }

    public function show(Licenca $licenca)
    {
        return response()->json($licenca->load('empresa'));
    }

    public function update(Request $request, Licenca $licenca)
    {
        $validated = $request->validate([
            'data_fim' => 'nullable|date|after_or_equal:data_inicio',
            'ativa' => 'boolean'
        ]);

        $licenca->update($validated);

        return response()->json($licenca);
    }

    public function destroy(Licenca $licenca)
    {
        $licenca->delete();
        return response()->json(null, 204);
    }
}
