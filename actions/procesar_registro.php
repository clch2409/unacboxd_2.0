<?php
  require_once __DIR__."/limpiar_textos.php";
  require_once __DIR__."/../controller/UsuarioController.php";

  $usuario_controller = new UsuarioController();
  $username=$password=$email=$preg_sec="";
  $usuario_creado_correctamente = null;


  if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    $username = limpiarTexto($_POST['nombre_usuario']);
    $password = limpiarTexto($_POST['contrasena']);
    $email = limpiarTexto($_POST['email']);
    $preg_sec = limpiarTexto($_POST['pregunta_secreta']);
    if (empty($username) || empty($password) || empty($email) || empty($preg_sec)){
      header("Location: ../view/registro.php?m=vacio");
      exit();
    }
    else{
      $ruta_imagen_bd = null;

      if (isset($_FILES['imagen_perfil']) && $_FILES['imagen_perfil']['error'] == 0){
        $nombre_original = $_FILES['imagen_perfil']['name'];
        $tmp_name = $_FILES['imagen_perfil']['tmp_name'];

        $extension = pathinfo($nombre_original, PATHINFO_EXTENSION);

        $nombre_unico = uniqid("perfil_") . '.' . $extension;
        $ruta_destino = '../assets/images/perfiles/' . $nombre_unico;

        if (move_uploaded_file($tmp_name, $ruta_destino)){
          $ruta_imagen_bd = $ruta_destino;
        }
      }

      $usuario_creado_correctamente = $usuario_controller->crearUsuario($username, $email, $password, $preg_sec, $ruta_imagen_bd);


      if (!$usuario_creado_correctamente){
        header("Location: ../view/registro.php?m=existe");
        exit();
      }
      else{
        header("Location: ../view/login.php");
        exit();
      }
    }
    
  }
  else{
    header("Location: ../view/registro.php?");
    exit();
  }
