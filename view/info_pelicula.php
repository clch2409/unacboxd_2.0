<?php
  require_once __DIR__.'/../model/Usuario.php';
  require_once __DIR__.'/../model/Rol.php';
  session_start();

  // 1. Protección de sesión
  if (!isset($_SESSION['usuario'])){
    header("Location: login.php");
    exit();
  }

  // 2. Verificamos que se haya enviado un ID por la URL
  if (!isset($_GET['id']) || empty($_GET['id'])) {
      header("Location: pag_principal.php");
      exit();
  }

  require_once __DIR__ . '/../controller/PeliculaController.php';
  $pelicula_controller = new PeliculaController();
  
  // 3. Buscamos la película en la BD
  $pelicula = $pelicula_controller->buscarPeliculaPorId($_GET['id']);

  // Si el usuario manipuló la URL y puso un ID que no existe, lo devolvemos
  if (!$pelicula) {
      header("Location: pag_principal.php");
      exit();
  }
  
  $usuario_logueado = $_SESSION['usuario'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $pelicula->getTitulo(); ?> - Unacboxd</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Arimo:ital,wght@0,400..700;1,400..700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/style/pag_principal.css">
  <link rel="stylesheet" href="../assets/style/info_pelicula.css">
</head>
<body>
  <!-- Reutilizamos tu navbar -->
  <nav>
    <a href="pag_principal.php">
      <img class="img1" src="../assets/images/logo_sin_texto.png" alt="Logo">
    </a>
    <div class="profile">
      <?php if (!$usuario_logueado->getFotoPerfil()): ?>
        <img class="img2" src="../assets/images/usuario.webp" alt="Usuario">
      <?php else: ?>
        <img class="img2" src="<?= $usuario_logueado->getFotoPerfil(); ?>" alt="Usuario">
      <?php endif;?>    
      <a href="../actions/logout.php">Cerrar Sesión</a>
    </div>
  </nav>

  <main class="movie-info-container">
    <!-- Columna Izquierda: Póster -->
    <div class="movie-poster-large">
      <img src="../<?= $pelicula->getPoster(); ?>" alt="<?= $pelicula->getTitulo(); ?>">
    </div>

    <!-- Columna Derecha: Detalles -->
    <div class="movie-details">
      <h1><?= $pelicula->getTitulo(); ?></h1>
      
      <div class="movie-meta">
        <span><?= $pelicula->getAnioEstreno(); ?></span>
        <span>Dirigida por <?= $pelicula->getDirector()->getNombre(); ?></span>
        <span><?= $pelicula->getDuracionMinutos(); ?> min</span>
        <span><?= $pelicula->getPais(); ?></span>
      </div>

      <p class="movie-synopsis"><?= $pelicula->getSinopsis(); ?></p>

      <div class="movie-crew-section">
        <div class="crew-block">
          <h3>Elenco</h3>
          <p>
            <?php 
              // Convertimos el arreglo de objetos Actor a un arreglo de nombres y los unimos con comas
              $nombres_actores = array_map(fn($actor) => $actor->getNombre(), $pelicula->getActores());
              echo implode(", ", $nombres_actores);
            ?>
          </p>
        </div>

        <div class="crew-block">
          <h3>Géneros</h3>
          <p>
            <?php 
              $nombres_generos = array_map(fn($genero) => $genero->getNombre(), $pelicula->getGeneros());
              echo implode(", ", $nombres_generos);
            ?>
          </p>
        </div>
      </div>
    </div>
  </main>

</body>
</html>