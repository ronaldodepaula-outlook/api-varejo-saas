<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NfeInformacoesAdicionais;

class NfeInformacoesAdicionaisController extends Controller
{
    public function index(Request $request)
    {
        $id_empresa = $request->header('X-ID-EMPRESA');
        $infos = NfeInformacoesAdicionais::whereHas('nfeCabecalho', function($q) use($id_empresa){
            $q->where('id_empresa', $id_empresa);
        })->get();

        return response()->json($infos);
    }

    public function show($id)
    {
        $info = NfeInformacoesAdicionais::findOrFail($id);
        return response()->json($info);
    }

    public function store(Request $request)
    {
        $info = NfeInformacoesAdicionais::create($request->all());
        return response()->json($info, 201);
    }

    public function update(Request $request, $id)
    {
        $info = NfeInformacoesAdicionais::findOrFail($id);
        $info->update($request->all());
        return response()->json($info);
    }

    public function destroy($id)
    {
        $info = NfeInformacoesAdicionais::findOrFail($id);
        $info->delete();
        return response()->json(null, 204);
    }
}
