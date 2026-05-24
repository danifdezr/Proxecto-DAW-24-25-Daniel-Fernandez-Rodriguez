<?php

namespace app\controllers;
use app\views\View;

class Controller{
    protected View $vista;

    public function __construct(){
        $this->vista = new View();
    }
}