<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

function sendContactEmail($name, $email, $subject, $message) {
    $config = require __DIR__ . '/../config/email.php';

    $mail = new PHPMailer(true);

    try {
        // Always use SMTP (InfinityFree does not allow mail()/sendmail)
        $mail->isSMTP();
        $mail->Host       = $config['smtp_host'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $config['smtp_user'];
        $mail->Password   = $config['smtp_pass'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = $config['smtp_port'];
        $mail->Timeout    = $config['timeout'] ?? 60;

        // Extra SSL options (for shared hosting / self-signed certs)
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ],
        ];

        // Charset
        $mail->CharSet = 'UTF-8';

        // Recipients
        $mail->setFrom($config['from_email'], $config['from_name']);
        $mail->addAddress($config['from_email'], $config['from_name']); // receive yourself
        $mail->addReplyTo($email, $name); // reply goes to sender

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'New Contact Form Submission: ' . $subject;

        $mail->Body = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #f4f4f4; padding: 20px; border-radius: 5px; margin-bottom: 20px; }
                .content { background-color: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
                .field { margin-bottom: 15px; }
                .label { font-weight: bold; color: #555; }
                .message-box { background-color: #f9f9f9; padding: 15px; border-left: 4px solid #007cba; margin-top: 10px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2 style='margin: 0; color: #333;'>New Contact Form Submission</h2>
                </div>
                <div class='content'>
                    <div class='field'>
                        <span class='label'>Name:</span><br>
                        " . htmlspecialchars($name) . "
                    </div>
                    <div class='field'>
                        <span class='label'>Email:</span><br>
                        <a href='mailto:" . htmlspecialchars($email) . "'>" . htmlspecialchars($email) . "</a>
                    </div>
                    <div class='field'>
                        <span class='label'>Subject:</span><br>
                        " . htmlspecialchars($subject) . "
                    </div>
                    <div class='field'>
                        <span class='label'>Message:</span>
                        <div class='message-box'>
                            " . nl2br(htmlspecialchars($message)) . "
                        </div>
                    </div>
                    <div class='field' style='margin-top: 20px; font-size: 12px; color: #666;'>
                        <span class='label'>Submitted on:</span><br>
                        " . date('F j, Y \a\t g:i A') . "
                    </div>
                </div>
            </div>
        </body>
        </html>";

        // Plain text fallback
        $mail->AltBody = "New Contact Form Submission\n\n" .
                        "Name: {$name}\n" .
                        "Email: {$email}\n" .
                        "Subject: {$subject}\n\n" .
                        "Message:\n{$message}\n\n" .
                        "Submitted on: " . date('F j, Y \a\t g:i A');

        return $mail->send();
    } catch (Exception $e) {
        error_log("Mailer Error: " . $mail->ErrorInfo);
        return false;
    }
}
