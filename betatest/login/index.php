<?php
$status = isset($_GET['status']) ? htmlspecialchars($_GET['status']) : '';
$error = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - FinApp</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../css/auth.css">
</head>
<body>
    <div class="auth-container">
        <h1>Login</h1>
        <p class="subtitle">Silakan masuk untuk melanjutkan.</p>

        <?php
        if ($error == 'invalid_credentials') {
            echo '<p style="color: var(--brand-danger); margin-bottom: 1.5rem; font-size: 0.9rem;">Email atau password salah.</p>';
        }
        if ($status == 'unverified') {
            echo '<p style="color: var(--brand-danger); margin-bottom: 1.5rem; font-size: 0.9rem;">Akunmu belum diverifikasi. Kami telah mengirimkan ulang kode verifikasi ke emailmu.</p>';
        }
        if ($status == 'verified') {
            echo '<p style="color: #16a34a; margin-bottom: 1.5rem; font-size: 0.9rem;">Verifikasi berhasil! Silakan login.</p>';
        }
        ?>
        
        <form class="login-form" action="../php_logic/auth.php" method="POST">
            <input type="hidden" name="action" value="login">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required>
            </div>
            <button id="login-btn" type="submit" class="btn">Login</button>
        </form>
        <div class="link">
            Belum punya akun? <a href="/FinApp/betatest/register/">Daftar di sini</a>
        </div>
    </div>
</body>
</html>