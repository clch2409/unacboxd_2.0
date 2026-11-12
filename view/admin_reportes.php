<?php
  require_once __DIR__.'/../actions/validar_admin.php';
  require_once __DIR__.'/../controller/ReporteController.php';

  $reporte_controller = new ReporteController();

  $resumen = $reporte_controller->obtenerResumenGeneral();
  $mejor_calificadas = $reporte_controller->obtenerPeliculasMejorCalificadas();
  $peor_calificadas = $reporte_controller->obtenerPeliculasPeorCalificadas();
  $mas_favoritas = $reporte_controller->obtenerPeliculasMasFavoritas();
  $sin_calificaciones = $reporte_controller->obtenerPeliculasSinCalificaciones();
  $peliculas_por_genero = $reporte_controller->obtenerPeliculasPorGenero();
  $usuarios_por_rol = $reporte_controller->obtenerUsuariosPorRol();
  $usuarios_activos = $reporte_controller->obtenerUsuariosMasActivos();
  $distribucion_calificaciones = $reporte_controller->obtenerDistribucionCalificaciones();

  // La distribución siempre muestra los 5 rangos, aunque alguno no tenga calificaciones
  $rangos_estrellas = [1 => '0 – 1', 2 => '1 – 2', 3 => '2 – 3', 4 => '3 – 4', 5 => '4 – 5'];
  $estrellas = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
  foreach($distribucion_calificaciones as $fila){
    $estrellas[(int) $fila['estrellas']] = (int) $fila['total'];
  }

  $max_genero = max(array_column($peliculas_por_genero, 'total') ?: [1]);
  $max_estrellas = max($estrellas) ?: 1;
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reportes - Administrador</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Arimo:ital,wght@0,400..700;1,400..700&display=swap"
    rel="stylesheet">
  <link rel="stylesheet" href="../assets/style/pag_principal.css">
  <link rel="stylesheet" href="../assets/style/admin.css">
</head>

<body>
  <?php require __DIR__.'/admin_nav.php'; ?>

  <main class="admin-main">
    <h1>Reportes</h1>
    <p class="admin-subtitle">Información relevante del sitio.</p>

    <section class="stat-grid">
      <div class="stat-card">
        <div class="stat-number"><?= $resumen['total_usuarios'] ?? 0 ?></div>
        <div class="stat-label">Usuarios</div>
      </div>
      <div class="stat-card">
        <div class="stat-number"><?= $resumen['total_administradores'] ?? 0 ?></div>
        <div class="stat-label">Administradores</div>
      </div>
      <div class="stat-card">
        <div class="stat-number"><?= $resumen['total_peliculas'] ?? 0 ?></div>
        <div class="stat-label">Películas</div>
      </div>
      <div class="stat-card">
        <div class="stat-number"><?= $resumen['total_directores'] ?? 0 ?></div>
        <div class="stat-label">Directores</div>
      </div>
      <div class="stat-card">
        <div class="stat-number"><?= $resumen['total_actores'] ?? 0 ?></div>
        <div class="stat-label">Actores</div>
      </div>
      <div class="stat-card">
        <div class="stat-number"><?= $resumen['total_generos'] ?? 0 ?></div>
        <div class="stat-label">Géneros</div>
      </div>
      <div class="stat-card">
        <div class="stat-number"><?= $resumen['total_calificaciones'] ?? 0 ?></div>
        <div class="stat-label">Calificaciones</div>
      </div>
      <div class="stat-card">
        <div class="stat-number"><?= $resumen['promedio_general'] ?? '—' ?></div>
        <div class="stat-label">Promedio general</div>
      </div>
      <div class="stat-card">
        <div class="stat-number"><?= $resumen['total_favoritas'] ?? 0 ?></div>
        <div class="stat-label">Películas favoritas</div>
      </div>
    </section>

    <section class="report-grid">
      <div class="report-box">
        <h2>Mejor calificadas</h2>
        <?php if (empty($mejor_calificadas)): ?>
          <p class="empty">Aún no hay calificaciones.</p>
        <?php else: ?>
          <ul class="plain-list">
            <?php foreach($mejor_calificadas as $fila): ?>
              <li><span><?= esc($fila['titulo']) ?> (<?= $fila['total_votos'] ?> votos)</span><span><?= $fila['promedio'] ?></span></li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </div>

      <div class="report-box">
        <h2>Peor calificadas</h2>
        <?php if (empty($peor_calificadas)): ?>
          <p class="empty">Aún no hay calificaciones.</p>
        <?php else: ?>
          <ul class="plain-list">
            <?php foreach($peor_calificadas as $fila): ?>
              <li><span><?= esc($fila['titulo']) ?> (<?= $fila['total_votos'] ?> votos)</span><span><?= $fila['promedio'] ?></span></li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </div>

      <div class="report-box">
        <h2>Más favoritas</h2>
        <?php if (empty($mas_favoritas)): ?>
          <p class="empty">Aún no hay películas favoritas.</p>
        <?php else: ?>
          <ul class="plain-list">
            <?php foreach($mas_favoritas as $fila): ?>
              <li><span><?= esc($fila['titulo']) ?></span><span><?= $fila['total_favoritos'] ?></span></li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </div>

      <div class="report-box">
        <h2>Usuarios más activos</h2>
        <?php if (empty($usuarios_activos)): ?>
          <p class="empty">Aún no hay interacciones de usuarios.</p>
        <?php else: ?>
          <ul class="plain-list">
            <?php foreach($usuarios_activos as $fila): ?>
              <li><span><?= esc($fila['nombre_usuario']) ?></span><span><?= $fila['total_interacciones'] ?> interacciones</span></li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </div>

      <div class="report-box">
        <h2>Distribución de calificaciones</h2>
        <?php foreach($estrellas as $cantidad_estrellas => $total): ?>
          <div class="bar-row">
            <span><?= $rangos_estrellas[$cantidad_estrellas] ?> ★</span>
            <div class="bar-track"><div class="bar-fill" style="width: <?= round($total / $max_estrellas * 100) ?>%"></div></div>
            <span class="bar-value"><?= $total ?></span>
          </div>
        <?php endforeach; ?>
      </div>

      <div class="report-box">
        <h2>Películas por género</h2>
        <?php if (empty($peliculas_por_genero)): ?>
          <p class="empty">Aún no hay géneros registrados.</p>
        <?php else: ?>
          <?php foreach($peliculas_por_genero as $fila): ?>
            <div class="bar-row">
              <span><?= esc($fila['nombre']) ?></span>
              <div class="bar-track"><div class="bar-fill" style="width: <?= round($fila['total'] / $max_genero * 100) ?>%"></div></div>
              <span class="bar-value"><?= $fila['total'] ?></span>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>

      <div class="report-box">
        <h2>Usuarios por rol</h2>
        <ul class="plain-list">
          <?php foreach($usuarios_por_rol as $fila): ?>
            <li><span><?= esc($fila['nombre']) ?></span><span><?= $fila['total'] ?></span></li>
          <?php endforeach; ?>
        </ul>
      </div>

      <div class="report-box">
        <h2>Películas sin calificaciones (<?= count($sin_calificaciones) ?>)</h2>
        <?php if (empty($sin_calificaciones)): ?>
          <p class="empty">Todas las películas tienen al menos una calificación.</p>
        <?php else: ?>
          <ul class="plain-list">
            <?php foreach($sin_calificaciones as $fila): ?>
              <li><span><?= esc($fila['titulo']) ?></span></li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </div>
    </section>
  </main>
</body>

</html>
