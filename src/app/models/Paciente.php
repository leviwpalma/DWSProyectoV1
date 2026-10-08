<?php

namespace App\Models;

use PDO;
use PDOException;

class Paciente
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function generarCodigoExpediente(): string
    {
        $sql = "SELECT codigo_expediente FROM pacientes ORDER BY id_paciente DESC LIMIT 1 FOR UPDATE";
        $stmt = $this->db->query($sql);
        $ultimo = $stmt->fetchColumn();

        $siguiente = $ultimo ? ((int) substr($ultimo, 4)) + 1 : 1;
        return 'EXP-' . str_pad($siguiente, 3, '0', STR_PAD_LEFT);
    }

    public function crear(array $datos): int
    {
        try {
            $this->db->beginTransaction();

            $codigo = $this->generarCodigoExpediente();

            $sql = "INSERT INTO pacientes (codigo_expediente, nombres, apellidos, documento_identidad, fecha_nacimiento, genero, telefono, email, direccion, antecedentes_medicos, estado) VALUES (:codigo, :nombres, :apellidos, :documento, :fecha_nacimiento, :genero, :telefono, :email, :direccion, :antecedentes, 'activo')";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':codigo'           => $codigo,
                ':nombres'          => $datos['nombres'],
                ':apellidos'        => $datos['apellidos'],
                ':documento'        => $datos['documento_identidad'] ?? null,
                ':fecha_nacimiento' => $datos['fecha_nacimiento'] ?? null,
                ':genero'           => $datos['genero'] ?? null,
                ':telefono'         => $datos['telefono'],
                ':email'            => $datos['email'] ?? null,
                ':direccion'        => $datos['direccion'] ?? null,
                ':antecedentes'     => $datos['antecedentes_medicos'] ?? null,
            ]);

            $id = (int) $this->db->lastInsertId();
            $this->db->commit();
            return $id;
        } catch (PDOException $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function actualizar(int $id, array $datos): bool
    {
        $sql = "UPDATE pacientes SET nombres = :nombres, apellidos = :apellidos, documento_identidad = :documento, fecha_nacimiento = :fecha_nacimiento, genero = :genero, telefono = :telefono, email = :email, direccion = :direccion, antecedentes_medicos = :antecedentes WHERE id_paciente = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id'               => $id,
            ':nombres'          => $datos['nombres'],
            ':apellidos'        => $datos['apellidos'],
            ':documento'        => $datos['documento_identidad'] ?? null,
            ':fecha_nacimiento' => $datos['fecha_nacimiento'] ?? null,
            ':genero'           => $datos['genero'] ?? null,
            ':telefono'         => $datos['telefono'],
            ':email'            => $datos['email'] ?? null,
            ':direccion'        => $datos['direccion'] ?? null,
            ':antecedentes'     => $datos['antecedentes_medicos'] ?? null,
        ]);
    }

    public function inactivar(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE pacientes SET estado = 'inactivo' WHERE id_paciente = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM pacientes WHERE id_paciente = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);
        return $fila ?: null;
    }

    public function all(): array
    {
        return $this->db->query("SELECT * FROM pacientes WHERE estado = 'activo' ORDER BY id_paciente")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscar(string $q): array
    {
        $sql = "SELECT id_paciente, codigo_expediente, nombres, apellidos, documento_identidad, telefono, email, fecha_nacimiento FROM pacientes WHERE estado = 'activo' AND (codigo_expediente LIKE :q1 OR CONCAT(nombres, ' ', apellidos) LIKE :q2 OR telefono LIKE :q3 OR documento_identidad LIKE :q4) ORDER BY id_paciente LIMIT 25";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':q1' => '%' . $q . '%',
            ':q2' => '%' . $q . '%',
            ':q3' => '%' . $q . '%',
            ':q4' => '%' . $q . '%',
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function historialCitas(int $idPaciente): array
    {
        try {
            $sql = "SELECT c.id_cita, c.fecha_hora_inicio, c.fecha_hora_fin, c.estado, c.motivo_consulta, 
                           s.nombre AS servicio, 
                           CONCAT(u.nombre, ' ', u.apellido) AS medico 
                    FROM citas c 
                    LEFT JOIN servicios s ON s.id_servicio = c.id_servicio 
                    LEFT JOIN medicos m   ON m.id_medico   = c.id_medico 
                    LEFT JOIN usuarios u  ON u.id_usuario  = m.id_usuario 
                    WHERE c.id_paciente = :id 
                    ORDER BY c.fecha_hora_inicio DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $idPaciente]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }
}
