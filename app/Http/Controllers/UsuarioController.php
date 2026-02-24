<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // Listar todos os usuÃ¡rios
    public function index()
    {
        $query = Usuario::with('empresa');

        if (request()->boolean('with_perfis')) {
            $query->with('perfis');
        }
        if (request()->boolean('with_acessos')) {
            $query->with(['permissoesEspeciais', 'restricoesFiliais']);
        }

        $usuarios = $query->get();
        return response()->json($usuarios);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    // Criar novo usuÃ¡rio
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_empresa' => 'required|exists:tb_empresas,id_empresa',
            'nome' => 'required|string|max:100',
            'email' => 'required|email|unique:tb_usuarios,email',
            'senha' => 'required|string|min:6',
            'perfil' => 'required|string',
            'ativo' => 'boolean',
            'aceitou_termos' => 'boolean',
            'newsletter' => 'boolean',
            'perfis' => 'nullable|array',
            'perfis.*' => 'integer|exists:tb_perfis_acesso,id_perfil',
        ]);
        $validated['senha'] = bcrypt($validated['senha']);
        $usuario = Usuario::create($validated);

        if (!empty($validated['perfis'])) {
            foreach ($validated['perfis'] as $idPerfil) {
                \App\Models\UsuarioPerfil::create([
                    'id_usuario' => $usuario->id_usuario,
                    'id_perfil' => $idPerfil,
                    'data_atribuicao' => now(),
                    'id_usuario_atribuidor' => optional(auth()->user())->id_usuario,
                ]);
            }
        }

        return response()->json($usuario, 201);
    }

    /**
     * Display the specified resource.
     */
    // Consultar usuÃ¡rio por ID
    public function show($id)
    {
        $query = Usuario::with('empresa');
        if (request()->boolean('with_perfis')) {
            $query->with('perfis');
        }
        if (request()->boolean('with_acessos')) {
            $query->with(['permissoesEspeciais', 'restricoesFiliais']);
        }

        $usuario = $query->find($id);
        if (!$usuario) {
            return response()->json(['message' => 'UsuÃ¡rio nÃ£o encontrado'], 404);
        }
        return response()->json($usuario);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Usuario $usuario)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    // Atualizar usuÃ¡rio por ID
    public function update(Request $request, $id)
    {
        $usuario = Usuario::find($id);
        if (!$usuario) {
            return response()->json(['message' => 'UsuÃ¡rio nÃ£o encontrado'], 404);
        }
        $validated = $request->validate([
            'nome' => 'sometimes|string|max:100',
            'email' => 'sometimes|email|unique:tb_usuarios,email,' . $id . ',id_usuario',
            'senha' => 'sometimes|string|min:6',
            'perfil' => 'sometimes|string',
            'ativo' => 'boolean',
            'aceitou_termos' => 'boolean',
            'newsletter' => 'boolean',
        ]);
        if (isset($validated['senha'])) {
            $validated['senha'] = bcrypt($validated['senha']);
        }
        $usuario->update($validated);
        return response()->json($usuario);
    }

    /**
     * Remove the specified resource from storage.
     */
    // Remover usuÃ¡rio por ID
    public function destroy($id)
    {
        $usuario = Usuario::find($id);
        if (!$usuario) {
            return response()->json(['message' => 'UsuÃ¡rio nÃ£o encontrado'], 404);
        }
        $usuario->delete();
        return response()->json(null, 204);
    }

    // Listar usuÃ¡rios de uma empresa
    public function usuariosPorEmpresa($id_empresa)
    {
        $usuarios = Usuario::where('id_empresa', $id_empresa)->with('empresa')->get();
        return response()->json($usuarios);
    }
}
