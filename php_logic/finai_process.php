<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    exit();
}
require 'db_connect.php';
$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Logic untuk menghapus semua chat
    if (isset($_POST['action']) && $_POST['action'] == 'clear_all_chats') {
        $stmt = $conn->prepare("DELETE FROM finai_chat WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        echo json_encode(['status' => 'success']);
        exit();
    }

    // Logic untuk mendapatkan chat dari sesi tertentu
    if (isset($_POST['action']) && $_POST['action'] == 'get_chat_session') {
        $session_id = $_POST['session_id'];
        $chat = [];
        $stmt = $conn->prepare("SELECT sender, message FROM finai_chat WHERE user_id = ? AND chat_session_id = ? ORDER BY timestamp ASC");
        $stmt->bind_param("ii", $user_id, $session_id);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $chat[] = $row;
        }
        echo json_encode(['chat' => $chat]);
        exit();
    }

    // Logic untuk mengirim pesan baru
    if (isset($_POST['message'])) {
        $user_message = trim($_POST['message']);
        $chat_session_id = $_POST['chat_session_id'] ?? null;

        if ($chat_session_id === null || empty($chat_session_id)) {
            // Jika sesi baru, buat ID sesi baru
            $stmt = $conn->prepare("SELECT MAX(chat_session_id) as max_id FROM finai_chat WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $chat_session_id = ($row['max_id'] ?? 0) + 1;
        }

        // Simpan pesan user ke database
        $stmt_user = $conn->prepare("INSERT INTO finai_chat (user_id, chat_session_id, sender, message) VALUES (?, ?, 'user', ?)");
        $stmt_user->bind_param("iis", $user_id, $chat_session_id, $user_message);
        $stmt_user->execute();
        
        $message = strtolower($user_message);
        
        // Logika respons AI sederhana
        if (strpos($message, 'halo') !== false || strpos($message, 'hai') !== false) {
            $response = "Halo! Ada yang bisa saya bantu terkait keuanganmu?";
        } elseif (strpos($message, 'keluar') !== false || strpos($message, 'pengeluaran') !== false) {
            $response = "Tentu. Kamu bisa catat pengeluaran di menu Transaksi. Apa kamu mau saya hitung total pengeluaranmu bulan ini?";
        } elseif (strpos($message, 'saldo') !== false) {
            $response = "Untuk melihat saldo, kamu bisa cek di dashboard atau menu Kelola Akun.";
        } elseif (strpos($message, 'hemat') !== false) {
            $response = "Tips hemat: Buat anggaran bulanan, catat setiap pengeluaran, dan hindari pengeluaran impulsif.";
        } else {
            $response = "Maaf, saya tidak mengerti. Coba tanyakan hal lain seputar keuangan.";
        }
        
        // Simpan pesan bot ke database
        $stmt_bot = $conn->prepare("INSERT INTO finai_chat (user_id, chat_session_id, sender, message) VALUES (?, ?, 'bot', ?)");
        $stmt_bot->bind_param("iis", $user_id, $chat_session_id, $response);
        $stmt_bot->execute();
        
        echo json_encode(['response' => $response, 'chat_session_id' => $chat_session_id]);
        exit();
    }
}
?>