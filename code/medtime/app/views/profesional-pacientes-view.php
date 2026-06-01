<?php
$profesional = $data['profesional'] ?? null;
$pacientes   = $data['pacientes']   ?? [];

$usuario = $_SESSION['usuario'];
$nombreCompleto = htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellidos']);

$meses = ['', 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];

function formatFechaCorta(string $datetime, array $meses): string {
    $ts = strtotime($datetime);
    return date('j', $ts) . ' ' . $meses[(int)date('n', $ts)] . '. · ' . date('H:i', $ts);
}

function calcularEdad(string $fechaNac): int {
    return (int)(new DateTime($fechaNac))->diff(new DateTime())->y;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="/assets/css/cssPrincipal.css?v=<?= filemtime('/var/www/html/public/assets/css/cssPrincipal.css') ?>">
  <link rel="stylesheet" href="/assets/css/profesional.css?v=<?= filemtime('/var/www/html/public/assets/css/profesional.css') ?>">
  <title>MedTime - Pacientes</title>
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
          <li><a href="/index.php?controller=ProfesionalController&action=listCitas">Mis citas</a></li>
          <li><a href="/index.php?controller=ProfesionalController&action=listPacientes" aria-current="page">Pacientes</a></li>
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
        <h1 class="page-title">Pacientes</h1>
        <p class="page-subtitle">
          <?= $profesional ? htmlspecialchars($profesional->getEspecialidad()) : '' ?> ·
          <strong><?= count($pacientes) ?></strong> paciente<?= count($pacientes) !== 1 ? 's' : '' ?> atendido<?= count($pacientes) !== 1 ? 's' : '' ?>
        </p>
      </div>
    </div>

    <?php if (empty($pacientes)): ?>
      <div class="card">
        <p class="empty-state">Aún no tienes pacientes con citas registradas.</p>
      </div>
    <?php else: ?>
      <div class="pacientes-grid">
        <?php foreach ($pacientes as $p):
          $iniciales = mb_strtoupper(mb_substr($p['nombre'], 0, 1) . mb_substr($p['apellidos'], 0, 1));
          $edad = calcularEdad($p['fecha_nacimiento']);
        ?>
          <article class="card paciente-card">

            <div class="paciente-card-top">
              <div class="paciente-avatar" aria-hidden="true"><?= $iniciales ?></div>
              <div class="paciente-id-info">
                <h2 class="paciente-nombre"><?= htmlspecialchars($p['nombre'] . ' ' . $p['apellidos']) ?></h2>
                <p class="paciente-meta"><?= $edad ?> años · DNI <?= htmlspecialchars($p['dni']) ?></p>
              </div>
              <span class="paciente-visitas" title="Total de citas con este profesional">
                <?= (int)$p['total_citas'] ?> cita<?= (int)$p['total_citas'] !== 1 ? 's' : '' ?>
              </span>
            </div>

            <div class="paciente-contacto">
              <?php if ($p['email']): ?>
                <a class="paciente-contacto-item" href="mailto:<?= htmlspecialchars($p['email']) ?>">
                  <span class="contacto-icon" aria-hidden="true">✉</span>
                  <?= htmlspecialchars($p['email']) ?>
                </a>
              <?php endif; ?>
              <?php if ($p['telefono']): ?>
                <a class="paciente-contacto-item" href="tel:<?= htmlspecialchars($p['telefono']) ?>">
                  <span class="contacto-icon" aria-hidden="true">☎</span>
                  <?= htmlspecialchars($p['telefono']) ?>
                </a>
              <?php endif; ?>
            </div>

            <div class="paciente-citas-info">
              <div class="paciente-cita-dato">
                <span class="cita-dato-label">Última cita</span>
                <span class="cita-dato-valor">
                  <?= $p['ultima_cita'] ? formatFechaCorta($p['ultima_cita'], $meses) : '—' ?>
                </span>
              </div>
              <div class="paciente-cita-dato paciente-cita-dato--proxima">
                <span class="cita-dato-label">Próxima cita</span>
                <span class="cita-dato-valor <?= $p['proxima_cita'] ? 'proxima-activa' : '' ?>">
                  <?= $p['proxima_cita'] ? formatFechaCorta($p['proxima_cita'], $meses) : 'Sin cita próxima' ?>
                </span>
              </div>
            </div>

          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

  </main>
</body>
</html>
