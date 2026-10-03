<?php

session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

define('BASE_PATH', dirname(__DIR__));

spl_autoload_register(function ($class) {
    $mapa = [
        'App\\'    => 'app/',
        'Config\\' => 'config/',
    ];

    foreach ($mapa as $prefijo => $carpeta) {
        if (str_starts_with($class, $prefijo)) {
            $resto = substr($class, strlen($prefijo));
            $partes = explode('\\', $resto);
            $partes[0] = strtolower($partes[0]);

            $ruta = BASE_PATH . '/' . $carpeta . implode('/', $partes) . '.php';
            if (file_exists($ruta)) {
                require $ruta;
            }
            return;
        }
    }
});

use App\Core\Router;
use App\Controllers\PacientesController;

$rutamiento = new Router();

$rutamiento->get('/pacientes',              [PacientesController::class, 'index']);
$rutamiento->get('/pacientes/create',       [PacientesController::class, 'create']);
$rutamiento->post('/pacientes/create',      [PacientesController::class, 'create']);
$rutamiento->get('/pacientes/{id}',         [PacientesController::class, 'show']);
$rutamiento->get('/pacientes/{id}/edit',    [PacientesController::class, 'edit']);
$rutamiento->post('/pacientes/{id}/edit',   [PacientesController::class, 'edit']);
$rutamiento->post('/pacientes/{id}/delete', [PacientesController::class, 'desactivar']);

$rutamiento->get('/api/pacientes',          [PacientesController::class, 'search']);

$rutamiento->despachar(
    $_SERVER['REQUEST_METHOD'],
    $_SERVER['REQUEST_URI']
);
