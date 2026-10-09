<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Horarios - UnionDental</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: #f4f7fb;
            color: #1f2937;
        }

        .app {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 230px;
            background: #ffffff;
            border-right: 1px solid #e5e7eb;
            padding: 30px 20px;
            display: flex;
            flex-direction: column;
        }

        .logo {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 40px;
        }

        .logo .union {
            color: #3b82f6;
        }

        .logo .dental {
            color: #1f2937;
        }

        .menu {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .menu a {
            text-decoration: none;
            color: #4b5563;
            padding: 12px 14px;
            border-radius: 8px;
            font-size: 14px;
        }

        .menu a:hover {
            background: #eff6ff;
            color: #2563eb;
        }

        .menu a.active {
            background: #dbeafe;
            color: #2563eb;
            font-weight: 600;
        }

        .logout {
            margin-top: auto;
        }

        .logout a {
            display: block;
            text-decoration: none;
            color: #4b5563;
            padding: 12px 14px;
            border-radius: 8px;
        }

        .logout a:hover {
            background: #fee2e2;
            color: #dc2626;
        }

        .main {
            flex: 1;
            padding: 35px;
        }

        .header {
            margin-bottom: 28px;
        }

        .header h1 {
            margin: 0;
            font-size: 28px;
        }

        .header p {
            color: #6b7280;
            margin-top: 5px;
        }

        .panel {
            background: #ffffff;
            padding: 26px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .info-card {
            background: #eff6ff;
            border-left: 4px solid #2563eb;
            padding: 16px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .btn {
            display: inline-block;
            padding: 10px 16px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
        }

        .btn-secondary {
            background: #e5e7eb;
            color: #374151;
        }

        .btn-secondary:hover {
            background: #d1d5db;
        }

        .user-box {
            margin-top: 25px;
            padding: 14px;
            background: #eff6ff;
            border-radius: 8px;
            font-size: 13px;
        }

        .user-name {
            font-weight: 700;
        }

        .user-role {
            color: #6b7280;
            margin-top: 4px;
        }
    </style>
</head>

<body>

<div class="app">

    <aside class="sidebar">

        <div class="logo">
            <span class="union">Union</span><span class="dental">Dental</span>
        </div>

        <nav class="menu">

            <a href="/?url=home/index">
                Inicio
            </a>

            <a href="/?url=medicos/index">
                Médicos
            </a>

            <a
                href="/?url=medicos/horarios"
                class="active"
            >
                Horarios
            </a>

            <a href="#">
                Calendario
            </a>

            <a href="/?url=pacientes/index">
                Pacientes
            </a>

            <a href="/?url=servicio/index">
                Servicios
            </a>

            <a href="#">
                Configuración
            </a>

        </nav>

        <?php if (!empty($_SESSION['usuario'])): ?>

            <div class="user-box">

                <div class="user-name">
                    <?= htmlspecialchars(
                        $_SESSION['usuario']['nombre']
                        . ' '
                        . $_SESSION['usuario']['apellido']
                    ) ?>
                </div>

                <div class="user-role">
                    <?= htmlspecialchars($_SESSION['usuario']['rol']) ?>
                </div>

            </div>

        <?php endif; ?>

        <div class="logout">

            <a href="/?url=auth/logout">
                Cerrar sesión
            </a>

        </div>

    </aside>

    <main class="main">

        <div class="header">

            <h1>
                Horarios de Atención Médica
            </h1>

            <p>
                Configuración de días y bloques de atención
            </p>

        </div>

        <div class="panel">

            <div class="info-card">

                <strong>
                    Servicio de Disponibilidad Activo:
                </strong>

                El cálculo de horarios para la reserva de citas se procesa
                automáticamente mediante el endpoint JSON de disponibilidad.

            </div>

            <a
                href="/?url=medicos/index"
                class="btn btn-secondary"
            >
                ← Volver a Médicos
            </a>

        </div>

    </main>

</div>

</body>
</html>