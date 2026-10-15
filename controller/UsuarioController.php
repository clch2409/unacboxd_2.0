<?php
  require_once __DIR__.'/../bd/bd_conection.php';
  require_once __DIR__.'/../model/Usuario.php';
  require_once __DIR__.'/../controller/RolController.php';

  class UsuarioController{

    private Database $bd;
    private PDO $con;

    public function __construct()
    {
      $this->bd = new Database();
      $this->con = $con ?? $this->bd->conectar();
    }

    public function crearUsuario(string $nombre_usuario, string $email, string $contrasena, string $pregunta_secreta){
      try{
        $prep = $this->con->prepare("SELECT nombre_usuario FROM usuario WHERE nombre_usuario = :username");
        $prep->execute([
          ":username" => $nombre_usuario,
        ]);
        if ($prep->rowCount() > 0){
          return false;
        }
        $prep = $this->con->prepare("SELECT nombre_usuario FROM usuario WHERE email = :email");
        $prep->execute([
          ":email" => $email,
        ]);
        if ($prep->rowCount() > 0){
          return false;
        }
        else{
          $prep = $this->con->prepare("INSERT INTO usuario (nombre_usuario, email, contrasena, pregunta_secreta, id_rol) VALUES (:username, :email, :password, :secret_question, 2)");
          $prep->execute([
            ":username" => $nombre_usuario,
            ":email" => $email,
            ":password" => password_hash($contrasena, PASSWORD_DEFAULT),
            ":secret_question" => password_hash($pregunta_secreta, PASSWORD_DEFAULT),
          ]);
          return true;
        }
      }
      catch(PDOException $e){
        var_dump($e);
        return false;
      }
    }

    public function validarLogin(string $nombre_usuario, string $contrasena){
      try{
        $usuario_encontrado = $this->buscarUsuarioPorNombreUsuario($nombre_usuario);

        if (!$usuario_encontrado || !password_verify($contrasena, $usuario_encontrado->getContrasena())){
          return false;
        }
        else{
          return $usuario_encontrado;
        }

      }
      catch(PDOException $e){
        var_dump($e);
        return false;
      }
    }

    public function buscarUsuarioPorNombreUsuario(string $nombre_usuario): Usuario | null{
      try{
        $prep = $this->con->prepare("SELECT * FROM usuario WHERE nombre_usuario = :username");
        

        $prep->execute([
          ":username" => $nombre_usuario,
        ]);

        $usuario_encontrado = $prep->fetch(PDO::FETCH_ASSOC);
        

        if ($usuario_encontrado){
          $usuario_encontrado_objeto = $this->crearObjetoUsuario($usuario_encontrado);
          return $usuario_encontrado_objeto;
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

    public function actualizarContrasena(string $nombre_usuario, string $contrasena_antigua, string $nueva_contrasena){
      try{
        $usuario_encontrado = $this->buscarUsuarioPorNombreUsuario($nombre_usuario);

        if (!$usuario_encontrado){
          return false;
        }
        else if (!password_verify($contrasena_antigua, $usuario_encontrado->getContrasena())){
          return false;
        }
      }
      catch(PDOException $e){
        var_dump($e);
        return true;
      }
    }

    public function crearObjetoUsuario(array $usuario_array_asoc): Usuario{
      $rol_controller = new RolController();
      return new Usuario(
        $usuario_array_asoc['nombre_usuario'],
        $usuario_array_asoc['email'],
        $usuario_array_asoc['contrasena'],
        $usuario_array_asoc['pregunta_secreta'],
        $rol_controller->obtenerRolPorId($usuario_array_asoc['id_rol']),
        $usuario_array_asoc['id_usuario']
      );
    }

  }


?>