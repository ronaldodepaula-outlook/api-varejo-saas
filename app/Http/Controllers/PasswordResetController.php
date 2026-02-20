<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\PasswordResetToken;
use App\Models\PasswordReset;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

class PasswordResetController extends Controller
{
    public function test()
    {
        return response()->json(['message' => 'PasswordResetController carregado com sucesso.']);
    }

    public function solicitarReset(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $email = $request->input('email');

        $usuario = Usuario::where('email', $email)->first();
        if (!$usuario) {
            return response()->json(['erro' => 'E-mail não encontrado.'], 404);
        }

        $now = Carbon::now();
        $tokenTtlMinutes = 60 * 24;

        // tenta reutilizar token ainda válido para evitar mismatch com o que foi enviado ao usuário
        $existing = null;
        if (class_exists(PasswordReset::class)) {
            $existing = PasswordReset::where('email', $email)->first();
        }
        if (!$existing) {
            $existing = PasswordResetToken::where('email', $email)->first();
        }

        $reuseToken = false;
        if ($existing && !empty($existing->token) && !empty($existing->created_at)) {
            $createdAt = $existing->created_at instanceof Carbon
                ? $existing->created_at
                : Carbon::parse($existing->created_at);
            if ($createdAt->diffInMinutes($now) < $tokenTtlMinutes) {
                $reuseToken = true;
            }
        }

        if ($reuseToken) {
            $token = $existing->token;
        } else {
            $token = Str::random(64);
            if ($existing) {
                $existing->token = $token;
                $existing->created_at = $now;
                $existing->save();
            } else {
                // tenta atualizar em duas possíveis tabelas/modelos que o projeto usa
                if (class_exists(PasswordReset::class)) {
                    PasswordReset::updateOrCreate(
                        ['email' => $email],
                        ['token' => $token, 'created_at' => $now]
                    );
                } else {
                    PasswordResetToken::updateOrCreate(
                        ['email' => $email],
                        ['token' => $token, 'created_at' => $now]
                    );
                }
            }
        }

        $webBase = rtrim(env('WEB_URL', env('APP_URL', url('/'))), '/');
        $link = $webBase . '/redefinir_senha.php?token=' . urlencode($token) . '&email=' . urlencode($email);
        $emailHtml = $this->buildResetEmailHtml($link, $email);
        $emailText = $this->buildResetEmailText($link);

        // envio simplificado — o projeto tem helpers próprios para envio
        if (!class_exists('App\\Helpers\\PHPMailerHelper')) {
            Log::error('PHPMailerHelper nao encontrado para envio de reset de senha.');
            return response()->json(['erro' => 'Servico de e-mail nao configurado.'], 500);
        }

        $sendResult = \App\Helpers\PHPMailerHelper::send(
            $email,
            'Recuperacao de Senha',
            $emailHtml,
            $emailText
        );
        $sendOk = $sendResult === true || (is_array($sendResult) && ($sendResult['success'] ?? false));
        if (!$sendOk) {
            $detalhe = null;
            $debugLog = null;
            if (is_array($sendResult)) {
                $detalhe = $sendResult['error'] ?? null;
                $debugLog = $sendResult['debug'] ?? null;
            } elseif (is_string($sendResult)) {
                $detalhe = $sendResult;
            }
            Log::error('Falha ao enviar e-mail de redefinicao.', [
                'email' => $email,
                'erro' => $detalhe ?? $sendResult,
            ]);
            $payload = ['erro' => 'Falha ao enviar e-mail de redefinição.'];
            if (config('app.debug')) {
                if ($detalhe) {
                    $payload['detalhe'] = $detalhe;
                }
                if ($debugLog) {
                    $payload['smtp_log'] = $debugLog;
                }
            }
            return response()->json($payload, 500);
        }

        if (config('app.debug') && is_array($sendResult) && !empty($sendResult['debug'])) {
            return response()->json([
                'mensagem' => 'E-mail de redefinição enviado.',
                'smtp_log' => $sendResult['debug'],
            ], 200);
        }

        return response()->json(['mensagem' => 'E-mail de redefinição enviado.'], 200);
    }

    public function validarToken(Request $request)
    {
        $token = $request->input('token');
        if (!$token) {
            return response()->json(['valid' => false], 400);
        }
        $email = $request->input('email');
        $record = null;
        if (class_exists(PasswordReset::class)) {
            $query = PasswordReset::where('token', $token);
            if ($email) {
                $query->where('email', $email);
            }
            $record = $query->first();
        }
        if (!$record) {
            $query = PasswordResetToken::where('token', $token);
            if ($email) {
                $query->where('email', $email);
            }
            $record = $query->first();
        }
        if (!$record && $email) {
            $emailRecord = null;
            if (class_exists(PasswordReset::class)) {
                $emailRecord = PasswordReset::where('email', $email)->first();
            }
            if (!$emailRecord) {
                $emailRecord = PasswordResetToken::where('email', $email)->first();
            }
            if ($emailRecord && !empty($emailRecord->token) && Hash::check($token, $emailRecord->token)) {
                $record = $emailRecord;
            }
        }
        if (!$record) {
            return response()->json(['valid' => false], 404);
        }
        if (empty($record->created_at)) {
            $record->delete();
            return response()->json(['valid' => false], 404);
        }
        $createdAt = $record->created_at instanceof Carbon
            ? $record->created_at
            : Carbon::parse($record->created_at);
        if ($createdAt->diffInMinutes(Carbon::now()) >= (60 * 24)) {
            $record->delete();
            return response()->json(['valid' => false], 404);
        }
        return response()->json(['valid' => true]);
    }

    public function resetarSenha(Request $request)
    {
        if (!$request->filled('password') && $request->filled('senha')) {
            $request->merge([
                'password' => $request->input('senha'),
                'password_confirmation' => $request->input('senha_confirmation'),
            ]);
        }

        $request->validate([
            'token' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string|min:6|confirmed'
        ]);

        $now = Carbon::now();
        $tokenTtlMinutes = 60 * 24;

        $token = $request->input('token');
        $email = $request->input('email');

        $record = null;
        if (class_exists(PasswordReset::class)) {
            $record = PasswordReset::where('email', $email)->where('token', $token)->first();
        }
        if (!$record) {
            $record = PasswordResetToken::where('email', $email)->where('token', $token)->first();
        }
        if (!$record) {
            // fallback: token armazenado como hash
            $emailRecord = null;
            if (class_exists(PasswordReset::class)) {
                $emailRecord = PasswordReset::where('email', $email)->first();
            }
            if (!$emailRecord) {
                $emailRecord = PasswordResetToken::where('email', $email)->first();
            }
            if ($emailRecord && !empty($emailRecord->token) && Hash::check($token, $emailRecord->token)) {
                $record = $emailRecord;
            }
        }
        if (!$record) {
            return response()->json(['erro' => 'Token inválido ou expirado.'], 400);
        }

        if (empty($record->created_at)) {
            $record->delete();
            return response()->json(['erro' => 'Token inválido ou expirado.'], 400);
        }

        $createdAt = $record->created_at instanceof Carbon
            ? $record->created_at
            : Carbon::parse($record->created_at);
        if ($createdAt->diffInMinutes($now) >= $tokenTtlMinutes) {
            $record->delete();
            return response()->json(['erro' => 'Token inválido ou expirado.'], 400);
        }

        $usuario = Usuario::where('email', $email)->first();
        if (!$usuario) {
            return response()->json(['erro' => 'Usuário não encontrado.'], 404);
        }

        $usuario->senha = Hash::make($request->input('password'));
        $usuario->save();

        // remove token
        $record->delete();

        return response()->json(['mensagem' => 'Senha redefinida com sucesso.']);
    }

    // Web compatibility: mostra formulário simples ou redireciona
    public function showResetForm($token)
    {
        return response()->json(['token' => $token]);
    }

    public function resetPassword(Request $request)
    {
        return $this->resetarSenha($request);
    }

    private function buildResetEmailHtml(string $link, string $email): string
    {
        $appName = config('app.name', 'NexusFlow');
        $year = date('Y');
        $emailSafe = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
        $linkSafe = htmlspecialchars($link, ENT_QUOTES, 'UTF-8');

        return <<<HTML
<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Redefinir senha</title>
</head>
<body style="margin:0; padding:0; background:#f8fafc; font-family:Arial, sans-serif; color:#0f172a;">
  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafc; padding:24px 0;">
    <tr>
      <td align="center">
        <table role="presentation" width="640" cellpadding="0" cellspacing="0" style="background:#ffffff; border-radius:16px; overflow:hidden; box-shadow:0 12px 30px rgba(15,23,42,0.08);">
          <tr>
            <td style="background:linear-gradient(135deg,#2563eb 0%,#0ea5e9 100%); padding:24px 32px; color:#fff;">
              <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                <tr>
                  <td style="width:48px;">
                    <div style="width:44px; height:44px; background:#ffffff; border-radius:12px; display:flex; align-items:center; justify-content:center; color:#2563eb; font-weight:700; font-size:20px;">
                      N
                    </div>
                  </td>
                  <td style="padding-left:12px;">
                    <div style="font-size:20px; font-weight:700; line-height:1;">{$appName}</div>
                    <div style="font-size:13px; opacity:0.9;">Recuperacao de senha</div>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td style="padding:32px;">
              <h1 style="margin:0 0 12px 0; font-size:22px; color:#0f172a;">Redefinir sua senha</h1>
              <p style="margin:0 0 16px 0; font-size:15px; color:#475569; line-height:1.5;">
                Recebemos sua solicitacao de recuperacao de senha para o e-mail <strong>{$emailSafe}</strong>.
              </p>
              <p style="margin:0 0 22px 0; font-size:15px; color:#475569; line-height:1.5;">
                Clique no botao abaixo para criar uma nova senha. O link tem validade de <strong>24 horas</strong>.
              </p>
              <table role="presentation" cellpadding="0" cellspacing="0" style="margin:0 0 22px 0;">
                <tr>
                  <td align="center" bgcolor="#2563eb" style="border-radius:10px;">
                    <a href="{$linkSafe}" style="display:inline-block; padding:12px 22px; color:#ffffff; text-decoration:none; font-weight:600; font-size:15px;">
                      Redefinir senha
                    </a>
                  </td>
                </tr>
              </table>
              <p style="margin:0 0 8px 0; font-size:13px; color:#64748b;">
                Se voce nao solicitou esta alteracao, ignore este e-mail.
              </p>
              <p style="margin:0; font-size:13px; color:#94a3b8;">
                Ou copie e cole este link no navegador:<br>
                <span style="color:#2563eb; word-break:break-all;">{$linkSafe}</span>
              </p>
            </td>
          </tr>
          <tr>
            <td style="background:#f1f5f9; padding:16px 32px; font-size:12px; color:#64748b;">
              © {$year} {$appName}. Todos os direitos reservados.
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>
HTML;
    }

    private function buildResetEmailText(string $link): string
    {
        return "Recuperacao de senha\n\nClique no link para redefinir sua senha (validade 24h):\n{$link}\n\nSe voce nao solicitou esta alteracao, ignore este e-mail.";
    }
}
