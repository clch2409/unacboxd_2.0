<?php
  require_once __DIR__.'/../bd/bd_conection.php';
  require_once __DIR__.'/../model/Usuario.php';
  require_once __DIR__.'/../controller/RolController.php';

  class UsuarioController{

    private Database $bd;
    private PDO $con;

    public function __construct(?PDO $con = null)
    {
      $this->bd = new Database();
      $this->con = $con ?? $this->bd->conectar();
    }

    public function crearUsuario(string $nombre_usuario, string $email, string $contrasena, string $pregunta_secreta, ?string $imagen_perfil, int $id_rol = 2){
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
          $prep = $this->con->prepare("INSERT INTO usuario (nombre_usuario, email, contrasena, pregunta_secreta, id_rol, foto_perfil) VALUES (:username, :email, :password, :secret_question, :id_rol, :foto_perfil)");
          $prep->execute([
            ":username" => $nombre_usuario,
            ":email" => $email,
            ":password" => password_hash($contrasena, PASSWORD_DEFAULT),
            ":secret_question" => password_hash($pregunta_secreta, PASSWORD_DEFAULT),
            ":id_rol" => $id_rol,
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

    public function listarUsuarios(){
      try{
        $query = $this->con->query("SELECT * FROM usuario ORDER BY id_usuario");
        $usuarios_array = $query->fetchAll(PDO::FETCH_ASSOC);
        $rol_controller = new RolController($this->con);
        return array_map(fn($usuario) => $this->crearObjetoUsuario($usuario, $rol_controller), $usuarios_array);
      }
      catch(PDOException $e){
        error_log($e->getMessage());
        return null;
      }
    }

    public function buscarUsuarioPorId(int $id_usuario): Usuario | null{
      try{
        $prep = $this->con->prepare("SELECT * FROM usuario WHERE id_usuario = :id");
        $prep->execute([
          ":id" => $id_usuario
        ]);
        $usuario_encontrado = $prep->fetch(PDO::FETCH_ASSOC);

        if ($usuario_encontrado){
          return $this->crearObjetoUsuario($usuario_encontrado);
        }
        else{
          return null;
        }
      }
      catch(PDOException $e){
        error_log($e->getMessage());
        return null;
      }
    }

    public function actualizarUsuario(int $id_usuario, string $nombre_usuario, string $email, int $id_rol, ?string $nueva_contrasena = null){
      try{
        $error = ["error" => ""];

        $prep = $this->con->prepare("SELECT id_usuario FROM usuario WHERE (nombre_usuario = :username OR email = :email) AND id_usuario != :id");
        $prep->execute([
          ":username" => $nombre_usuario,
          ":email" => $email,
          ":id" => $id_usuario
        ]);
        if ($prep->fetch(PDO::FETCH_ASSOC)){
          $error['error'] = "existe";
          return $error;
        }

        $rol_controller = new RolController($this->con);
        if (!$rol_controller->obtenerRolPorId($id_rol)){
          $error['error'] = "rol";
          return $error;
        }

        $parametros = [
          ":username" => $nombre_usuario,
          ":email" => $email,
          ":id_rol" => $id_rol,
          ":id" => $id_usuario
        ];
        $consulta = "UPDATE usuario SET nombre_usuario = :username, email = :email, id_rol = :id_rol";

        if (!empty($nueva_contrasena)){
          $consulta .= ", contrasena = :contra";
          $parametros[":contra"] = password_hash($nueva_contrasena, PASSWORD_DEFAULT);
        }

        $prep = $this->con->prepare($consulta." WHERE id_usuario = :id");
        $prep->execute($parametros);
        return true;
      }
      catch(PDOException $e){
        error_log($e->getMessage());
        return false;
      }
    }

    public function eliminarUsuario(int $id_usuario){
      try{
        $this->con->beginTransaction();

        $prep = $this->con->prepare("DELETE FROM pelicula_usuario WHERE id_usuario = :id");
        $prep->execute([
          ":id" => $id_usuario
        ]);

        $prep = $this->con->prepare("DELETE FROM usuario WHERE id_usuario = :id");
        $prep->execute([
          ":id" => $id_usuario
        ]);

        $this->con->commit();
        return true;
      }
      catch(PDOException $e){
        if ($this->con->inTransaction()){
          $this->con->rollBack();
        }
        error_log($e->getMessage());
        return false;
      }
    }

    public function crearObjetoUsuario(array $usuario_array_asoc, ?RolController $rol_controller = null): Usuario{
      $rol_controller = $rol_controller ?? new RolController($this->con);
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