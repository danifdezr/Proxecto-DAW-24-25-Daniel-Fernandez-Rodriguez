<?php
$profesional     = $data['profesional']     ?? null;
$citasHoy        = $data['citasHoy']        ?? [];
$citasProximas   = $data['citasProximas']   ?? [];
$citaEnConsulta  = $data['citaEnConsulta']  ?? null;
$pendientes      = $data['pendientes']      ?? 0;
$confirmadas     = $data['confirmadas']     ?? 0;
$finalizadas     = $data['finalizadas']     ?? 0;
$flashError      = $data['error']           ?? null;
$flashSuccess    = $data['success']         ?? null;

$usuario = $_SESSION['usuario'];
$nombreCompleto = htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellidos']);

$meses = ['', 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
$diasSemana = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
$hoy = new DateTime();
$fechaHoyTexto = $diasSemana[(int)$hoy->format('w')] . ', ' . $hoy->format('j') . ' de ' . $meses[(int)$hoy->format('n')] . '. de ' . $hoy->format('Y');
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="/assets/css/profesional.css">
  <link rel="stylesheet" href="/assets/css/modal.css">
  <meta name="csrf-token" content="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
  <title>MedTime - Panel profesional</title>
</head>
<body>
  <a class="skip-link" href="#contenido-principal">Saltar al contenido principal</a>

  <header class="site-header">
    <div class="header-inner">
      <a class="brand" href="/index.php?controller=MainController&action=listMain" aria-label="MedTime, ir al inicio">
        <span class="brand-mark" aria-hidden="true">M</span>
        <span>MedTime</span>
      </a>

      <nav aria-label="Navegación principal">
        <ul class="nav-list">
          <li><a href="/index.php?controller=ProfesionalController&action=listProfesional" aria-current="page">Panel</a></li>
          <li><a href="/index.php?controller=ProfesionalController&action=listCitas">Mis citas</a></li>
          <li><a href="/index.php?controller=ProfesionalController&action=listPacientes">Pacientes</a></li>
          <li><a href="/index.php?controller=ProfesionalController&action=perfil">Mi perfil</a></li>
        </ul>
      </nav>

      <div class="user-tools" aria-label="Herramientas de usuario">
        <button class="profile-button" type="button" aria-label="Perfil de <?= $nombreCompleto ?>">
          <span class="avatar-initials" aria-hidden="true"><?= htmlspecialchars(mb_strtoupper(mb_substr($usuario['nombre'], 0, 1))) ?></span>
          <span><?= $nombreCompleto ?></span>
          <span aria-hidden="true">⌄</span>
        </button>

        <a class="btn btn-logout" href="/index.php?controller=AuthController&action=logout" aria-label="Cerrar sesión">
          Salir
        </a>
      </div>
    </div>
  </header>

  <main id="contenido-principal" class="page">
    <h1 class="sr-only">Panel del profesional <?= htmlspecialchars($profesional->getNombre()) ?></h1>

    <?php if ($flashError): ?>
      <div class="alert alert-error" role="alert"><?= htmlspecialchars($flashError) ?></div>
    <?php endif; ?>
    <?php if ($flashSuccess): ?>
      <div class="alert alert-success" role="alert"><?= htmlspecialchars($flashSuccess) ?></div>
    <?php endif; ?>

    <div class="pro-welcome">
      <div>
        <p class="pro-greeting">Bienvenido/a,</p>
        <h2 class="pro-name">Dr. <?= htmlspecialchars($profesional->getNombre() . ' ' . $profesional->getApellidos()) ?></h2>
        <p class="pro-meta">
          <span class="pro-especialidad"><?= htmlspecialchars($profesional->getEspecialidad()) ?></span>
          <span class="pro-sep" aria-hidden="true">·</span>
          <span><?= $fechaHoyTexto ?></span>
        </p>
      </div>
      <span class="disponible-badge <?= $profesional->getDisponible() ? 'disponible-on' : 'disponible-off' ?>">
        <?= $profesional->getDisponible() ? 'Disponible' : 'No disponible' ?>
      </span>
    </div>

    <!-- Stats -->
    <div class="stats-row" aria-label="Resumen de citas de hoy">
      <div class="card stat-card">
        <span class="stat-number"><?= count($citasHoy) ?></span>
        <span class="stat-label">Citas hoy</span>
      </div>
      <div class="card stat-card stat-card--pending">
        <span class="stat-number"><?= $pendientes ?></span>
        <span class="stat-label">Pendientes</span>
      </div>
      <div class="card stat-card stat-card--confirmed">
        <span class="stat-number"><?= $confirmadas ?></span>
        <span class="stat-label">Confirmadas</span>
      </div>
      <div class="card stat-card stat-card--done">
        <span class="stat-number"><?= $finalizadas ?></span>
        <span class="stat-label">Finalizadas</span>
      </div>
    </div>

    <!-- Dashboard grid -->
    <div class="pro-dashboard">

      <!-- Citas de hoy -->
      <section class="card pro-main-card" aria-labelledby="citas-hoy-titulo">
        <div class="pro-section-head">
          <h2 id="citas-hoy-titulo" class="panel-title">Citas de hoy</h2>
          <span class="pro-date"><?= $fechaHoyTexto ?></span>
        </div>

        <?php if (empty($citasHoy)): ?>
          <p class="empty-state">No tienes citas programadas para hoy.</p>
        <?php else: ?>

          <?php if ($citaEnConsulta): ?>
            <div class="consulta-activa-banner" role="status" aria-live="polite">
              <span class="consulta-dot" aria-hidden="true"></span>
              <div class="consulta-activa-info">
                <p class="consulta-activa-nombre">En consulta: <?= htmlspecialchars($citaEnConsulta['paciente_nombre'] . ' ' . $citaEnConsulta['paciente_apellidos']) ?></p>
                <p class="consulta-activa-sub">Programada: <?= substr($citaEnConsulta['hora_cita'], 0, 5) ?>
                  <?php if ($citaEnConsulta['fecha_hora_real_inicio']): ?>
                    · Iniciada: <?= date('H:i', strtotime($citaEnConsulta['fecha_hora_real_inicio'])) ?>
                  <?php endif; ?>
                </p>
              </div>
              <form method="POST" action="/index.php?controller=ProfesionalController&action=finalizarConsulta"
                    id="form-finalizar-banner">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                <input type="hidden" name="id_cita" value="<?= (int)$citaEnConsulta['id_cita'] ?>">
                <button class="btn-finalizar" type="submit">Finalizar consulta</button>
              </form>
            </div>
          <?php endif; ?>

          <div class="citas-list" role="list">
            <?php foreach ($citasHoy as $cita):
              $horaP = substr($cita['hora_cita'], 0, 5);
              $horaE = $cita['fecha_hora_estimada']
                  ? substr($cita['fecha_hora_estimada'], 11, 5)
                  : null;
              $estadoClass   = strtolower(str_replace('_', '-', $cita['estado']));
              $estaActiva    = $cita['estado'] === 'EN_CONSULTA';
              $esFinalizada  = $cita['estado'] === 'FINALIZADA';
              $esLlamable    = in_array($cita['estado'], ['PENDIENTE', 'CONFIRMADA']) && $citaEnConsulta === null;
            ?>
              <div class="cita-item <?= $estaActiva ? 'cita-item--activa' : '' ?> <?= $esFinalizada ? 'cita-item--done' : '' ?>" role="listitem">

                <div class="cita-time">
                  <span class="cita-hour"><?= $horaP ?></span>
                  <?php if ($horaE && $horaE !== $horaP): ?>
                    <span class="cita-estimada" title="Hora estimada">~<?= $horaE ?></span>
                  <?php endif; ?>
                </div>

                <div class="cita-info">
                  <p class="cita-paciente">
                    <?= htmlspecialchars($cita['paciente_nombre'] . ' ' . $cita['paciente_apellidos']) ?>
                  </p>
                  <?php if ($cita['observaciones']): ?>
                    <p class="cita-obs"><?= htmlspecialchars($cita['observaciones']) ?></p>
                  <?php endif; ?>
                </div>

                <span class="estado-badge estado-<?= $estadoClass ?>">
                  <?= $cita['estado'] === 'EN_CONSULTA' ? 'En consulta' : $cita['estado'] ?>
                </span>

                <div class="cita-accion">
                  <?php if ($estaActiva): ?>
                    <form method="POST" action="/index.php?controller=ProfesionalController&action=finalizarConsulta">
                      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                      <input type="hidden" name="id_cita" value="<?= (int)$cita['id_cita'] ?>">
                      <button class="btn-accion btn-accion--fin" type="submit" title="Finalizar esta consulta">
                        ✓ Finalizar
                      </button>
                    </form>
                  <?php elseif ($esLlamable): ?>
                    <form method="POST" action="/index.php?controller=ProfesionalController&action=llamarPaciente">
                      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                      <input type="hidden" name="id_cita" value="<?= (int)$cita['id_cita'] ?>">
                      <button class="btn-accion btn-accion--llamar" type="submit" title="Llamar a este paciente">
                        ▶ Llamar
                      </button>
                    </form>
                  <?php elseif ($esFinalizada): ?>
                    <span class="accion-completada" aria-label="Completada">✓</span>
                  <?php else: ?>
                    <span class="accion-espera" title="Hay una consulta activa">—</span>
                  <?php endif; ?>
                </div>

              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </section>

      <!-- Panel lateral: próximas citas -->
      <aside class="pro-side" aria-label="Próximas citas">
        <section class="card pro-side-card" aria-labelledby="proximas-titulo">
          <h2 id="proximas-titulo" class="panel-title">Próximas citas</h2>
          <p class="pro-side-sub">Próximos 14 días</p>

          <?php if (empty($citasProximas)): ?>
            <p class="empty-state">No hay citas próximas agendadas.</p>
          <?php else: ?>
            <ul class="proximas-list">
              <?php foreach ($citasProximas as $cita):
                $ts = strtotime($cita['fecha_cita']);
                $dia = date('j', $ts);
                $mes = $meses[(int)date('n', $ts)];
                $estadoClass = strtolower($cita['estado']);
              ?>
                <li class="proxima-item">
                  <div class="proxima-fecha" aria-hidden="true">
                    <span class="proxima-dia"><?= $dia ?></span>
                    <span class="proxima-mes"><?= $mes ?></span>
                  </div>
                  <div class="proxima-info">
                    <p class="proxima-paciente">
                      <?= htmlspecialchars($cita['paciente_nombre'] . ' ' . $cita['paciente_apellidos']) ?>
                    </p>
                    <p class="proxima-hora"><?= substr($cita['hora_cita'], 0, 5) ?></p>
                  </div>
                  <span class="estado-badge estado-<?= $estadoClass ?>">
                    <?= $cita['estado'] ?>
                  </span>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </section>
      </aside>

    </div>
  </main>
  <script src="/assets/js/modal.js?v=<?= filemtime('/var/www/html/public/assets/js/modal.js') ?>"></script>
  <script src="/assets/js/profesional.js?v=<?= filemtime('/var/www/html/public/assets/js/profesional.js') ?>"></script>
</body>
</html>
