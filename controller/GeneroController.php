<?php
    require_once (realpath(dirname(__FILE__)).'/../bd/bd_conection.php');
    require_once (realpath(dirname(__FILE__)).'/../model/Genero.php');

    class GeneroController{

        private DataBase $bd;

        private PDO $con;

        public function __construct(?PDO $con = null){
            $this->bd = new DataBase();
            $this->con = $con ?? $this->bd->conectar();
        }

        public function listarGeneros(){
            try{
                $query = $this->con->query("SELECT * FROM genero ORDER BY nombre");
                $generos_array = $query->fetchAll(PDO::FETCH_ASSOC);
                return array_map([$this, 'convertirObjetoGenero'], $generos_array);
            }
            catch(PDOException $e){
                error_log($e->getMessage());
                return null;
            }
        }

        public function crearGenero(string $nombre){
            try{
                $prep = $this->con->prepare("SELECT id_genero FROM genero WHERE nombre = :nombre");
                $prep -> execute([
                    ":nombre" => $nombre
                ]);
                if ($prep->fetch(PDO::FETCH_ASSOC)){
                    return ["error" => "existe"];
                }
                $prep = $this->con->prepare("INSERT INTO genero (nombre) VALUES (:nombre)");
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

        public function actualizarGenero(int $id_genero, string $nombre){
            try{
                $prep = $this->con->prepare("SELECT id_genero FROM genero WHERE nombre = :nombre AND id_genero != :id_gen");
                $prep -> execute([
                    ":nombre" => $nombre,
                    ":id_gen" => $id_genero
                ]);
                if ($prep->fetch(PDO::FETCH_ASSOC)){
                    return ["error" => "existe"];
                }
                $prep = $this->con->prepare("UPDATE genero SET nombre = :nombre WHERE id_genero = :id_gen");
                $prep -> execute([
                    ":nombre" => $nombre,
                    ":id_gen" => $id_genero
                ]);
                return true;
            }
            catch(PDOException $e){
                error_log($e->getMessage());
                return false;
            }
        }

        public function eliminarGenero(int $id_genero){
            try{
                $prep = $this->con->prepare("DELETE FROM genero WHERE id_genero = :id_gen");
                $prep -> execute([
                    ":id_gen" => $id_genero
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

        public function buscarGeneroPorId(int $id_genero){
            try{
                $prep = $this->con->prepare("SELECT * FROM genero WHERE id_genero = :id_gen");
                $prep -> execute([
                    ":id_gen" => $id_genero
                ]);
                $genero_array_asoc = $prep->fetch(PDO::FETCH_ASSOC);
                if ($genero_array_asoc){
                    return new Genero(
                        nombre: $genero_array_asoc['nombre'],
                        idGenero: $genero_array_asoc['id_genero']
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

        public function convertirObjetoGenero($genero_array_asoc){
            $genero_objeto = new Genero(
                nombre: $genero_array_asoc['nombre'],
                idGenero: $genero_array_asoc['id_genero']
            );
            return $genero_objeto;
        }

        public function obtenerGenerosPorIdPelicula(int $id_pelicula){
            try{
                $prep = $this->con->prepare("SELECT g.* FROM genero g INNER JOIN pelicula_genero gp ON g.id_genero = gp.id_genero WHERE gp.id_pelicula = :id_peli");
                $prep -> execute([
                    ":id_peli" => $id_pelicula
                ]);
                $generos_array_asoc = $prep->fetchAll(PDO::FETCH_ASSOC);
                if ($generos_array_asoc){
                    return array_map([$this, 'convertirObjetoGenero'], $generos_array_asoc);
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