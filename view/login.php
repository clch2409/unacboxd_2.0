<?php

  $mensaje_negativo = "";

  if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['m'])){
    $mensaje_negativo = match($_GET['m']){
      'credenciales' => "Usuario o contraseña incorrectos; o usuario no existente",
      'vacio' => "Todos los campos sin obligatorios."
    };
  }

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Arimo:ital,wght@0,400..700;1,400..700&display=swap"
    rel="stylesheet">
  <link rel="stylesheet"
    href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=favorite,star,close" />
  <link rel="stylesheet" href="../assets/style/auth.css">
</head>
<body>
    <main class="auth-wrapper">
      <div class="auth-container">
        <div class="auth-logo-container">
            <img src="../assets/images/logo_unacboxd.png" alt="Logo Unacboxd" class="auth-logo">
        </div>
        <h2>Iniciar Sesión</h2>
        <form class="auth-form" action="../actions/procesar_login.php" method="POST">
          <input type="text" name="nombre_usuario" placeholder="Nombre de usuario" >
          <input type="password" name="contrasena" placeholder="Contraseña" >
          <button type="submit" class="btn-auth">Entrar</button>
          <?php if (!empty($mensaje_negativo)): ?>
            <div class="alert alert-error">
              <span class="material-symbols-outlined alert-icon">error</span>
              <span>
                  <?= $mensaje_negativo ?>
              </span>
            </div>
          <?php endif; ?>

        </form>
        <div class="auth-links">
          <a href="registro.php">¿No tienes cuenta? Regístrate</a><br><br>
          <a href="recuperar.php">¿Olvidaste tu contraseña?</a><br><br>
          <a href="cambiar.php">Cambiar Contraseña</a>
        </div>
      </div>
    </main>
    <script src="../assets/js/password-toggle.js"></script>
</body>
</html>