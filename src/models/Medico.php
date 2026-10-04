<?php

namespace Models;

use PDO;

class Medico
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Obtener todos los médicos registrados
     */
    public function obtenerTodos(): array
    {
        $sql = "SELECT 
                    m.id_medico,
                    m.id_usuario,
                    m.especialidad,
                    m.telefono,
                    m.estado,
                    COALESCE(CONCAT(u.nombre, ' ', u.apellido), 'Sin usuario asignado') AS nombre_completo
                FROM medicos m
                LEFT JOIN usuarios u ON m.id_usuario = u.id_usuario";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener un médico por su ID
     */
    public function obtenerPorId(int $idMedico): ?array
    {
        $sql = "SELECT 
                    m.id_medico,
                    m.id_usuario,
                    m.especialidad,
                    m.telefono,
                    m.estado,
                    COALESCE(CONCAT(u.nombre, ' ', u.apellido), 'Sin usuario asignado') AS nombre_completo
                FROM medicos m
                LEFT JOIN usuarios u ON m.id_usuario = u.id_usuario
                WHERE m.id_medico = :id_medico";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_medico' => $idMedico]);

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        return $resultado ?: null;
    }
}