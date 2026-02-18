<?php

namespace App\Http\Controllers;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Illuminate\Support\Facades\Log;

class EmailController extends Controller
{
    /**
     * Envia um e-mail usando PHPMailer com base nas configurações do .env.
     *
     * @param string $destinatario - E-mail do destinatário.
     * @param string $assunto - Assunto do e-mail.
     * @param string $mensagemHtml - Corpo do e-mail em HTML.
     * @param string|null $anexo - Caminho opcional de um arquivo para anexar.
     * @return array - Status e mensagem do envio.
     */
    public static function enviarEmail($destinatario, $assunto, $mensagemHtml, $anexo = null)
    {
        $mail = new PHPMailer(true);

        try {
            // 🔧 Configurações do servidor SMTP
            $mail->isSMTP();
            $mail->Host       = env('MAIL_HOST', 'smtp.gmail.com');
            $mail->SMTPAuth   = true;
            $mail->Username   = env('MAIL_USERNAME');
            $mail->Password   = env('MAIL_PASSWORD');

            // 🔒 Ajuste de segurança e porta conforme configuração
            $encryption = strtolower(env('MAIL_ENCRYPTION', 'tls'));
            if ($encryption === 'ssl') {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port = 465;
            } else {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;
            }

            // 🎯 Configurações de encoding UTF-8
            $mail->CharSet = 'UTF-8';
            $mail->Encoding = 'base64';

            // 🕒 Timeout e debug
            $mail->Timeout = 30;
            $mail->SMTPDebug = 0;
            $mail->Debugoutput = function($str, $level) {
                Log::debug("PHPMailer [Level $level]: $str");
            };

            // 📤 Remetente
            $fromEmail = env('MAIL_FROM_ADDRESS', $mail->Username);
            $fromName  = env('MAIL_FROM_NAME', 'NexusFlow');
            $mail->setFrom($fromEmail, $fromName);

            // 📥 Destinatário
            $mail->addAddress($destinatario);

            // 📎 Anexo (opcional)
            if ($anexo && file_exists($anexo)) {
                $mail->addAttachment($anexo);
            }

            // 📨 Conteúdo do e-mail
            $mail->isHTML(true);
            $mail->Subject = $assunto;
            $mail->Body    = $mensagemHtml;
            
            // Versão texto simples simplificada
            $textoSimples = strip_tags($mensagemHtml);
            $textoSimples = html_entity_decode($textoSimples, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $textoSimples = preg_replace('/\s+/', ' ', $textoSimples);
            $mail->AltBody = wordwrap($textoSimples, 70);

            // 🚀 Envio
            $mail->send();

            Log::info("E-mail enviado com sucesso para: {$destinatario}");

            return [
                'status' => true,
                'mensagem' => "E-mail enviado com sucesso para {$destinatario}"
            ];
        } catch (Exception $e) {
            Log::error("Erro ao enviar e-mail para {$destinatario}: {$mail->ErrorInfo}");

            return [
                'status' => false,
                'mensagem' => "Erro ao enviar e-mail. Tente novamente mais tarde."
            ];
        }
    }
}