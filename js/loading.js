document.addEventListener("DOMContentLoaded", function () {
  const loginForm = document.querySelector(".login-form");
  const registerForm = document.querySelector(".register-form");
  const loginBtn = document.getElementById("login-btn");
  const registerBtn = document.getElementById("register-btn");

  function createLoadingSpinner() {
    const spinner = document.createElement("i");
    spinner.className = "fas fa-spinner fa-spin";
    return spinner;
  }

  if (loginForm) {
    loginForm.addEventListener("submit", function () {
      loginBtn.disabled = true;
      loginBtn.innerHTML = "";
      loginBtn.appendChild(createLoadingSpinner());
    });
  }

  if (registerForm) {
    registerForm.addEventListener("submit", function () {
      registerBtn.disabled = true;
      registerBtn.innerHTML = "";
      registerBtn.appendChild(createLoadingSpinner());
    });
  }
});
