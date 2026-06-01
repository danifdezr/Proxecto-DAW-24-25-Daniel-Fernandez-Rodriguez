<?php

namespace app\controllers;

class MainController extends Controller
{
    // Redirección (Rol/Usuario)
    public function listMain(): void
    {
        $this->requireAuth();

        $rol = $_SESSION['usuario']['rol'] ?? '';

        if ($rol === 'PROFESIONAL') {
            $this->redirect('ProfesionalController', 'listProfesional');
        }

        if ($rol === 'ADMIN') {
            $this->redirect('AdminController', 'panel');
        }

        $this->redirect('PacienteController', 'panel');
    }
}
