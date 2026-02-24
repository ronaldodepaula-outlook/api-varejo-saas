<?php

namespace App\Http\Controllers;

use App\Models\CampoVisivelPerfil;
use App\Models\PerfilAcesso;
use Illuminate\Http\Request;

class CampoVisivelPerfilController extends Controller
{
    public function index($id_perfil)
    {
        $perfil = PerfilAcesso::findOrFail($id_perfil);

        if (!$this->usuarioPodeGerenciarPerfil($perfil)) {
            return response()->json(['message' => 'Acesso negado'], 403);
        }

        $campos = CampoVisivelPerfil::where('id_perfil', $perfil->id_perfil)
            ->orderBy('tabela')
            ->orderBy('campo')
            ->get();

        return response()->json($campos);
    }

    public function store(Request $request, $id_perfil)
    {
        $perfil = PerfilAcesso::findOrFail($id_perfil);

        if (!$this->usuarioPodeGerenciarPerfil($perfil)) {
            return response()->json(['message' => 'Acesso negado'], 403);
        }

        $data = $request->validate([
            'campos' => 'required|array|min:1',
            'campos.*.tabela' => 'required|string|max:100',
            'campos.*.campo' => 'required|string|max:100',
            'campos.*.visivel' => 'nullable|boolean',
            'campos.*.editavel' => 'nullable|boolean',
        ]);

        $result = [];
        foreach ($data['campos'] as $campo) {
            $registro = CampoVisivelPerfil::updateOrCreate(
                [
                    'id_perfil' => $perfil->id_perfil,
                    'tabela' => $campo['tabela'],
                    'campo' => $campo['campo'],
                ],
                [
                    'visivel' => isset($campo['visivel']) ? (bool) $campo['visivel'] : true,
                    'editavel' => isset($campo['editavel']) ? (bool) $campo['editavel'] : true,
                ]
            );
            $result[] = $registro;
        }

        return response()->json($result, 201);
    }

    public function destroy($id_perfil, $id_campo)
    {
        $perfil = PerfilAcesso::findOrFail($id_perfil);

        if (!$this->usuarioPodeGerenciarPerfil($perfil)) {
            return response()->json(['message' => 'Acesso negado'], 403);
        }

        $campo = CampoVisivelPerfil::where('id_perfil', $perfil->id_perfil)
            ->where('id_campo', $id_campo)
            ->firstOrFail();

        $campo->delete();

        return response()->json(null, 204);
    }

    private function usuarioPodeGerenciarPerfil(PerfilAcesso $perfil): bool
    {
        $usuario = auth()->user();
        if (!$usuario) {
            return true;
        }
        if ($usuario->isAdmin()) {
            return true;
        }
        return $perfil->nivel < $usuario->nivelMaximo();
    }
}
