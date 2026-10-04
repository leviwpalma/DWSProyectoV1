<?php
namespace Models;

use PDO;

class Medico {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    /**
     * Obtener la lista de todos los médicos registrados y activos
     */
    public function obtenerTodos(): array {
        $sql = "SELECT m.id, m.id_usuario, u.nombre, u.apellido, u.email, m.especialidad, m.estado 
                FROM medicos m
                INNER JOIN usuarios u ON m.id_usuario = u.id
                ORDER BY u.apellido ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Registrar un nuevo médico asociado a un id_usuario existente
     */
    public function crear(int $idUsuario, string $especialidad): bool {
        $sql = "INSERT INTO medicos (id_usuario, especialidad, estado) 
                VALUES (:id_usuario, :especialidad, 'Activo')";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id_usuario'   => $idUsuario,
            ':especialidad' => $especialidad
        ]);
    }

    /**
     * Desactivar o reactivar a un médico
     */
    public function cambiarEstado(int $idMedico, string $nuevoEstado): bool {
        $sql = "UPDATE medicos SET estado = :estado WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':estado' => $nuevoEstado,
            ':id'     => $idMedico
        ]);
    }
}