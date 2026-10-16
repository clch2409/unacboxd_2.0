<?php

  $mensaje_negativo = "";

  if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['m'])){
    $mensaje_negativo = match($_GET['m']){
      "vacio" => "Todos los campos son obligatorios.",
      "existe" => "El nombre de usuario o el email ingresado ya existen"
    };
  }
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registrar Usuario</title>
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
        <h2>Crear Cuenta</h2>
        <form class="auth-form" action="../actions/procesar_registro.php" method="POST" enctype="multipart/form-data">
          <input type="text" name="nombre_usuario" placeholder="Nombre de usuario">
          <input type="email" name="email" placeholder="Correo electrónico">
          <input type="password" name="contrasena" placeholder="Contraseña">
          <input type="text" name="pregunta_secreta" placeholder="¿Nombre de tu primera mascota?">
          
          <div class="file-input-container">
            <label for="imagen_perfil">Imagen de perfil (Opcional):</label>
            <input type="file" name="imagen_perfil" id="imagen_perfil" accept="image/png, image/jpeg, image/jpg">
          </div>

          <?php if (!empty($mensaje_negativo)): ?>
            <div class="alert alert-error">
              <span class="material-symbols-outlined alert-icon">error</span>
              <span><?= $mensaje_negativo ?></span>
            </div>
          <?php endif;?>
          
          <button type="submit" class="btn-auth">Registrarse</button>
        </form>
        <div class="auth-links">
          <a href="login.php">¿Ya tienes cuenta? Inicia sesión</a>
        </div>
      </div>
    </main>
</body>
</html>