<?php
  $mensaje_negativo = $mensaje_positivo = "";

  if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['m_n'])){
    $mensaje_negativo = match($_GET['m_n']){
      "vacio" => "Todos los campos son obligatorios.",
      "repetir_dif" => "Las contraseñas nuevas no coinciden.",
      "existe" => "El nombre de usuario no existe.",
      "secreta" => "La respuesta a la pregunta secreta es incorrecta.",
      "iguales" => "La nueva contraseña no puede ser igual a la anterior."
    };
  }
  else if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['m_p'])){
    $mensaje_positivo = match($_GET['m_p']){
      "correcto" => "Contraseña recuperada exitosamente. Ya puedes iniciar sesión."
    };
  }
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Recuperar Contraseña</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Arimo:ital,wght@0,400..700;1,400..700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=favorite,star,close,error,check_circle" />
  <link rel="stylesheet" href="../assets/style/auth.css">
</head>
<body>
    <main class="auth-wrapper">
      <div class="auth-container">
        <div class="auth-logo-container">
            <img src="../assets/images/logo_unacboxd.png" alt="Logo Unacboxd" class="auth-logo">
        </div>
        <h2>Recuperar Contraseña</h2>
        <form class="auth-form" action="../actions/procesar_recuperar.php" method="POST">
          <input type="text" name="nombre_usuario" placeholder="Nombre de usuario">
          <input type="password" name="pregunta_secreta" placeholder="Respuesta secreta (Primera mascota)">
          <input type="password" name="contrasena_nueva" placeholder="Nueva contraseña">
          <input type="password" name="repetir_contrasena_nueva" placeholder="Repita nueva contraseña">
          
          <?php if (!empty($mensaje_negativo)): ?>
            <div class="alert alert-error">
              <span class="material-symbols-outlined alert-icon">error</span>
              <span><?= $mensaje_negativo ?></span>
            </div>
          <?php endif;?>

          <?php if (!empty($mensaje_positivo)): ?>
            <div class="alert alert-success">
              <span class="material-symbols-outlined alert-icon">check_circle</span>
              <span><?= $mensaje_positivo ?></span>
            </div>
          <?php endif;?>
          
          <button type="submit" class="btn-auth">Restablecer</button>
        </form>
        <div class="auth-links">
          <a href="login.php">Volver al inicio de sesión</a>
        </div>
      </div>
    </main>
    <script src="../assets/js/password-toggle.js"></script>
</body>
</html>