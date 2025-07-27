<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /FinApp/login/");
    exit();
}
$page_title = "Riwayat Transaksi";
require '../php_logic/db_connect.php';
$user_id = $_SESSION['user_id'];
$accounts = [];
$stmt = $conn->prepare("SELECT id, account_name FROM accounts WHERE user_id = ? ORDER BY account_name");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) { $accounts[] = $row; }
$stmt->close();
$categories = [];
$stmt = $conn->prepare("SELECT id, category_name FROM categories WHERE user_id = ? ORDER BY category_name");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) { $categories[] = $row; }
$stmt->close();
$transactions = [];
$stmt = $conn->prepare("
    SELECT t.id, t.description, t.amount, t.type, t.transaction_date, c.category_name, a.account_name 
    FROM transactions t
    JOIN categories c ON t.category_id = c.id
    JOIN accounts a ON t.account_id = a.id
    WHERE t.user_id = ? 
    ORDER BY t.transaction_date DESC, t.id DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) { $transactions[] = $row; }
$stmt->close();
ob_start();
?>
<div class="page-container">
    <header class="page-header">
        <div class="header-icon-wrapper">
            <i class="fas fa-exchange-alt header-icon"></i>
        </div>
        <h1 class="page-title">Transaksi</h1>
        <p class="page-subtitle">Catat pemasukan dan pengeluaran baru, serta lihat seluruh riwayat keuanganmu di sini.</p>
    </header>
    <section class="card">
        <h2 class="card-title" style="text-align:left; font-size: 1.25rem;">Tambah Transaksi</h2>
        <form action="../php_logic/transaction_process.php" method="POST">
            <div class="form-group"><label for="type">Jenis</label><select id="type" name="type" required><option value="expense">Pengeluaran</option><option value="income">Pemasukan</option></select></div>
            <div class="form-group"><label for="amount">Jumlah (Rp)</label><input type="number" step="1" id="amount" name="amount" required></div>
            <div class="form-group"><label for="description">Deskripsi</label><input type="text" id="description" name="description" required></div>
            <div class="form-group"><label for="account_id">Akun/Dompet</label><select id="account_id" name="account_id" required><?php foreach($accounts as $acc){ echo "<option value='{$acc['id']}'>".htmlspecialchars($acc['account_name'])."</option>"; } ?></select></div>
            <div class="form-group"><label for="category_id">Kategori</label><select id="category_id" name="category_id" required><?php foreach($categories as $cat){ echo "<option value='{$cat['id']}'>".htmlspecialchars($cat['category_name'])."</option>"; } ?></select></div>
            <div class="form-group"><label for="transaction_date">Tanggal</label><input type="date" id="transaction_date" name="transaction_date" value="<?php echo date('Y-m-d'); ?>" required></div>
            <button type="submit" name="add_transaction" class="tool-button btn-yellow">Simpan Transaksi</button>
        </form>
    </section>
    <section class="card">
        <h2 class="card-title" style="text-align:left; font-size: 1.25rem;">Riwayat Transaksi</h2>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Deskripsi</th>
                        <th>Kategori</th>
                        <th>Akun</th>
                        <th style="text-align:right;">Jumlah</th>
                        <th style="text-align:right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($transactions)): ?>
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <i class="fas fa-file-invoice-dollar"></i>
                                <h4>Riwayat Masih Kosong</h4>
                                <p>Mulai catat transaksi pertamamu pada form di atas.</p>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($transactions as $trans): ?>
                        <tr>
                            <td style="white-space:nowrap;"><?php echo date('d M Y', strtotime($trans['transaction_date'])); ?></td>
                            <td><?php echo htmlspecialchars($trans['description']); ?></td>
                            <td><?php echo htmlspecialchars($trans['category_name']); ?></td>
                            <td><?php echo htmlspecialchars($trans['account_name']); ?></td>
                            <td class="<?php echo $trans['type'] == 'income' ? 'text-income' : 'text-expense'; ?>" style="text-align:right; font-weight:600; white-space:nowrap;">
                                <?php echo ($trans['type'] == 'income' ? '+' : '-') . 'Rp ' . number_format($trans['amount'], 0, ',', '.'); ?>
                            </td>
                            <td style="text-align:right;">
                                 <a href="#" class="delete-btn" data-delete-id="<?php echo $trans['id']; ?>" style="color: var(--brand-danger); text-decoration:none;">
                                    <i class="fas fa-trash"></i>
                                 </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>
<div id="delete-modal" class="modal-overlay">
    <div class="modal-card">
        <div class="icon-danger"><i class="fas fa-exclamation-triangle"></i></div>
        <h3>Yakin Ingin Menghapus?</h3>
        <p>Transaksi ini akan dihapus permanen dan saldo akun akan disesuaikan.</p>
        <div class="modal-actions">
            <button id="cancel-delete-btn" class="btn btn-secondary">Batal</button>
            <a href="#" id="confirm-delete-btn" class="btn btn-danger">Hapus</a>
        </div>
    </div>
</div>
<?php
$page_content = ob_get_clean();
require_once __DIR__ . '/../template.php';
?>