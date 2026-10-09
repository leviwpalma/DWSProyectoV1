<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titulo ?? $title ?? 'UnionDental') ?></title>
    <!-- Bootstrap 5 y Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="css/pacientes.css" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Arial, sans-serif; background: #f4f7fb; color: #1f2937; }
        .app { display: flex; min-height: 100vh; }
        .sidebar { width: 240px; background: #ffffff; border-right: 1px solid #e5e7eb; padding: 30px 20px; display: flex; flex-direction: column; flex-shrink: 0; }
        .logo { font-size: 24px; font-weight: 700; margin-bottom: 35px; }
        .logo .union { color: #3b82f6; }
        .logo .dental { color: #1f2937; }
        .menu { display: flex; flex-direction: column; gap: 8px; }
        .menu a { text-decoration: none; color: #4b5563; padding: 11px 14px; border-radius: 8px; font-size: 14px; display: flex; align-items: center; gap: 10px; transition: 0.2s; }
        .menu a:hover { background: #eff6ff; color: #2563eb; }
        .menu a.active { background: #dbeafe; color: #2563eb; font-weight: 600; }
        .user-box { margin-top: auto; padding: 12px; background: #eff6ff; border-radius: 8px; font-size: 13px; margin-bottom: 12px; }
        .user-name { font-weight: 700; color: #1e293b; }
        .user-role { color: #64748b; font-size: 12px; }
        .logout a { display: block; text-decoration: none; color: #dc2626; padding: 10px 14px; border-radius: 8px; font-size: 14px; font-weight: 600; }
        .logout a:hover { background: #fee2e2; }
        .main-content { flex: 1; padding: 32px; overflow-y: auto; }
    </style>
</head>
<body>
<div class="app">
    <aside class="sidebar">
        <div class="logo">
            <span class="union">Union</span><span class="dental">Dental</span>
        </div>
        <nav class="menu">
            <a href="?url=home/index"><i class="bi bi-house"></i> Inicio</a>
            <a href="?url=medicos/index"><i class="bi bi-person-badge"></i> Médicos</a>
            <a href="?url=medicos/horarios"><i class="bi bi-clock"></i> Horarios</a>
            <a href="?url=pacientes/index"><i class="bi bi-people"></i> Pacientes</a>
            <a href="?url=servicio/index"><i class="bi bi-clipboard2-pulse"></i> Servicios</a>
        </nav>

        <?php if (!empty($_SESSION['usuario'])): ?>
            <div class="user-box">
                <div class="user-name">
                    <?= htmlspecialchars($_SESSION['usuario']['nombre'] . ' ' . $_SESSION['usuario']['apellido']) ?>
                </div>
                <div class="user-role">
                    <?= htmlspecialchars($_SESSION['usuario']['rol']) ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="logout">
            <a href="?url=auth/logout"><i class="bi bi-box-arrow-right"></i> Cerrar sesión</a>
        </div>
    </aside>

    <main class="main-content">