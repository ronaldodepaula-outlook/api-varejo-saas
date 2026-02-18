<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\PasswordReset;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PasswordResetController extends Controller
{
    public function solicitarReset(Request $request)
    {
        $email = $request->input('email');

        $usuario = Usuario::where('email', $email)->first();
        if (!$usuario) {
            return response()->json(['erro' => 'E-mail não encontrado.'], 404);
        }

        // Cria token único
        $token = Str::random(64);

        // Grava na tabela password_resets
        PasswordReset::updateOrCreate(
            ['email' => $email],
            ['token' => $token, 'created_at' => Carbon::now()]
        );

        // Monta link de redefinição
        $link = url("/resetar-senha?token={$token}");

        // Corpo do e-mail
        $mensagem = "
            <h3>Redefinição de Senha</h3>
            <p>Olá, {$usuario->nome}.</p>
            <p>Você solicitou a redefinição de senha. Clique no link abaixo para criar uma nova senha:</p>
            <p><a href='{$link}' target='_blank'>Redefinir Senha</a></p>
            <p>Se não foi você, ignore este e-mail.</p>
        ";

        // Envia e-mail usando o EmailController
        $envio = EmailController::enviarEmail($email, 'Redefinição de Senha', $mensagem);

        if ($envio['status']) {
            return response()->json(['mensagem' => 'E-mail de redefinição enviado.'], 200);
        } else {
            return response()->json(['erro' => $envio['mensagem']], 500);
        }
    }
}
