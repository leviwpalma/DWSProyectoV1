<?php

namespace Controllers;

require_once __DIR__ . '/../models/Medico.php';
require_once __DIR__ . '/../models/HorarioAtencion.php';

use Models\Medico;
use Models\HorarioAtencion;
use Config\Database;
use PDO;

class MedicosController 
{
    private $medicoModel;
    private $horarioModel;

    public function __construct(?PDO $db = null) 
    {
        if ($db === null) {
            $db = Database::getConnection();
        }

        $this->medicoModel  = new Medico($db);
        $this->horarioModel = new HorarioAtencion($db);
    }

    /**
     * VISTA: Listado de Médicos y Especialidades
     * Ruta: /?url=medicos
     */
    public function index(): void 
    {
        $medicos = $this->medicoModel->obtenerTodos();
        require __DIR__ . '/../views/medicos/index.php';
    }

    /**
     * VISTA: Gestión de Horarios de Atención
     * Ruta: /?url=medicos/horarios
     */
    public function horarios(): void 
    {
        $medicos = $this->medicoModel->obtenerTodos();
        require __DIR__ . '/../views/medicos/horarios.php';
    }

    /**
     * ENDPOINT API: Retorna la disponibilidad médica en JSON para el módulo de citas
     * Ruta: /?url=medicos/getDisponibilidad&id_medico=1&fecha=2026-10-15
     */
    public function getDisponibilidad(): void 
    {
        header('Content-Type: application/json; charset=utf-8');

        $idMedico = isset($_GET['id_medico']) ? (int)$_GET['id_medico'] : null;
        $fecha    = isset($_GET['fecha']) ? $_GET['fecha'] : null;

        if (!$idMedico || !$fecha) {
            http_response_code(400);
            echo json_encode([
                'status'  => 'error',
                'message' => 'Faltan parámetros requeridos (id_medico, fecha).'
            ]);
            return;
        }

        $diasInglesAEspanol = [
            'Sunday'    => 'Domingo',
            'Monday'    => 'Lunes',
            'Tuesday'   => 'Martes',
            'Wednesday' => 'Miércoles',
            'Thursday'  => 'Jueves',
            'Friday'    => 'Viernes',
            'Saturday'  => 'Sábado'
        ];

        $diaIngles = date('l', strtotime($fecha));
        $diaSemana = $diasInglesAEspanol[$diaIngles];

        $bloques = $this->horarioModel->obtenerBloquesPorDia($idMedico, $diaSemana);

        echo json_encode([
            'status'           => 'success',
            'id_medico'        => $idMedico,
            'fecha'            => $fecha,
            'dia_semana'       => $diaSemana,
            'bloques_atencion' => $bloques
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}