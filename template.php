<?php 
$username = $_SESSION['username'] ?? 'Tamu';
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'FinApp'; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/FinApp/css/style.css">
</head>
<body class="preload">
    <button id="nav-toggle-btn" class="nav-toggle-btn"><i class="fas fa-bars"></i></button>
    <aside id="nav-panel" class="sidebar">
        <div class="sidebar-header">
            <h2 class="sidebar-title">FinApp</h2>
            <button id="mobile-nav-close" class="nav-close-btn"><i class="fas fa-times"></i></button>
        </div>
        <nav class="sidebar-nav">
            <a href="/FinApp/dashboard/" class="nav-link <?php echo ($current_page == 'index' && strpos($_SERVER['REQUEST_URI'], 'dashboard') !== false) ? 'active' : ''; ?>"><i class="fas fa-chart-pie nav-icon"></i><span>Dashboard</span></a>
            <a href="/FinApp/transactions/" class="nav-link <?php echo ($current_page == 'index' && strpos($_SERVER['REQUEST_URI'], 'transactions') !== false) ? 'active' : ''; ?>"><i class="fas fa-exchange-alt nav-icon"></i><span>Transaksi</span></a>
            <a href="/FinApp/accounts/" class="nav-link <?php echo ($current_page == 'index' && strpos($_SERVER['REQUEST_URI'], 'accounts') !== false) ? 'active' : ''; ?>"><i class="fas fa-wallet nav-icon"></i><span>Kelola Akun</span></a>
            <a href="/FinApp/categories/" class="nav-link <?php echo ($current_page == 'index' && strpos($_SERVER['REQUEST_URI'], 'categories') !== false) ? 'active' : ''; ?>"><i class="fas fa-tags nav-icon"></i><span>Kelola Kategori</span></a>
        </nav>
        <div class="sidebar-footer">
            <a href="/FinApp/php_logic/logout.php" class="nav-link" style="background-color: #ffe8e8; color: #dc3545;"><i class="fas fa-sign-out-alt nav-icon"></i><span>Logout</span></a>
        </div>
    </aside>
    <div id="nav-overlay" class="nav-overlay"></div>
    <main id="main-content" class="main-content">
        <?php echo $page_content ?? ''; ?>
    </main>
    <script src="/FinApp/js/script.js"></script>
</body>
</html>