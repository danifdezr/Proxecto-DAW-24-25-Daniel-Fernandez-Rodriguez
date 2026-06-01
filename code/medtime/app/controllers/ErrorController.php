<?php

namespace app\controllers;

class ErrorController extends Controller{
    public function pageNotFound(){
        header("HTTP/1.1 404 Page not found");
        $this->vista->showView("page_not_found");
        exit;
    }
}