<?php
  require_once __DIR__."/../model/Usuario.php";
  require_once __DIR__."/../model/Rol.php";
  session_start();

  require_once __DIR__."/../controller/PeliculaUsuarioController.php";
  require_once __DIR__."/../controller/CalificacionController.php";

  $usuario_peli_controller = new PeliculaUsuarioController();
  $calificacion_controller = new CalificacionController();

  header("Content-Type: application/json");

  $json = file_get_contents("php://input");
  $data = json_decode($json, true);


  if ($data){
    $id_pelicula = $data['id_pelicula'];
    $usuario = $_SESSION['usuario'];
    $id_usuario = $usuario->getIdUsuario();

    if ($data['tipo'] == 'favorita'){
      $usuario_peli_controller->agregarPeliculaUsuario($id_usuario, $id_pelicula, "favorita");
    }
    else if ($data['tipo'] == 'calificada'){
      $usuario_peli_controller->agregarPeliculaUsuario($id_usuario, $id_pelicula, "calificada");
      $calificacion_controller->agregarCalificacion($id_pelicula, $data['calificacion']);
    }
    echo json_encode([
      "estado" => "success",
      "mensaje" => "Se ha agregado correctamente la pelicula",
    ]);
  }
  else{
    http_response_code(400);
    echo json_encode([
      "estado" => "error",
      "mensaje" => "No se ha obtenido la información correctamente",
    ]);
  }