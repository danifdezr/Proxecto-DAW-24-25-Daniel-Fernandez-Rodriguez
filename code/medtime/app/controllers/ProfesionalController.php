<?php

namespace app\controllers;

use app\models\ProfesionalModel;
use app\models\CitaModel;
use app\models\PacienteModel;

class ProfesionalController extends Controller
{
    public function listProfesional(): void
    {
        $this->requireAuth();
        $this->requireRol('PROFESIONAL');

        $profesional = $this->getProfesionalActual();
        $flash       = $this->consumeFlash();

        $idProfesional = $profesional->getIdProfesional();
        $citasHoy      = CitaModel::getCitasByProfesionalHoy($idProfesional);
        $citasProximas = CitaModel::getCitasByProfesionalProximas($idProfesional);
        $citaEnConsulta = CitaModel::getCitaEnConsulta($idProfesional);

        $pendientes  = count(array_filter($citasHoy, fn($c) => $c['estado'] === 'PENDIENTE'));
        $confirmadas = count(array_filter($citasHoy, fn($c) => $c['estado'] === 'CONFIRMADA'));
        $finalizadas = count(array_filter($citasHoy, fn($c) => $c['estado'] === 'FINALIZADA'));

        $this->vista->showView('profesional', array_merge($flash, [
            'profesional'    => $profesional,
            'citasHoy'       => $citasHoy,
            'citasProximas'  => $citasProximas,
            'citaEnConsulta' => $citaEnConsulta,
            'pendientes'     => $pendientes,
            'confirmadas'    => $confirmadas,
            'finalizadas'    => $finalizadas,
        ]));
    }

    public function listCitas(): void
    {
        $this->requireAuth();
        $this->requireRol('PROFESIONAL');

        $profesional = $this->getProfesionalActual();

        $estadosValidos  = ['PENDIENTE', 'CONFIRMADA', 'FINALIZADA', 'CANCELADA'];
        $estadoFiltro    = $_GET['estado'] ?? null;
        if (!in_array($estadoFiltro, $estadosValidos, true)) {
            $estadoFiltro = null;
        }

        $todasLasCitas = CitaModel::getCitasByProfesional($profesional->getIdProfesional());

        $conteos = [
            'PENDIENTE'  => count(array_filter($todasLasCitas, fn($c) => $c['estado'] === 'PENDIENTE')),
            'CONFIRMADA' => count(array_filter($todasLasCitas, fn($c) => $c['estado'] === 'CONFIRMADA')),
            'FINALIZADA' => count(array_filter($todasLasCitas, fn($c) => $c['estado'] === 'FINALIZADA')),
            'CANCELADA'  => count(array_filter($todasLasCitas, fn($c) => $c['estado'] === 'CANCELADA')),
        ];

        $citasFiltradas = $estadoFiltro !== null
            ? array_values(array_filter($todasLasCitas, fn($c) => $c['estado'] === $estadoFiltro))
            : $todasLasCitas;

        $this->vista->showView('profesional-citas', [
            'profesional'   => $profesional,
            'citas'         => $citasFiltradas,
            'estadoFiltro'  => $estadoFiltro,
            'conteos'       => $conteos,
            'total'         => count($todasLasCitas),
        ]);
    }

    public function listPacientes(): void
    {
        $this->requireAuth();
        $this->requireRol('PROFESIONAL');

        $profesional = $this->getProfesionalActual();
        $pacientes   = PacienteModel::getPacientesByProfesional($profesional->getIdProfesional());

        $this->vista->showView('profesional-pacientes', [
            'profesional' => $profesional,
            'pacientes'   => $pacientes,
        ]);
    }

    public function llamarPaciente(): void
    {
        $this->requireAuth();
        $this->requireRol('PROFESIONAL');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('ProfesionalController', 'listProfesional');
        }

        $this->validateCsrfToken();

        $idCita      = (int)($_POST['id_cita'] ?? 0);
        $profesional = $this->getProfesionalActual();

        $cita = $idCita ? CitaModel::getCitaById($idCita) : null;

        if (!$cita || $cita->getIdProfesional() !== $profesional->getIdProfesional()) {
            $_SESSION['flash_error'] = 'Cita no encontrada.';
            $this->redirect('ProfesionalController', 'listProfesional');
        }

        if (!in_array($cita->getEstado(), ['PENDIENTE', 'CONFIRMADA'], true)) {
            $_SESSION['flash_error'] = 'Esta cita no se puede iniciar.';
            $this->redirect('ProfesionalController', 'listProfesional');
        }

        // Comprobar que no hay otra consulta activa
        $enCurso = CitaModel::getCitaEnConsulta($profesional->getIdProfesional());
        if ($enCurso !== null) {
            $_SESSION['flash_error'] = 'Ya hay una consulta activa. Finalizarla antes de llamar al siguiente paciente.';
            $this->redirect('ProfesionalController', 'listProfesional');
        }

        CitaModel::iniciarCita($idCita);
        $_SESSION['flash_success'] = 'Consulta iniciada.';
        $this->redirect('ProfesionalController', 'listProfesional');
    }

    public function finalizarConsulta(): void
    {
        $this->requireAuth();
        $this->requireRol('PROFESIONAL');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('ProfesionalController', 'listProfesional');
        }

        $this->validateCsrfToken();

        $idCita      = (int)($_POST['id_cita'] ?? 0);
        $profesional = $this->getProfesionalActual();

        $cita = $idCita ? CitaModel::getCitaById($idCita) : null;

        if (!$cita
            || $cita->getIdProfesional() !== $profesional->getIdProfesional()
            || $cita->getEstado() !== 'EN_CONSULTA'
        ) {
            $_SESSION['flash_error'] = 'No hay una consulta activa para finalizar.';
            $this->redirect('ProfesionalController', 'listProfesional');
        }

        CitaModel::finalizarCita($idCita);

        $duracion = $profesional->getDuracionMediaConsultaMin() ?? 20;
        CitaModel::recalcularEstimaciones(
            $profesional->getIdProfesional(),
            date('Y-m-d'),
            $duracion
        );

        $_SESSION['flash_success'] = 'Consulta finalizada. Tiempos estimados actualizados.';
        $this->redirect('ProfesionalController', 'listProfesional');
    }

    /* ════════════════════════════════════════
       PERFIL DEL PROFESIONAL
    ════════════════════════════════════════ */
    public function perfil(): void
    {
        $this->requireAuth();
        $this->requireRol('PROFESIONAL');

        $flash       = $this->consumeFlash();
        $id          = (int)$_SESSION['usuario']['id'];
        $usuario     = \app\models\UsuarioModel::getUsuarioById($id);
        $profesional = ProfesionalModel::getProfesionalByIdUsuario($id);

        $this->vista->showView('profesional-perfil', array_merge($flash, [
            'usuario'     => $usuario,
            'profesional' => $profesional,
        ]));
    }

    public function actualizarPerfil(): void
    {
        $this->requireAuth();
        $this->requireRol('PROFESIONAL');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('ProfesionalController', 'perfil');
        }

        $this->validateCsrfToken();

        $id        = (int)$_SESSION['usuario']['id'];
        $nombre    = trim($_POST['nombre']       ?? '');
        $apellidos = trim($_POST['apellidos']    ?? '');
        $email     = trim($_POST['email']        ?? '');
        $telefono  = trim($_POST['telefono']     ?? '');
        $especialidad = trim($_POST['especialidad'] ?? '');
        $duracion     = (int)($_POST['duracion']    ?? 20);
        $disponible   = ($_POST['disponible'] ?? '0') === '1';

        if (!$nombre || !$apellidos || !$email) {
            $_SESSION['flash_error'] = 'Nombre, apellidos y email son obligatorios.';
            $this->redirect('ProfesionalController', 'perfil');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['flash_error'] = 'El formato del email no es válido.';
            $this->redirect('ProfesionalController', 'perfil');
        }

        $existente = \app\models\UsuarioModel::getUsuarioByEmail($email);
        if ($existente && $existente->getIdUsuario() !== $id) {
            $_SESSION['flash_error'] = 'Ese email ya lo usa otra cuenta.';
            $this->redirect('ProfesionalController', 'perfil');
        }

        \app\models\UsuarioModel::actualizarPerfil($id, $nombre, $apellidos, $email, $telefono);
        ProfesionalModel::actualizarDatosProfesional($id, $especialidad ?: 'Sin especificar', max(5, $duracion), $disponible);

        $_SESSION['usuario']['nombre']    = $nombre;
        $_SESSION['usuario']['apellidos'] = $apellidos;
        $_SESSION['usuario']['email']     = $email;

        $_SESSION['flash_success'] = 'Perfil actualizado correctamente.';
        $this->redirect('ProfesionalController', 'perfil');
    }

    public function cambiarContrasena(): void
    {
        $this->requireAuth();
        $this->requireRol('PROFESIONAL');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('ProfesionalController', 'perfil');
        }

        $this->validateCsrfToken();

        $id        = (int)$_SESSION['usuario']['id'];
        $actual    = $_POST['contrasena_actual']    ?? '';
        $nueva     = $_POST['contrasena_nueva']     ?? '';
        $confirmar = $_POST['confirmar_contrasena'] ?? '';

        $usuario = \app\models\UsuarioModel::getUsuarioById($id);

        if (!password_verify($actual, $usuario->getContrasenaHash())) {
            $_SESSION['flash_error'] = 'La contraseña actual no es correcta.';
            $this->redirect('ProfesionalController', 'perfil');
        }

        if (strlen($nueva) < 8) {
            $_SESSION['flash_error'] = 'La nueva contraseña debe tener al menos 8 caracteres.';
            $this->redirect('ProfesionalController', 'perfil');
        }

        if ($nueva !== $confirmar) {
            $_SESSION['flash_error'] = 'Las contraseñas no coinciden.';
            $this->redirect('ProfesionalController', 'perfil');
        }

        \app\models\UsuarioModel::cambiarContrasena($id, password_hash($nueva, PASSWORD_BCRYPT));
        $_SESSION['flash_success'] = 'Contraseña cambiada correctamente.';
        $this->redirect('ProfesionalController', 'perfil');
    }

    private function getProfesionalActual()
    {
        $idUsuario   = $_SESSION['usuario']['id'];
        $profesional = ProfesionalModel::getProfesionalByIdUsuario($idUsuario);

        if (!$profesional) {
            $this->redirect('AuthController', 'logout');
        }

        return $profesional;
    }
}
