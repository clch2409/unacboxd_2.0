<?php
require_once (realpath(dirname(__FILE__)).'/../bd/bd_conection.php');
require_once (realpath(dirname(__FILE__)).'/../model/PeliculaUsuario.php');

class ActorController{

  private DataBase $bd;

  private PDO $con;

  public function __construct(?PDO $con = null){
      $this->bd = new DataBase();
      $this->con = $con ?? $this->bd->conectar();
  }

    
  public function agregarUsuarioPelicula(int $id_usuario, int $id_pelicula, string $tipo){
    try{
      $prep = $this->con->prepare("INSERT INTO pelicula_usuario (id_pelicula, id_usuario, tipo) VALUES (:id_p, :id_u, :tipo)");
      $prep->execute([
        ":id_p" => $id_pelicula,
        "id_u" => $id_usuario,
        ":tipo" => $tipo
      ]);
      return true;
    }
    catch(PDOException $e){
      var_dump($e);
      return false;
    }
  }
}