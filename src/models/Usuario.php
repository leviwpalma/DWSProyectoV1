<?php

namespace Models;

use Config\Database;
use PDO;

class Usuario
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function buscarPorEmail(string $email): ?array
    {
        $sql = "
            SELECT
                u.id_usuario,
                u.id_rol,
                u.nombre,
                u.apellido,
                u.email,
                u.password_hash,
                u.estado,
                r.nombre AS rol
            FROM usuarios u
            INNER JOIN roles r
                ON r.id_rol = u.id_rol
            WHERE u.email = :email
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'email' => $email
        ]);

        $usuario = $stmt->fetch();

        return $usuario ?: null;
    }
}