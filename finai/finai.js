document.addEventListener("DOMContentLoaded", function () {
  const chatForm = document.getElementById("chat-form");
  const chatInput = document.getElementById("chat-input");
  const chatBox = document.getElementById("chat-box");
  const historyList = document.getElementById("history-list");
  const newChatBtn = document.getElementById("new-chat-btn");
  const clearAllChatsBtn = document.getElementById("clear-all-chats-btn");

  let currentSessionId = null;

  function renderChat(messages) {
    chatBox.innerHTML = "";
    if (messages.length === 0) {
      chatBox.innerHTML = `
                <div class="chat-message bot-message">
                    <div class="message-content">
                        Halo! Saya FinAI, asisten keuanganmu. Tanya apa saja tentang keuangan dan saya akan bantu.
                    </div>
                </div>
            `;
    } else {
      messages.forEach((msg) => {
        const msgDiv = document.createElement("div");
        msgDiv.className = `chat-message ${msg.sender}-message`;
        msgDiv.innerHTML = `<div class="message-content">${msg.message}</div>`;
        chatBox.appendChild(msgDiv);
      });
    }
    chatBox.scrollTop = chatBox.scrollHeight;
  }

  // Fungsi untuk memuat chat dari sesi tertentu
  function loadChatSession(sessionId) {
    currentSessionId = sessionId;
    const historyItems = document.querySelectorAll(".history-item");
    historyItems.forEach((item) => {
      item.classList.remove("active");
      if (item.dataset.sessionId == sessionId) {
        item.classList.add("active");
      }
    });

    fetch("../php_logic/finai_process.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: `action=get_chat_session&session_id=${sessionId}`,
    })
      .then((response) => response.json())
      .then((data) => {
        renderChat(data.chat);
      });
  }

  // Muat chat pertama saat halaman dimuat
  const firstChat = historyList.querySelector(".history-item");
  if (firstChat) {
    loadChatSession(firstChat.dataset.sessionId);
  } else {
    renderChat([]);
  }

  // Event listener untuk item riwayat chat
  historyList.addEventListener("click", function (e) {
    const item = e.target.closest(".history-item");
    if (item) {
      loadChatSession(item.dataset.sessionId);
    }
  });

  if (chatForm) {
    chatForm.addEventListener("submit", function (e) {
      e.preventDefault();
      const userMessage = chatInput.value.trim();
      if (userMessage === "") return;

      // Tambahkan pesan user ke chatbox
      const userMsgDiv = document.createElement("div");
      userMsgDiv.className = "chat-message user-message";
      userMsgDiv.innerHTML = `<div class="message-content">${userMessage}</div>`;
      chatBox.appendChild(userMsgDiv);

      chatInput.value = "";
      chatBox.scrollTop = chatBox.scrollHeight;

      const bodyData = `message=${encodeURIComponent(
        userMessage
      )}&chat_session_id=${currentSessionId}`;

      fetch("../php_logic/finai_process.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: bodyData,
      })
        .then((response) => response.json())
        .then((data) => {
          const botMessage = data.response;
          currentSessionId = data.chat_session_id; // Perbarui sesi ID
          const botMsgDiv = document.createElement("div");
          botMsgDiv.className = "chat-message bot-message";
          botMsgDiv.innerHTML = `<div class="message-content">${botMessage}</div>`;
          chatBox.appendChild(botMsgDiv);
          chatBox.scrollTop = chatBox.scrollHeight;

          // Tambahkan item baru di riwayat chat jika sesi baru
          if (
            !historyList.querySelector(
              `[data-session-id="${currentSessionId}"]`
            )
          ) {
            const historyItem = document.createElement("div");
            historyItem.className = "history-item active";
            historyItem.dataset.sessionId = currentSessionId;
            historyItem.innerHTML = `
                         <i class="fas fa-comment-dots"></i>
                         <span class="history-text">${userMessage.substring(
                           0,
                           30
                         )}${userMessage.length > 30 ? "..." : ""}</span>
                     `;
            historyList.prepend(historyItem);
          }
        });
    });
  }

  if (newChatBtn) {
    newChatBtn.addEventListener("click", () => {
      currentSessionId = null;
      renderChat([]);
      const activeItem = document.querySelector(".history-item.active");
      if (activeItem) {
        activeItem.classList.remove("active");
      }
    });
  }

  if (clearAllChatsBtn) {
    clearAllChatsBtn.addEventListener("click", () => {
      if (confirm("Yakin ingin menghapus semua riwayat chat?")) {
        fetch("../php_logic/finai_process.php", {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: `action=clear_all_chats`,
        })
          .then((response) => response.json())
          .then((data) => {
            if (data.status === "success") {
              historyList.innerHTML = `
                            <div class="empty-state-history">
                                <i class="fas fa-history"></i>
                                <p>Riwayat chat kosong.</p>
                            </div>
                        `;
              currentSessionId = null;
              renderChat([]);
            }
          });
      }
    });
  }
});
