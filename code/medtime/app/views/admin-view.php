<?php
$flashError   = $data['error']   ?? null;
$flashSuccess = $data['success'] ?? null;

$usuario = $_SESSION['usuario'];
$nombreCompleto = htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellidos']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
  <link rel="stylesheet" href="/assets/css/admin.css?v=<?= filemtime('/var/www/html/public/assets/css/admin.css') ?>">
  <link rel="stylesheet" href="/assets/css/modal.css?v=<?= filemtime('/var/www/html/public/assets/css/modal.css') ?>">
  <title>MedTime — Administración</title>
</head>
<body>
  <a class="skip-link" href="#contenido-principal">Saltar al contenido principal</a>

  <header class="site-header">
    <div class="header-inner">
      <a class="brand" href="/index.php?controller=AdminController&action=panel">
        <span class="brand-mark" aria-hidden="true">M</span>
        <span>MedTime</span>
      </a>

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

    <div class="admin-page-head">
      <div>
        <h1 class="admin-page-title">Gestión de usuarios</h1>
        <p class="admin-page-sub">Total: <strong id="total-usuarios">—</strong> usuarios</p>
      </div>
      <button class="btn-nuevo" id="btn-nuevo-usuario" type="button">
        + Nuevo usuario
      </button>
    </div>

    <div class="admin-search-bar">
      <label class="sr-only" for="search-usuarios">Buscar usuario</label>
      <input
        class="admin-search-input"
        type="search"
        id="search-usuarios"
        placeholder="Buscar por nombre, email o rol…"
        autocomplete="off"
      >
    </div>

    <div class="card table-card">
      <div id="tabla-loading" aria-live="polite">Cargando usuarios…</div>
      <div id="tabla-error" hidden role="alert"></div>
      <div class="table-wrapper">
        <table class="admin-table" aria-label="Listado de usuarios del sistema">
          <caption class="sr-only">Usuarios registrados — usa los botones Editar y Eliminar para gestionarlos</caption>
          <thead>
            <tr>
              <th scope="col">Usuario</th>
              <th scope="col">Rol</th>
              <th scope="col">Especialidad / Info</th>
              <th scope="col">Teléfono</th>
              <th scope="col">Estado</th>
              <th scope="col">Acciones</th>
            </tr>
          </thead>
          <tbody id="usuarios-tbody"></tbody>
        </table>
      </div>
    </div>

  </main>

  <script src="/assets/js/modal.js?v=<?= filemtime('/var/www/html/public/assets/js/modal.js') ?>"></script>
  <script src="/assets/js/admin.js?v=<?= filemtime('/var/www/html/public/assets/js/admin.js') ?>"></script>
</body>
</html>
