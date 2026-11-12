<?php
    require_once (realpath(dirname(__FILE__)).'/../bd/bd_conection.php');
    require_once (realpath(dirname(__FILE__)).'/../model/Director.php');

    class DirectorController{

        private DataBase $bd;

        private PDO $con;

        public function __construct(?PDO $con = null){
            $this->bd = new DataBase();
            $this->con = $con ?? $this->bd->conectar();
        }

        public function listarDirectores(){
            try{
                $query = $this->con->query("SELECT * FROM director ORDER BY nombre");
                $directores_array = $query->fetchAll(PDO::FETCH_ASSOC);
                return array_map(fn($director) => new Director(
                    nombre: $director['nombre'],
                    idDirector: $director['id_director']
                ), $directores_array);
            }
            catch(PDOException $e){
                error_log($e->getMessage());
                return null;
            }
        }

        public function crearDirector(string $nombre){
            try{
                $prep = $this->con->prepare("SELECT id_director FROM director WHERE nombre = :nombre");
                $prep -> execute([
                    ":nombre" => $nombre
                ]);
                if ($prep->fetch(PDO::FETCH_ASSOC)){
                    return ["error" => "existe"];
                }
                $prep = $this->con->prepare("INSERT INTO director (nombre) VALUES (:nombre)");
                $prep -> execute([
                    ":nombre" => $nombre
                ]);
                return true;
            }
            catch(PDOException $e){
                error_log($e->getMessage());
                return false;
            }
        }

        public function actualizarDirector(int $id_director, string $nombre){
            try{
                $prep = $this->con->prepare("SELECT id_director FROM director WHERE nombre = :nombre AND id_director != :id_dir");
                $prep -> execute([
                    ":nombre" => $nombre,
                    ":id_dir" => $id_director
                ]);
                if ($prep->fetch(PDO::FETCH_ASSOC)){
                    return ["error" => "existe"];
                }
                $prep = $this->con->prepare("UPDATE director SET nombre = :nombre WHERE id_director = :id_dir");
                $prep -> execute([
                    ":nombre" => $nombre,
                    ":id_dir" => $id_director
                ]);
                return true;
            }
            catch(PDOException $e){
                error_log($e->getMessage());
                return false;
            }
        }

        public function eliminarDirector(int $id_director){
            try{
                $prep = $this->con->prepare("DELETE FROM director WHERE id_director = :id_dir");
                $prep -> execute([
                    ":id_dir" => $id_director
                ]);
                return true;
            }
            catch(PDOException $e){
                if ($e->getCode() == '23000'){
                    return ["error" => "asociado"];
                }
                error_log($e->getMessage());
                return false;
            }
        }

        public function buscarDirectorPorId(int $id_director){
            try{
                $prep = $this->con->prepare("SELECT * FROM director WHERE id_director = :id_dir");
                $prep -> execute([
                    ":id_dir" => $id_director
                ]);
                $director_array_asoc = $prep->fetch(PDO::FETCH_ASSOC);
                if ($director_array_asoc){
                    return new Director(
                        nombre: $director_array_asoc['nombre'],
                        idDirector: $director_array_asoc['id_director']
                    );
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