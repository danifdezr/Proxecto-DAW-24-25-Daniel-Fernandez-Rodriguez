<?php

namespace app\controllers;

use app\models\UsuarioModel;
use app\models\PacienteModel;

class AuthController extends Controller
{
    public function showLogin(): void
    {
        $this->requireGuest();
        $this->vista->showView('login', $this->consumeFlash());
    }

    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('AuthController', 'showLogin');
        }

        $this->validateCsrfToken();

        $email     = trim($_POST['email'] ?? '');
        $contrasena = $_POST['contrasena'] ?? '';

        if (!$email || !$contrasena) {
            $_SESSION['flash_error'] = 'Por favor, completa todos los campos.';
            $this->redirect('AuthController', 'showLogin');
        }

        $usuario = UsuarioModel::getUsuarioByEmail($email);

        if (!$usuario || !password_verify($contrasena, $usuario->getContrasenaHash())) {
            $_SESSION['flash_error'] = 'Email o contraseña incorrectos.';
            $this->redirect('AuthController', 'showLogin');
        }

        if (!$usuario->getActivo()) {
            $_SESSION['flash_error'] = 'Tu cuenta está desactivada. Contacta con el administrador.';
            $this->redirect('AuthController', 'showLogin');
        }

        session_regenerate_id(true);
        $_SESSION['usuario'] = [
            'id'        => $usuario->getIdUsuario(),
            'nombre'    => $usuario->getNombre(),
            'apellidos' => $usuario->getApellidos(),
            'email'     => $usuario->getEmail(),
            'rol'       => $usuario->getRol(),
        ];

        $this->redirect('MainController', 'listMain');
    }

    public function showRegister(): void
    {
        $this->requireGuest();
        $this->vista->showView('register', $this->consumeFlash());
    }

    public function register(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('AuthController', 'showRegister');
        }

        $this->validateCsrfToken();

        $nombre     = trim($_POST['nombre'] ?? '');
        $apellidos  = trim($_POST['apellidos'] ?? '');
        $email      = trim($_POST['email'] ?? '');
        $telefono   = trim($_POST['telefono'] ?? '');
        $dni        = strtoupper(trim($_POST['dni'] ?? ''));
        $fechaNac   = $_POST['fecha_nacimiento'] ?? '';
        $contrasena = $_POST['contrasena'] ?? '';
        $confirmar  = $_POST['confirmar_contrasena'] ?? '';

        $old = compact('nombre', 'apellidos', 'email', 'telefono', 'dni', 'fechaNac');

        if (!$nombre || !$apellidos || !$email || !$dni || !$fechaNac || !$contrasena) {
            $_SESSION['flash_error'] = 'Por favor, completa todos los campos obligatorios.';
            $_SESSION['flash_old']   = $old;
            $this->redirect('AuthController', 'showRegister');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['flash_error'] = 'El formato del email no es válido.';
            $_SESSION['flash_old']   = $old;
            $this->redirect('AuthController', 'showRegister');
        }

        if (strlen($contrasena) < 8) {
            $_SESSION['flash_error'] = 'La contraseña debe tener al menos 8 caracteres.';
            $_SESSION['flash_old']   = $old;
            $this->redirect('AuthController', 'showRegister');
        }

        if ($contrasena !== $confirmar) {
            $_SESSION['flash_error'] = 'Las contraseñas no coinciden.';
            $_SESSION['flash_old']   = $old;
            $this->redirect('AuthController', 'showRegister');
        }

        if (UsuarioModel::getUsuarioByEmail($email)) {
            $_SESSION['flash_error'] = 'Ya existe una cuenta con ese email.';
            $_SESSION['flash_old']   = $old;
            $this->redirect('AuthController', 'showRegister');
        }

        $hash      = password_hash($contrasena, PASSWORD_BCRYPT);
        $idUsuario = UsuarioModel::crearUsuario($nombre, $apellidos, $email, $telefono, $hash, 'PACIENTE');

        if (!$idUsuario) {
            $_SESSION['flash_error'] = 'Error al crear la cuenta. Por favor, inténtalo de nuevo.';
            $_SESSION['flash_old']   = $old;
            $this->redirect('AuthController', 'showRegister');
        }

        $numHistorial = 'HIST-' . str_pad((string) $idUsuario, 6, '0', STR_PAD_LEFT);
        PacienteModel::crearPaciente($idUsuario, $dni, $fechaNac, $numHistorial);

        $_SESSION['flash_success'] = '¡Cuenta creada correctamente! Ya puedes iniciar sesión.';
        $this->redirect('AuthController', 'showLogin');
    }

    public function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
        header('Location: /index.php?controller=AuthController&action=showLogin');
        exit;
    }

}
