<?php
  // Barra de navegación compartida por todas las vistas del administrador (requiere validar_admin.php)
  $pagina_actual = basename($_SERVER['PHP_SELF']);
  $tipo_actual = $_GET['tipo'] ?? '';
?>
<nav>
  <a href="admin_panel.php">
    <img class="img1" src="../assets/images/logo_sin_texto.png" alt="Logo">
  </a>
  <div class="admin-links">
    <a href="admin_usuarios.php" class="<?= $pagina_actual == 'admin_usuarios.php' ? 'active' : '' ?>">Usuarios</a>
    <a href="admin_peliculas.php" class="<?= $pagina_actual == 'admin_peliculas.php' ? 'active' : '' ?>">Películas</a>
    <a href="admin_catalogo.php?tipo=director" class="<?= $pagina_actual == 'admin_catalogo.php' && $tipo_actual == 'director' ? 'active' : '' ?>">Directores</a>
    <a href="admin_catalogo.php?tipo=actor" class="<?= $pagina_actual == 'admin_catalogo.php' && $tipo_actual == 'actor' ? 'active' : '' ?>">Actores</a>
    <a href="admin_catalogo.php?tipo=genero" class="<?= $pagina_actual == 'admin_catalogo.php' && $tipo_actual == 'genero' ? 'active' : '' ?>">Géneros</a>
    <a href="admin_reportes.php" class="<?= $pagina_actual == 'admin_reportes.php' ? 'active' : '' ?>">Reportes</a>
  </div>
  <div class="profile">
    <?php if (!$usuario_admin->getFotoPerfil()): ?>
      <img class="img2" src="../assets/images/usuario.webp" alt="Usuario">
    <?php else: ?>
      <img class="img2" src="<?= esc($usuario_admin->getFotoPerfil()); ?>" alt="Usuario">
    <?php endif;?>
    <a href="pag_principal.php">Ver sitio</a>
    <a href="../actions/logout.php">Cerrar Sesión</a>
  </div>
</nav>
