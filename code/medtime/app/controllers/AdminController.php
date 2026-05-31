<?php

namespace app\controllers;

use app\models\UsuarioModel;
use app\models\ProfesionalModel;

class AdminController extends Controller
{
    public function panel(): void
    {
        $this->requireAuth();
        $this->requireRol('ADMIN');
        $this->vista->showView('admin', $this->consumeFlash());
    }

    /* ─────────────── API JSON ─────────────── */

    public function getUsuarios(): void
    {
        $this->requireAuth();
        $this->requireRol('ADMIN');

        $rows = UsuarioModel::getUsuariosConProfesional();
        $data = array_map(fn($r) => [
            'id'           => (int)$r['id_usuario'],
            'nombre'       => $r['nombre'],
            'apellidos'    => $r['apellidos'],
            'email'        => $r['email'],
            'telefono'     => $r['telefono'],
            'rol'          => $r['rol'],
            'activo'       => (bool)$r['activo'],
            'fecha_alta'   => $r['fecha_alta'],
            'especialidad' => $r['especialidad'],
            'disponible'   => isset($r['disponible']) ? (bool)$r['disponible'] : null,
            'duracion_min' => isset($r['duracion_min']) ? (int)$r['duracion_min'] : null,
        ], $rows);

        $this->json(true, $data);
    }

    public function crearUsuario(): void
    {
        $this->requireAuth();
        $this->requireRol('ADMIN');
        $this->requirePost();
        $this->validateCsrfToken();

        $nombre    = trim($_POST['nombre']    ?? '');
        $apellidos = trim($_POST['apellidos'] ?? '');
        $email     = trim($_POST['email']     ?? '');
        $telefono  = trim($_POST['telefono']  ?? '');
        $rol       = $_POST['rol']            ?? 'PACIENTE';
        $password  = $_POST['password']       ?? '';

        if (!$nombre || !$apellidos || !$email || !$password) {
            $this->json(false, null, 'Faltan campos obligatorios.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->json(false, null, 'El formato del email no es válido.');
        }

        if (strlen($password) < 8) {
            $this->json(false, null, 'La contraseña debe tener al menos 8 caracteres.');
        }

        $rolesValidos = ['PACIENTE', 'PROFESIONAL', 'ADMIN'];
        if (!in_array($rol, $rolesValidos, true)) {
            $this->json(false, null, 'Rol no válido.');
        }

        if (UsuarioModel::getUsuarioByEmail($email)) {
            $this->json(false, null, 'Ya existe un usuario con ese email.');
        }

        $hash = password_hash($password, PASSWORD_BCRYPT);
        $id   = UsuarioModel::crearUsuario($nombre, $apellidos, $email, $telefono, $hash, $rol);

        if (!$id) {
            $this->json(false, null, 'Error al crear el usuario.');
        }

        // Si es profesional, crear también el registro en la tabla profesional
        if ($rol === 'PROFESIONAL') {
            $especialidad = trim($_POST['especialidad']    ?? 'Sin especificar');
            $numColegiado = trim($_POST['num_colegiado']   ?? 'COL-' . str_pad((string)$id, 4, '0', STR_PAD_LEFT));
            $duracion     = max(5, (int)($_POST['duracion_consulta'] ?? 20));
            ProfesionalModel::crearProfesional($id, $especialidad ?: 'Sin especificar', $numColegiado, $duracion);
        }

        $this->json(true, ['id' => $id]);
    }

    public function actualizarUsuario(): void
    {
        $this->requireAuth();
        $this->requireRol('ADMIN');
        $this->requirePost();
        $this->validateCsrfToken();

        $id        = (int)($_POST['id']        ?? 0);
        $nombre    = trim($_POST['nombre']    ?? '');
        $apellidos = trim($_POST['apellidos'] ?? '');
        $email     = trim($_POST['email']     ?? '');
        $telefono  = trim($_POST['telefono']  ?? '');
        $rol       = $_POST['rol']            ?? '';
        $activo    = ($_POST['activo'] ?? '0') === '1';

        if (!$id || !$nombre || !$apellidos || !$email) {
            $this->json(false, null, 'Faltan campos obligatorios.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->json(false, null, 'El formato del email no es válido.');
        }

        $rolesValidos = ['PACIENTE', 'PROFESIONAL', 'ADMIN'];
        if (!in_array($rol, $rolesValidos, true)) {
            $this->json(false, null, 'Rol no válido.');
        }

        $existente = UsuarioModel::getUsuarioByEmail($email);
        if ($existente && $existente->getIdUsuario() !== $id) {
            $this->json(false, null, 'El email ya lo usa otro usuario.');
        }

        $ok = UsuarioModel::actualizarUsuario($id, $nombre, $apellidos, $email, $telefono, $rol, $activo);

        if (!$ok) {
            $this->json(false, null, 'Error al actualizar el usuario.');
        }

        // Actualizar o crear datos profesionales si corresponde
        if ($rol === 'PROFESIONAL') {
            $especialidad = trim($_POST['especialidad'] ?? '');
            $duracion     = max(5, (int)($_POST['duracion_min'] ?? 20));
            $disponible   = ($_POST['disponible'] ?? '0') === '1';

            $profExistente = ProfesionalModel::getProfesionalByIdUsuario($id);
            if ($profExistente) {
                ProfesionalModel::actualizarDatosProfesional($id, $especialidad ?: $profExistente->getEspecialidad(), $duracion, $disponible);
            } else {
                $numColegiado = 'COL-' . str_pad((string)$id, 4, '0', STR_PAD_LEFT);
                ProfesionalModel::crearProfesional($id, $especialidad ?: 'Sin especificar', $numColegiado, $duracion);
            }
        }

        $this->json(true, null);
    }

    public function eliminarUsuario(): void
    {
        $this->requireAuth();
        $this->requireRol('ADMIN');
        $this->requirePost();
        $this->validateCsrfToken();

        $id = (int)($_POST['id'] ?? 0);

        if (!$id) {
            $this->json(false, null, 'ID no válido.');
        }

        if ($id === (int)$_SESSION['usuario']['id']) {
            $this->json(false, null, 'No puedes eliminar tu propia cuenta.');
        }

        $ok = UsuarioModel::eliminarUsuario($id);

        if (!$ok) {
            $this->json(false, null, 'Error al eliminar el usuario.');
        }

        $this->json(true, null);
    }

    /* ─────────────── Helpers privados ─────────────── */

    private function json(bool $success, $data = null, string $error = ''): never
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(
            $success
                ? ['success' => true,  'data'  => $data]
                : ['success' => false, 'error' => $error],
            JSON_UNESCAPED_UNICODE
        );
        exit;
    }

    private function requirePost(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(false, null, 'Método no permitido.');
        }
    }
}
