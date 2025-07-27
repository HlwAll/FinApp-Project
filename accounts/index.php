<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /FinApp/login/");
    exit();
}
$page_title = "Kelola Akun";
require '../php_logic/db_connect.php';
$user_id = $_SESSION['user_id'];
$accounts = [];
$stmt = $conn->prepare("SELECT id, account_name, balance FROM accounts WHERE user_id = ? ORDER BY account_name");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $accounts[] = $row;
}
$stmt->close();
ob_start();
?>
<div class="page-container">
    <header class="page-header">
        <div class="header-icon-wrapper">
            <i class="fas fa-wallet header-icon"></i>
        </div>
        <h1 class="page-title">Kelola Akun</h1>
        <p class="page-subtitle">Tambah, lihat, atau hapus akun & dompet keuanganmu di sini.</p>
    </header>
    <section class="tools-grid" style="grid-template-columns: 4fr 8fr; gap: 2rem;">
        <div class="card">
            <h2 class="card-title" style="text-align:left; font-size: 1.25rem;">Tambah Akun Baru</h2>
            <form action="../php_logic/account_process.php" method="POST">
                <div class="form-group">
                    <label for="account_name">Nama Akun (e.g., BCA, GoPay)</label>
                    <input type="text" id="account_name" name="account_name" required>
                </div>
                <div class="form-group">
                    <label for="balance">Saldo Awal (Rp)</label>
                    <input type="number" step="1" id="balance" name="balance" value="0" required>
                </div>
                <button type="submit" name="add_account" class="btn btn-yellow">Tambah Akun</button>
            </form>
        </div>
        <div class="card">
            <h2 class="card-title" style="text-align:left; font-size: 1.25rem;">Daftar Akun Saya</h2>
            <div style="overflow-x:auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Nama Akun</th>
                            <th style="text-align:right;">Saldo</th>
                            <th style="text-align:right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($accounts)): ?>
                        <tr><td colspan="3" style="text-align:center; padding: 2rem;">Belum ada akun.</td></tr>
                    <?php else: ?>
                        <?php foreach ($accounts as $account): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($account['account_name']); ?></td>
                                <td style="text-align:right; font-weight:600;">
                                    Rp <?php echo number_format($account['balance'], 0, ',', '.'); ?>
                                </td>
                                <td style="text-align:right;">
                                    <a href="../php_logic/account_process.php?delete_id=<?php echo $account['id']; ?>"
                                       onclick="return confirm('Yakin ingin menghapus akun ini? Semua transaksi terkait akan hilang.');"
                                       style="color: var(--brand-danger); text-decoration:none;">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
<?php
$page_content = ob_get_clean();
require_once __DIR__ . '/../template.php';
?>