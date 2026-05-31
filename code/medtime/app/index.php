<?php

require_once "globals.php";

spl_autoload_register(function ($clase) {
    $basePath = dirname(__DIR__);
    $ruta = $basePath . '/' . str_replace('\\', '/', $clase) . '.php';

    if (file_exists($ruta)) {
        require_once $ruta;
    } else {
        error_log("No se encuentra la clase: " . $ruta);
    }
});

// Whitelist de controladores permitidos
$allowedControllers = [
    'AuthController',
    'MainController',
    'PacienteController',
    'ProfesionalController',
    'AdminController',
    'ErrorController',
];

$controllerName = $_GET['controller'] ?? 'AuthController';
$action         = $_GET['action']     ?? 'showLogin';

if (!in_array($controllerName, $allowedControllers, true)) {
    $controllerName = 'ErrorController';
    $action         = 'pageNotFound';
}

$controllerClass = "app\\controllers\\$controllerName";

try {
    if (!class_exists($controllerClass)) {
        throw new \Exception("No existe el controlador: " . $controllerClass);
    }

    $objeto = new $controllerClass();

    if (!method_exists($objeto, $action)) {
        throw new \Exception("No existe la acción: " . $action . " en " . $controllerClass);
    }

    $objeto->$action();

} catch (\Throwable $th) {
    error_log($th->getMessage());
    error_log($th->getTraceAsString());

    $errorController = new app\controllers\ErrorController();
    $errorController->pageNotFound();
}
