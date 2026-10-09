<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Servicios - UnionDental</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Arial, Helvetica, sans-serif; background: #f4f7fb; color: #1f2937; }
        .app { display: flex; min-height: 100vh; }

        /* Estilos del sidebar unificados (230px) */
        .sidebar { width: 230px; background: #ffffff; border-right: 1px solid #e5e7eb; padding: 30px 20px; display: flex; flex-direction: column; flex-shrink: 0; }
        .logo { font-size: 24px; font-weight: 700; margin-bottom: 40px; }
        .logo .union { color: #3b82f6; }
        .logo .dental { color: #1f2937; }
        .menu { display: flex; flex-direction: column; gap: 8px; }
        .menu a { text-decoration: none; color: #4b5563; padding: 12px 14px; border-radius: 8px; font-size: 14px; }
        .menu a:hover { background: #eff6ff; color: #2563eb; }
        .menu a.active { background: #dbeafe; color: #2563eb; font-weight: 600; }
        .user-box { margin-top: auto; padding: 14px; background: #eff6ff; border-radius: 8px; font-size: 13px; margin-bottom: 15px; }
        .user-name { font-weight: 700; color: #1f2937; }
        .user-role { color: #6b7280; margin-top: 4px; }
        .logout a { display: block; text-decoration: none; color: #dc2626; padding: 12px 14px; border-radius: 8px; font-size: 14px; }
        .logout a:hover { background: #fee2e2; }

        .main { flex: 1; padding: 35px; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
        .header h1 { margin: 0; font-size: 28px; }
        .header p { margin: 6px 0 0; color: #6b7280; font-size: 14px; }

        .btn-primary {
            display: inline-block;
            background: #2563eb;
            color: white;
            text-decoration: none;
            padding: 10px 18px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
        }
        .btn-primary:hover { background: #1d4ed8; }

        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        table { width: 100%; border-collapse: collapse; text-align: left; }
        thead { background: #f8fafc; }
        th, td { padding: 14px 16px; border-bottom: 1px solid #e5e7eb; font-size: 14px; }
        th { font-weight: 600; color: #475569; }
        tr:hover td { background: #fbfcfe; }

        .estado {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
        }
        .estado.activo { background: #dcfce7; color: #166534; }
        .estado.inactivo { background: #fee2e2; color: #991b1b; }

        .acciones a {
            text-decoration: none;
            margin-right: 12px;
            font-weight: 600;
            font-size: 13px;
        }
        .editar { color: #2563eb; }
        .editar:hover { text-decoration: underline; }
        .desactivar { color: #dc2626; }
        .desactivar:hover { text-decoration: underline; }

        .empty {
            padding: 35px;
            text-align: center;
            color: #6b7280;
            font-size: 14px;
        }
    </style>
</head>

<body>
<div class="app">

    <?php require __DIR__ . '/../layouts/sidebar.php'; ?>

    <main class="main">

        <div class="header">
            <div>
                <h1>Catálogo de Servicios</h1>
                <p>Administra los procedimientos, duración y precios de referencia.</p>
            </div>

            <a href="?url=servicio/crear" class="btn-primary">
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
                            <th style="text-align: right;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($servicios as $servicio): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($servicio['nombre']) ?></strong></td>
                                <td><?= htmlspecialchars($servicio['categoria']) ?></td>
                                <td><?= (int) $servicio['duracion_minutos'] ?> min</td>
                                <td>$<?= number_format((float) $servicio['precio_ref'], 2) ?></td>
                                <td>
                                    <span class="estado <?= htmlspecialchars($servicio['estado']) ?>">
                                        <?= ucfirst(htmlspecialchars($servicio['estado'])) ?>
                                    </span>
                                </td>
                                <td class="acciones" style="text-align: right;">
                                    <a href="?url=servicio/editar/<?= (int) $servicio['id_servicio'] ?>" class="editar">
                                        Editar
                                    </a>

                                    <?php if ($servicio['estado'] === 'activo'): ?>
                                        <a href="?url=servicio/desactivar/<?= (int) $servicio['id_servicio'] ?>" 
                                           class="desactivar"
                                           onclick="return confirm('¿Deseas desactivar este servicio?')">
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