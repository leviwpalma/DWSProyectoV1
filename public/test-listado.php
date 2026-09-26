<?php
require __DIR__ . '/../app/core/Database.php';
require __DIR__ . '/../app/models/Paciente.php';

use App\Core\Database;
use App\Models\Paciente;

$model = new Paciente(Database::getConnection());
$pacientes = $model->all();

echo "<pre>";
var_dump($pacientes);
echo "</pre>";