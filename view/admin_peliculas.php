<?php
  require_once __DIR__.'/../actions/validar_admin.php';
  require_once __DIR__.'/../controller/PeliculaController.php';
  require_once __DIR__.'/../controller/DirectorController.php';
  require_once __DIR__.'/../controller/ActorController.php';
  require_once __DIR__.'/../controller/GeneroController.php';

  $mensaje_negativo = $mensaje_positivo = "";
  $pelicula_controller = new PeliculaController();

  if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['m_n'])){
    $mensaje_negativo = match($_GET['m_n']){
      "vacio" => "Título, país, sinopsis y director son obligatorios.",
      "largo" => "El título o el país son demasiado largos.",
      "anio" => "El año de estreno no es válido.",
      "duracion" => "La duración debe estar entre 1 y 600 minutos.",
      "clasificacion" => "La clasificación de edad no es válida.",
      "generos" => "Selecciona al menos un género.",
      "poster" => "El poster debe ser una imagen JPG, PNG o WEBP de máximo 4 MB.",
      "poster_obligatorio" => "Debes subir un poster para la nueva película.",
      "bd" => "Ocurrió un error al guardar los cambios.",
      default => ""
    };
  }
  else if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['m_p'])){
    $mensaje_positivo = match($_GET['m_p']){
      "creada" => "Película creada correctamente.",
      "editada" => "Película actualizada correctamente.",
      "eliminada" => "Película eliminada correctamente.",
      default => ""
    };
  }

  $peliculas = $pelicula_controller->listarPeliculas() ?: [];
  $directores = (new DirectorController())->listarDirectores() ?? [];
  $generos = (new GeneroController())->listarGeneros() ?? [];
  $actores = (new ActorController())->listarActores() ?? [];
  $clasificaciones = ['G', 'PG', 'PG-13', 'R', 'NC-17'];

  // Valores por defecto del formulario (se reemplazan si se está editando una película)
  $form = [
    'id' => '',
    'titulo' => '',
    'anio_estreno' => '',
    'duracion_minutos' => '',
    'pais' => '',
    'clasificacion_edad' => '',
    'sinopsis' => '',
    'color' => '#7c552e',
    'fondo_claro' => false,
    'id_director' => 0,
    'generos' => [],
    'actores' => [],
    'poster' => null,
  ];
  $editando = false;

  if (isset($_GET['editar'])){
    $pelicula_editar = $pelicula_controller->buscarPeliculaPorId((int) $_GET['editar']);

    if ($pelicula_editar){
      $editando = true;
      $form = [
        'id' => $pelicula_editar->getIdPelicula(),
        'titulo' => $pelicula_editar->getTitulo(),
        'anio_estreno' => $pelicula_editar->getAnioEstreno(),
        'duracion_minutos' => $pelicula_editar->getDuracionMinutos(),
        'pais' => $pelicula_editar->getPais(),
        'clasificacion_edad' => $pelicula_editar->getClasificacionEdad(),
        'sinopsis' => $pelicula_editar->getSinopsis(),
        'color' => $pelicula_editar->getColor() ?: '#7c552e',
        'fondo_claro' => (bool) $pelicula_editar->getFondoClaro(),
        'id_director' => $pelicula_editar->getDirector()->getIdDirector(),
        'generos' => array_map(fn(Genero $genero) => $genero->getIdGenero(), $pelicula_editar->getGeneros()),
        'actores' => array_map(fn(Actor $actor) => $actor->getIdActor(), $pelicula_editar->getActores()),
        'poster' => $pelicula_editar->getPoster(),
      ];
    }
  }
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Películas - Administrador</title>
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
    <h1>Películas</h1>

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
        <h2><?= $editando ? 'Editar película' : 'Agregar película' ?></h2>
        <form class="admin-form" action="../actions/admin_pelicula.php" method="POST" enctype="multipart/form-data">
          <?= campoCsrf() ?>
          <input type="hidden" name="accion" value="<?= $editando ? 'editar' : 'crear' ?>">
          <?php if ($editando): ?>
            <input type="hidden" name="id_pelicula" value="<?= $form['id'] ?>">
          <?php endif; ?>

          <label for="titulo">Título</label>
          <input type="text" id="titulo" name="titulo" value="<?= esc($form['titulo']) ?>" maxlength="150">

          <div class="form-row">
            <div>
              <label for="anio_estreno">Año de estreno</label>
              <input type="number" id="anio_estreno" name="anio_estreno" value="<?= esc($form['anio_estreno']) ?>" min="1888">
            </div>
            <div>
              <label for="duracion_minutos">Duración (min)</label>
              <input type="number" id="duracion_minutos" name="duracion_minutos" value="<?= esc($form['duracion_minutos']) ?>" min="1" max="600">
            </div>
          </div>

          <div class="form-row">
            <div>
              <label for="pais">País</label>
              <input type="text" id="pais" name="pais" value="<?= esc($form['pais']) ?>" maxlength="100">
            </div>
            <div>
              <label for="clasificacion_edad">Clasificación</label>
              <select id="clasificacion_edad" name="clasificacion_edad">
                <?php foreach($clasificaciones as $clasificacion): ?>
                  <option value="<?= $clasificacion ?>" <?= $form['clasificacion_edad'] == $clasificacion ? 'selected' : '' ?>><?= $clasificacion ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <label for="sinopsis">Sinopsis</label>
          <textarea id="sinopsis" name="sinopsis"><?= esc($form['sinopsis']) ?></textarea>

          <label for="id_director">Director</label>
          <select id="id_director" name="id_director">
            <option value="0">Selecciona un director</option>
            <?php foreach($directores as $director): ?>
              <option value="<?= $director->getIdDirector() ?>" <?= $form['id_director'] == $director->getIdDirector() ? 'selected' : '' ?>>
                <?= esc($director->getNombre()) ?>
              </option>
            <?php endforeach; ?>
          </select>

          <label>Géneros</label>
          <div class="check-list">
            <?php foreach($generos as $genero): ?>
              <label>
                <input type="checkbox" name="generos[]" value="<?= $genero->getIdGenero() ?>" <?= in_array($genero->getIdGenero(), $form['generos']) ? 'checked' : '' ?>>
                <?= esc($genero->getNombre()) ?>
              </label>
            <?php endforeach; ?>
          </div>

          <label>Elenco</label>
          <div class="check-list">
            <?php foreach($actores as $actor): ?>
              <label>
                <input type="checkbox" name="actores[]" value="<?= $actor->getIdActor() ?>" <?= in_array($actor->getIdActor(), $form['actores']) ? 'checked' : '' ?>>
                <?= esc($actor->getNombre()) ?>
              </label>
            <?php endforeach; ?>
          </div>

          <div class="form-row">
            <div>
              <label for="color">Color de la película</label>
              <input type="color" id="color" name="color" value="<?= esc($form['color']) ?>">
            </div>
            <div>
              <label>&nbsp;</label>
              <label class="check-inline">
                <input type="checkbox" name="fondo_claro" <?= $form['fondo_claro'] ? 'checked' : '' ?>>
                Fondo claro
              </label>
            </div>
          </div>

          <label for="poster"><?= $editando ? 'Poster (opcional, para reemplazar el actual)' : 'Poster' ?></label>
          <input type="file" id="poster" name="poster" accept="image/png, image/jpeg, image/webp">

          <div class="form-buttons">
            <button type="submit" class="btn-admin"><?= $editando ? 'Guardar cambios' : 'Agregar película' ?></button>
            <?php if ($editando): ?>
              <a href="admin_peliculas.php" class="btn-admin">Cancelar</a>
            <?php endif; ?>
          </div>
        </form>
      </section>

      <section>
        <h2>Catálogo (<?= count($peliculas) ?>)</h2>
        <div class="table-filter">
          <input type="text" placeholder="Filtrar por título, director o género" data-filtro-tabla="tabla-peliculas">
        </div>
        <div class="table-wrapper">
          <table class="admin-table" id="tabla-peliculas">
            <thead>
              <tr>
                <th>Poster</th>
                <th>Título</th>
                <th>Año</th>
                <th>Director</th>
                <th>Géneros</th>
                <th>Rating</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($peliculas as $pelicula): ?>
                <tr>
                  <td><img class="poster-mini" src="../<?= esc($pelicula->getPoster()) ?>" alt="<?= esc($pelicula->getTitulo()) ?>"></td>
                  <td><?= esc($pelicula->getTitulo()) ?></td>
                  <td><?= $pelicula->getAnioEstreno() ?></td>
                  <td><?= esc($pelicula->getDirector()->getNombre()) ?></td>
                  <td><?= esc(implode(", ", array_map(fn(Genero $genero) => $genero->getNombre(), $pelicula->getGeneros()))) ?></td>
                  <td><?= $pelicula->calcularPromedioCalificaciones() ?? '—' ?></td>
                  <td>
                    <div class="acciones">
                      <a class="btn-admin btn-small" href="admin_peliculas.php?editar=<?= $pelicula->getIdPelicula() ?>">Editar</a>
                      <form action="../actions/admin_pelicula.php" method="POST" onsubmit="return confirm('¿Eliminar esta película? También se borrarán sus calificaciones y los favoritos asociados.');">
                        <?= campoCsrf() ?>
                        <input type="hidden" name="accion" value="eliminar">
                        <input type="hidden" name="id_pelicula" value="<?= $pelicula->getIdPelicula() ?>">
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
