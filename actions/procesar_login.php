<?php
  session_start();

  require_once "limpiar_textos.php";
  require_once __DIR__ . "/../controller/UsuarioController.php";
  require_once __DIR__."/../model/Usuario.php";

  $nombre_usuario = $contrasena = $mensaje_negativo = $mensaje_positivo = "";
  $usuario_controller = new UsuarioController();
  $usuario_encontrado = null;


  if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    $nombre_usuario = limpiarTexto($_POST['nombre_usuario']);
    $contrasena = limpiarTexto($_POST['contrasena']);
    $usuario_encontrado = $usuario_controller->validarLogin($nombre_usuario, $contrasena);

    if (empty($nombre_usuario) || empty($contrasena)){
      header("Location: ../view/login.php?m=vacio");
    }
    else if (!$usuario_encontrado){
      header("Location: ../view/login.php?m=credenciales");
    }
    else{
      $_SESSION['usuario'] = $usuario_encontrado;
      header("Location: ../view/pag_principal.php");
    }
  }
  else{
    header("Location: ../view/login.php");
  }