document.addEventListener("DOMContentLoaded", function () {
  // Logika untuk Sidebar
  const navPanel = document.getElementById("nav-panel");
  const navToggleBtn = document.getElementById("nav-toggle-btn");
  const navCloseBtn = document.getElementById("mobile-nav-close");
  const navOverlay = document.getElementById("nav-overlay");

  if (navToggleBtn) {
    navToggleBtn.addEventListener("click", () => {
      navPanel.classList.add("open");
      navOverlay.style.display = "block";
    });
  }

  if (navCloseBtn) {
    navCloseBtn.addEventListener("click", () => {
      navPanel.classList.remove("open");
      navOverlay.style.display = "none";
    });
  }

  if (navOverlay) {
    navOverlay.addEventListener("click", () => {
      navPanel.classList.remove("open");
      navOverlay.style.display = "none";
    });
  }
});
