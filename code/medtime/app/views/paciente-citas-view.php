<?php
$paciente     = $data['paciente']     ?? null;
$citas        = $data['citas']        ?? [];
$estadoFiltro = $data['estadoFiltro'] ?? null;
$conteos      = $data['conteos']      ?? [];
$total        = $data['total']        ?? 0;
$flashError   = $data['error']        ?? null;
$flashSuccess = $data['success']      ?? null;

$usuario = $_SESSION['usuario'];
$nombreCompleto = htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellidos']);
$meses = ['', 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="/assets/css/cssPrincipal.css?v=<?= filemtime('/var/www/html/public/assets/css/cssPrincipal.css') ?>">
  <link rel="stylesheet" href="/assets/css/paciente.css?v=<?= filemtime('/var/www/html/public/assets/css/paciente.css') ?>">
  <link rel="stylesheet" href="/assets/css/modal.css?v=<?= filemtime('/var/www/html/public/assets/css/modal.css') ?>">
  <meta name="csrf-token" content="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
  <title>MedTime - Mis citas</title>
</head>
<body>
  <a class="skip-link" href="#contenido-principal">Saltar al contenido principal</a>

  <header class="site-header">
    <div class="header-inner">
      <a class="brand" href="/index.php?controller=PacienteController&action=panel" aria-label="MedTime, ir al panel">
        <span class="brand-mark" aria-hidden="true">M</span>
        <span>MedTime</span>
      </a>
      <nav aria-label="Navegación principal">
        <ul class="nav-list">
          <li><a href="/index.php?controller=PacienteController&action=panel">Panel</a></li>
          <li><a href="/index.php?controller=PacienteController&action=listCitas" aria-current="page">Mis citas</a></li>
          <li><a href="/index.php?controller=PacienteController&action=nuevaCita">Pedir cita</a></li>
          <li><a href="/index.php?controller=PacienteController&action=perfil">Mi perfil</a></li>
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

    <?php if ($flashError): ?>
      <div class="alert alert-error" role="alert"><?= htmlspecialchars($flashError) ?></div>
    <?php endif; ?>
    <?php if ($flashSuccess): ?>
      <div class="alert alert-success" role="alert"><?= htmlspecialchars($flashSuccess) ?></div>
    <?php endif; ?>

    <div class="page-head">
      <div>
        <h1 class="page-title">Mis citas</h1>
        <p class="page-subtitle"><strong><?= $total ?></strong> cita<?= $total !== 1 ? 's' : '' ?> en total</p>
      </div>
    </div>

    <!-- Tabs de filtro -->
    <div class="filter-tabs" role="tablist" aria-label="Filtrar por estado">
      <a class="filter-tab <?= $estadoFiltro === null ? 'filter-tab--active' : '' ?>"
         href="/index.php?controller=PacienteController&action=listCitas" role="tab">
        Todas <span class="filter-count"><?= $total ?></span>
      </a>
      <a class="filter-tab filter-tab--confirmada <?= $estadoFiltro === 'CONFIRMADA' ? 'filter-tab--active' : '' ?>"
         href="/index.php?controller=PacienteController&action=listCitas&estado=CONFIRMADA" role="tab">
        Confirmadas <span class="filter-count"><?= $conteos['CONFIRMADA'] ?? 0 ?></span>
      </a>
      <a class="filter-tab filter-tab--pendiente <?= $estadoFiltro === 'PENDIENTE' ? 'filter-tab--active' : '' ?>"
         href="/index.php?controller=PacienteController&action=listCitas&estado=PENDIENTE" role="tab">
        Pendientes <span class="filter-count"><?= $conteos['PENDIENTE'] ?? 0 ?></span>
      </a>
      <a class="filter-tab filter-tab--finalizada <?= $estadoFiltro === 'FINALIZADA' ? 'filter-tab--active' : '' ?>"
         href="/index.php?controller=PacienteController&action=listCitas&estado=FINALIZADA" role="tab">
        Realizadas <span class="filter-count"><?= $conteos['FINALIZADA'] ?? 0 ?></span>
      </a>
      <?php if (($conteos['CANCELADA'] ?? 0) > 0): ?>
      <a class="filter-tab filter-tab--cancelada <?= $estadoFiltro === 'CANCELADA' ? 'filter-tab--active' : '' ?>"
         href="/index.php?controller=PacienteController&action=listCitas&estado=CANCELADA" role="tab">
        Canceladas <span class="filter-count"><?= $conteos['CANCELADA'] ?></span>
      </a>
      <?php endif; ?>
    </div>

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
                <th scope="col">Profesional</th>
                <th scope="col">Especialidad</th>
                <th scope="col">Estado</th>
                <th scope="col">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($citas as $cita):
                $esPasada    = strtotime($cita['fecha_cita'] . ' ' . $cita['hora_cita']) < time();
                $cancelable  = !$esPasada && !in_array($cita['estado'], ['FINALIZADA', 'CANCELADA']);
                $tsRow = strtotime($cita['fecha_cita']);
              ?>
                <tr class="<?= $esPasada ? 'row-pasada' : '' ?>">
                  <td class="td-fecha">
                    <span class="fecha-dia"><?= date('j', $tsRow) ?></span>
                    <span class="fecha-mesanio"><?= $meses[(int)date('n', $tsRow)] ?>. <?= date('Y', $tsRow) ?></span>
                  </td>
                  <td class="td-hora"><?= substr($cita['hora_cita'], 0, 5) ?></td>
                  <td class="td-paciente"><?= htmlspecialchars($cita['profesional_nombre'] . ' ' . $cita['profesional_apellidos']) ?></td>
                  <td class="td-obs"><?= htmlspecialchars($cita['especialidad']) ?></td>
                  <td class="td-estado">
                    <span class="estado-badge estado-<?= strtolower($cita['estado']) ?>"><?= $cita['estado'] ?></span>
                  </td>
                  <td class="td-acciones">
                    <?php
                      $esHoy          = $cita['fecha_cita'] === date('Y-m-d');
                      $confirmable    = !$esPasada && $cita['estado'] === 'PENDIENTE' && $esHoy;
                      $reprogramable  = !$esPasada && !in_array($cita['estado'], ['FINALIZADA', 'CANCELADA', 'EN_CONSULTA']);
                      $fechaDetalle    = date('j', $tsRow) . ' ' . $meses[(int)date('n', $tsRow)] . '. ' . date('Y', $tsRow);
                      $horaEstimada   = $cita['fecha_hora_estimada'] ? substr($cita['fecha_hora_estimada'], 11, 5) : '';
                    ?>
                    <button class="btn-table" type="button"
                            data-cita-detalle
                            data-cita-prof="<?= htmlspecialchars($cita['profesional_nombre'] . ' ' . $cita['profesional_apellidos']) ?>"
                            data-cita-espec="<?= htmlspecialchars($cita['especialidad']) ?>"
                            data-cita-fecha="<?= htmlspecialchars($fechaDetalle) ?>"
                            data-cita-hora="<?= htmlspecialchars(substr($cita['hora_cita'], 0, 5)) ?>"
                            data-cita-hora-estimada="<?= htmlspecialchars($horaEstimada) ?>"
                            data-cita-estado="<?= htmlspecialchars($cita['estado']) ?>"
                            <?php if ($cita['observaciones']): ?>data-cita-obs="<?= htmlspecialchars($cita['observaciones']) ?>"<?php endif; ?>>
                      Ver
                    </button>
                    <?php if ($reprogramable): ?>
                      <a class="btn-table-edit"
                         href="/index.php?controller=PacienteController&action=mostrarReprogramar&id_cita=<?= (int)$cita['id_cita'] ?>">
                        Reprogramar
                      </a>
                    <?php endif; ?>
                    <?php if ($confirmable): ?>
                      <form method="POST" action="/index.php?controller=PacienteController&action=confirmarCita" class="form-inline">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                        <input type="hidden" name="id_cita" value="<?= (int)$cita['id_cita'] ?>">
                        <input type="hidden" name="from" value="citas">
                        <button class="btn-table-confirm" type="submit">Confirmar</button>
                      </form>
                    <?php endif; ?>
                    <?php if ($cancelable): ?>
                      <form method="POST" action="/index.php?controller=PacienteController&action=cancelarCita"
                            class="form-inline">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                        <input type="hidden" name="id_cita" value="<?= (int)$cita['id_cita'] ?>">
                        <input type="hidden" name="from" value="citas">
                        <button class="btn-table-danger" type="button"
                                data-confirm="¿Seguro que quieres cancelar esta cita?"
                                data-confirm-label="Sí, cancelar"
                                data-confirm-danger="true">
                          Cancelar
                        </button>
                      </form>
                    <?php elseif (!$confirmable): ?>
                      <span class="sin-obs">—</span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>

  </main>
  <script src="/assets/js/modal.js?v=<?= filemtime('/var/www/html/public/assets/js/modal.js') ?>"></script>
  <script src="/assets/js/paciente.js?v=<?= filemtime('/var/www/html/public/assets/js/paciente.js') ?>"></script>
</body>
</html>
