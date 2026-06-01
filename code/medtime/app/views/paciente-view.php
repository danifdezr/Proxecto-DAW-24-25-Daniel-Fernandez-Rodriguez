<?php
$paciente       = $data['paciente']       ?? null;
$proximaCita    = $data['proximaCita']    ?? null;
$restantesCitas = $data['restantesCitas'] ?? [];
$historial      = $data['historial']      ?? [];
$activas        = $data['activas']        ?? 0;
$totalVisitas   = $data['totalVisitas']   ?? 0;
$colaInfo       = $data['colaInfo']       ?? null;
$flashError     = $data['error']          ?? null;
$flashSuccess   = $data['success']        ?? null;

$usuario = $_SESSION['usuario'];
$nombreCompleto = htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellidos']);

$meses = ['', 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
$diasSemana = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
$hoy = new DateTime();
$fechaHoyTexto = $diasSemana[(int)$hoy->format('w')] . ', ' . $hoy->format('j') . ' de ' . $meses[(int)$hoy->format('n')] . '. de ' . $hoy->format('Y');

// Stat de próxima cita
if ($proximaCita) {
    $ts = strtotime($proximaCita['fecha_cita']);
    $proxStatTexto = $diasSemana[(int)date('w', $ts)] . ' ' . date('j', $ts) . ' ' . $meses[(int)date('n', $ts)] . '.';
} else {
    $proxStatTexto = 'Sin citas';
}

// Detectar retraso
$hayRetraso = false;
$retrasoMinutos = 0;
$horaRecomendada = null;
if ($proximaCita && $proximaCita['fecha_hora_estimada']) {
    $programada = strtotime($proximaCita['fecha_cita'] . ' ' . $proximaCita['hora_cita']);
    $estimada   = strtotime($proximaCita['fecha_hora_estimada']);
    $diff = ($estimada - $programada) / 60;
    if ($diff > 0) {
        $hayRetraso = true;
        $retrasoMinutos = (int)$diff;
        $horaRecomendada = date('H:i', $estimada);
    }
}
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
  <title>MedTime - Mi panel</title>
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
          <li><a href="/index.php?controller=PacienteController&action=panel" aria-current="page">Panel</a></li>
          <li><a href="/index.php?controller=PacienteController&action=listCitas">Mis citas</a></li>
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
    <h1 class="sr-only">Panel de paciente de <?= htmlspecialchars($usuario['nombre']) ?></h1>

    <?php if ($flashError): ?>
      <div class="alert alert-error" role="alert"><?= htmlspecialchars($flashError) ?></div>
    <?php endif; ?>
    <?php if ($flashSuccess): ?>
      <div class="alert alert-success" role="alert"><?= htmlspecialchars($flashSuccess) ?></div>
    <?php endif; ?>

    <!-- Bienvenida -->
    <div class="pac-welcome">
      <div>
        <p class="pac-greeting">Bienvenido/a,</p>
        <h2 class="pac-name"><?= htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellidos']) ?></h2>
        <p class="pac-meta"><?= $fechaHoyTexto ?></p>
      </div>
    </div>

    <!-- Stats -->
    <div class="stats-row" aria-label="Resumen de citas">
      <div class="card stat-card stat-card--prox">
        <span class="stat-number stat-date"><?= $proxStatTexto ?></span>
        <span class="stat-label">Próxima cita</span>
      </div>
      <div class="card stat-card stat-card--active">
        <span class="stat-number"><?= $activas ?></span>
        <span class="stat-label">Citas activas</span>
      </div>
      <div class="card stat-card stat-card--done">
        <span class="stat-number"><?= $totalVisitas ?></span>
        <span class="stat-label">Visitas realizadas</span>
      </div>
    </div>

    <!-- Dashboard -->
    <div class="pac-dashboard">

      <!-- Próxima cita -->
      <section class="card pac-main-card" aria-labelledby="proxima-cita-titulo">
        <h2 id="proxima-cita-titulo" class="panel-title">Tu próxima cita</h2>

        <?php if (!$proximaCita): ?>
          <div class="empty-state">
            <p>No tienes citas próximas programadas.</p>
            <a class="btn btn-primary" href="/index.php?controller=PacienteController&action=listCitas">Ver todas mis citas</a>
          </div>

        <?php else:
          $iniciales = mb_strtoupper(mb_substr($proximaCita['profesional_nombre'], 0, 1) . mb_substr($proximaCita['profesional_apellidos'], 0, 1));
          $ts = strtotime($proximaCita['fecha_cita']);
          $fechaFormateada = $diasSemana[(int)date('w', $ts)] . ', ' . date('j', $ts) . ' de ' . $meses[(int)date('n', $ts)] . '. de ' . date('Y', $ts);
        ?>
          <div class="prox-cita-layout">

            <div class="prox-cita-prof">
              <div class="prof-avatar-lg" aria-hidden="true"><?= $iniciales ?></div>
              <div>
                <p class="prof-nombre">Dr./Dra. <?= htmlspecialchars($proximaCita['profesional_nombre'] . ' ' . $proximaCita['profesional_apellidos']) ?></p>
                <p class="prof-especialidad"><?= htmlspecialchars($proximaCita['especialidad']) ?></p>
              </div>
            </div>

            <div class="prox-cita-detalle">
              <div class="cita-fecha-bloque">
                <span class="cita-big-hora"><?= substr($proximaCita['hora_cita'], 0, 5) ?></span>
                <div>
                  <p class="cita-fecha-texto"><?= $fechaFormateada ?></p>
                  <span class="estado-badge estado-<?= strtolower($proximaCita['estado']) ?>"><?= $proximaCita['estado'] ?></span>
                </div>
              </div>

              <?php if ($hayRetraso): ?>
                <div class="delay-alert" role="alert">
                  <span class="delay-icon" aria-hidden="true">⚠</span>
                  <div>
                    <p class="delay-title">Retraso estimado de <?= $retrasoMinutos ?> min</p>
                    <p class="delay-sub">Hora recomendada de llegada: <strong><?= $horaRecomendada ?></strong></p>
                  </div>
                </div>
              <?php endif; ?>

              <?php if ($proximaCita['observaciones']): ?>
                <p class="cita-obs-pac"><?= htmlspecialchars($proximaCita['observaciones']) ?></p>
              <?php endif; ?>
            </div>

            <?php if ($colaInfo !== null): ?>
              <div class="cola-widget" role="status" aria-live="polite">
                <?php if ($colaInfo['delante'] === 0 && !$colaInfo['enConsulta']): ?>
                  <div class="cola-estado cola-estado--libre">
                    <span class="cola-icon" aria-hidden="true">🟢</span>
                    <div>
                      <p class="cola-titulo">Eres el/la próximo/a</p>
                      <p class="cola-sub">El profesional está disponible</p>
                    </div>
                  </div>
                <?php elseif ($colaInfo['delante'] === 0 && $colaInfo['enConsulta']): ?>
                  <div class="cola-estado cola-estado--ultimo">
                    <span class="cola-icon" aria-hidden="true">🔵</span>
                    <div>
                      <p class="cola-titulo">Eres el/la próximo/a</p>
                      <p class="cola-sub">Hay 1 paciente en consulta ahora</p>
                    </div>
                  </div>
                <?php else: ?>
                  <div class="cola-estado cola-estado--espera">
                    <span class="cola-icon" aria-hidden="true">🟡</span>
                    <div>
                      <p class="cola-titulo">
                        <?= $colaInfo['delante'] ?> paciente<?= $colaInfo['delante'] !== 1 ? 's' : '' ?> delante de ti
                      </p>
                      <p class="cola-sub">
                        <?= $colaInfo['enConsulta'] ? '1 en consulta ahora · ' : '' ?><?= $colaInfo['delante'] - ($colaInfo['enConsulta'] ? 1 : 0) ?> en espera
                      </p>
                    </div>
                  </div>
                <?php endif; ?>
              </div>
            <?php endif; ?>

            <div class="cita-actions">
              <a class="btn" href="/index.php?controller=PacienteController&action=listCitas">Ver todas mis citas</a>
              <a class="btn" href="/index.php?controller=PacienteController&action=mostrarReprogramar&id_cita=<?= (int)$proximaCita['id_cita'] ?>">Reprogramar</a>
              <?php if ($proximaCita['estado'] === 'PENDIENTE' && $proximaCita['fecha_cita'] === date('Y-m-d')): ?>
                <form method="POST" action="/index.php?controller=PacienteController&action=confirmarCita">
                  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                  <input type="hidden" name="id_cita" value="<?= (int)$proximaCita['id_cita'] ?>">
                  <input type="hidden" name="from" value="panel">
                  <button class="btn btn-confirm" type="submit">Confirmar asistencia</button>
                </form>
              <?php endif; ?>
              <?php if (!in_array($proximaCita['estado'], ['FINALIZADA', 'CANCELADA'])): ?>
                <form method="POST" action="/index.php?controller=PacienteController&action=cancelarCita"
                      id="form-cancelar-cita">
                  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                  <input type="hidden" name="id_cita" value="<?= (int)$proximaCita['id_cita'] ?>">
                  <input type="hidden" name="from" value="panel">
                  <button class="btn btn-danger" type="button"
                          data-confirm="¿Seguro que quieres cancelar esta cita? Esta acción no se puede deshacer."
                          data-confirm-label="Sí, cancelar"
                          data-confirm-danger="true">
                    Cancelar cita
                  </button>
                </form>
              <?php endif; ?>
            </div>

          </div>
        <?php endif; ?>
      </section>

      <!-- Panel lateral -->
      <aside class="pac-side">

        <?php if (!empty($restantesCitas)): ?>
        <section class="card pac-side-card" aria-labelledby="prox-citas-titulo">
          <h2 id="prox-citas-titulo" class="panel-title">Próximas citas</h2>
          <ul class="prox-list">
            <?php foreach (array_slice($restantesCitas, 0, 4) as $cita):
              $tsC = strtotime($cita['fecha_cita']);
            ?>
              <li class="prox-item">
                <div class="prox-fecha" aria-hidden="true">
                  <span class="prox-dia"><?= date('j', $tsC) ?></span>
                  <span class="prox-mes"><?= $meses[(int)date('n', $tsC)] ?></span>
                </div>
                <div class="prox-info">
                  <p class="prox-prof"><?= htmlspecialchars($cita['profesional_nombre'] . ' ' . $cita['profesional_apellidos']) ?></p>
                  <p class="prox-spec"><?= htmlspecialchars($cita['especialidad']) ?> · <?= substr($cita['hora_cita'], 0, 5) ?></p>
                </div>
                <span class="estado-badge estado-<?= strtolower($cita['estado']) ?>"><?= $cita['estado'] ?></span>
              </li>
            <?php endforeach; ?>
          </ul>
        </section>
        <?php endif; ?>

        <section class="card pac-side-card" aria-labelledby="historial-titulo">
          <h2 id="historial-titulo" class="panel-title">Historial reciente</h2>
          <?php if (empty($historial)): ?>
            <p class="empty-state-sm">Sin visitas anteriores.</p>
          <?php else: ?>
            <ul class="historial-list">
              <?php foreach ($historial as $h):
                $tsH = strtotime($h['fecha_cita']);
              ?>
                <li class="historial-item">
                  <div class="historial-fecha" aria-hidden="true">
                    <span class="historial-dia"><?= date('j', $tsH) ?></span>
                    <span class="historial-mes"><?= $meses[(int)date('n', $tsH)] ?></span>
                  </div>
                  <div class="historial-info">
                    <p class="historial-prof"><?= htmlspecialchars($h['profesional_nombre'] . ' ' . $h['profesional_apellidos']) ?></p>
                    <p class="historial-spec"><?= htmlspecialchars($h['especialidad']) ?> · <?= substr($h['hora_cita'], 0, 5) ?></p>
                  </div>
                  <span class="estado-badge estado-<?= strtolower($h['estado']) ?>"><?= $h['estado'] ?></span>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </section>

      </aside>
    </div>
  </main>
  <script src="/assets/js/modal.js?v=<?= filemtime('/var/www/html/public/assets/js/modal.js') ?>"></script>
  <script src="/assets/js/paciente.js?v=<?= filemtime('/var/www/html/public/assets/js/paciente.js') ?>"></script>
</body>
</html>
