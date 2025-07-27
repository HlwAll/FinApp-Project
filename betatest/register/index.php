<?php
session_start();
$error = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - FinApp</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../css/auth.css">
</head>
<body>
    <div class="auth-container">
        <h1>Daftar Akun</h1>
        <p class="subtitle">Buat akun baru untuk mulai mengelola keuangan.</p>

        <?php
        if ($error == 'email_exists') {
            echo '<p style="color: var(--brand-danger); margin-bottom: 1.5rem; font-size: 0.9rem;">Email sudah terdaftar. Silakan gunakan email lain.</p>';
        }
        if ($error == 'email_send_failed') {
            echo '<p style="color: var(--brand-danger); margin-bottom: 1.5rem; font-size: 0.9rem;">Gagal mengirim email verifikasi. Mohon coba lagi.</p>';
        }
        ?>
        
        <form class="register-form" action="../php_logic/auth.php" method="POST">
            <input type="hidden" name="action" value="register">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required>
            </div>
            <button id="register-btn" type="submit" class="btn">Register</button>
        </form>
        <div class="link">
            Sudah punya akun? <a href="/FinApp/betatest/login/">Login di sini</a>
        </div>
    </div>
</body>
</html>