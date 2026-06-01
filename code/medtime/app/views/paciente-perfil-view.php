<?php
$usuario      = $data['usuario']  ?? null;
$flashError   = $data['error']    ?? null;
$flashSuccess = $data['success']  ?? null;

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
  <link rel="stylesheet" href="/assets/css/paciente.css?v=<?= filemtime('/var/www/html/public/assets/css/paciente.css') ?>">
  <title>MedTime - Mi perfil</title>
</head>
<body>
  <a class="skip-link" href="#contenido-principal">Saltar al contenido principal</a>

  <header class="site-header">
    <div class="header-inner">
      <a class="brand" href="/index.php?controller=PacienteController&action=panel">
        <span class="brand-mark" aria-hidden="true">M</span><span>MedTime</span>
      </a>
      <nav aria-label="Navegación principal">
        <ul class="nav-list">
          <li><a href="/index.php?controller=PacienteController&action=panel">Panel</a></li>
          <li><a href="/index.php?controller=PacienteController&action=listCitas">Mis citas</a></li>
          <li><a href="/index.php?controller=PacienteController&action=nuevaCita">Pedir cita</a></li>
          <li><a href="/index.php?controller=PacienteController&action=perfil" aria-current="page">Mi perfil</a></li>
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

      <!-- Información personal -->
      <section class="card perfil-card" aria-labelledby="info-titulo">
        <h2 id="info-titulo" class="perfil-section-title">Información personal</h2>

        <form method="POST" action="/index.php?controller=PacienteController&action=actualizarPerfil">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

          <div class="perfil-form-grid">
            <div class="perfil-form-row">
              <div class="perfil-form-group">
                <label class="perfil-label" for="nombre">Nombre <span class="req" aria-hidden="true">*</span></label>
                <input class="perfil-input" type="text" id="nombre" name="nombre"
                       value="<?= htmlspecialchars($usuario?->getNombre() ?? '') ?>" required autocomplete="given-name">
              </div>
              <div class="perfil-form-group">
                <label class="perfil-label" for="apellidos">Apellidos <span class="req" aria-hidden="true">*</span></label>
                <input class="perfil-input" type="text" id="apellidos" name="apellidos"
                       value="<?= htmlspecialchars($usuario?->getApellidos() ?? '') ?>" required autocomplete="family-name">
              </div>
            </div>

            <div class="perfil-form-group">
              <label class="perfil-label" for="email">Correo electrónico <span class="req" aria-hidden="true">*</span></label>
              <input class="perfil-input" type="email" id="email" name="email"
                     value="<?= htmlspecialchars($usuario?->getEmail() ?? '') ?>" required autocomplete="email">
            </div>

            <div class="perfil-form-group">
              <label class="perfil-label" for="telefono">Teléfono</label>
              <input class="perfil-input" type="tel" id="telefono" name="telefono"
                     value="<?= htmlspecialchars($usuario?->getTelefono() ?? '') ?>" autocomplete="tel">
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

        <form method="POST" action="/index.php?controller=PacienteController&action=cambiarContrasena">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

          <div class="perfil-form-grid">
            <div class="perfil-form-group">
              <label class="perfil-label" for="contrasena_actual">Contraseña actual <span class="req" aria-hidden="true">*</span></label>
              <input class="perfil-input" type="password" id="contrasena_actual" name="contrasena_actual"
                     required autocomplete="current-password" placeholder="••••••••">
            </div>
            <div class="perfil-form-row">
              <div class="perfil-form-group">
                <label class="perfil-label" for="contrasena_nueva">Nueva contraseña <span class="req" aria-hidden="true">*</span></label>
                <input class="perfil-input" type="password" id="contrasena_nueva" name="contrasena_nueva"
                       required autocomplete="new-password" placeholder="Mínimo 8 caracteres">
              </div>
              <div class="perfil-form-group">
                <label class="perfil-label" for="confirmar_contrasena">Confirmar nueva <span class="req" aria-hidden="true">*</span></label>
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
