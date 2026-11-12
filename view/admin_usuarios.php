<?php
  require_once __DIR__.'/../actions/validar_admin.php';
  require_once __DIR__.'/../controller/UsuarioController.php';
  require_once __DIR__.'/../controller/RolController.php';

  $mensaje_negativo = $mensaje_positivo = "";
  $usuario_controller = new UsuarioController();
  $rol_controller = new RolController();

  if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['m_n'])){
    $mensaje_negativo = match($_GET['m_n']){
      "vacio" => "Completa todos los campos obligatorios.",
      "email" => "El correo electrónico no es válido.",
      "pass_dif" => "Las contraseñas no coinciden.",
      "existe" => "El nombre de usuario o el email ingresado ya existen.",
      "rol" => "El rol seleccionado no existe.",
      "propio" => "No puedes eliminar tu propia cuenta.",
      "propio_rol" => "No puedes quitarte tu propio rol de administrador.",
      "bd" => "Ocurrió un error al guardar los cambios.",
      default => ""
    };
  }
  else if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['m_p'])){
    $mensaje_positivo = match($_GET['m_p']){
      "creado" => "Usuario creado correctamente.",
      "editado" => "Usuario actualizado correctamente.",
      "eliminado" => "Usuario eliminado correctamente.",
      default => ""
    };
  }

  $usuarios = $usuario_controller->listarUsuarios() ?? [];
  $roles = $rol_controller->listarRoles() ?? [];
  $usuario_editar = null;

  if (isset($_GET['editar'])){
    $usuario_editar = $usuario_controller->buscarUsuarioPorId((int) $_GET['editar']);
  }
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Usuarios - Administrador</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Arimo:ital,wght@0,400..700;1,400..700&display=swap"
    rel="stylesheet">
  <link rel="stylesheet"
    href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=favorite,star,close,error,check_circle" />
  <link rel="stylesheet" href="../assets/style/pag_principal.css">
  <link rel="stylesheet" href="../assets/style/admin.css">
</head>

<body>
  <?php require __DIR__.'/admin_nav.php'; ?>

  <main class="admin-main">
    <h1>Usuarios</h1>

    <?php if (!empty($mensaje_negativo)): ?>
      <div class="alert alert-error">
        <span class="material-symbols-outlined alert-icon">error</span>
        <span><?= $mensaje_negativo ?></span>
      </div>
    <?php endif; ?>
    <?php if (!empty($mensaje_positivo)): ?>
      <div class="alert alert-success">
        <span class="material-symbols-outlined alert-icon">check_circle</span>
        <span><?= $mensaje_positivo ?></span>
      </div>
    <?php endif; ?>

    <div class="admin-layout">
      <section class="admin-panel-box">
        <?php if ($usuario_editar): ?>
          <h2>Editar usuario</h2>
          <form class="admin-form" action="../actions/admin_usuario.php" method="POST">
            <?= campoCsrf() ?>
            <input type="hidden" name="accion" value="editar">
            <input type="hidden" name="id_usuario" value="<?= $usuario_editar->getIdUsuario() ?>">

            <label for="nombre_usuario">Nombre de usuario</label>
            <input type="text" id="nombre_usuario" name="nombre_usuario" value="<?= esc($usuario_editar->getNombreUsuario()) ?>" maxlength="100">

            <label for="email">Correo electrónico</label>
            <input type="email" id="email" name="email" value="<?= esc($usuario_editar->getEmail()) ?>" maxlength="100">

            <label for="id_rol">Rol</label>
            <select id="id_rol" name="id_rol">
              <?php foreach($roles as $rol): ?>
                <option value="<?= $rol->getIdRol() ?>" <?= $usuario_editar->getRol() && $usuario_editar->getRol()->getIdRol() == $rol->getIdRol() ? 'selected' : '' ?>>
                  <?= esc($rol->getNombre()) ?>
                </option>
              <?php endforeach; ?>
            </select>

            <label for="contrasena">Nueva contraseña (opcional)</label>
            <input type="password" id="contrasena" name="contrasena" placeholder="Déjala vacía para no cambiarla">
            <input type="password" name="repetir_contrasena" placeholder="Repite la nueva contraseña">

            <div class="form-buttons">
              <button type="submit" class="btn-admin">Guardar cambios</button>
              <a href="admin_usuarios.php" class="btn-admin">Cancelar</a>
            </div>
          </form>
        <?php else: ?>
          <h2>Crear usuario</h2>
          <form class="admin-form" action="../actions/admin_usuario.php" method="POST">
            <?= campoCsrf() ?>
            <input type="hidden" name="accion" value="crear">

            <label for="nombre_usuario">Nombre de usuario</label>
            <input type="text" id="nombre_usuario" name="nombre_usuario" maxlength="100">

            <label for="email">Correo electrónico</label>
            <input type="email" id="email" name="email" maxlength="100">

            <label for="id_rol">Rol</label>
            <select id="id_rol" name="id_rol">
              <?php foreach($roles as $rol): ?>
                <option value="<?= $rol->getIdRol() ?>" <?= $rol->getNombre() == 'user' ? 'selected' : '' ?>>
                  <?= esc($rol->getNombre()) ?>
                </option>
              <?php endforeach; ?>
            </select>

            <label for="contrasena">Contraseña</label>
            <input type="password" id="contrasena" name="contrasena">
            <input type="password" name="repetir_contrasena" placeholder="Repite la contraseña">

            <label for="pregunta_secreta">Pregunta secreta (¿Nombre de su primera mascota?)</label>
            <input type="text" id="pregunta_secreta" name="pregunta_secreta">

            <button type="submit" class="btn-admin">Crear usuario</button>
          </form>
        <?php endif; ?>
      </section>

      <section>
        <h2>Lista de usuarios (<?= count($usuarios) ?>)</h2>
        <div class="table-filter">
          <input type="text" placeholder="Filtrar por nombre, email o rol" data-filtro-tabla="tabla-usuarios">
        </div>
        <div class="table-wrapper">
          <table class="admin-table" id="tabla-usuarios">
            <thead>
              <tr>
                <th>Foto</th>
                <th>ID</th>
                <th>Usuario</th>
                <th>Email</th>
                <th>Rol</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($usuarios as $usuario): ?>
                <?php $es_admin = $usuario->getRol() && $usuario->getRol()->getNombre() == 'admin'; ?>
                <tr>
                  <td>
                    <img src="<?= $usuario->getFotoPerfil() ? esc($usuario->getFotoPerfil()) : '../assets/images/usuario.webp' ?>" alt="Foto de <?= esc($usuario->getNombreUsuario()) ?>">
                  </td>
                  <td><?= $usuario->getIdUsuario() ?></td>
                  <td><?= esc($usuario->getNombreUsuario()) ?></td>
                  <td><?= esc($usuario->getEmail()) ?></td>
                  <td><span class="badge <?= $es_admin ? 'badge-admin' : '' ?>"><?= $usuario->getRol() ? esc($usuario->getRol()->getNombre()) : 'sin rol' ?></span></td>
                  <td>
                    <div class="acciones">
                      <a class="btn-admin btn-small" href="admin_usuarios.php?editar=<?= $usuario->getIdUsuario() ?>">Editar</a>
                      <?php if ($usuario->getIdUsuario() != $usuario_admin->getIdUsuario()): ?>
                        <form action="../actions/admin_usuario.php" method="POST" onsubmit="return confirm('¿Eliminar a este usuario? También se borrarán sus favoritas y calificadas.');">
                          <?= campoCsrf() ?>
                          <input type="hidden" name="accion" value="eliminar">
                          <input type="hidden" name="id_usuario" value="<?= $usuario->getIdUsuario() ?>">
                          <button type="submit" class="btn-admin btn-small btn-danger">Eliminar</button>
                        </form>
                      <?php endif; ?>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </section>
    </div>
  </main>

  <script src="../assets/js/password-toggle.js"></script>
  <script src="../assets/js/admin.js"></script>
</body>

</html>
