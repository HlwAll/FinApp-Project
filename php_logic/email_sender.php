<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

function sendVerificationEmail($recipientEmail, $verificationCode) {
    $mail = new PHPMailer(true);

    try {
        // --- DI SINI KAMU GANTI EMAIL DAN PASSWORDNYA ---
        $smtp_host = 'smtp.gmail.com'; 
        $smtp_user = 'Lazaverify@gmail.com'; // GANTI INI DENGAN EMAIL ASLIMU
        $smtp_pass = 'yzsu kerq tacq jljc'; // GANTI INI DENGAN PASSWORD APLIKASIMU
        $from_email = 'Lazaverify@gmail.com'; // GANTI INI DENGAN EMAIL ASLIMU

        $mail->isSMTP();
        $mail->Host       = $smtp_host;
        $mail->SMTPAuth   = true;
        $mail->Username   = $smtp_user;
        $mail->Password   = $smtp_pass;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom($from_email, 'FinApp');
        $mail->addAddress($recipientEmail);

        $mail->isHTML(true);
        $mail->Subject = 'Kode Verifikasi Akun FinApp';
        $mail->Body    = "
            <h1>Verifikasi Akun FinApp</h1>
            <p>Halo,</p>
            <p>Terima kasih telah mendaftar di FinApp. Silakan gunakan kode verifikasi ini untuk mengaktifkan akunmu:</p>
            <h2 style='color: #6d28d9;'>$verificationCode</h2>
            <p>Jika kamu tidak merasa melakukan pendaftaran ini, abaikan saja email ini.</p>
        ";
        $mail->AltBody = "Kode verifikasi akunmu adalah: $verificationCode";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Gagal mengirim email: {$mail->ErrorInfo}");
        return false;
    }
}