<?php
$error = $data['error'] ?? null;
$old   = $data['old']   ?? [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="/assets/css/registro.css?v=<?= filemtime('/var/www/html/public/assets/css/registro.css') ?>">
  <title>MedTime - Crear cuenta</title>
</head>
<body>
  <a class="skip-link" href="#contenido-principal">Saltar al contenido principal</a>

  <header class="site-header">
    <div class="header-inner">
      <a class="brand" href="/index.php?controller=AuthController&action=showLogin" aria-label="MedTime, ir al inicio">
        <span class="brand-mark" aria-hidden="true">M</span>
        <span>MedTime</span>
      </a>
    </div>
  </header>

  <main id="contenido-principal" class="auth-page">
    <div class="auth-card auth-card--wide card">

      <div class="auth-logo">
        <span class="brand-mark brand-mark--lg" aria-hidden="true">M</span>
      </div>

      <h1 class="auth-title">Crear cuenta</h1>
      <p class="auth-subtitle">Regístrate como paciente en MedTime</p>

      <?php if ($error): ?>
        <div class="alert alert-error" role="alert"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST" action="/index.php?controller=AuthController&action=register" novalidate>
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

        <div class="form-row">
          <div class="form-group">
            <label class="form-label" for="nombre">
              Nombre <span class="required" aria-hidden="true">*</span>
            </label>
            <input
              class="form-input"
              type="text"
              id="nombre"
              name="nombre"
              required
              autocomplete="given-name"
              placeholder="Daniel"
              value="<?= htmlspecialchars($old['nombre'] ?? '') ?>"
            >
          </div>

          <div class="form-group">
            <label class="form-label" for="apellidos">
              Apellidos <span class="required" aria-hidden="true">*</span>
            </label>
            <input
              class="form-input"
              type="text"
              id="apellidos"
              name="apellidos"
              required
              autocomplete="family-name"
              placeholder="García López"
              value="<?= htmlspecialchars($old['apellidos'] ?? '') ?>"
            >
          </div>
        </div>

        <div class="form-group">
          <label class="form-label" for="email">
            Correo electrónico <span class="required" aria-hidden="true">*</span>
          </label>
          <input
            class="form-input"
            type="email"
            id="email"
            name="email"
            required
            autocomplete="email"
            placeholder="tu@email.com"
            value="<?= htmlspecialchars($old['email'] ?? '') ?>"
          >
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label" for="telefono">Teléfono</label>
            <input
              class="form-input"
              type="tel"
              id="telefono"
              name="telefono"
              autocomplete="tel"
              placeholder="600 123 456"
              value="<?= htmlspecialchars($old['telefono'] ?? '') ?>"
            >
          </div>

          <div class="form-group">
            <label class="form-label" for="dni">
              DNI <span class="required" aria-hidden="true">*</span>
            </label>
            <input
              class="form-input"
              type="text"
              id="dni"
              name="dni"
              required
              placeholder="12345678A"
              maxlength="9"
              value="<?= htmlspecialchars($old['dni'] ?? '') ?>"
            >
          </div>
        </div>

        <div class="form-group">
          <label class="form-label" for="fecha_nacimiento">
            Fecha de nacimiento <span class="required" aria-hidden="true">*</span>
          </label>
          <input
            class="form-input"
            type="date"
            id="fecha_nacimiento"
            name="fecha_nacimiento"
            required
            value="<?= htmlspecialchars($old['fechaNac'] ?? '') ?>"
          >
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label" for="contrasena">
              Contraseña <span class="required" aria-hidden="true">*</span>
            </label>
            <input
              class="form-input"
              type="password"
              id="contrasena"
              name="contrasena"
              required
              autocomplete="new-password"
              placeholder="Mínimo 8 caracteres"
            >
          </div>

          <div class="form-group">
            <label class="form-label" for="confirmar_contrasena">
              Confirmar contraseña <span class="required" aria-hidden="true">*</span>
            </label>
            <input
              class="form-input"
              type="password"
              id="confirmar_contrasena"
              name="confirmar_contrasena"
              required
              autocomplete="new-password"
              placeholder="Repite la contraseña"
            >
          </div>
        </div>

        <p class="form-hint">Los campos marcados con <span class="required">*</span> son obligatorios.</p>

        <button class="btn btn-primary btn-full" type="submit">
          Crear cuenta
        </button>
      </form>

      <p class="auth-switch">
        ¿Ya tienes cuenta?
        <a href="/index.php?controller=AuthController&action=showLogin">Inicia sesión</a>
      </p>
    </div>
  </main>

  <footer class="app-footer">
    <p>&copy; <?= date('Y') ?> Daniel Fernández Rodríguez · MedTime</p>
  </footer>
</body>
</html>
