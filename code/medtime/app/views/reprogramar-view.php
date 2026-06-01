<?php
$cita              = $data['cita']              ?? null;
$profesional       = $data['profesional']       ?? null;
$semanas           = $data['semanas']           ?? [];
$fechaSeleccionada = $data['fechaSeleccionada'] ?? null;
$horaSeleccionada  = $data['horaSeleccionada']  ?? null;
$slotsDelDia       = $data['slotsDelDia']       ?? [];
$duracion          = $data['duracion']          ?? 20;

$usuario = $_SESSION['usuario'];
$nombreCompleto = htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellidos']);

$meses      = ['', 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
$mesesLargo = ['', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
$diasSemana = ['', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];

$idProfesional = $profesional ? $profesional->getIdProfesional() : 0;
$idCita        = $cita ? $cita->getIdCita() : 0;
$baseUrl = "/index.php?controller=PacienteController&action=mostrarReprogramar&id_cita={$idCita}";

$fechaTexto = '';
if ($fechaSeleccionada) {
    $ts = strtotime($fechaSeleccionada);
    $fechaTexto = $diasSemana[(int)date('N', $ts)] . ', ' . date('j', $ts) . ' de ' . $mesesLargo[(int)date('n', $ts)];
}

// Fecha original de la cita para mostrar en el encabezado
$fechaOriginal = '';
if ($cita) {
    $tsOrig = strtotime($cita->getFechaCita());
    $fechaOriginal = date('j', $tsOrig) . ' ' . $meses[(int)date('n', $tsOrig)] . '. · ' . substr($cita->getHoraCita(), 0, 5);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
  <link rel="stylesheet" href="/assets/css/cssPrincipal.css?v=<?= filemtime('/var/www/html/public/assets/css/cssPrincipal.css') ?>">
  <link rel="stylesheet" href="/assets/css/paciente.css?v=<?= filemtime('/var/www/html/public/assets/css/paciente.css') ?>">
  <link rel="stylesheet" href="/assets/css/modal.css?v=<?= filemtime('/var/www/html/public/assets/css/modal.css') ?>">
  <title>MedTime - Reprogramar cita</title>
</head>
<body>
  <a class="skip-link" href="#contenido-principal">Saltar al contenido principal</a>

  <header class="site-header">
    <div class="header-inner">
      <a class="brand" href="/index.php?controller=PacienteController&action=panel">
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

    <!-- Breadcrumb -->
    <nav class="breadcrumb" aria-label="Ruta de navegación">
      <a href="/index.php?controller=PacienteController&action=listCitas">Mis citas</a>
      <span aria-hidden="true"> › </span>
      <span>Reprogramar cita</span>
    </nav>

    <!-- Info de la cita actual -->
    <div class="disp-prof-bar card">
      <div class="prof-avatar-lg" aria-hidden="true"><?= htmlspecialchars(mb_strtoupper(mb_substr($profesional->getNombre(), 0, 1) . mb_substr($profesional->getApellidos(), 0, 1))) ?></div>
      <div>
        <p class="prof-nombre">Dr./Dra. <?= htmlspecialchars($profesional->getNombre() . ' ' . $profesional->getApellidos()) ?></p>
        <p class="prof-especialidad"><?= htmlspecialchars($profesional->getEspecialidad()) ?> · Cita actual: <strong><?= htmlspecialchars($fechaOriginal) ?></strong></p>
      </div>
    </div>

    <div class="disp-layout">

      <!-- Calendario -->
      <section class="card disp-cal-card" aria-labelledby="cal-titulo">
        <h2 id="cal-titulo" class="panel-title">Elige un nuevo día</h2>
        <p class="disp-hint">Próximos 35 días · Sin domingos</p>

        <div class="cal-wrapper" role="grid" aria-label="Calendario de disponibilidad">
          <div class="cal-weekdays" role="row">
            <?php foreach (['Lun','Mar','Mié','Jue','Vie','Sáb','Dom'] as $wd): ?>
              <div class="cal-wd" role="columnheader"><?= $wd ?></div>
            <?php endforeach; ?>
          </div>

          <?php foreach ($semanas as $semana): ?>
            <div class="cal-week" role="row">
              <?php foreach ($semana as $dia):
                $esDom      = $dia['esDom'];
                $inRango    = $dia['inRango'];
                $sinSlots   = $inRango && !$esDom && $dia['slots'] === 0;
                $disponible = $inRango && !$esDom && $dia['slots'] > 0;
                $seleccionado = $fechaSeleccionada === $dia['fecha'] && $disponible;
              ?>
                <?php if (!$inRango): ?>
                  <div class="cal-dia cal-dia--vacio" role="gridcell" aria-hidden="true"></div>
                <?php elseif ($esDom || $sinSlots): ?>
                  <div class="cal-dia cal-dia--disabled" role="gridcell" aria-disabled="true">
                    <span class="cal-num"><?= $dia['dia'] ?></span>
                    <?php if ($dia['esHoy']): ?><span class="cal-hoy-dot" aria-hidden="true"></span><?php endif; ?>
                  </div>
                <?php else: ?>
                  <a class="cal-dia cal-dia--disponible <?= $seleccionado ? 'cal-dia--seleccionado' : '' ?> <?= $dia['esHoy'] ? 'cal-dia--hoy' : '' ?>"
                     href="<?= $baseUrl ?>&fecha=<?= $dia['fecha'] ?>"
                     role="gridcell"
                     aria-selected="<?= $seleccionado ? 'true' : 'false' ?>"
                     aria-label="<?= htmlspecialchars($dia['dia'] . ' ' . $meses[$dia['mes']] . ', ' . $dia['slots'] . ' huecos') ?>">
                    <span class="cal-num"><?= $dia['dia'] ?></span>
                    <span class="cal-slots-dot" aria-hidden="true"><?= $dia['slots'] ?></span>
                  </a>
                <?php endif; ?>
              <?php endforeach; ?>
            </div>
          <?php endforeach; ?>
        </div>

        <div class="cal-leyenda">
          <span class="leyenda-item"><span class="ley-dot ley-disponible"></span> Con huecos</span>
          <span class="leyenda-item"><span class="ley-dot ley-sin"></span> Sin huecos</span>
          <span class="leyenda-item"><span class="ley-dot ley-sel"></span> Seleccionado</span>
        </div>
      </section>

      <!-- Slots + confirmación -->
      <section class="card disp-slots-card" aria-labelledby="slots-titulo">

        <?php if (!$fechaSeleccionada): ?>
          <div class="slots-placeholder">
            <span class="slots-icon" aria-hidden="true">📅</span>
            <p>Selecciona un día disponible para ver los huecos libres.</p>
          </div>

        <?php elseif (empty($slotsDelDia)): ?>
          <h2 id="slots-titulo" class="panel-title"><?= $fechaTexto ?></h2>
          <p class="empty-state-sm">No quedan huecos disponibles este día.</p>
          <a class="btn" href="<?= $baseUrl ?>">Elegir otro día</a>

        <?php else: ?>
          <h2 id="slots-titulo" class="panel-title"><?= $fechaTexto ?></h2>
          <p class="disp-hint">Consulta ~<?= $duracion ?> min · Elige una hora</p>

          <div class="slots-grid" role="list" aria-label="Horas disponibles">
            <?php foreach ($slotsDelDia as $slot): ?>
              <a class="slot-btn <?= $horaSeleccionada === $slot ? 'slot-btn--activo' : '' ?>"
                 href="<?= $baseUrl ?>&fecha=<?= $fechaSeleccionada ?>&hora=<?= $slot ?>"
                 role="listitem"
                 aria-pressed="<?= $horaSeleccionada === $slot ? 'true' : 'false' ?>">
                <?= $slot ?>
              </a>
            <?php endforeach; ?>
          </div>

          <?php if ($horaSeleccionada): ?>
            <div class="confirm-form-wrapper">
              <div class="confirm-resumen">
                <span class="confirm-icon" aria-hidden="true">✓</span>
                <div>
                  <p class="confirm-texto">
                    <strong><?= $fechaTexto ?></strong> a las <strong><?= $horaSeleccionada ?></strong>
                  </p>
                  <p class="confirm-sub">con Dr./Dra. <?= htmlspecialchars($profesional->getNombre() . ' ' . $profesional->getApellidos()) ?></p>
                </div>
              </div>

              <form method="POST"
                    action="/index.php?controller=PacienteController&action=guardarReprogramacion"
                    data-confirm="¿Confirmas el cambio de cita? La cita del <?= htmlspecialchars($fechaOriginal) ?> quedará cancelada."
                    data-confirm-label="Sí, reprogramar">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                <input type="hidden" name="id_cita" value="<?= $idCita ?>">
                <input type="hidden" name="fecha"   value="<?= htmlspecialchars($fechaSeleccionada) ?>">
                <input type="hidden" name="hora"    value="<?= htmlspecialchars($horaSeleccionada) ?>">
                <button class="btn btn-primary btn-full" type="submit">
                  Confirmar nueva fecha
                </button>
              </form>
            </div>
          <?php endif; ?>

        <?php endif; ?>
      </section>

    </div>
  </main>

  <script src="/assets/js/modal.js?v=<?= filemtime('/var/www/html/public/assets/js/modal.js') ?>"></script>
  <script src="/assets/js/paciente.js?v=<?= filemtime('/var/www/html/public/assets/js/paciente.js') ?>"></script>
</body>
</html>
