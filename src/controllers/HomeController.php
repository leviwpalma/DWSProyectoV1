<?php

namespace Controllers;

use Config\Database;
use PDO;

class HomeController
{
    private PDO $db;

    public function __construct()
    {
        if (empty($_SESSION['usuario'])) {
            header('Location: ?url=auth/login');
            exit;
        }

        $this->db = Database::getConnection();
    }

    public function index(): void
    {
        // 1. Citas del día actual que no estén canceladas
        $sqlCitas = "SELECT COUNT(*) FROM citas 
                     WHERE DATE(fecha_hora_inicio) = CURDATE() 
                       AND estado != 'Cancelada'";
        $stmtCitas = $this->db->query($sqlCitas);
        $totalCitasHoy = (int) $stmtCitas->fetchColumn();

        // 2. Total de pacientes registrados y activos
        $sqlPacientes = "SELECT COUNT(*) FROM pacientes WHERE estado = 'activo'";
        $stmtPacientes = $this->db->query($sqlPacientes);
        $totalPacientes = (int) $stmtPacientes->fetchColumn();

        // 3. Total de servicios vigentes en el catálogo
        $sqlServicios = "SELECT COUNT(*) FROM servicios WHERE estado = 'activo'";
        $stmtServicios = $this->db->query($sqlServicios);
        $totalServicios = (int) $stmtServicios->fetchColumn();

        require __DIR__ . '/../views/home/index.php';
    }
}