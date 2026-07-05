<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../db_config.php';

class Mailer {

    private function buildMailer(): PHPMailer {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = SMTP_HOST;
    $mail->SMTPAuth   = true;
    $mail->Username   = SMTP_USER;
    $mail->Password   = SMTP_PASS;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = SMTP_PORT;
    $mail->setFrom(SMTP_FROM, SMTP_FROM_NAME);
    $mail->CharSet    = 'UTF-8';
    $mail->SMTPOptions = [
        'ssl' => [
            'verify_peer'       => false,
            'verify_peer_name'  => false,
            'allow_self_signed' => true,
        ]
    ];
    return $mail;
}

    public function sendActivation(string $toEmail, string $toName, string $token): bool {
        try {
            $mail = $this->buildMailer();
            $mail->addAddress($toEmail, $toName);
            $mail->isHTML(true);
            $mail->Subject = 'Activate Your IT Job Portal Account';
            $link = BASE_URL . 'activate.php?token=' . urlencode($token);
            $mail->Body = "
                <h2>Welcome to IT Job Portal!</h2>
                <p>Hello {$toName},</p>
                <p>Click the link below to activate your account:</p>
                <p><a href='{$link}'>{$link}</a></p>
                <p>This link is valid for 24 hours.</p>
            ";
            $mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function sendPasswordReset(string $toEmail, string $toName, string $token): bool {
        try {
            $mail = $this->buildMailer();
            $mail->addAddress($toEmail, $toName);
            $mail->isHTML(true);
            $mail->Subject = 'IT Job Portal – Password Reset Request';
            $link = BASE_URL . 'reset_password.php?token=' . urlencode($token);
            $mail->Body = "
                <h2>Password Reset</h2>
                <p>Hello {$toName},</p>
                <p>Click the link below to reset your password:</p>
                <p><a href='{$link}'>{$link}</a></p>
                <p>This link expires in 1 hour. If you did not request this, ignore this email.</p>
            ";
            $mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
