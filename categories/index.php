<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /FinApp/login/");
    exit();
}
$page_title = "Kelola Kategori";
require '../php_logic/db_connect.php';
$user_id = $_SESSION['user_id'];
$categories = [];
$stmt = $conn->prepare("SELECT id, category_name, type, icon, color_code FROM categories WHERE user_id = ? ORDER BY type DESC, category_name");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $categories[] = $row;
}
$stmt->close();
ob_start();
?>

<div class="page-container">
    <header class="page-header">
        <div class="header-icon-wrapper">
            <i class="fas fa-tags header-icon"></i>
        </div>
        <h1 class="page-title">Kelola Kategori</h1>
        <p class="page-subtitle">Tambah, lihat, atau hapus kategori untuk transaksi.</p>
    </header>

    <section class="card">
        <h2 class="card-title" style="text-align:left; font-size: 1.25rem;">Tambah Kategori Baru</h2>
        <form action="../php_logic/category_process.php" method="POST">
            <div class="form-group">
                <label for="category_name">Nama Kategori</label>
                <input type="text" id="category_name" name="category_name" required>
            </div>
            <div class="form-group">
                <label for="type">Jenis</label>
                <select id="type" name="type" required>
                    <option value="expense">Pengeluaran</option>
                    <option value="income">Pemasukan</option>
                </select>
            </div>
            <div class="form-group">
                <label for="color_code">Warna Kategori</label>
                <input type="color" id="color_code" name="color_code" value="#6d28d9" required>
            </div>
            <div class="form-group">
                <label for="icon">Ikon Kategori (Font Awesome)</label>
                <input type="text" id="icon" name="icon" value="fas fa-tag" placeholder="e.g., fas fa-utensils" required>
                <small>Cari ikon di <a href="https://fontawesome.com/v5/search" target="_blank">Font Awesome</a></small>
            </div>
            <button type="submit" name="add_category" class="btn btn-yellow">Tambah Kategori</button>
        </form>
    </section>

    <section class="card">
        <h2 class="card-title" style="text-align:left; font-size: 1.25rem;">Daftar Kategori Saya</h2>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Kategori</th>
                        <th>Jenis</th>
                        <th style="text-align:right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($categories)): ?>
                    <tr><td colspan="3" style="text-align:center; padding: 2rem;">Belum ada kategori.</td></tr>
                <?php else: ?>
                    <?php foreach ($categories as $cat): ?>
                        <tr>
                            <td>
                                <span class="category-display" style="background-color: <?php echo htmlspecialchars($cat['color_code']); ?>;">
                                    <i class="<?php echo htmlspecialchars($cat['icon']); ?>"></i>
                                </span>
                                <?php echo htmlspecialchars($cat['category_name']); ?>
                            </td>
                            <td class="<?php echo ($cat['type'] == 'income') ? 'text-income' : 'text-expense'; ?>">
                                <?php echo ucfirst($cat['type']); ?>
                            </td>
                            <td style="text-align:right;">
                                <a href="../php_logic/category_process.php?delete_id=<?php echo $cat['id']; ?>"
                                   onclick="return confirm('Yakin ingin menghapus kategori ini? Semua transaksi terkait akan hilang.');"
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
    </section>
</div>

<?php
$page_content = ob_get_clean();
require_once __DIR__ . '/../template.php';
?>