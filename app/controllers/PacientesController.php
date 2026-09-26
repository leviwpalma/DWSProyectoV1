<?php

namespace App\Controllers;

use App\Core\Database;
use App\Models\Paciente;

class PacientesController
{
    private Paciente $model;

    public function __construct()
    {
        $this->model = new Paciente(Database::getConnection());
    }

    //
    public function index(): void
    {
        $q = trim($_GET['q'] ?? '');
        $pacientes = $q !== '' ? $this->model->buscar($q) : $this->model->all();

        if ($this->esAjax()) {
            header('Content-Type: application/json');
            echo json_encode(['ok' => true, 'data' => $pacientes]);
            return;
        }

        require __DIR__ . '/../views/pacientes/index.php';
    }

    //
    public function create(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST'){
            $datos = $this->validaciones($_POST);
            $this->model->crear($datos);
            header('Location: /pacientes');
            exit;
        }

        $paciente = null;
        require __DIR__ . '/../views/pacientes/form.php';
    }

    //
    public function edit(int $id): void
    {
        $paciente = $this->model->findById($id);

        if (!$paciente) {
            http_response_code(404);
            exit('Paciente no existe');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = $this->validaciones($_POST);
            $this->model->actualizar($id, $datos);
            header('Location: /pacientes');
            exit;
        }

        require __DIR__ . '/../views/pacientes/form.php';
    }

    //
    public function show(int $id): void
    {
        $paciente = $this->model->findById($id);

        if (!$paciente) {
            http_response_code(404);
            exit('Paciente no existe');
        }

        $historial = $this->model->historialCitas($id);

        require __DIR__ . '/../views/pacientes/show.php';
    }

    public function desactivar(int $id): void
    {
        $this->model->inactivar($id);
        header('Location: /pacientes');
        exit;
    }

    public function search(): void
    {
        header('Content-Type: application/json');
        $q = trim($_GET['q'] ?? '');
        echo json_encode(['ok' => true, 'data' => $this->model->buscar($q)]);
    }

    private function validaciones(array $datos): array
    {
        $errores = [];

        if (empty(trim($datos['nombres'] ?? ''))) {
            $errores[] = 'El nombre es obligatorio.';
        }
        if (empty(trim($datos['apellidos'] ?? ''))) {
            $errores[] = 'El apellido es obligatorio.';
        }
        if (empty(trim($datos['telefono'] ?? ''))) {
            $errores[] = 'El teléfono es obligatorio.';
        }
        if (empty($datos['fecha_nacimiento'] ?? '')) {
            $errores[] = 'La fecha de nacimiento es obligatoria.';
        } elseif (strtotime($datos['fecha_nacimiento']) >= strtotime('today')) {
            $errores[] = 'La fecha de nacimiento debe ser anterior a hoy.';
        }
        if (!empty($datos['correo']) && !filter_var($datos['correo'], FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'El correo no tiene formato válido.';
        }

        if (!empty($errores)) {
            http_response_code(422);
            header('Content-Type: application/json');
            echo json_encode(['ok' => false, 'errores' => $errores]);
            exit;
        }

        return [
            'nombres'               => trim($datos['nombres']),
            'apellidos'             => trim($datos['apellidos']),
            'telefono'              => trim($datos['telefono']),
            'correo'                => !empty($datos['correo']) ? trim($datos['correo']) : null,
            'fecha_nacimiento'      => $datos['fecha_nacimiento'],
        ];
    }

    //
    private function esAjax(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === "xmlhttprequest";
    }
}