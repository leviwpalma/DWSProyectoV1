<?php

declare(strict_types=1);

namespace Models;

use Config\Database;
use DateTime;
use PDO;
use Exception;

class Cita
{
    private PDO $db;

    public function __construct()
    {
        // Corrección de llamada estática según src/config/Database.php
        $this->db = Database::getConnection(); 
    }

    public function existeTraslape(int $medicoId, string $fechaHoraInicio, string $fechaHoraFin, ?int $citaIdExcluir = null): bool
    {
        $sql = "SELECT COUNT(*) AS total 
                FROM citas 
                WHERE medico_id = :medico_id 
                  AND estado != 'Cancelada'
                  AND fecha_hora_inicio < :fecha_hora_fin 
                  AND fecha_hora_fin > :fecha_hora_inicio";

        if ($citaIdExcluir !== null) {
            $sql .= " AND id != :cita_id_excluir";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':medico_id', $medicoId, PDO::PARAM_INT);
        $stmt->bindValue(':fecha_hora_inicio', $fechaHoraInicio);
        $stmt->bindValue(':fecha_hora_fin', $fechaHoraFin);

        if ($citaIdExcluir !== null) {
            $stmt->bindValue(':cita_id_excluir', $citaIdExcluir, PDO::PARAM_INT);
        }

        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        return (int) $resultado['total'] > 0;
    }

    public function crear(array $data): bool
    {
        // Corrección del campo 'id' por 'id_servicio' según la tabla en MySQL
        $stmtServicio = $this->db->prepare("SELECT duracion_minutos FROM servicios WHERE id_servicio = :id");
        $stmtServicio->execute([':id' => $data['servicio_id']]);
        $servicio = $stmtServicio->fetch(PDO::FETCH_ASSOC);

        if (!$servicio) {
            throw new Exception("El servicio seleccionado no existe.");
        }

        $duracionMinutos = (int) $servicio['duracion_minutos'];

        $inicio = new DateTime($data['fecha_hora_inicio']);
        $fin = clone $inicio;
        $fin->modify("+{$duracionMinutos} minutes");

        $fechaHoraInicioStr = $inicio->format('Y-m-d H:i:s');
        $fechaHoraFinStr = $fin->format('Y-m-d H:i:s');

        if ($this->existeTraslape($data['medico_id'], $fechaHoraInicioStr, $fechaHoraFinStr)) {
            throw new Exception("El médico ya tiene una cita agendada en el rango seleccionado.");
        }

        $sql = "INSERT INTO citas (paciente_id, medico_id, servicio_id, usuario_id, fecha_hora_inicio, fecha_hora_fin, estado) 
                VALUES (:paciente_id, :medico_id, :servicio_id, :usuario_id, :fecha_hora_inicio, :fecha_hora_fin, 'Programada')";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':paciente_id'      => $data['paciente_id'],
            ':medico_id'        => $data['medico_id'],
            ':servicio_id'      => $data['servicio_id'],
            ':usuario_id'       => $data['usuario_id'],
            ':fecha_hora_inicio'=> $fechaHoraInicioStr,
            ':fecha_hora_fin'   => $fechaHoraFinStr
        ]);
    }

    public function actualizar(int $id, array $data): bool
    {
        $stmtCita = $this->db->prepare("SELECT * FROM citas WHERE id = :id");
        $stmtCita->execute([':id' => $id]);
        $citaActual = $stmtCita->fetch(PDO::FETCH_ASSOC);

        if (!$citaActual) {
            throw new Exception("La cita no existe.");
        }

        $medicoId = $data['medico_id'] ?? (int) $citaActual['medico_id'];
        $servicioId = $data['servicio_id'] ?? (int) $citaActual['servicio_id'];
        $estado = $data['estado'] ?? $citaActual['estado'];
        $fechaHoraInicioStr = $data['fecha_hora_inicio'] ?? $citaActual['fecha_hora_inicio'];

        $stmtServicio = $this->db->prepare("SELECT duracion_minutos FROM servicios WHERE id_servicio = :id");
        $stmtServicio->execute([':id' => $servicioId]);
        $servicio = $stmtServicio->fetch(PDO::FETCH_ASSOC);

        $inicio = new DateTime($fechaHoraInicioStr);
        $fin = clone $inicio;
        $fin->modify("+{$servicio['duracion_minutos']} minutes");

        $fechaHoraFinStr = $fin->format('Y-m-d H:i:s');

        // Si cambia a estado Cancelada, se libera automáticamente el bloque de tiempo al omitir traslapes
        if ($estado !== 'Cancelada') {
            if ($this->existeTraslape($medicoId, $fechaHoraInicioStr, $fechaHoraFinStr, $id)) {
                throw new Exception("Conflicto de horario: El médico ya tiene otra cita agendada en ese rango.");
            }
        }

        $sql = "UPDATE citas 
                SET medico_id = :medico_id, 
                    servicio_id = :servicio_id, 
                    fecha_hora_inicio = :fecha_hora_inicio, 
                    fecha_hora_fin = :fecha_hora_fin, 
                    estado = :estado 
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':medico_id'         => $medicoId,
            ':servicio_id'       => $servicioId,
            ':fecha_hora_inicio' => $fechaHoraInicioStr,
            ':fecha_hora_fin'    => $fechaHoraFinStr,
            ':estado'            => $estado,
            ':id'                => $id
        ]);
    }

    public function obtenerTodas(): array
    {
        $sql = "SELECT c.*, 
                       s.nombre AS servicio_nombre
                FROM citas c
                LEFT JOIN servicios s ON c.servicio_id = s.id_servicio
                ORDER BY c.fecha_hora_inicio DESC";

        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId(int $id): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM citas WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}