<?php

namespace Controllers;

require_once __DIR__ . '/../models/Consulta.php';

use Config\Database;
use Models\Consulta;
use PDO;

class ConsultasController
{
    private PDO $db;
    private Consulta $consultaModel;

    public function __construct()
    {
        if (empty($_SESSION['usuario'])) {
            header('Location: ?url=auth/login');
            exit;
        }

        $this->db = Database::getConnection();
        $this->consultaModel = new Consulta($this->db);
    }

    public function atender(int $idCita): void
    {
        $sql = "SELECT c.*, 
                       p.nombres, p.apellidos, p.codigo_expediente, p.documento_identidad, 
                       p.fecha_nacimiento, p.telefono, p.antecedentes_medicos,
                       s.nombre AS servicio_nombre,
                       CONCAT(u.nombre, ' ', u.apellido) AS medico_nombre,
                       m.especialidad, m.numero_junta
                FROM citas c
                INNER JOIN pacientes p ON p.id_paciente = c.id_paciente
                INNER JOIN servicios s ON s.id_servicio = c.id_servicio
                INNER JOIN medicos m ON m.id_medico = c.id_medico
                INNER JOIN usuarios u ON u.id_usuario = m.id_usuario
                WHERE c.id_cita = :id_cita LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_cita' => $idCita]);
        $cita = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$cita) {
            die('Cita no encontrada.');
        }

        // Obtener id_medico del usuario en sesión
        $rolSesion = $_SESSION['usuario']['rol'] ?? '';
        $idUsuario = (int) $_SESSION['usuario']['id_usuario'];
        $idMedicoSesion = null;

        if ($rolSesion === 'Doctor') {
            $stmtM = $this->db->prepare("SELECT id_medico FROM medicos WHERE id_usuario = :u LIMIT 1");
            $stmtM->execute([':u' => $idUsuario]);
            $idMedicoSesion = (int) $stmtM->fetchColumn();
        }

        // Determinar si puede editar: solo el doctor asignado a esta cita específica
        $puedeEditar = ($rolSesion === 'Doctor' && $idMedicoSesion === (int)$cita['id_medico']);

        $edad = '—';
        if (!empty($cita['fecha_nacimiento'])) {
            $fn = new \DateTime($cita['fecha_nacimiento']);
            $fc = new \DateTime($cita['fecha_hora_inicio']);
            $edad = $fn->diff($fc)->y . ' años';
        }

        $consultaExistente = $this->consultaModel->obtenerPorCita($idCita);

        require __DIR__ . '/../views/consultas/atender.php';
    }

    public function guardar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?url=citas/index');
            exit;
        }

        $idCita = (int) $_POST['id_cita'];

        // Validar que el usuario que intenta guardar sea un Doctor y el dueño de la cita
        $rolSesion = $_SESSION['usuario']['rol'] ?? '';
        $idUsuario = (int) $_SESSION['usuario']['id_usuario'];
        
        $stmtM = $this->db->prepare("SELECT m.id_medico FROM medicos m INNER JOIN citas c ON c.id_medico = m.id_medico WHERE m.id_usuario = :u AND c.id_cita = :cita LIMIT 1");
        $stmtM->execute([':u' => $idUsuario, ':cita' => $idCita]);
        $medicoValido = $stmtM->fetchColumn();

        if ($rolSesion !== 'Doctor' || !$medicoValido) {
            die('Acceso denegado: solo el médico tratante asignado puede guardar o modificar esta consulta.');
        }

        $this->consultaModel->guardar([
            'id_cita'                => $idCita,
            'id_paciente'            => (int) $_POST['id_paciente'],
            'id_medico'              => (int) $medicoValido,
            'motivo_consulta'        => trim($_POST['motivo_consulta']),
            'sintomas'               => trim($_POST['sintomas'] ?? ''),
            'diagnostico'            => trim($_POST['diagnostico']),
            'tratamiento_realizado'  => trim($_POST['tratamiento_realizado']),
            'receta_medica'          => trim($_POST['receta_medica'] ?? ''),
            'notas_observaciones'    => trim($_POST['notas_observaciones'] ?? ''),
            'proxima_cita_sugerida'  => $_POST['proxima_cita_sugerida'] ?: null,
        ]);

        header("Location: ?url=consultas/atender/{$idCita}&exito=guardado");
        exit;
    }

    public function imprimir(int $idCita): void
    {
        $stmtClinica = $this->db->query("SELECT * FROM configuracion_clinica WHERE id_config = 1 LIMIT 1");
        $clinica = $stmtClinica->fetch(PDO::FETCH_ASSOC);

        $sql = "SELECT c.*, 
                       cm.*,
                       p.nombres, p.apellidos, p.codigo_expediente, p.documento_identidad, 
                       p.fecha_nacimiento, p.telefono, p.antecedentes_medicos,
                       s.nombre AS servicio_nombre,
                       CONCAT(u.nombre, ' ', u.apellido) AS medico_nombre,
                       m.especialidad, m.numero_junta
                FROM citas c
                INNER JOIN consultas_medicas cm ON cm.id_cita = c.id_cita
                INNER JOIN pacientes p ON p.id_paciente = c.id_paciente
                INNER JOIN servicios s ON s.id_servicio = c.id_servicio
                INNER JOIN medicos m ON m.id_medico = c.id_medico
                INNER JOIN usuarios u ON u.id_usuario = m.id_usuario
                WHERE c.id_cita = :id_cita LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_cita' => $idCita]);
        $datos = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$datos) {
            die('Esta cita aún no tiene consulta redactada.');
        }

        $edad = '—';
        if (!empty($datos['fecha_nacimiento'])) {
            $fn = new \DateTime($datos['fecha_nacimiento']);
            $fc = new \DateTime($datos['fecha_consulta']);
            $edad = $fn->diff($fc)->y . ' años';
        }

        require __DIR__ . '/../views/consultas/imprimir.php';
    }
}