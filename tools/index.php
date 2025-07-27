<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /FinApp/login/");
    exit();
}
$page_title = "Tools";
require '../php_logic/db_connect.php';
$user_id = $_SESSION['user_id'];
ob_start();
?>

<div class="page-container">
    <header class="page-header">
        <div class="header-icon-wrapper">
            <i class="fas fa-tools header-icon"></i>
        </div>
        <h1 class="page-title">Tools</h1>
        <p class="page-subtitle">Berbagai alat bantu untuk mengelola keuanganmu dengan lebih baik.</p>
    </header>

    <section class="card">
        <h2 class="card-title" style="text-align:left; font-size: 1.25rem;">Kalkulator Finansial</h2>
        <p>Gunakan kalkulator ini untuk menghitung pinjaman, investasi, atau tujuan finansial lainnya.</p>
    </section>

    <section class="card">
        <h2 class="card-title" style="text-align:left; font-size: 1.25rem;">Laporan PDF</h2>
        <p>Buat laporan keuanganmu dalam format PDF untuk dicetak atau disimpan.</p>
    </section>
</div>

<?php
$page_content = ob_get_clean();
require_once __DIR__ . '/../template.php';
?>