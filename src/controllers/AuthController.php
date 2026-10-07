<?php

namespace Controllers;

require_once __DIR__ . '/../models/Usuario.php';

use Models\Usuario;

class AuthController
{
    private Usuario $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new Usuario();
    }

    public function login(): void
    {
        if (isset($_SESSION['usuario'])) {
            header('Location: ?url=home/index');
            exit;
        }

        require __DIR__ . '/../views/auth/login.php';
    }

    public function autenticar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            return;
        }

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $error = null;

        if ($email === '' || $password === '') {
            $error = 'Ingrese su correo y contraseña.';

            require __DIR__ . '/../views/auth/login.php';
            return;
        }

        $usuario = $this->usuarioModel->buscarPorEmail($email);

        if (!$usuario) {
            $error = 'Correo o contraseña incorrectos.';

            require __DIR__ . '/../views/auth/login.php';
            return;
        }

        if ($usuario['estado'] !== 'activo') {
            $error = 'Este usuario se encuentra inactivo.';

            require __DIR__ . '/../views/auth/login.php';
            return;
        }

        if (!password_verify($password, $usuario['password_hash'])) {
            $error = 'Correo o contraseña incorrectos.';

            require __DIR__ . '/../views/auth/login.php';
            return;
        }

        $_SESSION['usuario'] = [
            'id_usuario' => $usuario['id_usuario'],
            'id_rol' => $usuario['id_rol'],
            'nombre' => $usuario['nombre'],
            'apellido' => $usuario['apellido'],
            'email' => $usuario['email'],
            'rol' => $usuario['rol']
        ];

        header('Location: ?url=home/index');
        exit;
    }

    public function logout(): void
    {
        $_SESSION = [];

        session_destroy();

        header('Location: ?url=auth/login');
        exit;
    }
}