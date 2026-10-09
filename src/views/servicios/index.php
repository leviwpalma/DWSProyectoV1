<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Servicios - UnionDental</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f7fb;
            color: #1f2937;
        }

        .app {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 240px;
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

        .logo span:first-child {
            color: #3b82f6;
        }

        .logo span:last-child {
            color: #1f2937;
        }

        .menu {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .menu a {
            text-decoration: none;
            color: #4b5563;
            padding: 12px 14px;
            border-radius: 8px;
            transition: 0.2s;
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
            padding: 36px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .header h1 {
            margin: 0;
            font-size: 28px;
        }

        .header p {
            margin: 6px 0 0;
            color: #6b7280;
        }

        .btn-primary {
            display: inline-block;
            background: #2563eb;
            color: white;
            text-decoration: none;
            padding: 11px 18px;
            border-radius: 8px;
            font-weight: 600;
        }

        .btn-primary:hover {
            background: #1d4ed8;
        }

        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: #f8fafc;
        }

        th,
        td {
            padding: 16px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }

        th {
            font-size: 14px;
            color: #475569;
        }

        td {
            font-size: 14px;
        }

        .estado {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
        }

        .estado.activo {
            background: #dcfce7;
            color: #166534;
        }

        .estado.inactivo {
            background: #fee2e2;
            color: #991b1b;
        }

        .acciones a {
            text-decoration: none;
            margin-right: 12px;
            font-weight: 600;
            font-size: 13px;
        }

        .editar {
            color: #2563eb;
        }

        .desactivar {
            color: #dc2626;
        }

        .empty {
            padding: 30px;
            text-align: center;
            color: #6b7280;
        }
    </style>
</head>

<body>

<div class="app">

    <aside class="sidebar">

        <div class="logo">
            <span>Union</span><span>Dental</span>
        </div>

        <nav class="menu">

            <a href="/?url=home/index">
                Inicio
            </a>

            <a href="/?url=medicos/index">
                Médicos
            </a>

            <a href="/?url=medicos/horarios">
                Horarios
            </a>

            <a href="#">
                Calendario
            </a>

            <a href="/?url=pacientes/index">
                Pacientes
            </a>

            <a
                href="/?url=servicio/index"
                class="active"
            >
                Servicios
            </a>

            <a href="#">
                Configuración
            </a>

        </nav>

        <div class="logout">
            <a href="/?url=auth/logout">
                Cerrar sesión
            </a>
        </div>

    </aside>

    <main class="main">

        <div class="header">

            <div>
                <h1>Catálogo de Servicios</h1>

                <p>
                    Administra los procedimientos, duración y precios de referencia.
                </p>
            </div>

            <a
                href="/?url=servicio/crear"
                class="btn-primary"
            >
                + Nuevo servicio
            </a>

        </div>

        <div class="card">

            <?php if (empty($servicios)): ?>

                <div class="empty">
                    No hay servicios registrados.
                </div>

            <?php else: ?>

                <table>

                    <thead>
                        <tr>
                            <th>Servicio</th>
                            <th>Categoría</th>
                            <th>Duración</th>
                            <th>Precio</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php foreach ($servicios as $servicio): ?>

                            <tr>

                                <td>
                                    <?= htmlspecialchars($servicio['nombre']) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($servicio['categoria']) ?>
                                </td>

                                <td>
                                    <?= (int) $servicio['duracion_minutos'] ?> min
                                </td>

                                <td>
                                    $<?= number_format(
                                        (float) $servicio['precio_ref'],
                                        2
                                    ) ?>
                                </td>

                                <td>
                                    <span
                                        class="estado <?= htmlspecialchars($servicio['estado']) ?>"
                                    >
                                        <?= ucfirst(
                                            htmlspecialchars($servicio['estado'])
                                        ) ?>
                                    </span>
                                </td>

                                <td class="acciones">

                                    <a
                                        href="/?url=servicio/editar/<?= (int) $servicio['id_servicio'] ?>"
                                        class="editar"
                                    >
                                        Editar
                                    </a>

                                    <?php if ($servicio['estado'] === 'activo'): ?>

                                        <a
                                            href="/?url=servicio/desactivar/<?= (int) $servicio['id_servicio'] ?>"
                                            class="desactivar"
                                            onclick="return confirm('¿Deseas desactivar este servicio?')"
                                        >
                                            Desactivar
                                        </a>

                                    <?php endif; ?>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            <?php endif; ?>

        </div>

    </main>

</div>

</body>
</html>