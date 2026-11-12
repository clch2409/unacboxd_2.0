<?php
  require_once __DIR__.'/../actions/validar_admin.php';
  require_once __DIR__.'/../controller/DirectorController.php';
  require_once __DIR__.'/../controller/ActorController.php';
  require_once __DIR__.'/../controller/GeneroController.php';

  // Esta vista maneja las tres entidades que solo tienen nombre: director, actor y genero
  $tipo = $_GET['tipo'] ?? '';

  if (!in_array($tipo, ['director', 'actor', 'genero'])){
    header("Location: admin_panel.php");
    exit();
  }

  $mensaje_negativo = $mensaje_positivo = "";

  if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['m_n'])){
    $mensaje_negativo = match($_GET['m_n']){
      "vacio" => "El nombre es obligatorio.",
      "largo" => "El nombre es demasiado largo.",
      "existe" => "Ya existe un registro con ese nombre.",
      "asociado" => "No se puede eliminar porque está asociado a una o más películas.",
      "bd" => "Ocurrió un error al guardar los cambios.",
      default => ""
    };
  }
  else if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['m_p'])){
    $mensaje_positivo = match($_GET['m_p']){
      "creado" => "Registro creado correctamente.",
      "editado" => "Registro actualizado correctamente.",
      "eliminado" => "Registro eliminado correctamente.",
      default => ""
    };
  }

  $titulo_plural = match($tipo){
    'director' => 'Directores',
    'actor' => 'Actores',
    'genero' => 'Géneros',
  };
  $titulo_singular = match($tipo){
    'director' => 'director',
    'actor' => 'actor',
    'genero' => 'género',
  };

  // Se normalizan los objetos a [id, nombre] para poder usar la misma tabla con las tres entidades
  $registros = match($tipo){
    'director' => array_map(
      fn(Director $director) => ['id' => $director->getIdDirector(), 'nombre' => $director->getNombre()],
      (new DirectorController())->listarDirectores() ?? []
    ),
    'actor' => array_map(
      fn(Actor $actor) => ['id' => $actor->getIdActor(), 'nombre' => $actor->getNombre()],
      (new ActorController())->listarActores() ?? []
    ),
    'genero' => array_map(
      fn(Genero $genero) => ['id' => $genero->getIdGenero(), 'nombre' => $genero->getNombre()],
      (new GeneroController())->listarGeneros() ?? []
    ),
  };

  $registro_editar = null;

  if (isset($_GET['editar'])){
    foreach($registros as $registro){
      if ($registro['id'] == (int) $_GET['editar']){
        $registro_editar = $registro;
      }
    }
  }
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $titulo_plural ?> - Administrador</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Arimo:ital,wght@0,400..700;1,400..700&display=swap"
    rel="stylesheet">
  <link rel="stylesheet"
    href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=favorite,star,close,error,check_circle" />
  <link rel="stylesheet" href="../assets/style/pag_principal.css">
  <link rel="stylesheet" href="../assets/style/admin.css">
</head>

<body>
  <?php require __DIR__.'/admin_nav.php'; ?>

  <main class="admin-main">
    <h1><?= $titulo_plural ?></h1>

    <?php if (!empty($mensaje_negativo)): ?>
      <div class="alert alert-error">
        <span class="material-symbols-outlined alert-icon">error</span>
        <span><?= $mensaje_negativo ?></span>
      </div>
    <?php endif; ?>
    <?php if (!empty($mensaje_positivo)): ?>
      <div class="alert alert-success">
        <span class="material-symbols-outlined alert-icon">check_circle</span>
        <span><?= $mensaje_positivo ?></span>
      </div>
    <?php endif; ?>

    <div class="admin-layout">
      <section class="admin-panel-box">
        <h2><?= $registro_editar ? "Editar $titulo_singular" : "Agregar $titulo_singular" ?></h2>
        <form class="admin-form" action="../actions/admin_catalogo.php" method="POST">
          <?= campoCsrf() ?>
          <input type="hidden" name="tipo" value="<?= $tipo ?>">
          <input type="hidden" name="accion" value="<?= $registro_editar ? 'editar' : 'crear' ?>">
          <?php if ($registro_editar): ?>
            <input type="hidden" name="id" value="<?= $registro_editar['id'] ?>">
          <?php endif; ?>

          <label for="nombre">Nombre</label>
          <input type="text" id="nombre" name="nombre" value="<?= $registro_editar ? esc($registro_editar['nombre']) : '' ?>" maxlength="<?= $tipo == 'genero' ? 50 : 100 ?>">

          <div class="form-buttons">
            <button type="submit" class="btn-admin"><?= $registro_editar ? 'Guardar cambios' : 'Agregar' ?></button>
            <?php if ($registro_editar): ?>
              <a href="admin_catalogo.php?tipo=<?= $tipo ?>" class="btn-admin">Cancelar</a>
            <?php endif; ?>
          </div>
        </form>
      </section>

      <section>
        <h2>Lista de <?= strtolower($titulo_plural) ?> (<?= count($registros) ?>)</h2>
        <div class="table-filter">
          <input type="text" placeholder="Filtrar por nombre" data-filtro-tabla="tabla-catalogo">
        </div>
        <div class="table-wrapper">
          <table class="admin-table" id="tabla-catalogo">
            <thead>
              <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($registros as $registro): ?>
                <tr>
                  <td><?= $registro['id'] ?></td>
                  <td><?= esc($registro['nombre']) ?></td>
                  <td>
                    <div class="acciones">
                      <a class="btn-admin btn-small" href="admin_catalogo.php?tipo=<?= $tipo ?>&editar=<?= $registro['id'] ?>">Editar</a>
                      <form action="../actions/admin_catalogo.php" method="POST" onsubmit="return confirm('¿Eliminar este registro?');">
                        <?= campoCsrf() ?>
                        <input type="hidden" name="tipo" value="<?= $tipo ?>">
                        <input type="hidden" name="accion" value="eliminar">
                        <input type="hidden" name="id" value="<?= $registro['id'] ?>">
                        <button type="submit" class="btn-admin btn-small btn-danger">Eliminar</button>
                      </form>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </section>
    </div>
  </main>

  <script src="../assets/js/admin.js"></script>
</body>

</html>
