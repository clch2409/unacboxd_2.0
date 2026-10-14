<?php
  require_once (realpath(dirname(__FILE__)).'/../bd/bd_conection.php');
  require_once (realpath(dirname(__FILE__)).'/../model/Usuario.php');

  class RolController{

    private Database $bd;
    private PDO $con;

    public function __construct()
    {
      $this->bd = new Database();
      $this->con = $con ?? $this->bd->conectar();
    }

    public function obtenerRolPorId(int $id_rol){
      try{
        $prep = $this->con->prepare("SELECT * FROM rol WHERE id_rol = :id_r");
        $prep->execute([
          ":id_r" => $id_rol
        ]);
        $rol_encontrado = $prep->fetch(PDO::FETCH_ASSOC);
        if ($rol_encontrado){
          $rol_encontrado_objeto = new Rol(
            $rol_encontrado['nombre'],
            $rol_encontrado['id_rol']
          );
          
          return $rol_encontrado_objeto;
        }
        else{
          return null;
        }
      }
      catch(PDOException $e){
        var_dump($e);
        return null;
      }
    }

  }

?>