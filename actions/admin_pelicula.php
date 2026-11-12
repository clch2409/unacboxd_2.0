<?php
  require_once __DIR__.'/validar_admin.php';
  require_once __DIR__.'/limpiar_textos.php';
  require_once __DIR__.'/../controller/PeliculaController.php';

  if ($_SERVER['REQUEST_METHOD'] != 'POST' || !validarCsrf()){
    header("Location: ../view/admin_peliculas.php");
    exit();
  }

  // Guarda el poster en assets/posters y devuelve la ruta que se guarda en la BD (o false si no es válido)
  function guardarPoster(array $archivo){
    $extensiones_permitidas = ['jpg', 'jpeg', 'png', 'webp'];
    $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));

    if (!in_array($extension, $extensiones_permitidas) || $archivo['size'] > 4 * 1024 * 1024 || getimagesize($archivo['tmp_name']) === false){
      return false;
    }

    $nombre_unico = uniqid("poster_") . '.' . $extension;
    $ruta_destino = __DIR__.'/../assets/posters/'.$nombre_unico;

    if (move_uploaded_file($archivo['tmp_name'], $ruta_destino)){
      return 'assets/posters/'.$nombre_unico;
    }
    return false;
  }

  $pelicula_controller = new PeliculaController();
  $accion = $_POST['accion'] ?? '';

  if ($accion == 'eliminar'){
    $id_pelicula = (int) ($_POST['id_pelicula'] ?? 0);

    if (!$pelicula_controller->eliminarPelicula($id_pelicula)){
      header("Location: ../view/admin_peliculas.php?m_n=bd");
      exit();
    }
    header("Location: ../view/admin_peliculas.php?m_p=eliminada");
    exit();
  }
  else if ($accion == 'crear' || $accion == 'editar'){
    $id_pelicula = (int) ($_POST['id_pelicula'] ?? 0);
    $titulo = limpiarTexto($_POST['titulo'] ?? '');
    $anio_estreno = (int) ($_POST['anio_estreno'] ?? 0);
    $duracion_minutos = (int) ($_POST['duracion_minutos'] ?? 0);
    $pais = limpiarTexto($_POST['pais'] ?? '');
    $clasificacion_edad = limpiarTexto($_POST['clasificacion_edad'] ?? '');
    $sinopsis = limpiarTexto($_POST['sinopsis'] ?? '');
    $color = $_POST['color'] ?? '';
    $fondo_claro = isset($_POST['fondo_claro']);
    $id_director = (int) ($_POST['id_director'] ?? 0);
    $ids_generos = $_POST['generos'] ?? [];
    $ids_actores = $_POST['actores'] ?? [];

    $parametro_editar = $accion == 'editar' ? "&editar=$id_pelicula" : "";

    if (empty($titulo) || empty($pais) || empty($sinopsis) || $id_director <= 0){
      header("Location: ../view/admin_peliculas.php?m_n=vacio".$parametro_editar);
      exit();
    }
    else if (mb_strlen($titulo) > 150 || mb_strlen($pais) > 100){
      header("Location: ../view/admin_peliculas.php?m_n=largo".$parametro_editar);
      exit();
    }
    else if ($anio_estreno < 1888 || $anio_estreno > (int) date('Y') + 5){
      header("Location: ../view/admin_peliculas.php?m_n=anio".$parametro_editar);
      exit();
    }
    else if ($duracion_minutos < 1 || $duracion_minutos > 600){
      header("Location: ../view/admin_peliculas.php?m_n=duracion".$parametro_editar);
      exit();
    }
    else if (!in_array($clasificacion_edad, ['G', 'PG', 'PG-13', 'R', 'NC-17'])){
      header("Location: ../view/admin_peliculas.php?m_n=clasificacion".$parametro_editar);
      exit();
    }
    else if (empty($ids_generos)){
      header("Location: ../view/admin_peliculas.php?m_n=generos".$parametro_editar);
      exit();
    }

    if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $color)){
      $color = null;
    }

    $ruta_poster = null;
    $hay_poster = isset($_FILES['poster']) && $_FILES['poster']['error'] != UPLOAD_ERR_NO_FILE;

    if ($hay_poster){
      $ruta_poster = $_FILES['poster']['error'] == UPLOAD_ERR_OK ? guardarPoster($_FILES['poster']) : false;

      if (!$ruta_poster){
        header("Location: ../view/admin_peliculas.php?m_n=poster".$parametro_editar);
        exit();
      }
    }
    else if ($accion == 'crear'){
      header("Location: ../view/admin_peliculas.php?m_n=poster_obligatorio");
      exit();
    }

    if ($accion == 'crear'){
      $resultado = $pelicula_controller->crearPelicula(
        $titulo, $anio_estreno, $duracion_minutos, $pais, $clasificacion_edad, $sinopsis,
        $ruta_poster, $color, $fondo_claro, $id_director, $ids_generos, $ids_actores
      );
      $mensaje_exito = "creada";
    }
    else{
      $resultado = $pelicula_controller->actualizarPelicula(
        $id_pelicula, $titulo, $anio_estreno, $duracion_minutos, $pais, $clasificacion_edad, $sinopsis,
        $ruta_poster, $color, $fondo_claro, $id_director, $ids_generos, $ids_actores
      );
      $mensaje_exito = "editada";
    }

    if (!$resultado){
      header("Location: ../view/admin_peliculas.php?m_n=bd".$parametro_editar);
      exit();
    }
    header("Location: ../view/admin_peliculas.php?m_p=$mensaje_exito");
    exit();
  }
  else{
    header("Location: ../view/admin_peliculas.php");
    exit();
  }
