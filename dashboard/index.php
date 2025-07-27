<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /FinApp/login/");
    exit();
}
$page_title = "Dashboard";
require '../php_logic/db_connect.php';
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT SUM(balance) as total_balance FROM accounts WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$total_balance = $result->fetch_assoc()['total_balance'] ?? 0;
$first_day_month = date('Y-m-01');
$last_day_month = date('Y-m-t');
$stmt = $conn->prepare("SELECT SUM(amount) as total_income FROM transactions WHERE user_id = ? AND type = 'income' AND transaction_date BETWEEN ? AND ?");
$stmt->bind_param("iss", $user_id, $first_day_month, $last_day_month);
$stmt->execute();
$result = $stmt->get_result();
$total_income = $result->fetch_assoc()['total_income'] ?? 0;
$stmt = $conn->prepare("SELECT SUM(amount) as total_expense FROM transactions WHERE user_id = ? AND type = 'expense' AND transaction_date BETWEEN ? AND ?");
$stmt->bind_param("iss", $user_id, $first_day_month, $last_day_month);
$stmt->execute();
$result = $stmt->get_result();
$total_expense = $result->fetch_assoc()['total_expense'] ?? 0;
ob_start();
?>
<div class="page-container">
    <header class="page-header">
        <div class="header-icon-wrapper">
            <i class="fas fa-chart-pie header-icon"></i>
        </div>
        <h1 class="page-title">Dashboard</h1>
        <p class="page-subtitle">Selamat datang di FinApp, ini adalah ringkasan keuanganmu bulan ini.</p>
    </header>
    <section class="tools-grid">
        <div class="tool-card card">
            <div class="tool-header">
                <i class="fas fa-wallet tool-icon"></i>
                <h3 class="tool-title">Total Saldo</h3>
            </div>
            <h2 style="font-size: 2rem; text-align: center;">Rp <?php echo number_format($total_balance, 0, ',', '.'); ?></h2>
        </div>
        <div class="tool-card card">
            <div class="tool-header">
                <i class="fas fa-arrow-up tool-icon" style="color: green;"></i>
                <h3 class="tool-title">Pemasukan Bulan Ini</h3>
            </div>
             <h2 style="font-size: 2rem; text-align: center; color: green;">Rp <?php echo number_format($total_income, 0, ',', '.'); ?></h2>
        </div>
        <div class="tool-card card">
            <div class="tool-header">
                <i class="fas fa-arrow-down tool-icon" style="color: red;"></i>
                <h3 class="tool-title">Pengeluaran Bulan Ini</h3>
            </div>
             <h2 style="font-size: 2rem; text-align: center; color: red;">Rp <?php echo number_format($total_expense, 0, ',', '.'); ?></h2>
        </div>
    </section>
     <section class="card">
        <h2 class="card-title" style="text-align:left; font-size: 1.25rem;">Akses Cepat</h2>
        <div class="tool-actions" style="display: flex; gap: 1rem;">
             <a href="/FinApp/transactions/" class="tool-button btn-yellow"><i class="fas fa-plus"></i> Tambah Transaksi</a>
             <a href="/FinApp/accounts/" class="tool-button"><i class="fas fa-wallet"></i> Kelola Akun</a>
        </div>
    </section>
</div>
<?php
$page_content = ob_get_clean();
require_once __DIR__ . '/../template.php';
?>