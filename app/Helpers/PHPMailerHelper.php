<?php
namespace App\Helpers;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class PHPMailerHelper
{
    public static function send($to, $subject, $body, $altBody = '', $from = 'suporte@rdpsolutions.online', $fromName = 'SaaS MultiEmpresas')
    {
        $host = env('MAIL_HOST', 'smtp.gmail.com');
        $hostIp = env('MAIL_HOST_IP');
        $hosts = array_values(array_unique(array_filter([
            $host ?: null,
            $hostIp ?: null,
        ])));

        $encryption = strtolower((string) env('MAIL_ENCRYPTION', 'tls'));
        $portEnv = (int) env('MAIL_PORT');
        $timeout = (int) env('MAIL_TIMEOUT', 10);
        $overallTimeout = (int) env('MAIL_TIMEOUT_TOTAL', 20);
        $maxAttempts = (int) env('MAIL_MAX_ATTEMPTS', 2);
        $debugEnv = env('MAIL_DEBUG', null);
        $debug = null;
        if ($debugEnv !== null && $debugEnv !== '') {
            $debug = filter_var($debugEnv, FILTER_VALIDATE_BOOLEAN);
        }
        if ($debug === null) {
            $debug = function_exists('config') ? (bool) config('app.debug') : false;
        }
        $start = microtime(true);

        $variant = [
            'encryption' => $encryption,
            'port' => $portEnv ?: ($encryption === 'ssl' ? 465 : 587)
        ];

        $attempts = [];
        $lastError = 'Erro desconhecido.';

        $attemptCount = 0;
        $debugLines = [];

        if (count($hosts) === 0) {
            return $debug ? ['success' => false, 'error' => 'Host SMTP nao configurado.', 'debug' => []] : 'Host SMTP nao configurado.';
        }

        foreach ($hosts as $host) {
                if ($overallTimeout > 0 && (microtime(true) - $start) >= $overallTimeout) {
                    $lastError = 'Timeout total de envio excedido.';
                    break;
                }

                $key = $host . '|' . $variant['encryption'] . '|' . $variant['port'];
                if (isset($attempts[$key])) {
                    continue;
                }
                $attempts[$key] = true;
                $attemptCount++;
                if ($maxAttempts > 0 && $attemptCount > $maxAttempts) {
                    break;
                }

                $mail = new PHPMailer(true);
                try {
                    $timeoutToUse = $timeout;
                    if ($overallTimeout > 0 && $timeoutToUse > $overallTimeout) {
                        $timeoutToUse = $overallTimeout;
                    }
                    if ($timeoutToUse < 5) {
                        $timeoutToUse = 5;
                    }

                    @ini_set('default_socket_timeout', (string) $timeoutToUse);

                    $smtp = new \PHPMailer\PHPMailer\SMTP();
                    $smtp->Timeout = $timeoutToUse;
                    $smtp->Timelimit = $timeoutToUse;
                    $mail->setSMTPInstance($smtp);

                    $mail->isSMTP();
                    $mail->Host = $host;
                    $mail->SMTPAuth = true;
                    $mail->Username = env('MAIL_USERNAME');
                    $mail->Password = env('MAIL_PASSWORD');
                    if ($debug) {
                        $mail->SMTPDebug = 2;
                        $mail->Debugoutput = function ($str, $level) use (&$debugLines) {
                            $line = trim($str);
                            if ($line !== '') {
                                $debugLines[] = $line;
                            }
                            \Log::debug('SMTP: ' . $line);
                        };
                    }

                    if ($variant['encryption'] === 'ssl') {
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                    } else {
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    }
                    $mail->SMTPAutoTLS = false;
                    $mail->Port = (int) $variant['port'];
                    $mail->Timeout = $timeoutToUse;
                    $mail->SMTPKeepAlive = false;

                    $mail->SMTPOptions = [
                        'ssl' => [
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true,
                        ],
                    ];

                    $mail->CharSet = 'UTF-8';
                    $fromEmail = env('MAIL_FROM_ADDRESS', $from);
                    $fromLabel = env('MAIL_FROM_NAME', $fromName);
                    $mail->setFrom($fromEmail, $fromLabel);
                    $mail->addAddress($to);
                    $mail->isHTML(true);
                    $mail->Subject = $subject;
                    $mail->Body = $body;
                    $mail->AltBody = $altBody ?: strip_tags($body);

                    $mail->send();
                    if ($debug) {
                        \Log::info('SMTP enviado com sucesso.', [
                            'host' => $host,
                            'porta' => (int) $variant['port'],
                            'encryption' => $variant['encryption'],
                            'to' => $to,
                        ]);
                        return ['success' => true, 'debug' => $debugLines];
                    }
                    return true;
                } catch (Exception $e) {
                    $lastError = $mail->ErrorInfo ?: $e->getMessage();
                    if ($debug) {
                        \Log::warning('SMTP falhou.', [
                            'host' => $host,
                            'porta' => (int) $variant['port'],
                            'encryption' => $variant['encryption'],
                            'erro' => $lastError,
                        ]);
                    }
                }
        }

        if ($debug) {
            return ['success' => false, 'error' => $lastError, 'debug' => $debugLines];
        }
        return $lastError;
    }
}
