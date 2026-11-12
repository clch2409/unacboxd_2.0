<?php
    require_once (realpath(dirname(__FILE__)).'/../bd/bd_conection.php');
    require_once (realpath(dirname(__FILE__)).'/../model/Actor.php');

    class ActorController{

        private DataBase $bd;

        private PDO $con;

        public function __construct(?PDO $con = null){
            $this->bd = new DataBase();
            $this->con = $con ?? $this->bd->conectar();
        }

        public function crearActor(string $nombre){
            try{
                $prep = $this->con->prepare("SELECT id_actor FROM actor WHERE nombre = :nombre");
                $prep -> execute([
                    ":nombre" => $nombre
                ]);
                if ($prep->fetch(PDO::FETCH_ASSOC)){
                    return ["error" => "existe"];
                }
                $prep = $this->con->prepare("INSERT INTO actor (nombre) VALUES (:nombre)");
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

        public function actualizarActor(int $id_actor, string $nombre){
            try{
                $prep = $this->con->prepare("SELECT id_actor FROM actor WHERE nombre = :nombre AND id_actor != :id_act");
                $prep -> execute([
                    ":nombre" => $nombre,
                    ":id_act" => $id_actor
                ]);
                if ($prep->fetch(PDO::FETCH_ASSOC)){
                    return ["error" => "existe"];
                }
                $prep = $this->con->prepare("UPDATE actor SET nombre = :nombre WHERE id_actor = :id_act");
                $prep -> execute([
                    ":nombre" => $nombre,
                    ":id_act" => $id_actor
                ]);
                return true;
            }
            catch(PDOException $e){
                error_log($e->getMessage());
                return false;
            }
        }

        public function eliminarActor(int $id_actor){
            try{
                $prep = $this->con->prepare("DELETE FROM actor WHERE id_actor = :id_act");
                $prep -> execute([
                    ":id_act" => $id_actor
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

        public function listarActores(){
            try{
                $query = $this->con->query("SELECT * FROM actor ORDER BY nombre");
                $actores_array = $query->fetchAll(PDO::FETCH_ASSOC);
                if ($actores_array){
                    return array_map(fn($actor) => $this->convertirObjetoActor($actor), $actores_array);
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

        public function buscarActorPorId(int $id_actor){
            try{
                $prep = $this->con->prepare("SELECT * FROM actor WHERE id_actor = :id_act");
                $prep -> execute([
                    ":id_act" => $id_actor
                ]);
                $actor_array_asoc = $prep->fetch(PDO::FETCH_ASSOC);
                if ($actor_array_asoc){
                    return new Actor(
                        nombre: $actor_array_asoc['nombre'],
                        idActor: $actor_array_asoc['id_actor']
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

        public function obtenerActoresPorIdPelicula(int $id_pelicula){
            try{
                $prep = $this->con->prepare("SELECT a.* FROM actor a INNER JOIN pelicula_actor ap ON a.id_actor = ap.id_actor WHERE ap.id_pelicula = :id_peli");
                $prep -> execute([
                    ":id_peli" => $id_pelicula
                ]);
                $actores_array_asoc = $prep->fetchAll(PDO::FETCH_ASSOC);
                if ($actores_array_asoc){
                    return array_map([$this, 'convertirObjetoActor'], $actores_array_asoc);
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

        public function convertirObjetoActor($actor_array_asoc){
            $actor_objeto = new Actor(
                nombre: $actor_array_asoc['nombre'],
                idActor: $actor_array_asoc['id_actor']
            );
            return $actor_objeto;
        }

    }