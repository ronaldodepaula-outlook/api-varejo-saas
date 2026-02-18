<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$mail = new PHPMailer(true);

try {
    // Configurações do servidor SMTP do Gmail
    $mail->isSMTP();
    $mail->Host       = 'mail.rdpsolutions.online';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'suporte@rdpsolutions.online'; // Seu e-mail Gmail
    $mail->Password   = 'Ti@Msl0912';        // Senha de app do Google
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // Remetente e destinatário
    $mail->setFrom('suporte@rdpsolutions.online', 'Seu Nome');
    $mail->addAddress('ronaldodepaulasurf@yahoo.com.br', 'Destinatário');

    // Conteúdo do e-mail
    $mail->isHTML(true);
    $mail->Subject = 'Assunto do E-mail';
    $mail->Body    = 'Conteúdo <b>HTML</b> do e-mail.';
    $mail->AltBody = 'Conteúdo texto do e-mail.';

    $mail->send();
    echo 'E-mail enviado com sucesso!';
} catch (Exception $e) {
    echo "Erro ao enviar e-mail: {$mail->ErrorInfo}";
}
?>