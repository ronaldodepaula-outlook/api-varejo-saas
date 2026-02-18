<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\PasswordReset;
use App\Http\Controllers\EmailController;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class PasswordResetController extends Controller
{
    /**
     * Solicita o reset de senha (envia e-mail com token)
     */
    public function solicitarReset(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $email = $request->input('email');
        $usuario = Usuario::where('email', $email)->first();

        if (!$usuario) {
            return response()->json(['erro' => 'E-mail não encontrado.'], 404);
        }

        // Cria token único
        $token = Str::random(64);

        // Grava ou atualiza token na tabela password_resets
        PasswordReset::updateOrCreate(
            ['email' => $email],
            ['token' => $token, 'created_at' => Carbon::now()]
        );

        // Monta link para a criação da nova senha
        $link = url("/resetar-senha.php?token={$token}&email={$email}");

        // Obtém o template do e-mail
        $mensagem = $this->getEmailTemplate($usuario->nome, $link);

        // Envia e-mail
        $envio = EmailController::enviarEmail($email, 'Redefinição de Senha - NexusFlow', $mensagem);

        if ($envio['status']) {
            return response()->json(['mensagem' => 'E-mail de redefinição enviado.'], 200);
        } else {
            return response()->json(['erro' => $envio['mensagem']], 500);
        }
    }

    /**
     * Template de e-mail corrigido com UTF-8
     */
    private function getEmailTemplate($nomeUsuario, $linkReset)
    {
        // Codifica o nome do usuário para prevenir problemas
        $nomeUsuarioSeguro = htmlspecialchars($nomeUsuario, ENT_QUOTES, 'UTF-8');
        
        return '<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinição de Senha - NexusFlow</title>
    <style type="text/css">
        /* Reset CSS */
        body, table, td, a {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }
        table {
            border-collapse: collapse;
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }
        img {
            border: 0;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
            -ms-interpolation-mode: bicubic;
        }
        
        /* Estilos principais */
        body {
            height: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            background-color: #f6f6f6;
            font-family: Arial, Helvetica, sans-serif;
        }
        
        .container {
            display: block;
            margin: 0 auto !important;
            max-width: 600px;
            padding: 20px;
        }
        
        .content {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .header {
            background: #2563eb;
            padding: 40px 30px;
            text-align: center;
        }
        
        .header h1 {
            color: #ffffff;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 28px;
            font-weight: bold;
            line-height: 1.2;
            margin: 0;
            padding: 0;
        }
        
        .header p {
            color: rgba(255,255,255,0.9);
            font-family: Arial, Helvetica, sans-serif;
            font-size: 16px;
            line-height: 1.4;
            margin: 10px 0 0 0;
        }
        
        .body-content {
            padding: 40px;
        }
        
        .greeting {
            font-size: 18px;
            font-weight: bold;
            color: #111827;
            margin-bottom: 20px;
        }
        
        .btn-primary {
            background-color: #2563eb;
            border-radius: 6px;
            color: #ffffff !important;
            display: inline-block;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 16px;
            font-weight: bold;
            line-height: 1.2;
            margin: 25px 0;
            padding: 16px 32px;
            text-decoration: none;
            text-align: center;
        }
        
        .link-box {
            background-color: #f8f9fa;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            color: #64748b;
            font-family: monospace;
            font-size: 14px;
            margin: 20px 0;
            padding: 16px;
            text-align: center;
            word-break: break-all;
        }
        
        .warning-box {
            background-color: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 6px;
            color: #92400e;
            margin: 20px 0;
            padding: 16px;
        }
        
        .footer {
            background-color: #f8fafc;
            border-top: 1px solid #e2e8f0;
            padding: 30px;
            text-align: center;
        }
        
        .footer p {
            color: #64748b;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 14px;
            line-height: 1.4;
            margin: 0 0 10px 0;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-muted {
            color: #6b7280;
        }
        
        .icon-large {
            font-size: 48px;
            margin-bottom: 20px;
        }
        
        /* Responsividade */
        @media only screen and (max-width: 600px) {
            .container {
                padding: 10px !important;
                width: 100% !important;
            }
            
            .body-content {
                padding: 20px !important;
            }
            
            .header {
                padding: 30px 20px !important;
            }
            
            .header h1 {
                font-size: 24px !important;
            }
        }
    </style>
</head>
<body>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td align="center" style="padding: 40px 20px;">
                <div class="container">
                    <div class="content">
                        <!-- Header -->
                        <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                            <tr>
                                <td class="header">
                                    <div class="icon-large" style="color: white;">🔒</div>
                                    <h1>Redefinição de Senha</h1>
                                    <p>NexusFlow - Sistema de Gestão</p>
                                </td>
                            </tr>
                        </table>
                        
                        <!-- Body Content -->
                        <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                            <tr>
                                <td class="body-content">
                                    <div class="greeting">Olá, ' . $nomeUsuarioSeguro . '!</div>
                                    
                                    <p>Recebemos uma solicitação para redefinir a senha da sua conta NexusFlow. Para criar uma nova senha, clique no botão abaixo:</p>
                                    
                                    <div class="text-center">
                                        <a href="' . $linkReset . '" class="btn-primary" target="_blank">🔑 Redefinir Minha Senha</a>
                                    </div>
                                    
                                    <p class="text-muted">Se o botão não funcionar, copie e cole o link abaixo no seu navegador:</p>
                                    
                                    <div class="link-box">' . $linkReset . '</div>
                                    
                                    <div class="warning-box">
                                        <strong>⚠️ Importante:</strong> Este link é válido por <strong>60 minutos</strong>. 
                                        Após este período, será necessário solicitar um novo link de redefinição.
                                    </div>
                                    
                                    <p>Se você não solicitou a redefinição de senha, por favor ignore este e-mail. Sua senha atual permanecerá inalterada.</p>
                                    
                                    <p class="text-muted" style="font-size: 14px;">
                                        <strong>Dica de segurança:</strong> Utilize uma senha forte, com letras maiúsculas e minúsculas, números e caracteres especiais.
                                    </p>
                                </td>
                            </tr>
                        </table>
                        
                        <!-- Footer -->
                        <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                            <tr>
                                <td class="footer">
                                    <p><strong>NexusFlow © ' . date('Y') . '</strong><br/>Sistema de Gestão Multi-Empresas</p>
                                    <p style="font-size: 12px; color: #94a3b8;">
                                        Este é um e-mail automático, por favor não responda.<br/>
                                        Caso tenha alguma dúvida, entre em contato com nosso suporte.
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </td>
        </tr>
    </table>
</body>
</html>';
    }

    /**
     * Valida token e retorna dados para criar nova senha
     */
    public function validarToken(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'email' => 'required|email'
        ]);

        $reset = PasswordReset::where('email', $request->email)
                                ->where('token', $request->token)
                                ->first();

        if (!$reset) {
            return response()->json(['erro' => 'Token inválido ou expirado.'], 404);
        }

        // Checar validade do token (60 minutos)
        $validade = Carbon::parse($reset->created_at)->addMinutes(60);
        if (Carbon::now()->gt($validade)) {
            // Remove token expirado
            $reset->delete();
            return response()->json(['erro' => 'Token expirado. Solicite um novo.'], 403);
        }

        return response()->json([
            'mensagem' => 'Token válido.',
            'email' => $reset->email,
            'token' => $reset->token
        ], 200);
    }

    /**
     * Altera a senha do usuário
     */
    public function resetarSenha(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required|string',
            'senha' => 'required|min:6|confirmed'
        ]);

        // Busca token na tabela
        $reset = PasswordReset::where('email', $request->email)
                                ->where('token', $request->token)
                                ->first();

        if (!$reset) {
            return response()->json(['erro' => 'Token inválido ou expirado.'], 404);
        }

        // Verifica se o token ainda é válido
        $validade = Carbon::parse($reset->created_at)->addMinutes(60);
        if (Carbon::now()->gt($validade)) {
            $reset->delete();
            return response()->json(['erro' => 'Token expirado. Solicite um novo.'], 403);
        }

        // Atualiza senha
        $usuario = Usuario::where('email', $request->email)->first();
        if (!$usuario) {
            return response()->json(['erro' => 'Usuário não encontrado.'], 404);
        }

        $usuario->senha = Hash::make($request->senha);
        $usuario->save();

        // Remove o token após uso
        $reset->delete();

        return response()->json(['mensagem' => 'Senha alterada com sucesso!'], 200);
    }
}