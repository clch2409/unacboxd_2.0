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
      $mensaje_negativo = "Todos los campos son obligatorios";
      header("Location: ../view/login.php?m=vacio");
    }
    else if (!$usuario_encontrado){
      $mensaje_negativo = "Ingrese correctamente sus datos o cree una nueva cuenta";
      header("Location: ../view/login.php?m=credenciales");
    }
    else{
      $nombreUsuario = $usuario_encontrado->getNombreUsuario();
      $_SESSION['usuario'] = $usuario_encontrado;
    }
  }