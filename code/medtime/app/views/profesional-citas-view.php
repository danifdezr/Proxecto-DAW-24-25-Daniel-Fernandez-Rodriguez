<?php
$profesional  = $data['profesional']  ?? null;
$citas        = $data['citas']        ?? [];
$estadoFiltro = $data['estadoFiltro'] ?? null;
$conteos      = $data['conteos']      ?? [];
$total        = $data['total']        ?? 0;

$usuario = $_SESSION['usuario'];
$nombreCompleto = htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellidos']);

$meses = ['', 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];

function formatFecha(string $fecha, array $meses): string {
    $ts = strtotime($fecha);
    return date('j', $ts) . ' ' . $meses[(int)date('n', $ts)] . '. ' . date('Y', $ts);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="/assets/css/cssPrincipal.css?v=<?= filemtime('/var/www/html/public/assets/css/cssPrincipal.css') ?>">
  <link rel="stylesheet" href="/assets/css/profesional.css?v=<?= filemtime('/var/www/html/public/assets/css/profesional.css') ?>">
  <title>MedTime - Mis citas</title>
</head>
<body>
  <a class="skip-link" href="#contenido-principal">Saltar al contenido principal</a>

  <header class="site-header">
    <div class="header-inner">
      <a class="brand" href="/index.php?controller=ProfesionalController&action=listProfesional" aria-label="MedTime, ir al panel">
        <span class="brand-mark" aria-hidden="true">M</span>
        <span>MedTime</span>
      </a>

      <nav aria-label="Navegación principal">
        <ul class="nav-list">
          <li><a href="/index.php?controller=ProfesionalController&action=listProfesional">Panel</a></li>
          <li><a href="/index.php?controller=ProfesionalController&action=listCitas" aria-current="page">Mis citas</a></li>
          <li><a href="/index.php?controller=ProfesionalController&action=listPacientes">Pacientes</a></li>
          <li><a href="/index.php?controller=ProfesionalController&action=perfil">Mi perfil</a></li>
        </ul>
      </nav>

      <div class="user-tools">
        <button class="profile-button" type="button" aria-label="Perfil de <?= $nombreCompleto ?>">
          <span class="avatar-initials" aria-hidden="true"><?= htmlspecialchars(mb_strtoupper(mb_substr($usuario['nombre'], 0, 1))) ?></span>
          <span><?= $nombreCompleto ?></span>
          <span aria-hidden="true">⌄</span>
        </button>
        <a class="btn-logout" href="/index.php?controller=AuthController&action=logout">Salir</a>
      </div>
    </div>
  </header>

  <main id="contenido-principal" class="page">

    <div class="page-head">
      <div>
        <h1 class="page-title">Mis citas</h1>
        <p class="page-subtitle">
          <?= $profesional ? htmlspecialchars($profesional->getEspecialidad()) : '' ?> ·
          <strong><?= $total ?></strong> cita<?= $total !== 1 ? 's' : '' ?> en total
        </p>
      </div>
    </div>

    <!-- Tabs de filtro -->
    <div class="filter-tabs" role="tablist" aria-label="Filtrar citas por estado">
      <a class="filter-tab <?= $estadoFiltro === null ? 'filter-tab--active' : '' ?>"
         href="/index.php?controller=ProfesionalController&action=listCitas"
         role="tab" aria-selected="<?= $estadoFiltro === null ? 'true' : 'false' ?>">
        Todas
        <span class="filter-count"><?= $total ?></span>
      </a>
      <a class="filter-tab filter-tab--pendiente <?= $estadoFiltro === 'PENDIENTE' ? 'filter-tab--active' : '' ?>"
         href="/index.php?controller=ProfesionalController&action=listCitas&estado=PENDIENTE"
         role="tab" aria-selected="<?= $estadoFiltro === 'PENDIENTE' ? 'true' : 'false' ?>">
        Pendientes
        <span class="filter-count"><?= $conteos['PENDIENTE'] ?? 0 ?></span>
      </a>
      <a class="filter-tab filter-tab--confirmada <?= $estadoFiltro === 'CONFIRMADA' ? 'filter-tab--active' : '' ?>"
         href="/index.php?controller=ProfesionalController&action=listCitas&estado=CONFIRMADA"
         role="tab" aria-selected="<?= $estadoFiltro === 'CONFIRMADA' ? 'true' : 'false' ?>">
        Confirmadas
        <span class="filter-count"><?= $conteos['CONFIRMADA'] ?? 0 ?></span>
      </a>
      <a class="filter-tab filter-tab--finalizada <?= $estadoFiltro === 'FINALIZADA' ? 'filter-tab--active' : '' ?>"
         href="/index.php?controller=ProfesionalController&action=listCitas&estado=FINALIZADA"
         role="tab" aria-selected="<?= $estadoFiltro === 'FINALIZADA' ? 'true' : 'false' ?>">
        Finalizadas
        <span class="filter-count"><?= $conteos['FINALIZADA'] ?? 0 ?></span>
      </a>
      <?php if (($conteos['CANCELADA'] ?? 0) > 0): ?>
      <a class="filter-tab filter-tab--cancelada <?= $estadoFiltro === 'CANCELADA' ? 'filter-tab--active' : '' ?>"
         href="/index.php?controller=ProfesionalController&action=listCitas&estado=CANCELADA"
         role="tab" aria-selected="<?= $estadoFiltro === 'CANCELADA' ? 'true' : 'false' ?>">
        Canceladas
        <span class="filter-count"><?= $conteos['CANCELADA'] ?? 0 ?></span>
      </a>
      <?php endif; ?>
    </div>

    <!-- Tabla de citas -->
    <div class="card table-card">
      <?php if (empty($citas)): ?>
        <p class="empty-state">No hay citas<?= $estadoFiltro ? ' con estado "' . htmlspecialchars($estadoFiltro) . '"' : '' ?>.</p>
      <?php else: ?>
        <div class="table-wrapper">
          <table class="citas-table" aria-label="Listado de citas">
            <thead>
              <tr>
                <th scope="col">Fecha</th>
                <th scope="col">Hora</th>
                <th scope="col">Paciente</th>
                <th scope="col">Estado</th>
                <th scope="col">Observaciones</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($citas as $cita):
                $esPasada = strtotime($cita['fecha_cita'] . ' ' . $cita['hora_cita']) < time();
              ?>
                <tr class="<?= $esPasada ? 'row-pasada' : '' ?>">
                  <td class="td-fecha">
                    <span class="fecha-dia"><?= date('j', strtotime($cita['fecha_cita'])) ?></span>
                    <span class="fecha-mesanio"><?= $meses[(int)date('n', strtotime($cita['fecha_cita']))] ?>. <?= date('Y', strtotime($cita['fecha_cita'])) ?></span>
                  </td>
                  <td class="td-hora"><?= substr($cita['hora_cita'], 0, 5) ?></td>
                  <td class="td-paciente"><?= htmlspecialchars($cita['paciente_nombre'] . ' ' . $cita['paciente_apellidos']) ?></td>
                  <td class="td-estado">
                    <span class="estado-badge estado-<?= strtolower($cita['estado']) ?>">
                      <?= $cita['estado'] ?>
                    </span>
                  </td>
                  <td class="td-obs"><?= $cita['observaciones'] ? htmlspecialchars($cita['observaciones']) : '<span class="sin-obs">—</span>' ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>

  </main>

  <footer class="app-footer">
    <p>&copy; <?= date('Y') ?> Daniel Fernández Rodríguez · MedTime</p>
  </footer>
</body>
</html>
