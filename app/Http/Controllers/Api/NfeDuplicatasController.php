<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NfeDuplicatas;

class NfeDuplicatasController extends Controller
{
    public function index(Request $request)
    {
        $id_empresa = $request->header('X-ID-EMPRESA');
        $duplicatas = NfeDuplicatas::whereHas('cobranca.nfeCabecalho', function($q) use($id_empresa){
            $q->where('id_empresa', $id_empresa);
        })->get();

        return response()->json($duplicatas);
    }

    public function show($id)
    {
        $dup = NfeDuplicatas::findOrFail($id);
        return response()->json($dup);
    }

    public function store(Request $request)
    {
        $dup = NfeDuplicatas::create($request->all());
        return response()->json($dup, 201);
    }

    public function update(Request $request, $id)
    {
        $dup = NfeDuplicatas::findOrFail($id);
        $dup->update($request->all());
        return response()->json($dup);
    }

    public function destroy($id)
    {
        $dup = NfeDuplicatas::findOrFail($id);
        $dup->delete();
        return response()->json(null, 204);
    }
}
