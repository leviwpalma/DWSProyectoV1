<?php

declare(strict_types=1);

namespace Controllers;

// Cargamos explícitamente el modelo para asegurar compatibilidad sin modificar index.php
require_once __DIR__ . '/../models/Cita.php';

use Models\Cita;
use Exception;

class CitasController
{
    private Cita $citaModel;

    public function __construct()
    {
        $this->citaModel = new Cita();
    }

    // GET /citas
    public function index(): void
    {
        $citas = $this->citaModel->obtenerTodas();
        require_once __DIR__ . '/../views/citas/index.php';
    }

    // GET /citas/crear
    public function crear(): void
    {
        require_once __DIR__ . '/../views/citas/crear.php';
    }

    // POST /citas/store
    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = [
                    'paciente_id'       => (int) $_POST['paciente_id'],
                    'medico_id'         => (int) $_POST['medico_id'],
                    'servicio_id'       => (int) $_POST['servicio_id'],
                    'usuario_id'        => (int) ($_SESSION['user_id'] ?? 1),
                    'fecha_hora_inicio' => $_POST['fecha_hora_inicio']
                ];

                $this->citaModel->crear($data);
                header('Location: /citas?mensaje=Cita agendada correctamente');
                exit;
            } catch (Exception $e) {
                $error = $e->getMessage();
                require_once __DIR__ . '/../views/citas/crear.php';
            }
        }
    }

    // GET /citas/editar/{id}
    public function editar(int $id): void
    {
        $cita = $this->citaModel->obtenerPorId($id);
        require_once __DIR__ . '/../views/citas/editar.php';
    }

    // POST /citas/update/{id}
    public function update(int $id): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = [
                    'medico_id'         => (int) $_POST['medico_id'],
                    'servicio_id'       => (int) $_POST['servicio_id'],
                    'fecha_hora_inicio' => $_POST['fecha_hora_inicio'],
                    'estado'            => $_POST['estado'] // Programada, Confirmada, Atendida, Cancelada
                ];

                $this->citaModel->actualizar($id, $data);
                header('Location: /citas?mensaje=Cita actualizada correctamente');
                exit;
            } catch (Exception $e) {
                $error = $e->getMessage();
                $cita = $this->citaModel->obtenerPorId($id);
                require_once __DIR__ . '/../views/citas/editar.php';
            }
        }
    }
}