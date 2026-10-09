<?php

namespace Controllers;

use Config\Database;
use PDO;

class CitasController
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
        $filtroEstado = $_GET['estado'] ?? 'todos';
        $filtroFecha  = $_GET['fecha'] ?? '';

        // Condicionales de consulta
        $where = [];
        $params = [];

        if ($filtroEstado === 'pendientes') {
            $where[] = "c.estado IN ('Programada', 'Confirmada')";
        } elseif ($filtroEstado === 'atendidas') {
            $where[] = "c.estado = 'Atendida'";
        }

        if (!empty($filtroFecha)) {
            $where[] = "DATE(c.fecha_hora_inicio) = :fecha";
            $params[':fecha'] = $filtroFecha;
        }

        // Si es Doctor, filtrar solo sus citas
        $rol = $_SESSION['usuario']['rol'] ?? '';
        $idUsuario = (int) $_SESSION['usuario']['id_usuario'];
        $idMedicoSesion = null;

        if ($rol === 'Doctor') {
            $stmtM = $this->db->prepare("SELECT id_medico FROM medicos WHERE id_usuario = :u LIMIT 1");
            $stmtM->execute([':u' => $idUsuario]);
            $idMedicoSesion = (int) $stmtM->fetchColumn();
            $where[] = "c.id_medico = :mi_medico";
            $params[':mi_medico'] = $idMedicoSesion;
        }

        $whereSql = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        $sql = "SELECT c.id_cita, c.id_medico, c.fecha_hora_inicio, c.fecha_hora_fin, c.estado, c.motivo_consulta,
                       CONCAT(p.nombres, ' ', p.apellidos) AS paciente,
                       p.telefono AS paciente_telefono,
                       CONCAT(u.nombre, ' ', u.apellido) AS medico,
                       s.nombre AS servicio,
                       (SELECT id_consulta FROM consultas_medicas cm WHERE cm.id_cita = c.id_cita LIMIT 1) AS id_consulta
                FROM citas c
                INNER JOIN pacientes p ON p.id_paciente = c.id_paciente
                INNER JOIN medicos m ON m.id_medico = c.id_medico
                INNER JOIN usuarios u ON u.id_usuario = m.id_usuario
                INNER JOIN servicios s ON s.id_servicio = c.id_servicio
                {$whereSql}
                ORDER BY c.fecha_hora_inicio ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $citas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Datos para modal de agendar
        $pacientes = $this->db->query("SELECT id_paciente, CONCAT(nombres, ' ', apellidos) AS nombre_completo, codigo_expediente FROM pacientes WHERE estado = 'activo' ORDER BY nombres ASC")->fetchAll(PDO::FETCH_ASSOC);
        $medicos   = $this->db->query("SELECT m.id_medico, CONCAT(u.nombre, ' ', u.apellido) AS nombre_completo, m.especialidad FROM medicos m INNER JOIN usuarios u ON u.id_usuario = m.id_usuario WHERE m.estado = 'activo'")->fetchAll(PDO::FETCH_ASSOC);
        $servicios = $this->db->query("SELECT id_servicio, nombre, duracion_minutos, precio_ref FROM servicios WHERE estado = 'activo'")->fetchAll(PDO::FETCH_ASSOC);

        $error = $_GET['error'] ?? null;
        $exito = $_GET['exito'] ?? null;

        require __DIR__ . '/../views/citas/index.php';
    }

    public function agendar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?url=citas/index');
            exit;
        }

        $idPaciente = (int) ($_POST['id_paciente'] ?? 0);
        $idMedico   = (int) ($_POST['id_medico'] ?? 0);
        $idServicio = (int) ($_POST['id_servicio'] ?? 0);
        $fechaHora  = $_POST['fecha_hora'] ?? '';
        $motivo     = trim($_POST['motivo_consulta'] ?? '');

        if (!$idPaciente || !$idMedico || !$idServicio || empty($fechaHora)) {
            header('Location: ?url=citas/index&error=campos_requeridos');
            exit;
        }

        $stmtServ = $this->db->prepare("SELECT duracion_minutos FROM servicios WHERE id_servicio = :id LIMIT 1");
        $stmtServ->execute([':id' => $idServicio]);
        $duracion = (int) $stmtServ->fetchColumn() ?: 30;

        $inicioTs = strtotime($fechaHora);
        $finTs    = $inicioTs + ($duracion * 60);

        $inicioStr = date('Y-m-d H:i:s', $inicioTs);
        $finStr    = date('Y-m-d H:i:s', $finTs);

        $sqlConf = "SELECT COUNT(*) FROM citas 
                    WHERE id_medico = :id_medico 
                      AND estado IN ('Programada', 'Confirmada')
                      AND fecha_hora_inicio < :fin 
                      AND fecha_hora_fin > :inicio";
        $stmtConf = $this->db->prepare($sqlConf);
        $stmtConf->execute([':id_medico' => $idMedico, ':inicio' => $inicioStr, ':fin' => $finStr]);

        if ($stmtConf->fetchColumn() > 0) {
            header('Location: ?url=citas/index&error=horario_ocupado');
            exit;
        }

        $idUsuario = (int) $_SESSION['usuario']['id_usuario'];
        $sqlIns = "INSERT INTO citas (id_paciente, id_medico, id_servicio, id_usuario_registro, fecha_hora_inicio, fecha_hora_fin, estado, motivo_consulta)
                   VALUES (:p, :m, :s, :u, :ini, :fin, 'Programada', :motivo)";
        $stmtIns = $this->db->prepare($sqlIns);
        $stmtIns->execute([
            ':p' => $idPaciente, ':m' => $idMedico, ':s' => $idServicio, ':u' => $idUsuario,
            ':ini' => $inicioStr, ':fin' => $finStr, ':motivo' => $motivo ?: null
        ]);

        header('Location: ?url=citas/index&exito=cita_agendada');
        exit;
    }
}