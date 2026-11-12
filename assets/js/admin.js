/* Filtro de texto para las tablas del apartado de administrador */
document.querySelectorAll("[data-filtro-tabla]").forEach((input) => {
  const tabla = document.getElementById(input.dataset.filtroTabla);

  if (!tabla) return;

  input.addEventListener("keyup", () => {
    const texto = input.value.toLowerCase();

    tabla.querySelectorAll("tbody tr").forEach((fila) => {
      const coincide = fila.textContent.toLowerCase().includes(texto);
      fila.style.display = coincide ? "" : "none";
    });
  });
});
