<?php
$error   = $data['error']   ?? null;
$success = $data['success'] ?? null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="/assets/css/login.css?v=<?= filemtime('/var/www/html/public/assets/css/login.css') ?>">
  <title>MedTime - Iniciar sesión</title>
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
    <div class="auth-card card">

      <div class="auth-logo">
        <span class="brand-mark brand-mark--lg" aria-hidden="true">M</span>
      </div>

      <h1 class="auth-title">Bienvenido a MedTime</h1>
      <p class="auth-subtitle">Inicia sesión para continuar</p>

      <?php if ($error): ?>
        <div class="alert alert-error" role="alert"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <?php if ($success): ?>
        <div class="alert alert-success" role="alert"><?= htmlspecialchars($success) ?></div>
      <?php endif; ?>

      <form method="POST" action="/index.php?controller=AuthController&action=login" novalidate>
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
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
          >
        </div>

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
            autocomplete="current-password"
            placeholder="••••••••"
          >
        </div>

        <button class="btn btn-primary btn-full" type="submit">
          Iniciar sesión
        </button>
      </form>

      <p class="auth-switch">
        ¿No tienes cuenta?
        <a href="/index.php?controller=AuthController&action=showRegister">Regístrate</a>
      </p>
    </div>
  </main>

  <footer class="app-footer">
    <p>&copy; <?= date('Y') ?> Daniel Fernández Rodríguez · MedTime</p>
  </footer>
</body>
</html>
