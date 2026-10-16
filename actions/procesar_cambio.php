<?php

  require_once __DIR__.'/../controller/UsuarioController.php';
  require_once __DIR__.'/limpiar_textos.php';

  $usuario_controller = new UsuarioController();
  $nombre_usuario = $contrasena_actual = $nueva_contra = $repetir_contra = "";

  if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    $nombre_usuario = limpiarTexto($_POST['nombre_usuario']);
    $contrasena_actual = limpiarTexto($_POST['contrasena_actual']);
    $nueva_contra = limpiarTexto($_POST['contrasena_nueva']);
    $repetir_contra = limpiarTexto($_POST['repetir_contrasena_nueva']);

    if (empty($nombre_usuario) || empty($contrasena_actual) || empty($nueva_contra) || empty($repetir_contra)){
      header("Location: ../view/cambiar.php?m_n=vacio");
      exit();
    }
    else if ($nueva_contra != $repetir_contra){
      header("Location: ../view/cambiar.php?m_n=repetir_dif");
      exit();
    }
    else{
      $contra_actualizada = $usuario_controller->cambiarContrasena($nombre_usuario, $contrasena_actual, $nueva_contra);
      if (isset($contra_actualizada['error'])){
        $error = $contra_actualizada['error'];
        header("Location: ../view/cambiar.php?m_n=$error");
        exit();
      }
      else{
        header("Location: ../view/cambiar.php?m_p=correcto");
        exit();
      }
    }
  }
  else{
    header("Location: ../view/login.php");
    exit();
  }