<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NfeEmitente;

class NfeEmitenteController extends Controller
{
    public function index(Request $request)
    {
        $id_empresa = $request->header('X-ID-EMPRESA');
        $emitentes = NfeEmitente::whereHas('nfeCabecalho', function($q) use($id_empresa){
            $q->where('id_empresa', $id_empresa);
        })->get();

        return response()->json($emitentes);
    }

    public function show($id)
    {
        $emitente = NfeEmitente::findOrFail($id);
        return response()->json($emitente);
    }

    public function store(Request $request)
    {
        $emitente = NfeEmitente::create($request->all());
        return response()->json($emitente, 201);
    }

    public function update(Request $request, $id)
    {
        $emitente = NfeEmitente::findOrFail($id);
        $emitente->update($request->all());
        return response()->json($emitente);
    }

    public function destroy($id)
    {
        $emitente = NfeEmitente::findOrFail($id);
        $emitente->delete();
        return response()->json(null, 204);
    }
}
