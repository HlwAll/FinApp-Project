<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    die("Akses ditolak.");
}

require 'db_connect.php';
$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_transaction'])) {
    $type = $_POST['type'];
    $amount = $_POST['amount'];
    $description = $_POST['description'];
    $account_id = $_POST['account_id'];
    $category_id = $_POST['category_id'];
    $transaction_date = $_POST['transaction_date'];

    if (empty($amount) || !is_numeric($amount) || $amount <= 0) {
        die("Jumlah transaksi tidak valid.");
    }
    
    $conn->begin_transaction();
    try {
        $stmt = $conn->prepare("INSERT INTO transactions (user_id, account_id, category_id, type, amount, description, transaction_date) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iiisdss", $user_id, $account_id, $category_id, $type, $amount, $description, $transaction_date);
        $stmt->execute();
        
        if ($type == 'income') {
            $stmt = $conn->prepare("UPDATE accounts SET balance = balance + ? WHERE id = ? AND user_id = ?");
        } else {
            $stmt = $conn->prepare("UPDATE accounts SET balance = balance - ? WHERE id = ? AND user_id = ?");
        }
        $stmt->bind_param("dii", $amount, $account_id, $user_id);
        $stmt->execute();

        $conn->commit();
        header("Location: /FinApp/transactions/");
        exit();

    } catch (mysqli_sql_exception $e) {
        $conn->rollback();
        die("Gagal memproses transaksi: " . $e->getMessage());
    }
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['delete_id'])) {
    $transaction_id = $_GET['delete_id'];

    if (empty($transaction_id)) {
        die("ID transaksi tidak ditemukan.");
    }
    
    $conn->begin_transaction();
    try {
        $stmt = $conn->prepare("SELECT amount, type, account_id FROM transactions WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $transaction_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $transaction = $result->fetch_assoc();

        if ($transaction) {
            $amount = $transaction['amount'];
            $type = $transaction['type'];
            $account_id = $transaction['account_id'];

            $stmt = $conn->prepare("DELETE FROM transactions WHERE id = ? AND user_id = ?");
            $stmt->bind_param("ii", $transaction_id, $user_id);
            $stmt->execute();

            if ($type == 'income') {
                $stmt = $conn->prepare("UPDATE accounts SET balance = balance - ? WHERE id = ? AND user_id = ?");
            } else {
                $stmt = $conn->prepare("UPDATE accounts SET balance = balance + ? WHERE id = ? AND user_id = ?");
            }
            $stmt->bind_param("dii", $amount, $account_id, $user_id);
            $stmt->execute();
        }

        $conn->commit();
        header("Location: /FinApp/transactions/");
        exit();

    } catch (mysqli_sql_exception $e) {
        $conn->rollback();
        die("Gagal menghapus transaksi: " . $e->getMessage());
    }
}

header("Location: /FinApp/dashboard/");
exit();
?>