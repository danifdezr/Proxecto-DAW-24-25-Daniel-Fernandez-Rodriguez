<?php

namespace app\controllers;

use app\views\View;

class Controller
{
    protected View $vista;

    public function __construct()
    {
        $this->vista = new View();

        // Asegurar que siempre existe un token CSRF en sesión
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }

    // Redirección
    protected function redirect(string $controller, string $action, array $params = []): never
    {
        $url = "/index.php?controller=$controller&action=$action";
        foreach ($params as $key => $value) {
            $url .= '&' . urlencode((string)$key) . '=' . urlencode((string)$value);
        }
        header("Location: $url");
        exit;
    }

    //Validar token
    protected function validateCsrfToken(): void
    {
        $submitted = $_POST['csrf_token'] ?? '';

        if (empty($_SESSION['csrf_token'])
            || !hash_equals($_SESSION['csrf_token'], $submitted)
        ) {
            http_response_code(403);
            die('Petición rechazada: token de seguridad inválido. Recarga la página e inténtalo de nuevo.');
        }
    }

    //Comprobación inicio sesión
    protected function requireAuth(): void
    {
        if (!isset($_SESSION['usuario'])) {
            $this->redirect('AuthController', 'showLogin');
        }
    }

    //Usuario ya logueado intenta acceder a login o registro
    protected function requireGuest(): void
    {
        if (isset($_SESSION['usuario'])) {
            $this->redirect('MainController', 'listMain');
        }
    }

    //Validación de los roles
    protected function requireRol(string ...$roles): void
    {
        if (!in_array($_SESSION['usuario']['rol'] ?? '', $roles, true)) {
            $this->redirect('MainController', 'listMain');
        }
    }

    // Lectura mensaje error, éxito y datos antiguos del formulario guardados en sesión.
    // Return como Array y cierra la sesión.
    protected function consumeFlash(): array
    {
        $data = [
            'error'   => $_SESSION['flash_error']   ?? null,
            'success' => $_SESSION['flash_success'] ?? null,
            'old'     => $_SESSION['flash_old']     ?? [],
        ];
        unset($_SESSION['flash_error'], $_SESSION['flash_success'], $_SESSION['flash_old']);
        return $data;
    }
}
