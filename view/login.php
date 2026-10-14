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
          <input type="text" name="nombre_usuario" placeholder="Nombre de usuario" required>
          <input type="password" name="contrasena" placeholder="Contraseña" required>
          <button type="submit" class="btn-auth">Entrar</button>
        </form>
        <div class="auth-links">
          <a href="registro.php">¿No tienes cuenta? Regístrate</a><br><br>
          <a href="recuperar.php">¿Olvidaste tu contraseña?</a>
        </div>
      </div>
    </main>
</body>
</html>