<?php


  class UsuarioController{

    private Database $bd;
    private PDO $con;

    public function __construct()
    {
      $this->bd = new Database();
      $this->con = $this->bd->conectar();
    }

    public function crearUsuario(string $nombre_usuario, string $email, string $contrasena, string $pregunta_secreta){
      try{
        $contrasena_hash = password_hash($contrasena, PASSWORD_DEFAULT);
        $prep = $this->con->prepare("INSERT INTO usuario (nombre_usuario, email, contrasena, pregunta_secreta, id_rol) VALUES (:nombre_usuario, :email, :contra, :preg_sec)");
        $prep->execute([
          ":nombre_usuario" => $nombre_usuario,
          ":email" => $email,
          ":contra" => $contrasena_hash,
          ":preg_sec" => $pregunta_secreta,
        ]);
        return true;
      }
      catch(PDOException $e){
        var_dump($e);
        return false;
      }
    }

    public function validarLogin(string $nombre_usuario, string $contrasena){
      try{
        $usuario_encontrado = $this->buscarUsuarioPorNombreUsuario($nombre_usuario);

        if (password_verify($contrasena, $usuario_encontrado->getContrasena())){
          return true;
        }
        else{
          return false;
        }

      }
      catch(PDOException $e){
        var_dump($e);
        return false;
      }
    }

    public function buscarUsuarioPorNombreUsuario(string $nombre_usuario): Usuario | null{
      try{
        $prep = $this->con->prepare("SELECT nombre_usuario, contrasena FROM usuario WHERE nombre_usuario = :username");
        

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