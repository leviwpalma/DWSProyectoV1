<?php

namespace Models;

use Config\Database;
use PDO;

class CategoriaServicio
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function obtenerTodas(): array
    {
        $sql = "
            SELECT *
            FROM categorias_servicio
            ORDER BY nombre
        ";

        $stmt = $this->db->query($sql);

        return $stmt->fetchAll();
    }

    public function obtenerActivas(): array
    {
        $sql = "
            SELECT *
            FROM categorias_servicio
            WHERE estado = 'activo'
            ORDER BY nombre
        ";

        $stmt = $this->db->query($sql);

        return $stmt->fetchAll();
    }

    public function obtenerPorId(int $id): ?array
    {
        $sql = "
            SELECT *
            FROM categorias_servicio
            WHERE id_categoria = :id
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'id' => $id
        ]);

        $categoria = $stmt->fetch();

        return $categoria ?: null;
    }

    public function crear(array $datos): bool
    {
        $sql = "
            INSERT INTO categorias_servicio
            (
                nombre,
                descripcion,
                estado
            )
            VALUES
            (
                :nombre,
                :descripcion,
                :estado
            )
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'nombre' => $datos['nombre'],
            'descripcion' => $datos['descripcion'] ?? null,
            'estado' => $datos['estado'] ?? 'activo'
        ]);
    }

    public function actualizar(int $id, array $datos): bool
    {
        $sql = "
            UPDATE categorias_servicio
            SET
                nombre = :nombre,
                descripcion = :descripcion,
                estado = :estado
            WHERE id_categoria = :id
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'id' => $id,
            'nombre' => $datos['nombre'],
            'descripcion' => $datos['descripcion'] ?? null,
            'estado' => $datos['estado']
        ]);
    }

    public function desactivar(int $id): bool
    {
        $sql = "
            UPDATE categorias_servicio
            SET estado = 'inactivo'
            WHERE id_categoria = :id
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'id' => $id
        ]);
    }
}