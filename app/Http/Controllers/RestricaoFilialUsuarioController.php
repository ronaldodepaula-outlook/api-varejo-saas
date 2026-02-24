<?php

namespace App\Http\Controllers;

use App\Models\RestricaoFilialUsuario;
use App\Models\Usuario;
use App\Models\Filial;
use Illuminate\Http\Request;

class RestricaoFilialUsuarioController extends Controller
{
    public function index($id_usuario)
    {
        $usuario = Usuario::findOrFail($id_usuario);

        if (!$this->usuarioAtualPodeGerenciar($usuario)) {
            return response()->json(['message' => 'Acesso negado'], 403);
        }

        $restricoes = RestricaoFilialUsuario::with('filial')
            ->where('id_usuario', $usuario->id_usuario)
            ->orderBy('id_filial')
            ->get();

        return response()->json($restricoes);
    }

    public function store(Request $request, $id_usuario, $id_filial)
    {
        $usuario = Usuario::findOrFail($id_usuario);
        $filial = Filial::findOrFail($id_filial);

        if (!$this->usuarioAtualPodeGerenciar($usuario)) {
            return response()->json(['message' => 'Acesso negado'], 403);
        }
        $usuarioAtual = auth()->user();
        if ($usuarioAtual && !$usuarioAtual->isAdmin() && (int) $filial->id_empresa !== (int) $usuarioAtual->id_empresa) {
            return response()->json(['message' => 'Filial fora da empresa do usuario atual'], 403);
        }

        $data = $request->validate([
            'pode_acessar' => 'nullable|boolean',
        ]);

        $restricao = RestricaoFilialUsuario::updateOrCreate(
            [
                'id_usuario' => $usuario->id_usuario,
                'id_filial' => $id_filial,
            ],
            [
                'pode_acessar' => isset($data['pode_acessar']) ? (bool) $data['pode_acessar'] : true,
            ]
        );

        return response()->json($restricao, 201);
    }

    public function destroy($id_usuario, $id_filial)
    {
        $usuario = Usuario::findOrFail($id_usuario);

        if (!$this->usuarioAtualPodeGerenciar($usuario)) {
            return response()->json(['message' => 'Acesso negado'], 403);
        }

        $restricao = RestricaoFilialUsuario::where('id_usuario', $usuario->id_usuario)
            ->where('id_filial', $id_filial)
            ->firstOrFail();

        $restricao->delete();

        return response()->json(null, 204);
    }

    private function usuarioAtualPodeGerenciar(Usuario $alvo): bool
    {
        $usuario = auth()->user();
        if (!$usuario) {
            return true;
        }
        if ($usuario->isAdmin()) {
            return true;
        }
        if ((int) $usuario->id_empresa !== (int) $alvo->id_empresa) {
            return false;
        }
        return $usuario->nivelMaximo() > $alvo->nivelMaximo();
    }
}
