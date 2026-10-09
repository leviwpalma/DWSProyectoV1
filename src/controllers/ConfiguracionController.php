<?php

namespace Controllers;

use Config\Database;
use PDO;

class ConfiguracionController
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
        $esAdmin = ($_SESSION['usuario']['rol'] ?? '') === 'Administrador';

        // 1. Obtener datos de la clínica
        $stmtConf = $this->db->query("SELECT * FROM configuracion_clinica WHERE id_config = 1 LIMIT 1");
        $clinica = $stmtConf->fetch(PDO::FETCH_ASSOC) ?: [
            'nombre_clinica' => 'UnionDental',
            'telefono'       => '2222-0000',
            'email'          => 'contacto@uniondental.com',
            'direccion'      => 'San Salvador, El Salvador',
            'horario_apertura' => '08:00',
            'horario_cierre'   => '17:00'
        ];

        // 2. Si es admin, obtener lista de personal de recepción (rol 2)
        $recepcionistas = [];
        if ($esAdmin) {
            $stmtRecep = $this->db->query("SELECT id_usuario, nombre, apellido, email, estado FROM usuarios WHERE id_rol = 2 ORDER BY id_usuario DESC");
            $recepcionistas = $stmtRecep->fetchAll(PDO::FETCH_ASSOC);
        }

        $tab    = $_GET['tab'] ?? 'perfil';
        $error  = $_GET['error'] ?? null;
        $exito  = $_GET['exito'] ?? null;

        require __DIR__ . '/../views/configuracion/index.php';
    }

    public function cambiarPassword(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?url=configuracion/index');
            exit;
        }

        $idUsuario   = (int) $_SESSION['usuario']['id_usuario'];
        $actual      = $_POST['password_actual'] ?? '';
        $nueva       = $_POST['password_nueva'] ?? '';
        $confirmar   = $_POST['password_confirm'] ?? '';

        if (empty($actual) || empty($nueva) || empty($confirmar)) {
            header('Location: ?url=configuracion/index&tab=perfil&error=campos_vacios');
            exit;
        }

        if ($nueva !== $confirmar) {
            header('Location: ?url=configuracion/index&tab=perfil&error=password_no_coincide');
            exit;
        }

        if (mb_strlen($nueva) < 6) {
            header('Location: ?url=configuracion/index&tab=perfil&error=password_corta');
            exit;
        }

        // Validar password actual
        $stmt = $this->db->prepare("SELECT password_hash FROM usuarios WHERE id_usuario = :id LIMIT 1");
        $stmt->execute([':id' => $idUsuario]);
        $hashActual = $stmt->fetchColumn();

        if (!$hashActual || !password_verify($actual, $hashActual)) {
            header('Location: ?url=configuracion/index&tab=perfil&error=password_actual_invalida');
            exit;
        }

        // Actualizar
        $nuevoHash = password_hash($nueva, PASSWORD_BCRYPT);
        $stmtUp = $this->db->prepare("UPDATE usuarios SET password_hash = :p WHERE id_usuario = :id");
        $stmtUp->execute([':p' => $nuevoHash, ':id' => $idUsuario]);

        header('Location: ?url=configuracion/index&tab=perfil&exito=password_actualizada');
        exit;
    }

    public function guardarRecepcionista(): void
    {
        $this->exigirAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?url=configuracion/index&tab=personal');
            exit;
        }

        $nombre   = trim($_POST['nombre'] ?? '');
        $apellido = trim($_POST['apellido'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $pass     = $_POST['password'] ?? '';

        if (empty($nombre) || empty($apellido) || empty($email) || empty($pass)) {
            header('Location: ?url=configuracion/index&tab=personal&error=campos_vacios');
            exit;
        }

        // Verificar correo duplicado
        $stmtCheck = $this->db->prepare("SELECT COUNT(*) FROM usuarios WHERE email = :email");
        $stmtCheck->execute([':email' => $email]);
        if ($stmtCheck->fetchColumn() > 0) {
            header('Location: ?url=configuracion/index&tab=personal&error=email_duplicado');
            exit;
        }

        $hash = password_hash($pass, PASSWORD_BCRYPT);
        $stmt = $this->db->prepare("INSERT INTO usuarios (id_rol, nombre, apellido, email, password_hash, estado) VALUES (2, :nombre, :apellido, :email, :hash, 'activo')");
        $stmt->execute([
            ':nombre'   => $nombre,
            ':apellido' => $apellido,
            ':email'    => $email,
            ':hash'     => $hash
        ]);

        header('Location: ?url=configuracion/index&tab=personal&exito=recepcionista_creada');
        exit;
    }

    public function cambiarEstadoUsuario(int $id): void
    {
        $this->exigirAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?url=configuracion/index&tab=personal');
            exit;
        }

        $nuevoEstado = $_POST['estado'] ?? 'inactivo';
        if (!in_array($nuevoEstado, ['activo', 'inactivo'], true)) {
            header('Location: ?url=configuracion/index&tab=personal');
            exit;
        }

        $stmt = $this->db->prepare("UPDATE usuarios SET estado = :e WHERE id_usuario = :id AND id_rol = 2");
        $stmt->execute([':e' => $nuevoEstado, ':id' => $id]);

        header('Location: ?url=configuracion/index&tab=personal&exito=estado_actualizado');
        exit;
    }

    public function guardarClinica(): void
    {
        $this->exigirAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?url=configuracion/index&tab=clinica');
            exit;
        }

        $nombre    = trim($_POST['nombre_clinica'] ?? 'UnionDental');
        $telefono  = trim($_POST['telefono'] ?? '');
        $email     = trim($_POST['email'] ?? '');
        $direccion = trim($_POST['direccion'] ?? '');

        $sql = "UPDATE configuracion_clinica 
                SET nombre_clinica = :nom, telefono = :tel, email = :email, direccion = :dir 
                WHERE id_config = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':nom'   => $nombre,
            ':tel'   => $telefono,
            ':email' => $email,
            ':dir'   => $direccion
        ]);

        header('Location: ?url=configuracion/index&tab=clinica&exito=clinica_actualizada');
        exit;
    }

    private function exigirAdmin(): void
    {
        if (($_SESSION['usuario']['rol'] ?? '') !== 'Administrador') {
            http_response_code(403);
            die('Acceso denegado.');
        }
    }
}