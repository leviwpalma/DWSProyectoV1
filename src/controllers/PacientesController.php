<?php

namespace Controllers;

require_once __DIR__ . '/../models/Paciente.php';

use Config\Database;
use Models\Paciente;
use PDO;

class PacientesController
{
    private Paciente $model;
    private PDO $db;

    public function __construct()
    {
        if (empty($_SESSION['usuario'])) {
            header('Location: ?url=auth/login');
            exit;
        }

        $this->db = Database::getConnection();
        $this->model = new Paciente($this->db);
    }

    public function index(): void
    {
        $q = trim($_GET['q'] ?? '');
        $pacientes = $q !== '' ? $this->model->buscar($q) : $this->model->all();

        if ($this->esAjax()) {
            header('Content-Type: application/json');
            echo json_encode(['ok' => true, 'data' => $pacientes]);
            return;
        }

        require __DIR__ . '/../views/pacientes/index.php';
    }

    public function create(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $resultado = $this->validaciones($_POST);

            if (!empty($resultado['errores'])) {
                $paciente = $_POST;
                $errores = $resultado['errores'];
                require __DIR__ . '/../views/pacientes/form.php';
                return;
            }

            $this->model->crear($resultado['datos']);
            header('Location: ?url=pacientes/index');
            exit;
        }

        $paciente = null;
        $errores = [];
        require __DIR__ . '/../views/pacientes/form.php';
    }

    public function edit(int $id): void
    {
        $paciente = $this->model->findById($id);

        if (!$paciente) {
            http_response_code(404);
            exit('Paciente no existe');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $resultado = $this->validaciones($_POST);

            if (!empty($resultado['errores'])) {
                $paciente = $_POST;
                $paciente['id_paciente'] = $id;
                $errores = $resultado['errores'];
                require __DIR__ . '/../views/pacientes/form.php';
                return;
            }

            $this->model->actualizar($id, $resultado['datos']);
            header('Location: ?url=pacientes/index');
            exit;
        }

        $errores = [];
        require __DIR__ . '/../views/pacientes/form.php';
    }

    public function eliminar(int $id): void
    {
        $paciente = $this->model->findById($id);

        if (!$paciente) {
            http_response_code(404);
            exit('Paciente no existe');
        }

        $eliminando = true;
        require __DIR__ . '/../views/pacientes/form.php';
    }

    public function show(int $id): void
    {
        $paciente = $this->model->findById($id);

        if (!$paciente) {
            http_response_code(404);
            exit('Paciente no existe');
        }

        // Consultar historial enriquecido con diagnósticos médicos y citas
        $sql = "SELECT c.id_cita, c.fecha_hora_inicio, c.estado,
                       s.nombre AS servicio,
                       CONCAT(u.nombre, ' ', u.apellido) AS medico,
                       cm.id_consulta,
                       cm.diagnostico,
                       cm.tratamiento_realizado
                FROM citas c
                INNER JOIN servicios s ON s.id_servicio = c.id_servicio
                INNER JOIN medicos m ON m.id_medico = c.id_medico
                INNER JOIN usuarios u ON u.id_usuario = m.id_usuario
                LEFT JOIN consultas_medicas cm ON cm.id_cita = c.id_cita
                WHERE c.id_paciente = :id
                ORDER BY c.fecha_hora_inicio DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $citas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../views/pacientes/show.php';
    }

    public function desactivar(int $id): void
    {
        $this->model->inactivar($id);
        header('Location: ?url=pacientes/index');
        exit;
    }

    public function search(): void
    {
        header('Content-Type: application/json');
        $q = trim($_GET['q'] ?? '');
        echo json_encode(['ok' => true, 'data' => $this->model->buscar($q)]);
    }

    private function validaciones(array $datos): array
    {
        $errores = [];

        $nombres = trim($datos['nombres'] ?? '');
        if ($nombres === '') {
            $errores[] = 'El nombre es obligatorio.';
        } elseif (mb_strlen($nombres) < 3) {
            $errores[] = 'El nombre debe tener al menos 3 caracteres.';
        } elseif (!preg_match('/^[A-Za-zÁÉÍÓÚáéíóúÑñÜü\s]+$/u', $nombres)) {
            $errores[] = 'El nombre solo puede contener letras y espacios.';
        }

        $apellidos = trim($datos['apellidos'] ?? '');
        if ($apellidos === '') {
            $errores[] = 'El apellido es obligatorio.';
        } elseif (mb_strlen($apellidos) < 3) {
            $errores[] = 'El apellido debe tener al menos 3 caracteres.';
        } elseif (!preg_match('/^[A-Za-zÁÉÍÓÚáéíóúÑñÜü\s]+$/u', $apellidos)) {
            $errores[] = 'El apellido solo puede contener letras y espacios.';
        }

        $documento = strtoupper(trim($datos['documento_identidad'] ?? ''));
        if ($documento === '') {
            $errores[] = 'El documento de identidad o pasaporte es obligatorio.';
        } elseif (!preg_match('/^(\d{9}|[A-Z]{1,3}\d{6,9})$/', $documento)) {
            $errores[] = 'El documento no tiene un formato válido (DUI: 9 dígitos; Pasaporte: A1234567).';
        }

        $fecha = $datos['fecha_nacimiento'] ?? '';
        if (empty($fecha)) {
            $errores[] = 'La fecha de nacimiento es obligatoria.';
        } else {
            $timestamp = strtotime($fecha);
            if ($timestamp === false) {
                $errores[] = 'La fecha de nacimiento no es válida.';
            } elseif ($timestamp >= strtotime('today')) {
                $errores[] = 'La fecha de nacimiento debe ser anterior a hoy.';
            } elseif ($timestamp < strtotime('-120 years')) {
                $errores[] = 'La fecha de nacimiento no puede ser mayor a 120 años.';
            }
        }

        $genero = $datos['genero'] ?? '';
        if (!in_array($genero, ['M', 'F', 'Otro'], true)) {
            $errores[] = 'El género es obligatorio.';
        }

        $telefono = trim($datos['telefono'] ?? '');
        if ($telefono === '') {
            $errores[] = 'El teléfono es obligatorio.';
        } elseif (!preg_match('/^\d{4}-\d{4}$/', $telefono)) {
            $errores[] = 'El teléfono debe tener el formato 0000-0000.';
        }

        $email = trim($datos['email'] ?? '');
        if ($email === '') {
            $errores[] = 'El correo es obligatorio.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'El correo no tiene un formato válido.';
        }

        $direccion = trim($datos['direccion'] ?? '');
        if ($direccion !== '' && mb_strlen($direccion) < 5) {
            $errores[] = 'La dirección debe tener al menos 5 caracteres si se ingresa.';
        }

        $antecedentes = trim($datos['antecedentes_medicos'] ?? '');

        if (!empty($errores)) {
            return ['datos' => null, 'errores' => $errores];
        }

        return [
            'datos' => [
                'nombres'              => $nombres,
                'apellidos'            => $apellidos,
                'documento_identidad'  => $documento,
                'fecha_nacimiento'      => $fecha,
                'genero'               => $genero,
                'telefono'             => $telefono,
                'email'                => $email,
                'direccion'            => $direccion !== '' ? $direccion : null,
                'antecedentes_medicos' => $antecedentes !== '' ? $antecedentes : null,
            ],
            'errores' => [],
        ];
    }

    private function esAjax(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === "xmlhttprequest";
    }
}