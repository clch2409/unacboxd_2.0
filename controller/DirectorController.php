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