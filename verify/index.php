<?php
session_start();
$error = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';
$status = isset($_GET['status']) ? htmlspecialchars($_GET['status']) : '';
$email = $_SESSION['verifying_email'] ?? '';

if (empty($email)) {
    header("Location: ../register/");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Akun - FinApp</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/verify.css">
</head>
<body>
    <div class="verify-container">
        <div class="icon-wrapper">
            <i class="fas fa-envelope-open-text"></i>
        </div>
        <h1>Verifikasi Akun</h1>
        
        <?php if ($status == 'unverified'): ?>
             <p class="subtitle error-message">Akunmu belum diverifikasi. Kami telah mengirimkan ulang kode.</p>
        <?php else: ?>
             <p class="subtitle">Kami sudah mengirim kode verifikasi 6 digit ke email <b><?php echo htmlspecialchars($email); ?></b>.</p>
        <?php endif; ?>
        
        <p class="subtitle">Mohon cek emailmu dan masukkan kodenya di bawah.</p>
        
        <?php if ($error == 'invalid_code'): ?>
            <p class="error-message">Kode verifikasi salah. Mohon periksa kembali.</p>
        <?php endif; ?>

        <form action="../php_logic/auth.php" method="POST">
            <input type="hidden" name="action" value="verify">
            <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
            <div class="form-group">
                <input type="tel" name="code" id="code" inputmode="numeric" pattern="[0-9]*" maxlength="6" required>
            </div>
            <button type="submit" class="btn">Verifikasi</button>
        </form>
        <div class="link">
            <a href="/FinApp/login/">Kembali ke halaman login</a>
        </div>
    </div>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const codeInput = document.getElementById('code');

        // Menghapus karakter non-angka saat user mengetik
        codeInput.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        // Mencegah paste karakter non-angka
        codeInput.addEventListener('paste', function(e) {
            const pasteData = e.clipboardData.getData('text');
            if (/[^0-9]/.test(pasteData)) {
                e.preventDefault();
            }
        });
    });
    </script>
</body>
</html>