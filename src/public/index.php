<?php
declare(strict_types=1);

// Cargar configuración de base de datos
require_once __DIR__ . '/../config/Database.php';

// Manejo básico de URL (Front Controller)
$url = $_GET['url'] ?? 'home';
$urlParts = explode('/', filter_var(rtrim($url, '/'), FILTER_SANITIZE_URL));

$controllerName = !empty($urlParts[0]) ? ucfirst($urlParts[0]) . 'Controller' : 'HomeController';
$actionName = $urlParts[1] ?? 'index';

$controllerFile = __DIR__ . '/../controllers/' . $controllerName . '.php';

if (file_exists($controllerFile)) {
    require_once $controllerFile;
    $controllerClass = "Controllers\\{$controllerName}";
    if (class_exists($controllerClass)) {
        $controller = new $controllerClass();
        if (method_exists($controller, $actionName)) {
            $controller->$actionName();
            exit;
        }
    }
}

// Vista por defecto 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clínica Dental - Core MVC</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; margin: 40px; background: #f8fafc; color: #1e293b; }
        .card { background: white; padding: 24px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); max-width: 650px; margin: auto; }
        h1 { color: #0f172a; margin-top: 0; }
        .badge { background: #dcfce7; color: #166534; padding: 4px 10px; border-radius: 9999px; font-weight: 600; font-size: 0.85rem; }
        code { background: #f1f5f9; padding: 2px 6px; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="card">
        <span class="badge">Sistema Base Operativo</span>
        <h1>Clínica Dental - MVC Core</h1>
        <p>Infraestructura levantada y base de datos lista para integración de módulos.</p>
        <ul>
            <li><strong>Ruta solicitada:</strong> <code>/<?= htmlspecialchars($url) ?></code></li>
            <li><strong>Controlador esperado:</strong> <code>src/controllers/<?= htmlspecialchars($controllerName) ?>.php</code></li>
            <li><strong>Acción / Método:</strong> <code><?= htmlspecialchars($actionName) ?>()</code></li>
        </ul>
        <p><em>Rama actual de trabajo: <code>feature/docker-core-mvc</code></em></p>
    </div>
</body>
</html>