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

        public function __construct(){
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
            foreach($calificaciones as $calificacion){
                $pelicula_objeto->agregarCalificacion($calificacion);
            }
            foreach($actores as $actor){
                $pelicula_objeto->agregarActor($actor);
            }
            foreach($generos as $genero){
                $pelicula_objeto->agregarGenero($genero);
            }
            return $pelicula_objeto;
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