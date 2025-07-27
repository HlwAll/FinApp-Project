<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    die("Akses ditolak.");
}

require 'db_connect.php';
$user_id = $_SESSION['user_id'];

// --- LOGIKA MENAMBAH AKUN ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_account'])) {
    $account_name = $_POST['account_name'];
    $balance = $_POST['balance'];

    if (empty($account_name) || !is_numeric($balance)) {
        die("Nama akun dan saldo awal tidak valid.");
    }

    $stmt = $conn->prepare("INSERT INTO accounts (user_id, account_name, balance) VALUES (?, ?, ?)");
    $stmt->bind_param("isd", $user_id, $account_name, $balance);
    
    if ($stmt->execute()) {
        header("Location: /FinApp/accounts/");
        exit();
    } else {
        die("Gagal menambahkan akun.");
    }
}

// --- LOGIKA MENGHAPUS AKUN ---
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['delete_id'])) {
    $account_id = $_GET['delete_id'];

    if (empty($account_id)) {
        die("ID akun tidak ditemukan.");
    }

    $stmt = $conn->prepare("DELETE FROM accounts WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $account_id, $user_id);
    
    if ($stmt->execute()) {
        header("Location: /FinApp/accounts/");
        exit();
    } else {
        die("Gagal menghapus akun.");
    }
}

header("Location: /FinApp/dashboard/");
exit();
?>