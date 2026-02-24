<?php

namespace App\Http\Controllers;

use App\Models\PerfilAcesso;
use App\Models\Usuario;
use App\Models\UsuarioPerfil;
use Illuminate\Http\Request;

class UsuarioPerfilController extends Controller
{
    public function index($id_usuario)
    {
        $usuario = Usuario::with('perfis')->findOrFail($id_usuario);

        if (!$this->usuarioAtualPodeGerenciar($usuario)) {
            return response()->json(['message' => 'Acesso negado'], 403);
        }

        return response()->json($usuario->perfis);
    }

    public function store(Request $request, $id_usuario, $id_perfil)
    {
        $usuario = Usuario::findOrFail($id_usuario);
        $perfil = PerfilAcesso::findOrFail($id_perfil);

        if (!$this->usuarioAtualPodeGerenciar($usuario)) {
            return response()->json(['message' => 'Acesso negado'], 403);
        }

        if (!$this->usuarioAtualPodeAtribuirPerfil($perfil)) {
            return response()->json(['message' => 'Nivel do perfil acima do permitido para o usuario atual'], 403);
        }

        $jaExiste = UsuarioPerfil::where('id_usuario', $usuario->id_usuario)
            ->where('id_perfil', $perfil->id_perfil)
            ->whereNull('data_revogacao')
            ->exists();

        if ($jaExiste) {
            return response()->json(['message' => 'Perfil ja atribuido ao usuario'], 409);
        }

        $usuarioPerfil = UsuarioPerfil::create([
            'id_usuario' => $usuario->id_usuario,
            'id_perfil' => $perfil->id_perfil,
            'data_atribuicao' => now(),
            'id_usuario_atribuidor' => optional(auth()->user())->id_usuario,
        ]);

        return response()->json($usuarioPerfil, 201);
    }

    public function destroy(Request $request, $id_usuario, $id_perfil)
    {
        $usuario = Usuario::findOrFail($id_usuario);
        $perfil = PerfilAcesso::findOrFail($id_perfil);

        if (!$this->usuarioAtualPodeGerenciar($usuario)) {
            return response()->json(['message' => 'Acesso negado'], 403);
        }

        if (!$this->usuarioAtualPodeAtribuirPerfil($perfil)) {
            return response()->json(['message' => 'Nivel do perfil acima do permitido para o usuario atual'], 403);
        }

        $registro = UsuarioPerfil::where('id_usuario', $usuario->id_usuario)
            ->where('id_perfil', $perfil->id_perfil)
            ->whereNull('data_revogacao')
            ->first();

        if (!$registro) {
            return response()->json(['message' => 'Perfil nao encontrado para o usuario'], 404);
        }

        $registro->data_revogacao = now();
        $registro->motivo_revogacao = $request->input('motivo_revogacao');
        $registro->save();

        return response()->json(['message' => 'Perfil revogado com sucesso']);
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

    private function usuarioAtualPodeAtribuirPerfil(PerfilAcesso $perfil): bool
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
