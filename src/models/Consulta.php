<?php

namespace Models;

use PDO;

class Consulta
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function obtenerPorCita(int $idCita): ?array
    {
        $sql = "SELECT c.*, 
                       CONCAT(u.nombre, ' ', u.apellido) AS medico_nombre,
                       m.especialidad,
                       m.numero_junta
                FROM consultas_medicas c
                INNER JOIN medicos m ON m.id_medico = c.id_medico
                INNER JOIN usuarios u ON u.id_usuario = m.id_usuario
                WHERE c.id_cita = :id_cita LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_cita' => $idCita]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res ?: null;
    }

    public function obtenerHistorialPaciente(int $idPaciente): array
    {
        $sql = "SELECT c.*, 
                       ci.fecha_hora_inicio,
                       s.nombre AS servicio_nombre,
                       CONCAT(u.nombre, ' ', u.apellido) AS doctor
                FROM consultas_medicas c
                INNER JOIN citas ci ON ci.id_cita = c.id_cita
                INNER JOIN servicios s ON s.id_servicio = ci.id_servicio
                INNER JOIN medicos m ON m.id_medico = c.id_medico
                INNER JOIN usuarios u ON u.id_usuario = m.id_usuario
                WHERE c.id_paciente = :id_paciente
                ORDER BY c.fecha_consulta DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_paciente' => $idPaciente]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function guardar(array $datos): int
    {
        $sql = "INSERT INTO consultas_medicas 
                (id_cita, id_paciente, id_medico, motivo_consulta, sintomas, diagnostico, tratamiento_realizado, receta_medica, notas_observaciones, proxima_cita_sugerida)
                VALUES 
                (:cita, :paciente, :medico, :motivo, :sintomas, :diagnostico, :tratamiento, :receta, :notas, :proxima)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':cita'         => $datos['id_cita'],
            ':paciente'     => $datos['id_paciente'],
            ':medico'       => $datos['id_medico'],
            ':motivo'       => $datos['motivo_consulta'],
            ':sintomas'     => $datos['sintomas'] ?: null,
            ':diagnostico'  => $datos['diagnostico'],
            ':tratamiento'  => $datos['tratamiento_realizado'],
            ':receta'       => $datos['receta_medica'] ?: null,
            ':notas'        => $datos['notas_observaciones'] ?: null,
            ':proxima'      => $datos['proxima_cita_sugerida'] ?: null,
        ]);

        // Marcar la cita como Atendida
        $stmtUp = $this->db->prepare("UPDATE citas SET estado = 'Atendida' WHERE id_cita = :id_cita");
        $stmtUp->execute([':id_cita' => $datos['id_cita']]);

        return (int) $this->db->lastInsertId();
    }
}