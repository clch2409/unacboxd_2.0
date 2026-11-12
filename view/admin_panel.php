<?php
  require_once __DIR__.'/../actions/validar_admin.php';
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Panel de Administrador - Unacboxd</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Arimo:ital,wght@0,400..700;1,400..700&display=swap"
    rel="stylesheet">
  <link rel="stylesheet" href="../assets/style/pag_principal.css">
  <link rel="stylesheet" href="../assets/style/admin.css">
</head>

<body>
  <?php require __DIR__.'/admin_nav.php'; ?>

  <main class="admin-main">
    <h1>Panel de Administrador</h1>
    <p class="admin-subtitle">Bienvenido, <?= esc($usuario_admin->getNombreUsuario()); ?>. Elige qué quieres administrar.</p>

    <section class="admin-cards">
      <a class="admin-card" href="admin_usuarios.php">
        <h3>Usuarios</h3>
        <p>Crea usuarios y administradores, edita sus datos o elimínalos.</p>
      </a>
      <a class="admin-card" href="admin_peliculas.php">
        <h3>Películas</h3>
        <p>Agrega, edita o elimina películas del catálogo con su director, géneros y elenco.</p>
      </a>
      <a class="admin-card" href="admin_catalogo.php?tipo=director">
        <h3>Directores</h3>
        <p>Administra la lista de directores.</p>
      </a>
      <a class="admin-card" href="admin_catalogo.php?tipo=actor">
        <h3>Actores</h3>
        <p>Administra la lista de actores y actrices.</p>
      </a>
      <a class="admin-card" href="admin_catalogo.php?tipo=genero">
        <h3>Géneros</h3>
        <p>Administra los géneros de las películas.</p>
      </a>
      <a class="admin-card" href="admin_reportes.php">
        <h3>Reportes</h3>
        <p>Consulta la información más relevante del sitio.</p>
      </a>
    </section>
  </main>
</body>

</html>
