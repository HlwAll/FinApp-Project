<?php
require 'db_connect.php';
require 'email_sender.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'];

    if ($action == 'register') {
        $email = $_POST['email'];
        $password = $_POST['password'];

        try {
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            if ($stmt->get_result()->num_rows > 0) {
                header("Location: /FinApp/register/?error=email_exists");
                exit();
            }

            $hash = password_hash($password, PASSWORD_DEFAULT);
            $verification_code = strval(random_int(100000, 999999));

            $stmt = $conn->prepare("INSERT INTO users (email, password_hash, verification_code) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $email, $hash, $verification_code);

            if ($stmt->execute()) {
                if (sendVerificationEmail($email, $verification_code)) {
                    $_SESSION['verifying_email'] = $email;
                    header("Location: /FinApp/verify/");
                    exit();
                } else {
                    header("Location: /FinApp/register/?error=email_send_failed");
                    exit();
                }
            } else {
                die("Registrasi Gagal.");
            }
        } catch (mysqli_sql_exception $e) {
            die("Registrasi Gagal: " . $e->getMessage());
        }
    }

    if ($action == 'verify') {
        $email = $_SESSION['verifying_email'] ?? '';
        $code = $_POST['code'];

        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND verification_code = ?");
        $stmt->bind_param("ss", $email, $code);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $stmt = $conn->prepare("UPDATE users SET is_verified = 1, verification_code = NULL WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();

            unset($_SESSION['verifying_email']);
            header("Location: /FinApp/login/?status=verified");
            exit();
        } else {
            header("Location: /FinApp/verify/?error=invalid_code");
            exit();
        }
    }

    if ($action == 'login') {
        $email = $_POST['email'];
        $password = $_POST['password'];

        $stmt = $conn->prepare("SELECT id, email, password_hash, is_verified FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            if (password_verify($password, $user['password_hash'])) {
                if ($user['is_verified'] == 1) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['email'] = $user['email'];
                    header("Location: /FinApp/dashboard/");
                    exit();
                } else {
                    $new_code = strval(random_int(100000, 999999));
                    
                    $stmt_update = $conn->prepare("UPDATE users SET verification_code = ? WHERE email = ?");
                    $stmt_update->bind_param("ss", $new_code, $email);
                    $stmt_update->execute();
                    sendVerificationEmail($email, $new_code);

                    $_SESSION['verifying_email'] = $email;
                    header("Location: /FinApp/verify/?status=unverified");
                    exit();
                }
            }
        }
        header("Location: /FinApp/login/?error=invalid_credentials");
        exit();
    }
}
?>