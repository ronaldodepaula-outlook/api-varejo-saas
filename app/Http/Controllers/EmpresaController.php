<?php
namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmpresaController extends Controller
{
    public function index()
    {
        return response()->json(Empresa::all());
    }

    public function store(Request $request)
    {
        $empresa = Empresa::create($request->all());
        return response()->json($empresa, 201);
    }

    public function show($id)
    {
        return response()->json(Empresa::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $empresa = Empresa::findOrFail($id);
        $empresa->update($request->all());
        return response()->json($empresa);
    }

    public function destroy($id)
    {
        Empresa::destroy($id);
        return response()->json(null, 204);
    }

    /**
     * Lista a empresa referente ao usuário autenticado
     * Equivalente ao SQL: SELECT * FROM tb_usuarios U INNER JOIN tb_empresas E ON E.id_empresa = U.id_empresa
     */
    public function empresaDoUsuario(Request $request)
    {
        try {
            // Obtém o usuário autenticado
            $usuario = Auth::user();
            
            if (!$usuario) {
                return response()->json([
                    'message' => 'Usuário não autenticado'
                ], 401);
            }

            // Carrega a empresa do usuário com o relacionamento
            $usuarioComEmpresa = Usuario::with('empresa')
                ->where('id_usuario', $usuario->id_usuario)
                ->first();

            if (!$usuarioComEmpresa) {
                return response()->json([
                    'message' => 'Usuário não encontrado'
                ], 404);
            }

            if (!$usuarioComEmpresa->empresa) {
                return response()->json([
                    'message' => 'Empresa não encontrada para este usuário'
                ], 404);
            }

            return response()->json([
                'usuario' => [
                    'id_usuario' => $usuarioComEmpresa->id_usuario,
                    'nome' => $usuarioComEmpresa->nome,
                    'email' => $usuarioComEmpresa->email,
                    'perfil' => $usuarioComEmpresa->perfil,
                    'ativo' => $usuarioComEmpresa->ativo,
                    'id_empresa' => $usuarioComEmpresa->id_empresa
                ],
                'empresa' => $usuarioComEmpresa->empresa
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao buscar empresa do usuário',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lista a empresa por ID do usuário (para admin)
     */
    public function empresaPorUsuario($idUsuario)
    {
        try {
            $usuarioComEmpresa = Usuario::with('empresa')
                ->where('id_usuario', $idUsuario)
                ->first();

            if (!$usuarioComEmpresa) {
                return response()->json([
                    'message' => 'Usuário não encontrado'
                ], 404);
            }

            if (!$usuarioComEmpresa->empresa) {
                return response()->json([
                    'message' => 'Empresa não encontrada para este usuário'
                ], 404);
            }

            return response()->json([
                'usuario' => [
                    'id_usuario' => $usuarioComEmpresa->id_usuario,
                    'nome' => $usuarioComEmpresa->nome,
                    'email' => $usuarioComEmpresa->email,
                    'perfil' => $usuarioComEmpresa->perfil,
                    'ativo' => $usuarioComEmpresa->ativo,
                    'id_empresa' => $usuarioComEmpresa->id_empresa
                ],
                'empresa' => $usuarioComEmpresa->empresa
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao buscar empresa do usuário',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}