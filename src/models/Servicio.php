<?php

namespace Models;

use Config\Database;
use PDO;

class Servicio
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function obtenerTodos(): array
    {
        $sql = "
            SELECT 
                s.id_servicio,
                s.id_categoria,
                s.nombre,
                s.descripcion,
                s.duracion_minutos,
                s.precio_ref,
                s.estado,
                c.nombre AS categoria
            FROM servicios s
            INNER JOIN categorias_servicio c
                ON c.id_categoria = s.id_categoria
            ORDER BY c.nombre, s.nombre
        ";

        $stmt = $this->db->query($sql);

        return $stmt->fetchAll();
    }

    public function obtenerActivos(): array
    {
        $sql = "
            SELECT 
                s.id_servicio,
                s.id_categoria,
                s.nombre,
                s.descripcion,
                s.duracion_minutos,
                s.precio_ref,
                s.estado,
                c.nombre AS categoria
            FROM servicios s
            INNER JOIN categorias_servicio c
                ON c.id_categoria = s.id_categoria
            WHERE s.estado = 'activo'
              AND c.estado = 'activo'
            ORDER BY c.nombre, s.nombre
        ";

        $stmt = $this->db->query($sql);

        return $stmt->fetchAll();
    }

    public function obtenerPorId(int $id): ?array
    {
        $sql = "
            SELECT *
            FROM servicios
            WHERE id_servicio = :id
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'id' => $id
        ]);

        $servicio = $stmt->fetch();

        return $servicio ?: null;
    }

    public function esServicioValido(int $id): bool
    {
        $sql = "
            SELECT id_servicio
            FROM servicios
            WHERE id_servicio = :id
              AND estado = 'activo'
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'id' => $id
        ]);

        return $stmt->fetch() !== false;
    }

    public function crear(array $datos): bool
    {
        $sql = "
            INSERT INTO servicios
            (
                id_categoria,
                nombre,
                descripcion,
                duracion_minutos,
                precio_ref,
                estado
            )
            VALUES
            (
                :id_categoria,
                :nombre,
                :descripcion,
                :duracion_minutos,
                :precio_ref,
                :estado
            )
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'id_categoria' => $datos['id_categoria'],
            'nombre' => $datos['nombre'],
            'descripcion' => $datos['descripcion'] ?? null,
            'duracion_minutos' => $datos['duracion_minutos'],
            'precio_ref' => $datos['precio_ref'],
            'estado' => $datos['estado'] ?? 'activo'
        ]);
    }

    public function actualizar(int $id, array $datos): bool
    {
        $sql = "
            UPDATE servicios
            SET
                id_categoria = :id_categoria,
                nombre = :nombre,
                descripcion = :descripcion,
                duracion_minutos = :duracion_minutos,
                precio_ref = :precio_ref,
                estado = :estado
            WHERE id_servicio = :id
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'id' => $id,
            'id_categoria' => $datos['id_categoria'],
            'nombre' => $datos['nombre'],
            'descripcion' => $datos['descripcion'] ?? null,
            'duracion_minutos' => $datos['duracion_minutos'],
            'precio_ref' => $datos['precio_ref'],
            'estado' => $datos['estado']
        ]);
    }

    public function desactivar(int $id): bool
    {
        $sql = "
            UPDATE servicios
            SET estado = 'inactivo'
            WHERE id_servicio = :id
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'id' => $id
        ]);
    }
}