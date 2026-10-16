<?php
  session_start();

  if (!isset($_SESSION['usuario'])){
    http_response_code(401);
    echo json_encode(["error" => "Acceso denegado"]);
    exit();
  }

  require_once __DIR__.'/../controller/PeliculaController.php';
  require_once __DIR__.'/../model/Actor.php';
  require_once __DIR__.'/../model/Genero.php';

  header("Content-Type: application/json; charset=utf-8");

  $pelicula_controller = new PeliculaController();

  $peliculas = $pelicula_controller->listarPeliculas();
  $peliculas_json = [];

  if ($peliculas){
    foreach($peliculas as $pelicula){
      $peliculas_json[] = [
        "id" => $pelicula->getIdPelicula(),
        "titulo" => $pelicula->getTitulo(),
        "poster" => $pelicula->getPoster(),
        "rating" => $pelicula->calcularPromedioCalificaciones() ?? 0,
        "elenco" => array_map(fn(Actor $actor) => $actor->getNombre(), $pelicula->getActores()),
        "director" => $pelicula->getDirector()->getNombre(),
        "genero" => array_map(fn(Genero $genero) => $genero->getNombre(), $pelicula->getGeneros()),
        "favorito" => false,
        "calificada" => false
      ];
    }
  }

  echo json_encode($peliculas_json);
  exit();
