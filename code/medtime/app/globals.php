<?php

date_default_timezone_set('Europe/Madrid');

define("ROOT_PATH", dirname(__DIR__));
define("MODEL_PATH", ROOT_PATH . "/app/models/");
define("VIEW_PATH", ROOT_PATH . "/app/views/");
define("CONTROLLER_PATH", ROOT_PATH . "/app/controllers/");

ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.use_strict_mode', '1');

session_start();