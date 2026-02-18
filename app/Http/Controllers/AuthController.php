<?php
namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\VerificacaoEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function registrar(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:100',
            'email' => 'required|email|unique:tb_usuarios,email',
            'senha' => 'required|min:6'
        ]);

        $usuario = Usuario::create([
            'nome' => $request->nome,
            'email' => $request->email,
            'senha' => Hash::make($request->senha),
            'perfil' => 'usuario',
            'ativo' => 0
        ]);

        $token = Str::random(60);
        VerificacaoEmail::create([
            'id_usuario' => $usuario->id_usuario,
            'token' => $token
        ]);

        $link = url("/api/verificar-email/{$token}");

        // Enviar e-mail
        Mail::raw("Clique no link para validar sua conta: $link", function ($message) use ($usuario) {
            $message->to($usuario->email)
                    ->subject('Validação de Conta - SaaS MultiEmpresas');
        });

        return response()->json(['message' => 'Usuário registrado. Verifique seu e-mail para ativar a conta.'], 201);
    }

    public function verificarEmail($token)
    {
        $verificacao = VerificacaoEmail::where('token', $token)->first();

        if (!$verificacao) {
            return response()->json(['message' => 'Token inválido ou expirado.'], 400);
        }

        $usuario = Usuario::find($verificacao->id_usuario);
        $usuario->ativo = 1;
        $usuario->email_verificado_em = now();
        $usuario->save();

        $verificacao->delete();
        return response()->json(['message' => 'Conta validada com sucesso!']);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'senha' => 'required',
        ]);

        $usuario = Usuario::where('email', $request->email)->first();
        if (!$usuario) {
            return response()->json(['message' => 'Credenciais inválidas'], 401);
        }
        $senhaHash = $usuario->senha;
        try {
            $senhaValida = \Hash::check($request->senha, $senhaHash);
        } catch (\Exception $e) {
            // Se não for hash, compara direto (texto puro)
            $senhaValida = $request->senha === $senhaHash;
        }
        if (!$senhaValida) {
            return response()->json(['message' => 'Credenciais inválidas'], 401);
        }
        if (!$usuario->ativo) {
            return response()->json(['message' => 'Usuário inativo ou e-mail não verificado'], 403);
        }

        // Gera token Sanctum
        $token = $usuario->createToken('api_token')->plainTextToken;

        // Buscar empresa e licença ativa
        $empresa = $usuario->empresa;
        $licenca = $empresa ? $empresa->licencas()->where('status', 'ativa')->orderByDesc('data_fim')->first() : null;

        return response()->json([
            'usuario' => [
                'id_usuario' => $usuario->id_usuario,
                'nome' => $usuario->nome,
                'email' => $usuario->email,
                'perfil' => $usuario->perfil,
            ],
            'empresa' => $empresa,
            'licenca' => $licenca,
            'token' => $token,
            'message' => 'Login realizado com sucesso.'
        ]);
    }
}
