<?php

namespace App\Http\Controllers;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Illuminate\Support\Facades\Log;

class EmailController extends Controller
{
    /**
     * Envia um e-mail usando PHPMailer com base nas configuracoes do .env.
     *
     * @param string $destinatario - E-mail do destinatario.
     * @param string $assunto - Assunto do e-mail.
     * @param string $mensagemHtml - Corpo do e-mail em HTML.
     * @param string|null $anexo - Caminho opcional de um arquivo para anexar.
     * @return array - Status e mensagem do envio.
     */
    public static function enviarEmail($destinatario, $assunto, $mensagemHtml, $anexo = null)
    {
        $hosts = array_values(array_filter([
            env('MAIL_HOST'),
            env('MAIL_HOST_IP')
        ]));
        if (empty($hosts)) {
            $hosts = ['smtp.gmail.com'];
        }

        $encryption = strtolower((string) env('MAIL_ENCRYPTION', 'tls'));
        $portEnv = (int) env('MAIL_PORT');
        $timeout = (int) env('MAIL_TIMEOUT', 30);

        $variants = [];
        $variants[] = [
            'encryption' => $encryption,
            'port' => $portEnv ?: ($encryption === 'ssl' ? 465 : 587)
        ];
        $altEncryption = $encryption === 'ssl' ? 'tls' : 'ssl';
        $variants[] = [
            'encryption' => $altEncryption,
            'port' => $altEncryption === 'ssl' ? 465 : 587
        ];

        $attempts = [];
        foreach ($hosts as $host) {
            foreach ($variants as $variant) {
                $key = $host . '|' . $variant['encryption'] . '|' . $variant['port'];
                if (isset($attempts[$key])) {
                    continue;
                }
                $attempts[$key] = true;

                $mail = new PHPMailer(true);
                try {
                    // Configuracoes do servidor SMTP
                    $mail->isSMTP();
                    $mail->Host = $host;
                    $mail->SMTPAuth = true;
                    $mail->Username = env('MAIL_USERNAME');
                    $mail->Password = env('MAIL_PASSWORD');

                    // Seguranca e porta
                    if ($variant['encryption'] === 'ssl') {
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                    } else {
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    }
                    $mail->SMTPAutoTLS = false;
                    $mail->Port = (int) $variant['port'];
                    $mail->Timeout = $timeout;

                    $mail->SMTPOptions = [
                        'ssl' => [
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true,
                        ],
                    ];

                    // Encoding UTF-8
                    $mail->CharSet = 'UTF-8';
                    $mail->Encoding = 'base64';

                    // Debug
                    $mail->SMTPDebug = 0;
                    $mail->Debugoutput = function ($str, $level) {
                        Log::debug("PHPMailer [Level $level]: $str");
                    };

                    // Remetente
                    $fromEmail = env('MAIL_FROM_ADDRESS', $mail->Username);
                    $fromName = env('MAIL_FROM_NAME', 'NexusFlow');
                    $mail->setFrom($fromEmail, $fromName);

                    // Destinatario
                    $mail->addAddress($destinatario);

                    // Anexo (opcional)
                    if ($anexo && file_exists($anexo)) {
                        $mail->addAttachment($anexo);
                    }

                    // Conteudo do e-mail
                    $mail->isHTML(true);
                    $mail->Subject = $assunto;
                    $mail->Body = $mensagemHtml;

                    // Versao texto simples
                    $textoSimples = strip_tags($mensagemHtml);
                    $textoSimples = html_entity_decode($textoSimples, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    $textoSimples = preg_replace('/\s+/', ' ', $textoSimples);
                    $mail->AltBody = wordwrap($textoSimples, 70);

                    // Envio
                    $mail->send();

                    Log::info("E-mail enviado com sucesso para: {$destinatario}");
                    return [
                        'status' => true,
                        'mensagem' => "E-mail enviado com sucesso para {$destinatario}"
                    ];
                } catch (Exception $e) {
                    Log::error("Erro ao enviar e-mail para {$destinatario} ({$host}:{$variant['port']}/{$variant['encryption']}): {$mail->ErrorInfo}");
                }
            }
        }

        return [
            'status' => false,
            'mensagem' => 'Erro ao enviar e-mail. Tente novamente mais tarde.'
        ];
    }
}
