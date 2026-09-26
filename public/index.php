<?php

session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

define('BASE_PATH', dirname(__DIR__));

spl_autoload_register(function ($class){
    $class = str_replace('App\\', '', $class);
    $parts = explode('\\', $class);
    $parts[0] = strtolower($parts[0]);

    $rute = BASE_PATH . '/app/' . implode('/', $parts) . '.php';

    if (file_exists($rute)) {
        require $rute;
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