<?php
    require_once (realpath(dirname(__FILE__)).'/../bd/bd_conection.php');
    require_once (realpath(dirname(__FILE__)).'/../model/Calificacion.php');
    require_once (realpath(dirname(__FILE__)).'/../model/Pelicula.php');

    class CalificacionController{
        
        private DataBase $bd;

        private PDO $con;

        public function __construct(?PDO $con = null){
            $this->bd = new DataBase();
            $this->con = $con ?? $this->bd->conectar();
        }

        public function obtenerCalificacionesPorIdPelicula(int $id_pelicula, Pelicula $pelicula){
            try{

                $prep = $this->con->prepare("SELECT * FROM calificacion WHERE id_pelicula = :id_peli");
                $prep -> execute([
                    ":id_peli" => $id_pelicula
                ]);

                $calificaciones_array = $prep->fetchAll(PDO::FETCH_ASSOC);
                if ($calificaciones_array){
                    return array_map(
                        fn(array $calificacion) => $this->convertirCalificacion($calificacion, $pelicula),
                        $calificaciones_array
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
        
        public function convertirCalificacion(array $calificacion_array_asoc, Pelicula $pelicula){
            $calificacion_objeto = new Calificacion(
                calificacion: $calificacion_array_asoc['calificacion'],
                pelicula: $pelicula,
                idCalificacion: $calificacion_array_asoc['id_calificacion']
            );
            return $calificacion_objeto;
        }

    }

?>