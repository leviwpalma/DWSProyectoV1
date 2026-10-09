<?php

namespace Models;

use PDO;
use PDOException;

class Medico
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function obtenerTodos(): array
    {
        $sql = "SELECT 
                    m.id_medico,
                    m.id_usuario,
                    m.especialidad,
                    m.numero_junta,
                    m.telefono,
                    m.estado,
                    u.email,
                    COALESCE(CONCAT(u.nombre, ' ', u.apellido), 'Sin usuario asignado') AS nombre_completo
                FROM medicos m
                LEFT JOIN usuarios u ON m.id_usuario = u.id_usuario
                ORDER BY m.id_medico DESC";

        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId(int $idMedico): ?array
    {
        $sql = "SELECT 
                    m.id_medico,
                    m.id_usuario,
                    m.especialidad,
                    m.numero_junta,
                    m.telefono,
                    m.estado,
                    u.nombre,
                    u.apellido,
                    u.email
                FROM medicos m
                LEFT JOIN usuarios u ON m.id_usuario = u.id_usuario
                WHERE m.id_medico = :id_medico
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_medico' => $idMedico]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res ?: null;
    }

    public function crearConUsuario(array $datos): int
    {
        try {
            $this->db->beginTransaction();

            // 1. Crear usuario con rol 3 (Doctor)
            $sqlUsuario = "INSERT INTO usuarios (id_rol, nombre, apellido, email, password_hash, estado) 
                           VALUES (3, :nombre, :apellido, :email, :password_hash, 'activo')";
            $stmtUser = $this->db->prepare($sqlUsuario);
            $stmtUser->execute([
                ':nombre'        => $datos['nombre'],
                ':apellido'      => $datos['apellido'],
                ':email'         => $datos['email'],
                ':password_hash' => password_hash($datos['password'], PASSWORD_BCRYPT)
            ]);

            $idUsuario = (int) $this->db->lastInsertId();

            // 2. Crear médico vinculado al id_usuario
            $sqlMedico = "INSERT INTO medicos (id_usuario, especialidad, numero_junta, telefono, estado) 
                          VALUES (:id_usuario, :especialidad, :numero_junta, :telefono, 'activo')";
            $stmtMed = $this->db->prepare($sqlMedico);
            $stmtMed->execute([
                ':id_usuario'    => $idUsuario,
                ':especialidad'  => $datos['especialidad'],
                ':numero_junta'  => $datos['numero_junta'] ?? null,
                ':telefono'      => $datos['telefono'] ?? null
            ]);

            $idMedico = (int) $this->db->lastInsertId();

            $this->db->commit();
            return $idMedico;
        } catch (PDOException $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function cambiarEstado(int $idMedico, string $nuevoEstado): bool
    {
        $sql = "UPDATE medicos SET estado = :estado WHERE id_medico = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':estado' => $nuevoEstado,
            ':id'     => $idMedico
        ]);
    }
}