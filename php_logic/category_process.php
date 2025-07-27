<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    die("Akses ditolak.");
}

require 'db_connect.php';
$user_id = $_SESSION['user_id'];

// --- LOGIKA MENAMBAH KATEGORI ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_category'])) {
    $category_name = $_POST['category_name'];
    $type = $_POST['type'];
    $icon = $_POST['icon'] ?? 'fas fa-tag';
    $color_code = $_POST['color_code'] ?? '#6d28d9';

    if (empty($category_name) || !in_array($type, ['income', 'expense'])) {
        die("Nama kategori atau jenis tidak valid.");
    }

    $stmt = $conn->prepare("INSERT INTO categories (user_id, category_name, type, icon, color_code) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $user_id, $category_name, $type, $icon, $color_code);
    
    if ($stmt->execute()) {
        header("Location: /FinApp/categories/");
        exit();
    } else {
        die("Gagal menambahkan kategori.");
    }
}

// --- LOGIKA MENGHAPUS KATEGORI ---
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['delete_id'])) {
    $category_id = $_GET['delete_id'];

    if (empty($category_id)) {
        die("ID kategori tidak ditemukan.");
    }

    $stmt = $conn->prepare("DELETE FROM categories WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $category_id, $user_id);
    
    if ($stmt->execute()) {
        header("Location: /FinApp/categories/");
        exit();
    } else {
        die("Gagal menghapus kategori.");
    }
}

header("Location: /FinApp/dashboard/");
exit();
?>