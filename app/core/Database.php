<?php

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $conn = null;

    public static function getConnection(): PDO
    {
        if (self::$conn === null) {
            $host = getenv('DB_HOST') ?: 'db';
            $name = getenv('DB_NAME') ?: 'uniondental';
            $user = getenv('DB_USER') ?: 'uniondental_user';
            $pass = getenv('DB_PASS') ?: 'uniondental_pass';

            $dsn = "mysql:host={$host};dbname={$name};charset=utf8mb4";

            try {
                self::$conn = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
            } catch (PDOException $e) {
                http_response_code(500);
                exit('Error de conexión a la base de datos: ' . $e->getMessage());
            }
        }

        return self::$conn;
    }
}