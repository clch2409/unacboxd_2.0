<?php
  require_once __DIR__.'/validar_admin.php';
  require_once __DIR__.'/limpiar_textos.php';
  require_once __DIR__.'/../controller/UsuarioController.php';

  if ($_SERVER['REQUEST_METHOD'] != 'POST' || !validarCsrf()){
    header("Location: ../view/admin_usuarios.php");
    exit();
  }

  $usuario_controller = new UsuarioController();
  $accion = $_POST['accion'] ?? '';

  if ($accion == 'crear'){
    $username = limpiarTexto($_POST['nombre_usuario'] ?? '');
    $email = limpiarTexto($_POST['email'] ?? '');
    $password = limpiarTexto($_POST['contrasena'] ?? '');
    $repeat_password = limpiarTexto($_POST['repetir_contrasena'] ?? '');
    $preg_sec = limpiarTexto($_POST['pregunta_secreta'] ?? '');
    $id_rol = (int) ($_POST['id_rol'] ?? 0);

    if (empty($username) || empty($email) || empty($password) || empty($repeat_password) || empty($preg_sec) || $id_rol <= 0){
      header("Location: ../view/admin_usuarios.php?m_n=vacio");
      exit();
    }
    else if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
      header("Location: ../view/admin_usuarios.php?m_n=email");
      exit();
    }
    else if ($password != $repeat_password){
      header("Location: ../view/admin_usuarios.php?m_n=pass_dif");
      exit();
    }
    else{
      $usuario_creado = $usuario_controller->crearUsuario($username, $email, $password, $preg_sec, null, $id_rol);

      if (!$usuario_creado){
        header("Location: ../view/admin_usuarios.php?m_n=existe");
        exit();
      }
      header("Location: ../view/admin_usuarios.php?m_p=creado");
      exit();
    }
  }
  else if ($accion == 'editar'){
    $id_usuario = (int) ($_POST['id_usuario'] ?? 0);
    $username = limpiarTexto($_POST['nombre_usuario'] ?? '');
    $email = limpiarTexto($_POST['email'] ?? '');
    $password = limpiarTexto($_POST['contrasena'] ?? '');
    $repeat_password = limpiarTexto($_POST['repetir_contrasena'] ?? '');
    $id_rol = (int) ($_POST['id_rol'] ?? 0);

    if (empty($username) || empty($email) || $id_rol <= 0){
      header("Location: ../view/admin_usuarios.php?editar=$id_usuario&m_n=vacio");
      exit();
    }
    else if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
      header("Location: ../view/admin_usuarios.php?editar=$id_usuario&m_n=email");
      exit();
    }
    else if ($password != $repeat_password){
      header("Location: ../view/admin_usuarios.php?editar=$id_usuario&m_n=pass_dif");
      exit();
    }
    else if ($id_usuario == $usuario_admin->getIdUsuario() && $id_rol != $usuario_admin->getRol()->getIdRol()){
      header("Location: ../view/admin_usuarios.php?editar=$id_usuario&m_n=propio_rol");
      exit();
    }
    else{
      $usuario_actualizado = $usuario_controller->actualizarUsuario($id_usuario, $username, $email, $id_rol, $password);

      if (isset($usuario_actualizado['error'])){
        $error = $usuario_actualizado['error'];
        header("Location: ../view/admin_usuarios.php?editar=$id_usuario&m_n=$error");
        exit();
      }
      else if (!$usuario_actualizado){
        header("Location: ../view/admin_usuarios.php?editar=$id_usuario&m_n=bd");
        exit();
      }

      // Si el admin se editó a sí mismo se refresca la sesión con los datos nuevos
      if ($id_usuario == $usuario_admin->getIdUsuario()){
        $_SESSION['usuario'] = $usuario_controller->buscarUsuarioPorId($id_usuario);
      }
      header("Location: ../view/admin_usuarios.php?m_p=editado");
      exit();
    }
  }
  else if ($accion == 'eliminar'){
    $id_usuario = (int) ($_POST['id_usuario'] ?? 0);

    if ($id_usuario == $usuario_admin->getIdUsuario()){
      header("Location: ../view/admin_usuarios.php?m_n=propio");
      exit();
    }

    if (!$usuario_controller->eliminarUsuario($id_usuario)){
      header("Location: ../view/admin_usuarios.php?m_n=bd");
      exit();
    }
    header("Location: ../view/admin_usuarios.php?m_p=eliminado");
    exit();
  }
  else{
    header("Location: ../view/admin_usuarios.php");
    exit();
  }
