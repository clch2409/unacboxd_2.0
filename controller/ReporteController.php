<?php
    require_once (realpath(dirname(__FILE__)).'/../bd/bd_conection.php');

    class ReporteController{

        private DataBase $bd;

        private PDO $con;

        public function __construct(?PDO $con = null){
            $this->bd = new DataBase();
            $this->con = $con ?? $this->bd->conectar();
        }

        public function obtenerResumenGeneral(){
            $consultas = [
                "total_usuarios" => "SELECT COUNT(*) FROM usuario",
                "total_administradores" => "SELECT COUNT(*) FROM usuario u INNER JOIN rol r ON u.id_rol = r.id_rol WHERE r.nombre = 'admin'",
                "total_peliculas" => "SELECT COUNT(*) FROM pelicula",
                "total_directores" => "SELECT COUNT(*) FROM director",
                "total_actores" => "SELECT COUNT(*) FROM actor",
                "total_generos" => "SELECT COUNT(*) FROM genero",
                "total_calificaciones" => "SELECT COUNT(*) FROM calificacion",
                "total_favoritas" => "SELECT COUNT(*) FROM (SELECT DISTINCT id_pelicula, id_usuario FROM pelicula_usuario WHERE tipo = 'favorita') favoritas",
                "promedio_general" => "SELECT ROUND(AVG(calificacion), 2) FROM calificacion",
            ];
            $resumen = [];

            try{
                foreach($consultas as $clave => $consulta){
                    $resumen[$clave] = $this->con->query($consulta)->fetchColumn();
                }
            }
            catch(PDOException $e){
                error_log($e->getMessage());
            }
            return $resumen;
        }

        public function obtenerPeliculasMejorCalificadas(int $limite = 5){
            return $this->consultarTodos("SELECT p.id_pelicula, p.titulo, ROUND(AVG(c.calificacion), 2) AS promedio, COUNT(c.id_calificacion) AS total_votos FROM pelicula p INNER JOIN calificacion c ON p.id_pelicula = c.id_pelicula GROUP BY p.id_pelicula, p.titulo ORDER BY promedio DESC, total_votos DESC, p.titulo LIMIT ".$limite);
        }

        public function obtenerPeliculasPeorCalificadas(int $limite = 5){
            return $this->consultarTodos("SELECT p.id_pelicula, p.titulo, ROUND(AVG(c.calificacion), 2) AS promedio, COUNT(c.id_calificacion) AS total_votos FROM pelicula p INNER JOIN calificacion c ON p.id_pelicula = c.id_pelicula GROUP BY p.id_pelicula, p.titulo ORDER BY promedio ASC, total_votos DESC, p.titulo LIMIT ".$limite);
        }

        public function obtenerPeliculasMasFavoritas(int $limite = 5){
            return $this->consultarTodos("SELECT p.id_pelicula, p.titulo, COUNT(DISTINCT pu.id_usuario) AS total_favoritos FROM pelicula p INNER JOIN pelicula_usuario pu ON p.id_pelicula = pu.id_pelicula WHERE pu.tipo = 'favorita' GROUP BY p.id_pelicula, p.titulo ORDER BY total_favoritos DESC, p.titulo LIMIT ".$limite);
        }

        public function obtenerPeliculasSinCalificaciones(){
            return $this->consultarTodos("SELECT p.id_pelicula, p.titulo FROM pelicula p LEFT JOIN calificacion c ON p.id_pelicula = c.id_pelicula WHERE c.id_calificacion IS NULL ORDER BY p.titulo");
        }

        public function obtenerPeliculasPorGenero(){
            return $this->consultarTodos("SELECT g.nombre, COUNT(pg.id_pelicula) AS total FROM genero g LEFT JOIN pelicula_genero pg ON g.id_genero = pg.id_genero GROUP BY g.id_genero, g.nombre ORDER BY total DESC, g.nombre");
        }

        public function obtenerUsuariosPorRol(){
            return $this->consultarTodos("SELECT r.nombre, COUNT(u.id_usuario) AS total FROM rol r LEFT JOIN usuario u ON r.id_rol = u.id_rol GROUP BY r.id_rol, r.nombre ORDER BY r.id_rol");
        }

        public function obtenerUsuariosMasActivos(int $limite = 5){
            return $this->consultarTodos("SELECT u.nombre_usuario, COUNT(*) AS total_interacciones FROM usuario u INNER JOIN (SELECT DISTINCT id_usuario, id_pelicula, tipo FROM pelicula_usuario) pu ON u.id_usuario = pu.id_usuario GROUP BY u.id_usuario, u.nombre_usuario ORDER BY total_interacciones DESC, u.nombre_usuario LIMIT ".$limite);
        }

        public function obtenerDistribucionCalificaciones(){
            // Agrupa las calificaciones (que pueden ser decimales) por rango: 1 = (0, 1], 2 = (1, 2], ..., 5 = (4, 5]
            return $this->consultarTodos("SELECT CASE WHEN calificacion <= 1 THEN 1 WHEN calificacion <= 2 THEN 2 WHEN calificacion <= 3 THEN 3 WHEN calificacion <= 4 THEN 4 ELSE 5 END AS estrellas, COUNT(*) AS total FROM calificacion GROUP BY estrellas ORDER BY estrellas");
        }

        private function consultarTodos(string $consulta){
            try{
                $query = $this->con->query($consulta);
                return $query->fetchAll(PDO::FETCH_ASSOC);
            }
            catch(PDOException $e){
                error_log($e->getMessage());
                return [];
            }
        }

    }
?>
