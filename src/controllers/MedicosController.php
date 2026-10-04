<?php
namespace Controllers;

use Models\Medico;
use Models\HorarioAtencion;
use PDO;

class MedicosController {
    private $medicoModel;
    private $horarioModel;

    public function __construct(PDO $db) {
        $this->medicoModel  = new Medico($db);
        $this->horarioModel = new HorarioAtencion($db);
    }

    /**
     * ENDPOINT API: Retorna la disponibilidad médica en JSON para peticiones asíncronas (Fetch API)
     * Ejemplo de llamado: /api/medicos/disponibilidad?id_medico=1&fecha=2026-10-15
     */
    public function getDisponibilidad(): void {
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

        // Mapear el día de la semana a español
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
            'status'            => 'success',
            'id_medico'         => $idMedico,
            'fecha'             => $fecha,
            'dia_semana'        => $diaSemana,
            'bloques_atencion'  => $bloques
        ]);
    }
}