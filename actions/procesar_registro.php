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
    }
    else{

      $usuario_creado_correctamente = $usuario_controller->crearUsuario($username, $email, $password, $preg_sec);

      if (!$usuario_creado_correctamente){
        header("Location: ../view/registro.php?m=existe");
      }
      else{
        header("Location: ../view/login.php");
      }
    }
    exit();
  }
  else{
    header("Location: ../view/registro.php?");
  }
