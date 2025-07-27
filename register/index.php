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
    <link rel="stylesheet" href="../css/auth.css">
</head>
<body>
    <button id="nav-toggle-btn" class="nav-toggle-btn"><i class="fas fa-bars"></i></button>
    <aside id="nav-panel" class="sidebar">
        <div class="sidebar-header">
            <h2 class="sidebar-title">FinApp</h2>
            <button id="mobile-nav-close" class="nav-close-btn"><i class="fas fa-times"></i></button>
        </div>
        <nav class="sidebar-nav">
            <a href="/FinApp/login/" class="nav-link"><i class="fas fa-sign-in-alt nav-icon"></i><span>Login</span></a>
            <a href="/FinApp/register/" class="nav-link active"><i class="fas fa-user-plus nav-icon"></i><span>Register</span></a>
            <a href="https://tools-hlwall.fwh.is/" class="nav-link"><i class="fas fa-tools nav-icon"></i><span>HlwAll Tools</span></a>
        </nav>
    </aside>
    <div id="nav-overlay" class="nav-overlay"></div>
    <main id="main-content" class="main-content">
        <div class="auth-card">
            <h1>Register</h1>
            <p class="subtitle">Buat akun baru untuk mulai mengelola keuangan.</p>

            <?php
            if ($error == 'email_exists') {
                echo '<p style=" text-align: center; color: var(--brand-danger); margin-bottom: 1.5rem; font-size: 0.9rem;">Email sudah terdaftar. Silakan gunakan email lain.</p>';
            }
            if ($error == 'email_send_failed') {
                echo '<p style=" text-align: center; color: var(--brand-danger); margin-bottom: 1.5rem; font-size: 0.9rem;">Gagal mengirim email verifikasi. Mohon coba lagi.</p>';
            }
            ?>
            
            <a href="#" class="btn btn-github">
                <i class="fab fa-github"></i> Daftar dengan GitHub
            </a>

            <div class="divider"><span>atau</span></div>

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
        </div>
    </main>
    <script src="../js/loading.js"></script>
    <script>
    // Logika untuk Sidebar Mobile
    document.addEventListener('DOMContentLoaded', function() {
        const navPanel = document.getElementById('nav-panel');
        const navToggleBtn = document.getElementById('nav-toggle-btn');
        const navCloseBtn = document.getElementById('mobile-nav-close');
        const navOverlay = document.getElementById('nav-overlay');

        if (navToggleBtn) {
            navToggleBtn.addEventListener('click', () => {
                navPanel.classList.add('open');
                navOverlay.style.display = 'block';
            });
        }
        if (navCloseBtn) {
            navCloseBtn.addEventListener('click', () => {
                navPanel.classList.remove('open');
                navOverlay.style.display = 'none';
            });
        }
        if (navOverlay) {
            navOverlay.addEventListener('click', () => {
                navPanel.classList.remove('open');
                navOverlay.style.display = 'none';
            });
        }
    });
    </script>
</body>
</html>