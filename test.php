<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/db_config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = SMTP_USER;
    $mail->Password   = SMTP_PASS;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
    $mail->SMTPDebug  = 2;
    $mail->SMTPOptions = [
        'ssl' => [
            'verify_peer'       => false,
            'verify_peer_name'  => false,
            'allow_self_signed' => true,
        ]
    ];
    $mail->setFrom(SMTP_USER, 'Test');
    $mail->addAddress(SMTP_USER);
    $mail->Subject = 'Test';
    $mail->Body    = 'Test email';
    $mail->send();
    echo '<br><b style="color:green">Email poslat uspesno!</b>';
} catch (Exception $e) {
    echo '<br><b style="color:red">Greska: ' . $mail->ErrorInfo . '</b>';
}
?>