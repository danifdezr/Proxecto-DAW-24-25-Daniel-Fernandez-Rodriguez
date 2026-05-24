<?php

namespace app\controllers;

use app\controllers\ErrorController;
use app\models\vo\UsuarioVo;
use app\models\UsuarioModel;

class UsuarioController extends Controller{

    public function listarUsuarios(){
        $usuarios = UsuarioModel::getUsers();
        $this->vista->showView("usuarios",['usuarios'=>$usuarios]);
    }
}