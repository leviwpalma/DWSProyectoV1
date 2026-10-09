<?php

namespace Controllers;

require_once __DIR__ . '/../models/Medico.php';
require_once __DIR__ . '/../models/HorarioAtencion.php';
require_once __DIR__ . '/../models/Usuario.php';

use Models\Medico;
use Models\HorarioAtencion;
use Models\Usuario;
use Config\Database;
use PDO;

class MedicosController 
{
    private Medico $medicoModel;
    private HorarioAtencion $horarioModel;
    private Usuario $usuarioModel;
    private PDO $db;

    public function __construct(?PDO $db = null) 
    {
        if (empty($_SESSION['usuario'])) {
            header('Location: ?url=auth/login');
            exit;
        }

        if ($db === null) {
            $db = Database::getConnection();
        }

        $this->db           = $db;
        $this->medicoModel  = new Medico($db);
        $this->horarioModel = new HorarioAtencion($db);
        $this->usuarioModel = new Usuario();
    }

    public function index(): void 
    {
        $medicos = $this->medicoModel->obtenerTodos();
        $esAdmin = ($_SESSION['usuario']['rol'] ?? '') === 'Administrador';
        $error   = $_GET['error'] ?? null;
        $exito   = $_GET['exito'] ?? null;

        require __DIR__ . '/../views/medicos/index.php';
    }

    public function crear(): void
    {
        $this->exigirAdmin();
        $errores = [];
        require __DIR__ . '/../views/medicos/crear.php';
    }

    public function guardar(): void
    {
        $this->exigirAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?url=medicos/index');
            exit;
        }

        $nombre        = trim($_POST['nombre'] ?? '');
        $apellido      = trim($_POST['apellido'] ?? '');
        $email         = trim($_POST['email'] ?? '');
        $especialidad  = trim($_POST['especialidad'] ?? '');
        $numeroJunta   = trim($_POST['numero_junta'] ?? '');
        $telefono      = trim($_POST['telefono'] ?? '');
        $passwordDoc   = $_POST['password'] ?? '';
        $adminPassword = $_POST['admin_password'] ?? '';

        $errores = [];

        // 1. Confirmar contraseña del Administrador actual
        if (!$this->verificarPasswordAdmin($adminPassword)) {
            $errores[] = 'Contraseña del Administrador incorrecta. Acción no autorizada.';
        }

        // 2. Validaciones de datos
        if ($nombre === '' || $apellido === '') {
            $errores[] = 'Nombre y apellido son obligatorios.';
        }
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'Ingrese un correo electrónico válido.';
        } else {
            if ($this->usuarioModel->buscarPorEmail($email)) {
                $errores[] = 'El correo ya se encuentra registrado.';
            }
        }
        if ($especialidad === '') {
            $errores[] = 'La especialidad es obligatoria.';
        }
        if (mb_strlen($passwordDoc) < 6) {
            $errores[] = 'La contraseña del médico debe tener al menos 6 caracteres.';
        }

        if (!empty($errores)) {
            require __DIR__ . '/../views/medicos/crear.php';
            return;
        }

        $this->medicoModel->crearConUsuario([
            'nombre'        => $nombre,
            'apellido'      => $apellido,
            'email'         => $email,
            'especialidad'  => $especialidad,
            'numero_junta'  => $numeroJunta !== '' ? $numeroJunta : null,
            'telefono'      => $telefono !== '' ? $telefono : null,
            'password'      => $passwordDoc
        ]);

        header('Location: ?url=medicos/index&exito=creado');
        exit;
    }

    public function cambiarEstado(int $id): void
    {
        $this->exigirAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?url=medicos/index');
            exit;
        }

        $adminPassword = $_POST['admin_password'] ?? '';
        $nuevoEstado   = $_POST['estado'] ?? 'inactivo';

        if (!in_array($nuevoEstado, ['activo', 'inactivo'], true)) {
            header('Location: ?url=medicos/index&error=estado_invalido');
            exit;
        }

        if (!$this->verificarPasswordAdmin($adminPassword)) {
            header('Location: ?url=medicos/index&error=password_incorrecta');
            exit;
        }

        $this->medicoModel->cambiarEstado($id, $nuevoEstado);
        header('Location: ?url=medicos/index&exito=actualizado');
        exit;
    }

    /**
     * VISTA: Agenda visual de Disponibilidad y Ocupación por Doctor
     * Ruta: /?url=medicos/horarios
     */
    public function horarios(): void 
    {
        $medicos = $this->medicoModel->obtenerTodos();

        // 1. Tomar médico y fecha de los filtros (o valores por defecto: primer médico y hoy)
        $idMedicoSeleccionado = isset($_GET['id_medico']) ? (int) $_GET['id_medico'] : (!empty($medicos) ? (int)$medicos[0]['id_medico'] : 0);
        $fechaSeleccionada = isset($_GET['fecha']) && !empty($_GET['fecha']) ? $_GET['fecha'] : date('Y-m-d');

        // 2. Consultar las citas del médico para ese día específico
        $citasDelDia = [];
        if ($idMedicoSeleccionado > 0) {
            $sql = "SELECT c.id_cita, c.fecha_hora_inicio, c.fecha_hora_fin, c.estado, c.motivo_consulta,
                           CONCAT(p.nombres, ' ', p.apellidos) AS paciente,
                           p.telefono,
                           s.nombre AS servicio,
                           s.duracion_minutos
                    FROM citas c
                    INNER JOIN pacientes p ON p.id_paciente = c.id_paciente
                    INNER JOIN servicios s ON s.id_servicio = c.id_servicio
                    WHERE c.id_medico = :id_medico
                      AND DATE(c.fecha_hora_inicio) = :fecha
                      AND c.estado != 'Cancelada'
                    ORDER BY c.fecha_hora_inicio ASC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':id_medico' => $idMedicoSeleccionado,
                ':fecha'     => $fechaSeleccionada
            ]);
            $citasDelDia = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // 3. Generar bloques horarios de 08:00 a 17:00 (intervalos de 30 minutos)
        $horaApertura = strtotime($fechaSeleccionada . ' 08:00:00');
        $horaCierre   = strtotime($fechaSeleccionada . ' 17:00:00');

        $grillaHorarios = [];
        for ($horaActual = $horaApertura; $horaActual < $horaCierre; $horaActual += (30 * 60)) {
            $finSlot = $horaActual + (30 * 60);
            
            // Verificar si este bloque interseca con alguna cita agendada
            $citaOcupante = null;
            foreach ($citasDelDia as $cita) {
                $inicioCita = strtotime($cita['fecha_hora_inicio']);
                $finCita    = strtotime($cita['fecha_hora_fin']);

                if ($horaActual < $finCita && $finSlot > $inicioCita) {
                    $citaOcupante = $cita;
                    break;
                }
            }

            $grillaHorarios[] = [
                'hora_inicio_str' => date('H:i', $horaActual),
                'hora_fin_str'    => date('H:i', $finSlot),
                'ocupado'         => ($citaOcupante !== null),
                'cita'            => $citaOcupante
            ];
        }

        require __DIR__ . '/../views/medicos/horarios.php';
    }

    public function getDisponibilidad(): void 
    {
        header('Content-Type: application/json; charset=utf-8');

        $idMedico = isset($_GET['id_medico']) ? (int)$_GET['id_medico'] : null;
        $fecha    = isset($_GET['fecha']) ? $_GET['fecha'] : null;

        if (!$idMedico || !$fecha) {
            http_response_code(400);
            echo json_encode([
                'status'  => 'error',
                'message' => 'Faltan parámetros requeridos (id_medico, fecha).'
            ]);
            return;
        }

        $diasInglesAEspanol = [
            'Sunday'    => 'Domingo',
            'Monday'    => 'Lunes',
            'Tuesday'   => 'Martes',
            'Wednesday' => 'Miercoles',
            'Thursday'  => 'Jueves',
            'Friday'    => 'Viernes',
            'Saturday'  => 'Sabado'
        ];

        $diaIngles = date('l', strtotime($fecha));
        $diaSemana = $diasInglesAEspanol[$diaIngles];

        $bloques = $this->horarioModel->obtenerBloquesPorDia($idMedico, $diaSemana);

        echo json_encode([
            'status'           => 'success',
            'id_medico'        => $idMedico,
            'fecha'            => $fecha,
            'dia_semana'       => $diaSemana,
            'bloques_atencion' => $bloques
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    private function exigirAdmin(): void
    {
        if (($_SESSION['usuario']['rol'] ?? '') !== 'Administrador') {
            http_response_code(403);
            die('Acceso denegado. Se requieren permisos de Administrador.');
        }
    }

    private function verificarPasswordAdmin(string $password): bool
    {
        if (empty($password)) return false;

        $idAdmin = (int) $_SESSION['usuario']['id_usuario'];
        $stmt = $this->db->prepare("SELECT password_hash FROM usuarios WHERE id_usuario = :id LIMIT 1");
        $stmt->execute([':id' => $idAdmin]);
        $hash = $stmt->fetchColumn();

        return $hash ? password_verify($password, $hash) : false;
    }
}