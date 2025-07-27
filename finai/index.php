<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /FinApp/login/");
    exit();
}
$page_title = "FinAI";
require '../php_logic/db_connect.php';
$user_id = $_SESSION['user_id'];

$chat_history = [];
$stmt = $conn->prepare("SELECT DISTINCT chat_session_id FROM finai_chat WHERE user_id = ? ORDER BY chat_session_id DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$chat_sessions = [];
while ($row = $result->fetch_assoc()) {
    $session_id = $row['chat_session_id'];
    $first_message = '';

    // Dapatkan pesan pertama dari setiap sesi
    $stmt_msg = $conn->prepare("SELECT message FROM finai_chat WHERE user_id = ? AND chat_session_id = ? AND sender = 'user' ORDER BY timestamp ASC LIMIT 1");
    $stmt_msg->bind_param("ii", $user_id, $session_id);
    $stmt_msg->execute();
    $result_msg = $stmt_msg->get_result();
    if ($row_msg = $result_msg->fetch_assoc()) {
        $first_message = $row_msg['message'];
    }

    $chat_sessions[] = [
        'id' => $session_id,
        'first_message' => $first_message,
    ];
}
ob_start();
?>
<link rel="stylesheet" href="finai.css">

<div class="page-container">
    <header class="page-header">
        <div class="header-icon-wrapper">
            <i class="fas fa-robot header-icon"></i>
        </div>
        <h1 class="page-title">FinAI</h1>
        <p class="page-subtitle">Asisten keuangan pribadimu. Tanya apa saja tentang keuangan dan FinAI akan membantumu.</p>
    </header>

    <div class="finai-container">
        <div class="finai-history-panel" id="history-panel">
            <div class="panel-header">
                <h3 class="panel-title">Riwayat Chat</h3>
                <button id="new-chat-btn" class="btn btn-yellow">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
            <div class="history-list" id="history-list">
                <?php if (empty($chat_sessions)): ?>
                    <div class="empty-state-history">
                        <i class="fas fa-history"></i>
                        <p>Riwayat chat kosong.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($chat_sessions as $session): ?>
                        <div class="history-item" data-session-id="<?php echo $session['id']; ?>">
                            <i class="fas fa-comment-dots"></i>
                            <span class="history-text"><?php echo htmlspecialchars($session['first_message']); ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <button id="clear-all-chats-btn" class="btn btn-full btn-red">
                <i class="fas fa-trash-alt"></i> Hapus Semua
            </button>
        </div>

        <div class="finai-chat-panel">
            <div class="chat-box" id="chat-box">
                </div>
            <form class="chat-form" id="chat-form">
                <input type="text" id="chat-input" placeholder="Tanyakan sesuatu..." required>
                <button type="submit" class="btn"><i class="fas fa-paper-plane"></i></button>
            </form>
        </div>
    </div>
</div>

<script src="finai.js"></script>

<?php
$page_content = ob_get_clean();
require_once __DIR__ . '/../template.php';
?>