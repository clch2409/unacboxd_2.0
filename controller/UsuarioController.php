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

    public function crearUsuario(string $nombre_usuario, string $email, string $contrasena, string $pregunta_secreta, ?string $imagen_perfil){
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
          $prep = $this->con->prepare("INSERT INTO usuario (nombre_usuario, email, contrasena, pregunta_secreta, id_rol, foto_perfil) VALUES (:username, :email, :password, :secret_question, 2, :foto_perfil)");
          $prep->execute([
            ":username" => $nombre_usuario,
            ":email" => $email,
            ":password" => password_hash($contrasena, PASSWORD_DEFAULT),
            ":secret_question" => password_hash($pregunta_secreta, PASSWORD_DEFAULT),
            ":foto_perfil" => $imagen_perfil
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

    public function cambiarContrasena(string $nombre_usuario, string $contrasena_antigua, string $nueva_contrasena){
      try{
        $usuario_encontrado = $this->buscarUsuarioPorNombreUsuario($nombre_usuario);
        $error = [
          "error" => ""
        ];

        if (!$usuario_encontrado){
          $error['error'] = "existe";
          return $error;
        }
        else if (password_verify($nueva_contrasena, $usuario_encontrado->getContrasena())){
          $error['error'] = "iguales";
          return $error;
        }
        else if (!password_verify($contrasena_antigua, $usuario_encontrado->getContrasena())){
          $error['error'] = "actual";
          return $error;
        }
        else{
          $prep = $this->con->prepare("UPDATE usuario SET contrasena = :contra WHERE id_usuario = :id");
          $nueva_contra_hash = password_hash($nueva_contrasena, PASSWORD_DEFAULT);
          $prep->execute([
            ":contra" => $nueva_contra_hash,
            ":id" => $usuario_encontrado->getIdUsuario()
          ]);
          return true;
        }
      }
      catch(PDOException $e){
        var_dump($e);
        return false;
      }
    }

    public function recuperarContrasena(string $nombre_usuario, string $respuesta_secreta, string $nueva_contrasena){
      try{
        $usuario_encontrado = $this->buscarUsuarioPorNombreUsuario($nombre_usuario);
        $error = ["error" => ""];

        if (!$usuario_encontrado){
          $error['error'] = "existe";
          return $error;
        }
        else if (!password_verify($respuesta_secreta, $usuario_encontrado->getPreguntaSecreta())){
          $error['error'] = "secreta";
          return $error;
        }
        else if (password_verify($nueva_contrasena, $usuario_encontrado->getContrasena())){
          $error['error'] = "iguales";
          return $error;
        }
        else{
          $prep = $this->con->prepare("UPDATE usuario SET contrasena = :contra WHERE id_usuario = :id");
          $prep->execute([
            ":contra" => password_hash($nueva_contrasena, PASSWORD_DEFAULT),
            ":id" => $usuario_encontrado->getIdUsuario()
          ]);
          
          return true;
        }
      }
      catch(PDOException $e){
        var_dump($e);
        return false;
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
        $usuario_array_asoc['foto_perfil'],
        $usuario_array_asoc['id_usuario'],
      );
    }

  }


?>