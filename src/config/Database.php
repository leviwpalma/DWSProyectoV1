<?php
namespace Config;

use PDO;
use PDOException;

class Database {
    private static ?PDO $connection = null;

    public static function getConnection(): PDO {
        if (self::$connection === null) {
            $host = getenv('DB_HOST') ?: 'clinica_db';
            $port = getenv('DB_PORT') ?: '3306';
            $dbname = getenv('DB_NAME') ?: 'clinica_dental_db';
            $user = getenv('DB_USER') ?: 'clinica_user';
            $pass = getenv('DB_PASS') ?: 'Clinica2026!Pass';

            $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                self::$connection = new PDO($dsn, $user, $pass, $options);
            } catch (PDOException $e) {
                error_log("Error de conexión a la BD: " . $e->getMessage());
                die(json_encode([
                    'error' => true,
                    'message' => 'Error al conectar con la base de datos.'
                ]));
            }
        }
        return self::$connection;
    }
}