<?php

namespace app\controllers;

use app\models\PacienteModel;
use app\models\CitaModel;
use app\models\ProfesionalModel;
use app\models\UsuarioModel;

class PacienteController extends Controller
{
    // Panel principal
    public function panel(): void
    {
        $this->requireAuth();
        $this->requireRol('PACIENTE', 'ADMIN');

        $paciente = $this->getPacienteActual();
        $flash    = $this->consumeFlash();

        $idPaciente    = $paciente->getIdPaciente();
        $proximaCita   = CitaModel::getProximaCitaPaciente($idPaciente);
        $citasProximas = CitaModel::getCitasProximasPaciente($idPaciente);
        $historial     = CitaModel::getHistorialPaciente($idPaciente, 4);

        $activas = count(array_filter(
            $citasProximas,
            fn($c) => in_array($c['estado'], ['PENDIENTE', 'CONFIRMADA'])
        ));
        $totalVisitas = count(array_filter(
            CitaModel::getCitasByPacienteConProfesional($idPaciente),
            fn($c) => $c['estado'] === 'FINALIZADA'
        ));

        $restantesCitas = array_slice($citasProximas, 1);

        // Info de cola para citas de hoy
        $colaInfo = null;
        if ($proximaCita && $proximaCita['fecha_cita'] === date('Y-m-d')) {
            $idProf  = (int)$proximaCita['id_profesional'];
            $delante = CitaModel::getPacientesDelante(
                $idProf,
                $proximaCita['fecha_cita'],
                substr($proximaCita['hora_cita'], 0, 5)
            );
            $enConsulta = CitaModel::getCitaEnConsulta($idProf);
            $colaInfo = [
                'delante'    => $delante,
                'enConsulta' => $enConsulta !== null,
            ];
        }

        $this->vista->showView('paciente', array_merge($flash, [
            'paciente'       => $paciente,
            'proximaCita'    => $proximaCita,
            'restantesCitas' => $restantesCitas,
            'historial'      => $historial,
            'activas'        => $activas,
            'totalVisitas'   => $totalVisitas,
            'colaInfo'       => $colaInfo,
        ]));
    }

    //Listado de citas
    public function listCitas(): void
    {
        $this->requireAuth();
        $this->requireRol('PACIENTE', 'ADMIN');

        $paciente = $this->getPacienteActual();
        $flash    = $this->consumeFlash();

        $estadosValidos = ['PENDIENTE', 'CONFIRMADA', 'FINALIZADA', 'CANCELADA'];
        $estadoFiltro   = $_GET['estado'] ?? null;
        if (!in_array($estadoFiltro, $estadosValidos, true)) {
            $estadoFiltro = null;
        }

        $todasLasCitas = CitaModel::getCitasByPacienteConProfesional($paciente->getIdPaciente());

        $conteos = [
            'PENDIENTE'  => count(array_filter($todasLasCitas, fn($c) => $c['estado'] === 'PENDIENTE')),
            'CONFIRMADA' => count(array_filter($todasLasCitas, fn($c) => $c['estado'] === 'CONFIRMADA')),
            'FINALIZADA' => count(array_filter($todasLasCitas, fn($c) => $c['estado'] === 'FINALIZADA')),
            'CANCELADA'  => count(array_filter($todasLasCitas, fn($c) => $c['estado'] === 'CANCELADA')),
        ];

        $citasFiltradas = $estadoFiltro !== null
            ? array_values(array_filter($todasLasCitas, fn($c) => $c['estado'] === $estadoFiltro))
            : $todasLasCitas;

        $this->vista->showView('paciente-citas', array_merge($flash, [
            'paciente'     => $paciente,
            'citas'        => $citasFiltradas,
            'estadoFiltro' => $estadoFiltro,
            'conteos'      => $conteos,
            'total'        => count($todasLasCitas),
        ]));
    }

    //Confirma cita (Únicamente el día de la cita)
    public function confirmarCita(): void
    {
        $this->requireAuth();
        $this->requireRol('PACIENTE', 'ADMIN');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('PacienteController', 'panel');
        }

        $this->validateCsrfToken();

        $idCita   = (int)($_POST['id_cita'] ?? 0);
        $from     = $_POST['from'] ?? 'panel';
        $paciente = $this->getPacienteActual();

        $cita = $idCita ? CitaModel::getCitaById($idCita) : null;

        if (!$cita
            || $cita->getIdPaciente() !== $paciente->getIdPaciente()
            || $cita->getEstado() !== 'PENDIENTE'
        ) {
            $_SESSION['flash_error'] = 'No se puede confirmar esta cita.';
            $this->redirect('PacienteController', $from === 'citas' ? 'listCitas' : 'panel');
        }

        if ($cita->getFechaCita() !== date('Y-m-d')) {
            $_SESSION['flash_error'] = 'Solo puedes confirmar tu asistencia el mismo día de la cita.';
            $this->redirect('PacienteController', $from === 'citas' ? 'listCitas' : 'panel');
        }

        CitaModel::cambiarEstadoCita($idCita, 'CONFIRMADA');
        $_SESSION['flash_success'] = '¡Asistencia confirmada! Nos vemos hoy.';
        $this->redirect('PacienteController', $from === 'citas' ? 'listCitas' : 'panel');
    }

   // Cancelar cita
    public function cancelarCita(): void
    {
        $this->requireAuth();
        $this->requireRol('PACIENTE', 'ADMIN');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('PacienteController', 'panel');
        }

        $this->validateCsrfToken();

        $idCita   = (int)($_POST['id_cita'] ?? 0);
        $from     = $_POST['from'] ?? 'panel';
        $paciente = $this->getPacienteActual();

        $cita = $idCita ? CitaModel::getCitaById($idCita) : null;

        if (!$cita
            || $cita->getIdPaciente() !== $paciente->getIdPaciente()
            || in_array($cita->getEstado(), ['FINALIZADA', 'CANCELADA'])
        ) {
            $_SESSION['flash_error'] = 'No se puede cancelar esta cita.';
            $this->redirect('PacienteController', $from === 'citas' ? 'listCitas' : 'panel');
        }

        CitaModel::cambiarEstadoCita($idCita, 'CANCELADA');
        $_SESSION['flash_success'] = 'La cita ha sido cancelada correctamente.';
        $this->redirect('PacienteController', $from === 'citas' ? 'listCitas' : 'panel');
    }

    //Nueva cita
    public function nuevaCita(): void
    {
        $this->requireAuth();
        $this->requireRol('PACIENTE', 'ADMIN');

        $busqueda      = trim($_GET['q'] ?? '');
        $profesionales = ProfesionalModel::buscarProfesionales($busqueda !== '' ? $busqueda : null);
        $flash         = $this->consumeFlash();

        $this->vista->showView('nueva-cita', array_merge($flash, [
            'profesionales' => $profesionales,
            'busqueda'      => $busqueda,
        ]));
    }

    //Nueva cita (Calendario de disponibilidad)
    public function disponibilidad(): void
    {
        $this->requireAuth();
        $this->requireRol('PACIENTE', 'ADMIN');

        $idProfesional = (int)($_GET['id_profesional'] ?? 0);
        if (!$idProfesional) {
            $this->redirect('PacienteController', 'nuevaCita');
        }

        $profesional = ProfesionalModel::getProfesionalById($idProfesional);
        if (!$profesional || !$profesional->getDisponible()) {
            $_SESSION['flash_error'] = 'El profesional seleccionado no está disponible.';
            $this->redirect('PacienteController', 'nuevaCita');
        }

        $duracion          = $profesional->getDuracionMediaConsultaMin() ?? 20;
        $fechaSeleccionada = $_GET['fecha'] ?? null;
        $horaSeleccionada  = $_GET['hora']  ?? null;

        // Validar fecha
        if ($fechaSeleccionada && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaSeleccionada)) {
            $fechaSeleccionada = null;
        }

        // Generar calendario (próximos 35 días)
        $hoy    = new \DateTime('today');
        $maxDt  = new \DateTime('+35 days');
        $semanas = $this->generarSemanas($hoy, $maxDt, $idProfesional, $duracion);

        // Slots del día seleccionado
        $slotsDelDia = [];
        if ($fechaSeleccionada) {
            $dtFecha = new \DateTime($fechaSeleccionada);
            if ($dtFecha >= $hoy && $dtFecha <= $maxDt) {
                $slotsDelDia = $this->generarSlots($idProfesional, $fechaSeleccionada, $duracion);
            }
        }

        // Validar hora seleccionada (que siga en la lista)
        if ($horaSeleccionada && !in_array($horaSeleccionada, $slotsDelDia, true)) {
            $horaSeleccionada = null;
        }

        $this->vista->showView('disponibilidad', [
            'profesional'       => $profesional,
            'semanas'           => $semanas,
            'fechaSeleccionada' => $fechaSeleccionada,
            'horaSeleccionada'  => $horaSeleccionada,
            'slotsDelDia'       => $slotsDelDia,
            'duracion'          => $duracion,
        ]);
    }

    // Nueva cita - Guardar
    public function guardarCita(): void
    {
        $this->requireAuth();
        $this->requireRol('PACIENTE', 'ADMIN');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('PacienteController', 'nuevaCita');
        }

        $this->validateCsrfToken();

        $idProfesional = (int)($_POST['id_profesional'] ?? 0);
        $fecha         = $_POST['fecha'] ?? '';
        $hora          = $_POST['hora']  ?? '';
        $observaciones = trim($_POST['observaciones'] ?? '') ?: null;

        if (!$idProfesional || !$fecha || !$hora) {
            $_SESSION['flash_error'] = 'Faltan datos para crear la cita.';
            $this->redirect('PacienteController', 'nuevaCita');
        }

        $profesional = ProfesionalModel::getProfesionalById($idProfesional);
        if (!$profesional || !$profesional->getDisponible()) {
            $_SESSION['flash_error'] = 'El profesional ya no está disponible.';
            $this->redirect('PacienteController', 'nuevaCita');
        }

        $fechaHora = $fecha . ' ' . $hora . ':00';
        if (strtotime($fechaHora) <= time()) {
            $_SESSION['flash_error'] = 'La fecha y hora seleccionadas ya han pasado.';
            $this->redirect('PacienteController', 'disponibilidad', [
                'id_profesional' => $idProfesional,
                'fecha'          => $fecha,
            ]);
        }

        $duracion = $profesional->getDuracionMediaConsultaMin() ?? 20;
        $slots    = $this->generarSlots($idProfesional, $fecha, $duracion);
        if (!in_array($hora, $slots, true)) {
            $_SESSION['flash_error'] = 'El horario ya no está disponible. Por favor, elige otro.';
            $this->redirect('PacienteController', 'disponibilidad', [
                'id_profesional' => $idProfesional,
                'fecha'          => $fecha,
            ]);
        }

        $paciente = $this->getPacienteActual();
        $idCita   = CitaModel::crearCita($paciente->getIdPaciente(), $idProfesional, $fechaHora, $observaciones);

        if (!$idCita) {
            $_SESSION['flash_error'] = 'Error al crear la cita. Por favor, inténtalo de nuevo.';
            $this->redirect('PacienteController', 'disponibilidad', [
                'id_profesional' => $idProfesional,
                'fecha'          => $fecha,
            ]);
        }

        $meses = ['', 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
        $ts = strtotime($fecha);
        $fechaTexto = date('j', $ts) . ' de ' . $meses[(int)date('n', $ts)] . '. de ' . date('Y', $ts);

        $_SESSION['flash_success'] = "Cita solicitada para el $fechaTexto a las $hora con " .
            $profesional->getNombre() . ' ' . $profesional->getApellidos() . '.';

        $this->redirect('PacienteController', 'panel');
    }

    /* Helpers
       Reprogramar cita*/
    public function mostrarReprogramar(): void
    {
        $this->requireAuth();
        $this->requireRol('PACIENTE', 'ADMIN');

        $idCita   = (int)($_GET['id_cita'] ?? 0);
        $paciente = $this->getPacienteActual();
        $cita     = $idCita ? CitaModel::getCitaById($idCita) : null;

        if (!$cita
            || $cita->getIdPaciente() !== $paciente->getIdPaciente()
            || in_array($cita->getEstado(), ['FINALIZADA', 'CANCELADA', 'EN_CONSULTA'])
        ) {
            $_SESSION['flash_error'] = 'No se puede reprogramar esta cita.';
            $this->redirect('PacienteController', 'panel');
        }

        $profesional = ProfesionalModel::getProfesionalById($cita->getIdProfesional());
        if (!$profesional || !$profesional->getDisponible()) {
            $_SESSION['flash_error'] = 'El profesional ya no está disponible.';
            $this->redirect('PacienteController', 'panel');
        }

        $duracion          = $profesional->getDuracionMediaConsultaMin() ?? 20;
        $fechaSeleccionada = $_GET['fecha'] ?? null;
        $horaSeleccionada  = $_GET['hora']  ?? null;

        if ($fechaSeleccionada && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaSeleccionada)) {
            $fechaSeleccionada = null;
        }

        $hoy   = new \DateTime('today');
        $maxDt = new \DateTime('+35 days');
        $semanas = $this->generarSemanas($hoy, $maxDt, $cita->getIdProfesional(), $duracion, $idCita);

        $slotsDelDia = [];
        if ($fechaSeleccionada) {
            $dtFecha = new \DateTime($fechaSeleccionada);
            if ($dtFecha >= $hoy && $dtFecha <= $maxDt) {
                $slotsDelDia = $this->generarSlots($cita->getIdProfesional(), $fechaSeleccionada, $duracion, $idCita);

                // Si es el mismo día que la cita original, eliminar su hora para obligar a elegir otra
                if ($cita->getFechaCita() === $fechaSeleccionada) {
                    $horaOriginal = substr($cita->getHoraCita(), 0, 5);
                    $slotsDelDia  = array_values(array_filter($slotsDelDia, fn($s) => $s !== $horaOriginal));
                }
            }
        }

        if ($horaSeleccionada && !in_array($horaSeleccionada, $slotsDelDia, true)) {
            $horaSeleccionada = null;
        }

        $this->vista->showView('reprogramar', [
            'cita'              => $cita,
            'profesional'       => $profesional,
            'semanas'           => $semanas,
            'fechaSeleccionada' => $fechaSeleccionada,
            'horaSeleccionada'  => $horaSeleccionada,
            'slotsDelDia'       => $slotsDelDia,
            'duracion'          => $duracion,
        ]);
    }

    public function guardarReprogramacion(): void
    {
        $this->requireAuth();
        $this->requireRol('PACIENTE', 'ADMIN');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('PacienteController', 'panel');
        }

        $this->validateCsrfToken();

        $idCita = (int)($_POST['id_cita'] ?? 0);
        $fecha  = $_POST['fecha']         ?? '';
        $hora   = $_POST['hora']          ?? '';
        $paciente = $this->getPacienteActual();
        $cita     = $idCita ? CitaModel::getCitaById($idCita) : null;

        if (!$cita
            || $cita->getIdPaciente() !== $paciente->getIdPaciente()
            || in_array($cita->getEstado(), ['FINALIZADA', 'CANCELADA', 'EN_CONSULTA'])
        ) {
            $_SESSION['flash_error'] = 'No se puede reprogramar esta cita.';
            $this->redirect('PacienteController', 'panel');
        }

        $fechaHora = $fecha . ' ' . $hora . ':00';
        if (strtotime($fechaHora) <= time() + 3600) {
            $_SESSION['flash_error'] = 'La nueva hora debe ser al menos 1 hora en el futuro.';
            $this->redirect('PacienteController', 'mostrarReprogramar', ['id_cita' => $idCita]);
        }

        $profesional = ProfesionalModel::getProfesionalById($cita->getIdProfesional());
        $duracion    = $profesional->getDuracionMediaConsultaMin() ?? 20;
        $slots       = $this->generarSlots($cita->getIdProfesional(), $fecha, $duracion, $idCita);

        if (!in_array($hora, $slots, true)) {
            $_SESSION['flash_error'] = 'El horario seleccionado ya no está disponible. Elige otro.';
            $this->redirect('PacienteController', 'mostrarReprogramar', ['id_cita' => $idCita, 'fecha' => $fecha]);
        }

        CitaModel::reprogramarCita($idCita, $fechaHora);

        $meses = ['', 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
        $ts = strtotime($fecha);
        $fechaTexto = date('j', $ts) . ' de ' . $meses[(int)date('n', $ts)] . '. de ' . date('Y', $ts);

        $_SESSION['flash_success'] = "Cita reprogramada para el $fechaTexto a las $hora.";
        $this->redirect('PacienteController', 'panel');
    }

    // Perfil del paciente ("Mi Perfil")
    public function perfil(): void
    {
        $this->requireAuth();
        $this->requireRol('PACIENTE', 'ADMIN');

        $flash = $this->consumeFlash();
        $id    = (int)$_SESSION['usuario']['id'];
        $usuario = UsuarioModel::getUsuarioById($id);

        $this->vista->showView('paciente-perfil', array_merge($flash, [
            'usuario' => $usuario,
        ]));
    }

    public function actualizarPerfil(): void
    {
        $this->requireAuth();
        $this->requireRol('PACIENTE', 'ADMIN');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('PacienteController', 'perfil');
        }

        $this->validateCsrfToken();

        $id        = (int)$_SESSION['usuario']['id'];
        $nombre    = trim($_POST['nombre']    ?? '');
        $apellidos = trim($_POST['apellidos'] ?? '');
        $email     = trim($_POST['email']     ?? '');
        $telefono  = trim($_POST['telefono']  ?? '');

        if (!$nombre || !$apellidos || !$email) {
            $_SESSION['flash_error'] = 'Nombre, apellidos y email son obligatorios.';
            $this->redirect('PacienteController', 'perfil');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['flash_error'] = 'El formato del email no es válido.';
            $this->redirect('PacienteController', 'perfil');
        }

        $existente = UsuarioModel::getUsuarioByEmail($email);
        if ($existente && $existente->getIdUsuario() !== $id) {
            $_SESSION['flash_error'] = 'Ese email ya lo usa otra cuenta.';
            $this->redirect('PacienteController', 'perfil');
        }

        UsuarioModel::actualizarPerfil($id, $nombre, $apellidos, $email, $telefono);

        // Actualizar datos en sesión
        $_SESSION['usuario']['nombre']    = $nombre;
        $_SESSION['usuario']['apellidos'] = $apellidos;
        $_SESSION['usuario']['email']     = $email;

        $_SESSION['flash_success'] = 'Perfil actualizado correctamente.';
        $this->redirect('PacienteController', 'perfil');
    }

    public function cambiarContrasena(): void
    {
        $this->requireAuth();
        $this->requireRol('PACIENTE', 'ADMIN');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('PacienteController', 'perfil');
        }

        $this->validateCsrfToken();

        $id         = (int)$_SESSION['usuario']['id'];
        $actual     = $_POST['contrasena_actual']    ?? '';
        $nueva      = $_POST['contrasena_nueva']     ?? '';
        $confirmar  = $_POST['confirmar_contrasena'] ?? '';

        $usuario = UsuarioModel::getUsuarioById($id);

        if (!password_verify($actual, $usuario->getContrasenaHash())) {
            $_SESSION['flash_error'] = 'La contraseña actual no es correcta.';
            $this->redirect('PacienteController', 'perfil');
        }

        if (strlen($nueva) < 8) {
            $_SESSION['flash_error'] = 'La nueva contraseña debe tener al menos 8 caracteres.';
            $this->redirect('PacienteController', 'perfil');
        }

        if ($nueva !== $confirmar) {
            $_SESSION['flash_error'] = 'Las contraseñas nuevas no coinciden.';
            $this->redirect('PacienteController', 'perfil');
        }

        UsuarioModel::cambiarContrasena($id, password_hash($nueva, PASSWORD_BCRYPT));
        $_SESSION['flash_success'] = 'Contraseña cambiada correctamente.';
        $this->redirect('PacienteController', 'perfil');
    }

    private function generarSlots(int $idProfesional, string $fecha, int $duracionMin, ?int $excludeCitaId = null): array
    {
        if ((int)(new \DateTime($fecha))->format('N') === 7) {
            return [];
        }

        $horarios = [
            ['inicio' => '09:00', 'fin' => '14:00'],
            ['inicio' => '16:00', 'fin' => '20:00'],
        ];

        $citasExistentes = CitaModel::getCitasByProfesionalFecha($idProfesional, $fecha, $excludeCitaId);

        $ocupados = array_map(function ($c) use ($fecha, $duracionMin) {
            $ini = strtotime($fecha . ' ' . substr($c['hora_cita'], 0, 5));
            return ['ini' => $ini, 'fin' => $ini + $duracionMin * 60];
        }, $citasExistentes);

        // solo huecos con al menos 1 hora de antelación
        $umbral = time() + 3600;
        $slots  = [];

        foreach ($horarios as $h) {
            $cur = strtotime($fecha . ' ' . $h['inicio']);
            $end = strtotime($fecha . ' ' . $h['fin']);

            while ($cur + $duracionMin * 60 <= $end) {
                $slotFin = $cur + $duracionMin * 60;

                if ($cur >= $umbral) {
                    $conflicto = false;
                    foreach ($ocupados as $oc) {
                        if ($cur < $oc['fin'] && $slotFin > $oc['ini']) {
                            $conflicto = true;
                            break;
                        }
                    }
                    if (!$conflicto) {
                        $slots[] = date('H:i', $cur);
                    }
                }

                $cur += $duracionMin * 60;
            }
        }

        return $slots;
    }

    private function generarSemanas(\DateTime $desde, \DateTime $hasta, int $idProfesional, int $duracion, ?int $excludeCitaId = null): array
    {
        $slotsCache = [];

        // Empezar el lunes de la semana actual
        $inicio = clone $desde;
        $dow = (int)$inicio->format('N');
        if ($dow > 1) {
            $inicio->modify('-' . ($dow - 1) . ' days');
        }

        $semanas = [];
        $current = clone $inicio;

        while ($current <= $hasta) {
            $semana = [];
            for ($d = 0; $d < 7; $d++) {
                $fechaStr = $current->format('Y-m-d');
                $inRango  = $current >= $desde && $current <= $hasta;
                $esDom    = (int)$current->format('N') === 7;

                $slotsCount = 0;
                if ($inRango && !$esDom) {
                    if (!isset($slotsCache[$fechaStr])) {
                        $slotsCache[$fechaStr] = count($this->generarSlots($idProfesional, $fechaStr, $duracion, $excludeCitaId));
                    }
                    $slotsCount = $slotsCache[$fechaStr];
                }

                $semana[] = [
                    'fecha'     => $fechaStr,
                    'dia'       => (int)$current->format('j'),
                    'mes'       => (int)$current->format('n'),
                    'inRango'   => $inRango,
                    'esHoy'     => $fechaStr === $desde->format('Y-m-d'),
                    'esDom'     => $esDom,
                    'slots'     => $slotsCount,
                ];
                $current->modify('+1 day');
            }
            $semanas[] = $semana;
        }

        return $semanas;
    }

    private function getPacienteActual()
    {
        $paciente = PacienteModel::getPacienteByIdUsuario($_SESSION['usuario']['id']);

        if (!$paciente) {
            $_SESSION['flash_error'] = 'No se encontró tu perfil de paciente.';
            $this->redirect('AuthController', 'logout');
        }

        return $paciente;
    }
}
