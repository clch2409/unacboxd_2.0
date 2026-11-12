<?php
    require_once (realpath(dirname(__FILE__)).'/../bd/bd_conection.php');
    require_once (realpath(dirname(__FILE__)).'/../model/Pelicula.php');
    require_once (realpath(dirname(__FILE__)).'/../model/Director.php');
    require_once (realpath(dirname(__FILE__)).'/../model/Actor.php');
    require_once (realpath(dirname(__FILE__)).'/../model/Calificacion.php');
    require_once (realpath(dirname(__FILE__)).'/../model/Genero.php');
    require_once (realpath(dirname(__FILE__)).'/../controller/DirectorController.php');
    require_once (realpath(dirname(__FILE__)).'/../controller/ActorController.php');
    require_once (realpath(dirname(__FILE__)).'/../controller/CalificacionController.php');
    require_once (realpath(dirname(__FILE__)).'/../controller/GeneroController.php');

    class PeliculaController{

        private DataBase $bd;

        private PDO $con;

        public function __construct(?PDO $con = null){
            $this->bd = new DataBase();
            $this->con = $con ?? $this->bd->conectar();
        }

        public function listarPeliculas(){
            try{

                $prep = $this->con->query("SELECT * FROM pelicula");
                $peliculas = $prep->fetchAll(PDO::FETCH_ASSOC);
                if ($peliculas){
                    $peliculas_objeto = array_map(fn($peli) => $this->convertirPeliculaObjeto($peli), $peliculas);
                    return $peliculas_objeto;
                }
                else{
                    return null;
                }

            }
            catch(PDOException $e){
                var_dump($e);
                return false;
            }
        }

        public function convertirPeliculaObjeto(array $pelicula_array_asoc){
            $director_controller = new DirectorController($this->con);
            $pelicula_objeto = new Pelicula(
                titulo: $pelicula_array_asoc['titulo'],
                anioEstreno: $pelicula_array_asoc['anio_estreno'],
                duracionMinutos: $pelicula_array_asoc['duracion_minutos'],
                pais: $pelicula_array_asoc['pais'],
                clasificacionEdad: $pelicula_array_asoc['clasificacion_edad'],
                sinopsis: $pelicula_array_asoc['sinopsis'],
                director: $director_controller->buscarDirectorPorId($pelicula_array_asoc['id_director']),
                poster: $pelicula_array_asoc['poster'],
                color: $pelicula_array_asoc['color'],
                fondoClaro: $pelicula_array_asoc['fondo_claro'],
                idPelicula: $pelicula_array_asoc['id_pelicula'],
            );

            $calificacion_controller = new CalificacionController($this->con);
            $calificaciones = $calificacion_controller->obtenerCalificacionesPorIdPelicula(
                $pelicula_array_asoc['id_pelicula'],
                $pelicula_objeto
            );
            $actor_controller = new ActorController($this->con);
            $actores = $actor_controller->obtenerActoresPorIdPelicula($pelicula_array_asoc['id_pelicula']);
            $genero_controller = new GeneroController($this->con);
            $generos = $genero_controller->obtenerGenerosPorIdPelicula($pelicula_array_asoc['id_pelicula']);
            foreach($calificaciones ?? [] as $calificacion){
                $pelicula_objeto->agregarCalificacion($calificacion);
            }
            foreach($actores ?? [] as $actor){
                $pelicula_objeto->agregarActor($actor);
            }
            foreach($generos ?? [] as $genero){
                $pelicula_objeto->agregarGenero($genero);
            }
            return $pelicula_objeto;
        }

        public function crearPelicula(
            string $titulo,
            int $anio_estreno,
            int $duracion_minutos,
            string $pais,
            string $clasificacion_edad,
            string $sinopsis,
            ?string $poster,
            ?string $color,
            ?bool $fondo_claro,
            int $id_director,
            array $ids_generos,
            array $ids_actores
        ){
            try{
                $this->con->beginTransaction();

                $prep = $this->con->prepare("INSERT INTO pelicula (titulo, anio_estreno, duracion_minutos, pais, clasificacion_edad, sinopsis, poster, color, fondo_claro, id_director) VALUES (:titulo, :anio, :duracion, :pais, :clasificacion, :sinopsis, :poster, :color, :fondo_claro, :id_dir)");
                $prep -> execute([
                    ":titulo" => $titulo,
                    ":anio" => $anio_estreno,
                    ":duracion" => $duracion_minutos,
                    ":pais" => $pais,
                    ":clasificacion" => $clasificacion_edad,
                    ":sinopsis" => $sinopsis,
                    ":poster" => $poster,
                    ":color" => $color,
                    ":fondo_claro" => $fondo_claro === null ? null : (int) $fondo_claro,
                    ":id_dir" => $id_director
                ]);
                $id_pelicula = (int) $this->con->lastInsertId();

                $this->asociarGenerosYActores($id_pelicula, $ids_generos, $ids_actores);

                $this->con->commit();
                return $id_pelicula;
            }
            catch(PDOException $e){
                if ($this->con->inTransaction()){
                    $this->con->rollBack();
                }
                error_log($e->getMessage());
                return false;
            }
        }

        public function actualizarPelicula(
            int $id_pelicula,
            string $titulo,
            int $anio_estreno,
            int $duracion_minutos,
            string $pais,
            string $clasificacion_edad,
            string $sinopsis,
            ?string $poster,
            ?string $color,
            ?bool $fondo_claro,
            int $id_director,
            array $ids_generos,
            array $ids_actores
        ){
            try{
                $this->con->beginTransaction();

                $parametros = [
                    ":titulo" => $titulo,
                    ":anio" => $anio_estreno,
                    ":duracion" => $duracion_minutos,
                    ":pais" => $pais,
                    ":clasificacion" => $clasificacion_edad,
                    ":sinopsis" => $sinopsis,
                    ":color" => $color,
                    ":fondo_claro" => $fondo_claro === null ? null : (int) $fondo_claro,
                    ":id_dir" => $id_director,
                    ":id_peli" => $id_pelicula
                ];
                $consulta = "UPDATE pelicula SET titulo = :titulo, anio_estreno = :anio, duracion_minutos = :duracion, pais = :pais, clasificacion_edad = :clasificacion, sinopsis = :sinopsis, color = :color, fondo_claro = :fondo_claro, id_director = :id_dir";

                // Si no se sube un poster nuevo se conserva el que ya tiene la película
                if ($poster !== null){
                    $consulta .= ", poster = :poster";
                    $parametros[":poster"] = $poster;
                }

                $prep = $this->con->prepare($consulta." WHERE id_pelicula = :id_peli");
                $prep -> execute($parametros);

                $prep = $this->con->prepare("DELETE FROM pelicula_genero WHERE id_pelicula = :id_peli");
                $prep -> execute([":id_peli" => $id_pelicula]);
                $prep = $this->con->prepare("DELETE FROM pelicula_actor WHERE id_pelicula = :id_peli");
                $prep -> execute([":id_peli" => $id_pelicula]);

                $this->asociarGenerosYActores($id_pelicula, $ids_generos, $ids_actores);

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

        public function eliminarPelicula(int $id_pelicula){
            try{
                $this->con->beginTransaction();

                foreach(["pelicula_genero", "pelicula_actor", "pelicula_usuario", "calificacion"] as $tabla){
                    $prep = $this->con->prepare("DELETE FROM $tabla WHERE id_pelicula = :id_peli");
                    $prep -> execute([":id_peli" => $id_pelicula]);
                }

                $prep = $this->con->prepare("DELETE FROM pelicula WHERE id_pelicula = :id_peli");
                $prep -> execute([":id_peli" => $id_pelicula]);

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

        private function asociarGenerosYActores(int $id_pelicula, array $ids_generos, array $ids_actores){
            $prep_genero = $this->con->prepare("INSERT INTO pelicula_genero (id_pelicula, id_genero) VALUES (:id_peli, :id_gen)");
            foreach(array_unique(array_map('intval', $ids_generos)) as $id_genero){
                $prep_genero -> execute([
                    ":id_peli" => $id_pelicula,
                    ":id_gen" => $id_genero
                ]);
            }

            $prep_actor = $this->con->prepare("INSERT INTO pelicula_actor (id_pelicula, id_actor) VALUES (:id_peli, :id_act)");
            foreach(array_unique(array_map('intval', $ids_actores)) as $id_actor){
                $prep_actor -> execute([
                    ":id_peli" => $id_pelicula,
                    ":id_act" => $id_actor
                ]);
            }
        }

        public function buscarPeliculaPorId(int $id_pelicula){
            try{
                $prep = $this->con->prepare("SELECT * FROM pelicula WHERE id_pelicula = :id_peli");
                $prep -> execute([
                    ":id_peli" => $id_pelicula
                ]);
                $pelicula_array_asoc = $prep->fetch(PDO::FETCH_ASSOC);
                if ($pelicula_array_asoc){
                    return $this->convertirPeliculaObjeto($pelicula_array_asoc);
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