<?php

namespace app\controllers;

use app\controllers\ErrorController;
use app\models\vo\UsuarioVo;
use app\models\UsuarioModel;

class MainController extends Controller{

    public function listMain(){
        $this->vista->showView("main");
    }
}