<?php
// Ejecuta el algoritmo real de la app para recalcular las horas estimadas.
// Uso (dentro del contenedor web):  php scripts/recalcular.php [idProfesional] [duracionMin]
// Ej:  docker compose exec -T web php scripts/recalcular.php 1 20

spl_autoload_register(function ($clase) {
    $ruta = '/var/www/html/' . str_replace('\\', '/', $clase) . '.php';
    if (file_exists($ruta)) {
        require_once $ruta;
    }
});

date_default_timezone_set('Europe/Madrid');

$idProfesional = (int)($argv[1] ?? 1);
$duracion      = (int)($argv[2] ?? 20);
$fecha         = date('Y-m-d');

app\models\CitaModel::recalcularEstimaciones($idProfesional, $fecha, $duracion);

echo "✅ recalcularEstimaciones(prof=$idProfesional, $fecha, {$duracion} min) ejecutado\n";
