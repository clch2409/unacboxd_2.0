<?php
  require_once __DIR__.'/validar_admin.php';
  require_once __DIR__.'/limpiar_textos.php';
  require_once __DIR__.'/../controller/DirectorController.php';
  require_once __DIR__.'/../controller/ActorController.php';
  require_once __DIR__.'/../controller/GeneroController.php';

  // Este action maneja el CRUD de las tres entidades que solo tienen nombre: director, actor y genero
  $tipo = $_POST['tipo'] ?? '';

  if (!in_array($tipo, ['director', 'actor', 'genero'])){
    header("Location: ../view/admin_panel.php");
    exit();
  }

  if ($_SERVER['REQUEST_METHOD'] != 'POST' || !validarCsrf()){
    header("Location: ../view/admin_catalogo.php?tipo=$tipo");
    exit();
  }

  $accion = $_POST['accion'] ?? '';
  $id = (int) ($_POST['id'] ?? 0);
  $nombre = limpiarTexto($_POST['nombre'] ?? '');
  $limite_nombre = $tipo == 'genero' ? 50 : 100;
  $parametro_editar = $accion == 'editar' ? "&editar=$id" : "";

  if (!in_array($accion, ['crear', 'editar', 'eliminar'])){
    header("Location: ../view/admin_catalogo.php?tipo=$tipo");
    exit();
  }

  if ($accion != 'eliminar'){
    if (empty($nombre)){
      header("Location: ../view/admin_catalogo.php?tipo=$tipo&m_n=vacio".$parametro_editar);
      exit();
    }
    else if (mb_strlen($nombre) > $limite_nombre){
      header("Location: ../view/admin_catalogo.php?tipo=$tipo&m_n=largo".$parametro_editar);
      exit();
    }
  }

  $resultado = match($tipo.'.'.$accion){
    'director.crear' => (new DirectorController())->crearDirector($nombre),
    'director.editar' => (new DirectorController())->actualizarDirector($id, $nombre),
    'director.eliminar' => (new DirectorController())->eliminarDirector($id),
    'actor.crear' => (new ActorController())->crearActor($nombre),
    'actor.editar' => (new ActorController())->actualizarActor($id, $nombre),
    'actor.eliminar' => (new ActorController())->eliminarActor($id),
    'genero.crear' => (new GeneroController())->crearGenero($nombre),
    'genero.editar' => (new GeneroController())->actualizarGenero($id, $nombre),
    'genero.eliminar' => (new GeneroController())->eliminarGenero($id),
  };

  if (isset($resultado['error'])){
    $error = $resultado['error'];
    header("Location: ../view/admin_catalogo.php?tipo=$tipo&m_n=$error".$parametro_editar);
    exit();
  }
  else if (!$resultado){
    header("Location: ../view/admin_catalogo.php?tipo=$tipo&m_n=bd".$parametro_editar);
    exit();
  }

  $mensaje_exito = match($accion){
    'crear' => 'creado',
    'editar' => 'editado',
    'eliminar' => 'eliminado',
  };
  header("Location: ../view/admin_catalogo.php?tipo=$tipo&m_p=$mensaje_exito");
  exit();
