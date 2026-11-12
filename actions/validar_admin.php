<?php
  require_once __DIR__.'/../model/Usuario.php';
  require_once __DIR__.'/../model/Rol.php';

  session_start();

  if (!isset($_SESSION['usuario'])){
    header("Location: ../view/login.php");
    exit();
  }

  $usuario_admin = $_SESSION['usuario'];

  if (!$usuario_admin->getRol() || $usuario_admin->getRol()->getNombre() != 'admin'){
    header("Location: ../view/pag_principal.php");
    exit();
  }

  if (empty($_SESSION['csrf_token'])){
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
  }

  function campoCsrf(){
    return '<input type="hidden" name="csrf_token" value="'.$_SESSION['csrf_token'].'">';
  }

  // Escapa texto para mostrarlo en las vistas del admin sin volver a codificar lo ya codificado por limpiarTexto()
  function esc($texto){
    return htmlspecialchars((string) $texto, ENT_QUOTES, 'UTF-8', false);
  }

  function validarCsrf(){
    $token = $_POST['csrf_token'] ?? '';
    return hash_equals($_SESSION['csrf_token'], $token);
  }
