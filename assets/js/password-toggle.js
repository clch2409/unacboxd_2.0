document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll('input[type="password"]').forEach((passwordInput) => {
    const container = document.createElement("div");
    container.className = "password-field";
    passwordInput.parentNode.insertBefore(container, passwordInput);
    container.appendChild(passwordInput);

    const toggleButton = document.createElement("button");
    toggleButton.type = "button";
    toggleButton.className = "password-toggle";
    toggleButton.textContent = "Mostrar";
    toggleButton.setAttribute("aria-label", "Mostrar contraseña");

    toggleButton.addEventListener("click", () => {
      const isHidden = passwordInput.type === "password";
      passwordInput.type = isHidden ? "text" : "password";
      toggleButton.textContent = isHidden ? "Ocultar" : "Mostrar";
      toggleButton.setAttribute(
        "aria-label",
        `${isHidden ? "Ocultar" : "Mostrar"} contraseña`
      );
    });

    container.appendChild(toggleButton);
  });
});
