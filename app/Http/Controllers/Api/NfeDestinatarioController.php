<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NfeDestinatario;

class NfeDestinatarioController extends Controller
{
    public function index(Request $request)
    {
        $id_empresa = $request->header('X-ID-EMPRESA');
        $destinatarios = NfeDestinatario::whereHas('nfeCabecalho', function($q) use($id_empresa){
            $q->where('id_empresa', $id_empresa);
        })->get();

        return response()->json($destinatarios);
    }

    public function show($id)
    {
        $destinatario = NfeDestinatario::findOrFail($id);
        return response()->json($destinatario);
    }

    public function store(Request $request)
    {
        $destinatario = NfeDestinatario::create($request->all());
        return response()->json($destinatario, 201);
    }

    public function update(Request $request, $id)
    {
        $destinatario = NfeDestinatario::findOrFail($id);
        $destinatario->update($request->all());
        return response()->json($destinatario);
    }

    public function destroy($id)
    {
        $destinatario = NfeDestinatario::findOrFail($id);
        $destinatario->delete();
        return response()->json(null, 204);
    }
}
