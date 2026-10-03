<?php

namespace Controllers;

require_once __DIR__ . '/../models/Servicio.php';
require_once __DIR__ . '/../models/CategoriaServicio.php';

use Models\Servicio;
use Models\CategoriaServicio;

class ServicioController
{
    private Servicio $servicioModel;
    private CategoriaServicio $categoriaModel;

    public function __construct()
    {
        $this->servicioModel = new Servicio();
        $this->categoriaModel = new CategoriaServicio();
    }

    /**
     * Mostrar listado completo de servicios.
     */
    public function index(): void
    {
        $servicios = $this->servicioModel->obtenerTodos();

        require __DIR__ . '/../views/servicios/index.php';
    }

    /**
     * Mostrar formulario para crear un servicio.
     */
    public function crear(): void
    {
        $categorias = $this->categoriaModel->obtenerActivas();

        require __DIR__ . '/../views/servicios/crear.php';
    }

    /**
     * Guardar un nuevo servicio.
     */
    public function guardar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo 'Método no permitido.';
            return;
        }

        $nombre = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $idCategoria = (int) ($_POST['id_categoria'] ?? 0);
        $duracion = (int) ($_POST['duracion_minutos'] ?? 0);
        $precio = (float) ($_POST['precio_ref'] ?? -1);

        $errores = [];

        // Validar nombre
        if ($nombre === '') {
            $errores[] = 'El nombre del servicio es obligatorio.';
        }

        // Validar categoría
        if ($idCategoria <= 0) {
            $errores[] = 'Debe seleccionar una categoría válida.';
        }

        // Validar duración
        if ($duracion <= 0) {
            $errores[] = 'La duración debe ser mayor que 0.';
        }

        // Validar precio
        if ($precio < 0) {
            $errores[] = 'El precio no puede ser negativo.';
        }

        // Validar que la categoría exista y esté activa
        if ($idCategoria > 0) {
            $categoria = $this->categoriaModel->obtenerPorId($idCategoria);

            if (!$categoria || $categoria['estado'] !== 'activo') {
                $errores[] = 'La categoría seleccionada no existe o está inactiva.';
            }
        }

        // Si hay errores, volver al formulario
        if (!empty($errores)) {
            $categorias = $this->categoriaModel->obtenerActivas();

            require __DIR__ . '/../views/servicios/crear.php';
            return;
        }

        // Crear servicio
        $this->servicioModel->crear([
            'id_categoria' => $idCategoria,
            'nombre' => $nombre,
            'descripcion' => $descripcion,
            'duracion_minutos' => $duracion,
            'precio_ref' => $precio,
            'estado' => 'activo'
        ]);

        header('Location: ?url=servicio/index');
        exit;
    }

    /**
     * Mostrar formulario de edición.
     */
    public function editar(int $id): void
    {
        $servicio = $this->servicioModel->obtenerPorId($id);

        if (!$servicio) {
            http_response_code(404);
            echo 'Servicio no encontrado.';
            return;
        }

        $categorias = $this->categoriaModel->obtenerActivas();

        require __DIR__ . '/../views/servicios/editar.php';
    }

    /**
     * Actualizar un servicio existente.
     */
    public function actualizar(int $id): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo 'Método no permitido.';
            return;
        }

        $servicioActual = $this->servicioModel->obtenerPorId($id);

        if (!$servicioActual) {
            http_response_code(404);
            echo 'Servicio no encontrado.';
            return;
        }

        $nombre = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $idCategoria = (int) ($_POST['id_categoria'] ?? 0);
        $duracion = (int) ($_POST['duracion_minutos'] ?? 0);
        $precio = (float) ($_POST['precio_ref'] ?? -1);
        $estado = $_POST['estado'] ?? 'activo';

        $errores = [];

        // Validar nombre
        if ($nombre === '') {
            $errores[] = 'El nombre del servicio es obligatorio.';
        }

        // Validar categoría
        if ($idCategoria <= 0) {
            $errores[] = 'Debe seleccionar una categoría válida.';
        }

        // Validar duración
        if ($duracion <= 0) {
            $errores[] = 'La duración debe ser mayor que 0.';
        }

        // Validar precio
        if ($precio < 0) {
            $errores[] = 'El precio no puede ser negativo.';
        }

        // Validar estado
        if (!in_array($estado, ['activo', 'inactivo'], true)) {
            $errores[] = 'El estado seleccionado no es válido.';
        }

        // Validar que la categoría exista y esté activa
        if ($idCategoria > 0) {
            $categoria = $this->categoriaModel->obtenerPorId($idCategoria);

            if (!$categoria || $categoria['estado'] !== 'activo') {
                $errores[] = 'La categoría seleccionada no existe o está inactiva.';
            }
        }

        // Si hay errores, volver al formulario
        if (!empty($errores)) {
            $servicio = [
                'id_servicio' => $id,
                'id_categoria' => $idCategoria,
                'nombre' => $nombre,
                'descripcion' => $descripcion,
                'duracion_minutos' => $duracion,
                'precio_ref' => $precio,
                'estado' => $estado
            ];

            $categorias = $this->categoriaModel->obtenerActivas();

            require __DIR__ . '/../views/servicios/editar.php';
            return;
        }

        // Actualizar servicio
        $this->servicioModel->actualizar($id, [
            'id_categoria' => $idCategoria,
            'nombre' => $nombre,
            'descripcion' => $descripcion,
            'duracion_minutos' => $duracion,
            'precio_ref' => $precio,
            'estado' => $estado
        ]);

        header('Location: ?url=servicio/index');
        exit;
    }

 
    public function desactivar(int $id): void
    {
        $servicio = $this->servicioModel->obtenerPorId($id);

        if (!$servicio) {
            http_response_code(404);
            echo 'Servicio no encontrado.';
            return;
        }

        $this->servicioModel->desactivar($id);

        header('Location: ?url=servicio/index');
        exit;
    }

  
    public function listarActivos(): void
    {
        $servicios = $this->servicioModel->obtenerActivos();

        header('Content-Type: application/json; charset=utf-8');

        echo json_encode([
            'error' => false,
            'servicios' => $servicios
        ]);

        exit;
    }

    /**
     * Validación RN-03.
     *
     * Verifica que un servicio esté registrado y activo.
     */
    public function validarServicio(int $id): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $valido = $this->servicioModel->esServicioValido($id);

        if (!$valido) {
            http_response_code(400);

            echo json_encode([
                'error' => true,
                'message' => 'El servicio seleccionado no existe o está inactivo.'
            ]);

            exit;
        }

        echo json_encode([
            'error' => false,
            'message' => 'Servicio válido.'
        ]);

        exit;
    }
}