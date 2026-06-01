<?php
$usuario      = $data['usuario']     ?? null;
$profesional  = $data['profesional'] ?? null;
$flashError   = $data['error']       ?? null;
$flashSuccess = $data['success']     ?? null;

$u = $_SESSION['usuario'];
$nombreCompleto = htmlspecialchars($u['nombre'] . ' ' . $u['apellidos']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
  <link rel="stylesheet" href="/assets/css/cssPrincipal.css?v=<?= filemtime('/var/www/html/public/assets/css/cssPrincipal.css') ?>">
  <link rel="stylesheet" href="/assets/css/profesional.css?v=<?= filemtime('/var/www/html/public/assets/css/profesional.css') ?>">
  <title>MedTime - Mi perfil</title>
</head>
<body>
  <a class="skip-link" href="#contenido-principal">Saltar al contenido principal</a>

  <header class="site-header">
    <div class="header-inner">
      <a class="brand" href="/index.php?controller=ProfesionalController&action=listProfesional">
        <span class="brand-mark" aria-hidden="true">M</span><span>MedTime</span>
      </a>
      <nav aria-label="Navegación principal">
        <ul class="nav-list">
          <li><a href="/index.php?controller=ProfesionalController&action=listProfesional">Panel</a></li>
          <li><a href="/index.php?controller=ProfesionalController&action=listCitas">Mis citas</a></li>
          <li><a href="/index.php?controller=ProfesionalController&action=listPacientes">Pacientes</a></li>
          <li><a href="/index.php?controller=ProfesionalController&action=perfil" aria-current="page">Mi perfil</a></li>
        </ul>
      </nav>
      <div class="user-tools">
        <button class="profile-button" type="button" aria-label="Perfil de <?= $nombreCompleto ?>">
          <span class="avatar-initials" aria-hidden="true"><?= htmlspecialchars(mb_strtoupper(mb_substr($u['nombre'], 0, 1))) ?></span>
          <span><?= $nombreCompleto ?></span>
          <span aria-hidden="true">⌄</span>
        </button>
        <a class="btn-logout" href="/index.php?controller=AuthController&action=logout">Salir</a>
      </div>
    </div>
  </header>

  <main id="contenido-principal" class="page">

    <?php if ($flashError):   ?><div class="alert alert-error"   role="alert"><?= htmlspecialchars($flashError) ?></div><?php endif; ?>
    <?php if ($flashSuccess): ?><div class="alert alert-success" role="alert"><?= htmlspecialchars($flashSuccess) ?></div><?php endif; ?>

    <div class="page-head">
      <h1 class="page-title">Mi perfil</h1>
    </div>

    <div class="perfil-grid">

      <!-- Información personal + datos profesionales -->
      <section class="card perfil-card perfil-card--wide" aria-labelledby="info-titulo">
        <h2 id="info-titulo" class="perfil-section-title">Información personal y profesional</h2>

        <form method="POST" action="/index.php?controller=ProfesionalController&action=actualizarPerfil">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

          <div class="perfil-form-grid">
            <div class="perfil-form-row">
              <div class="perfil-form-group">
                <label class="perfil-label" for="nombre">Nombre <span class="req" aria-hidden="true">*</span></label>
                <input class="perfil-input" type="text" id="nombre" name="nombre"
                       value="<?= htmlspecialchars($usuario?->getNombre() ?? '') ?>" required>
              </div>
              <div class="perfil-form-group">
                <label class="perfil-label" for="apellidos">Apellidos <span class="req" aria-hidden="true">*</span></label>
                <input class="perfil-input" type="text" id="apellidos" name="apellidos"
                       value="<?= htmlspecialchars($usuario?->getApellidos() ?? '') ?>" required>
              </div>
            </div>

            <div class="perfil-form-group">
              <label class="perfil-label" for="email">Correo electrónico <span class="req" aria-hidden="true">*</span></label>
              <input class="perfil-input" type="email" id="email" name="email"
                     value="<?= htmlspecialchars($usuario?->getEmail() ?? '') ?>" required>
            </div>

            <div class="perfil-form-group">
              <label class="perfil-label" for="telefono">Teléfono</label>
              <input class="perfil-input" type="tel" id="telefono" name="telefono"
                     value="<?= htmlspecialchars($usuario?->getTelefono() ?? '') ?>">
            </div>

            <div class="perfil-divider"><span>Datos profesionales</span></div>

            <div class="perfil-form-group">
              <label class="perfil-label" for="especialidad">Especialidad <span class="req" aria-hidden="true">*</span></label>
              <input class="perfil-input" type="text" id="especialidad" name="especialidad"
                     value="<?= htmlspecialchars($profesional?->getEspecialidad() ?? '') ?>" required>
            </div>

            <div class="perfil-form-row">
              <div class="perfil-form-group">
                <label class="perfil-label" for="duracion">Duración media consulta (min)</label>
                <input class="perfil-input" type="number" id="duracion" name="duracion" min="5" max="120"
                       value="<?= (int)($profesional?->getDuracionMediaConsultaMin() ?? 20) ?>">
              </div>
              <div class="perfil-form-group perfil-form-group--check">
                <label class="perfil-label" for="disponible">Disponibilidad</label>
                <label class="perfil-toggle">
                  <input type="checkbox" id="disponible" name="disponible" value="1"
                         <?= $profesional?->getDisponible() ? 'checked' : '' ?>>
                  <span class="perfil-toggle-track" aria-hidden="true"></span>
                  <span class="perfil-toggle-label">Disponible para nuevas citas</span>
                </label>
              </div>
            </div>
          </div>

          <div class="perfil-actions">
            <button class="btn btn-primary" type="submit">Guardar cambios</button>
          </div>
        </form>
      </section>

      <!-- Cambiar contraseña -->
      <section class="card perfil-card" aria-labelledby="pass-titulo">
        <h2 id="pass-titulo" class="perfil-section-title">Cambiar contraseña</h2>

        <form method="POST" action="/index.php?controller=ProfesionalController&action=cambiarContrasena">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

          <div class="perfil-form-grid">
            <div class="perfil-form-group">
              <label class="perfil-label" for="contrasena_actual">Contraseña actual</label>
              <input class="perfil-input" type="password" id="contrasena_actual" name="contrasena_actual"
                     required autocomplete="current-password" placeholder="••••••••">
            </div>
            <div class="perfil-form-row">
              <div class="perfil-form-group">
                <label class="perfil-label" for="contrasena_nueva">Nueva contraseña</label>
                <input class="perfil-input" type="password" id="contrasena_nueva" name="contrasena_nueva"
                       required autocomplete="new-password" placeholder="Mínimo 8 caracteres">
              </div>
              <div class="perfil-form-group">
                <label class="perfil-label" for="confirmar_contrasena">Confirmar nueva</label>
                <input class="perfil-input" type="password" id="confirmar_contrasena" name="confirmar_contrasena"
                       required autocomplete="new-password" placeholder="Repite la contraseña">
              </div>
            </div>
          </div>

          <div class="perfil-actions">
            <button class="btn btn-primary" type="submit">Cambiar contraseña</button>
          </div>
        </form>
      </section>

    </div>
  </main>

  <footer class="app-footer">
    <p>&copy; <?= date('Y') ?> Daniel Fernández Rodríguez · MedTime</p>
  </footer>
</body>
</html>
