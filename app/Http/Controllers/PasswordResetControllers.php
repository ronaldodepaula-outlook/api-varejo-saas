<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\PasswordResetToken;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class PasswordResetController extends Controller
{
    // Teste simples para verificar se o controller esta carregando
    public function test()
    {
        return response()->json(['message' => 'PasswordResetController carregado com sucesso.']);
    }

    // Solicita recuperacao de senha (simplificado)
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $usuario = Usuario::where('email', $request->email)->first();
        if (!$usuario) {
            return response()->json(['message' => 'Usuario nao encontrado.'], 404);
        }
        $token = Str::random(60);
        PasswordResetToken::updateOrCreate(
            ['email' => $usuario->email],
            ['token' => $token, 'created_at' => Carbon::now()]
        );
        $link = url('/public/password/reset.php?token=' . $token . '&email=' . urlencode($usuario->email));
        \App\Helpers\PHPMailerHelper::send(
            $usuario->email,
            'Recuperacao de Senha',
            "Clique no link para redefinir sua senha: <a href='$link'>Redefinir Senha</a>"
        );
        return response()->json(['message' => 'Link de recuperacao enviado para o e-mail.']);
    }

    // Alias legado: esqueci a senha
    public function sendResetLink(Request $request)
    {
        return $this->sendResetLinkEmail($request);
    }

    // Alias atual: solicitar reset
    public function solicitarReset(Request $request)
    {
        return $this->sendResetLinkEmail($request);
    }
}
