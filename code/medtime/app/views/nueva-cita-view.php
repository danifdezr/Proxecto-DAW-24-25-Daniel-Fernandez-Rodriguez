<?php
$profesionales = $data['profesionales'] ?? [];
$busqueda      = $data['busqueda']      ?? '';
$flashError    = $data['error']         ?? null;

$usuario = $_SESSION['usuario'];
$nombreCompleto = htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellidos']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="/assets/css/cssPrincipal.css?v=<?= filemtime('/var/www/html/public/assets/css/cssPrincipal.css') ?>">
  <link rel="stylesheet" href="/assets/css/paciente.css?v=<?= filemtime('/var/www/html/public/assets/css/paciente.css') ?>">
  <title>MedTime - Pedir cita</title>
</head>
<body>
  <a class="skip-link" href="#contenido-principal">Saltar al contenido principal</a>

  <header class="site-header">
    <div class="header-inner">
      <a class="brand" href="/index.php?controller=PacienteController&action=panel" aria-label="MedTime">
        <span class="brand-mark" aria-hidden="true">M</span>
        <span>MedTime</span>
      </a>
      <nav aria-label="Navegación principal">
        <ul class="nav-list">
          <li><a href="/index.php?controller=PacienteController&action=panel">Panel</a></li>
          <li><a href="/index.php?controller=PacienteController&action=listCitas">Mis citas</a></li>
          <li><a href="/index.php?controller=PacienteController&action=nuevaCita" aria-current="page">Pedir cita</a></li>
          <li><a href="/index.php?controller=PacienteController&action=perfil">Mi perfil</a></li>
        </ul>
      </nav>
      <div class="user-tools">
        <button class="profile-button" type="button">
          <span class="avatar-initials" aria-hidden="true"><?= htmlspecialchars(mb_strtoupper(mb_substr($usuario['nombre'], 0, 1))) ?></span>
          <span><?= $nombreCompleto ?></span>
          <span aria-hidden="true">⌄</span>
        </button>
        <a class="btn-logout" href="/index.php?controller=AuthController&action=logout">Salir</a>
      </div>
    </div>
  </header>

  <main id="contenido-principal" class="page">

    <?php if ($flashError): ?>
      <div class="alert alert-error" role="alert"><?= htmlspecialchars($flashError) ?></div>
    <?php endif; ?>

    <div class="page-head">
      <div>
        <h1 class="page-title">Pedir cita</h1>
        <p class="page-subtitle">Busca un médico y selecciona el hueco que mejor te venga</p>
      </div>
    </div>

    <!-- Buscador -->
    <form class="search-bar" method="GET" action="/index.php" role="search">
      <input type="hidden" name="controller" value="PacienteController">
      <input type="hidden" name="action" value="nuevaCita">
      <label class="sr-only" for="q">Buscar médico o especialidad</label>
      <input
        class="search-input"
        type="search"
        id="q"
        name="q"
        value="<?= htmlspecialchars($busqueda) ?>"
        placeholder="Buscar por nombre o especialidad…"
        autocomplete="off"
      >
      <button class="btn btn-primary search-btn" type="submit">Buscar</button>
    </form>

    <!-- Resultados -->
    <?php if (empty($profesionales)): ?>
      <div class="card">
        <p class="empty-state">
          <?= $busqueda !== ''
            ? 'No se encontraron profesionales para "' . htmlspecialchars($busqueda) . '".'
            : 'No hay profesionales disponibles en este momento.' ?>
        </p>
      </div>
    <?php else: ?>
      <?php if ($busqueda !== ''): ?>
        <p class="search-result-count">
          <?= count($profesionales) ?> resultado<?= count($profesionales) !== 1 ? 's' : '' ?>
          para "<strong><?= htmlspecialchars($busqueda) ?></strong>"
        </p>
      <?php endif; ?>

      <div class="profesionales-grid">
        <?php foreach ($profesionales as $prof):
          $iniciales = mb_strtoupper(mb_substr($prof->getNombre(), 0, 1) . mb_substr($prof->getApellidos(), 0, 1));
        ?>
          <article class="card profesional-card">
            <div class="prof-card-top">
              <div class="prof-card-avatar" aria-hidden="true"><?= $iniciales ?></div>
              <div class="prof-card-info">
                <h2 class="prof-card-nombre">Dr./Dra. <?= htmlspecialchars($prof->getNombre() . ' ' . $prof->getApellidos()) ?></h2>
                <p class="prof-card-especialidad"><?= htmlspecialchars($prof->getEspecialidad()) ?></p>
              </div>
            </div>
            <div class="prof-card-meta">
              <span class="prof-duracion">⏱ Consulta ~<?= $prof->getDuracionMediaConsultaMin() ?? 20 ?> min</span>
              <span class="disponible-badge disponible-on">Disponible</span>
            </div>
            <a class="btn btn-primary prof-card-btn"
               href="/index.php?controller=PacienteController&action=disponibilidad&id_profesional=<?= $prof->getIdProfesional() ?>">
              Ver disponibilidad
            </a>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

  </main>

  <footer class="app-footer">
    <p>&copy; <?= date('Y') ?> Daniel Fernández Rodríguez · MedTime</p>
  </footer>
</body>
</html>
