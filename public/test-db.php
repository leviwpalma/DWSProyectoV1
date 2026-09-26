<?php
require __DIR__ . '/../app/core/Database.php';

use App\Core\Database;

try {
    $db = Database::getConnection();
    $result = $db->query("SELECT COUNT(*) FROM pacientes")->fetchColumn();
    echo "✅ Conexión OK. Pacientes en la tabla: $result";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}