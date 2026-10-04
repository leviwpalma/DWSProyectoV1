<?php
namespace Models;

use PDO;

class HorarioAtencion {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    /**
     * Obtener todas las franjas/bloques de atención configurados para un médico específico
     */
    public function obtenerPorMedico(int $idMedico): array {
        $sql = "SELECT id, id_medico, dia_semana, hora_apertura, hora_cierre 
                FROM horarios_atencion 
                WHERE id_medico = :id_medico 
                ORDER BY FIELD(dia_semana, 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'), hora_apertura ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_medico' => $idMedico]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Guardar o asignar una nueva franja horaria a un médico
     */
    public function crear(int $idMedico, string $diaSemana, string $horaApertura, string $horaCierre): bool {
        $sql = "INSERT INTO horarios_atencion (id_medico, dia_semana, hora_apertura, hora_cierre) 
                VALUES (:id_medico, :dia_semana, :hora_apertura, :hora_cierre)";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id_medico'     => $idMedico,
            ':dia_semana'    => $diaSemana,
            ':hora_apertura' => $horaApertura,
            ':hora_cierre'   => $horaCierre
        ]);
    }

    /**
     * Regla de Negocio RN-02: Comprueba si un médico atiende en una fecha y hora determinadas
     */
    public function validarRN02RangoAtencion(int $idMedico, string $diaSemana, string $horaConsulta): bool {
        $sql = "SELECT COUNT(*) FROM horarios_atencion 
                WHERE id_medico = :id_medico 
                AND LOWER(dia_semana) = LOWER(:dia_semana) 
                AND :hora_consulta BETWEEN hora_apertura AND hora_cierre";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':id_medico'      => $idMedico,
            ':dia_semana'     => $diaSemana,
            ':hora_consulta'  => $horaConsulta
        ]);

        return $stmt->fetchColumn() > 0;
    }

    /**
     * Obtener bloques de atención de un médico en un día específico (Usado para Fetch API)
     */
    public function obtenerBloquesPorDia(int $idMedico, string $diaSemana): array {
        $sql = "SELECT hora_apertura, hora_cierre 
                FROM horarios_atencion 
                WHERE id_medico = :id_medico 
                AND LOWER(dia_semana) = LOWER(:dia_semana)";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':id_medico' => $idMedico,
            ':dia_semana' => $diaSemana
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}